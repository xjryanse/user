<?php

namespace xjryanse\user\service;

use xjryanse\finance\service\FinanceAccountLogService;
use xjryanse\finance\service\FinanceAccountService;
use xjryanse\finance\service\FinanceStatementService;
use xjryanse\system\interfaces\MainModelInterface;
use xjryanse\user\service\UserAccountService;
use xjryanse\order\service\OrderService;
use xjryanse\order\service\OrderFlowNodeService;
use xjryanse\logic\Arrays;
use xjryanse\logic\DataCheck;
use xjryanse\user\model\UserAccountLog;
use think\Db;

/**
 * 用户账户流水
 */
class UserAccountLogService extends Base implements MainModelInterface {

    use \xjryanse\traits\InstTrait;
    use \xjryanse\traits\MainModelTrait;

    protected static $mainModel;
    protected static $mainModelClass = '\\xjryanse\\user\\model\\UserAccountLog';
    //直接执行后续触发动作
    protected static $directAfter = true;        

    /**
     * 取最后一个来源表id，一般用于校验条件
     * @param type $accountId   账号
     * @param type $changeCate  变动类型
     * @param type $fromTable   表名
     * @param type $con         条件
     * @return type
     */
    public static function lastFromTableId( $accountId, $changeCate,$fromTable = '',$con=[])
    {
        $con[] = ['account_id','=',$accountId];
        $con[] = ['change_cate','=',$changeCate];
        if($fromTable){
            $con[] = ['from_table','=',$fromTable];
        }
        return self::mainModel()->where($con)->order('id desc')->value('from_table_id');
    }
    /**
     * 入账逻辑
     * @param type $userId      用户id
     * @param type $accountType 账户类型
     * @param type $value       变动值
     * @param type $data        额外数据
     * @return type
     */
    public static function doIncome( $userId, $accountType, $value, $data= [] )
    {
        //事务校验
        UserAccountService::checkTransaction();
        //账户校验
        if(!UserAccountService::getByUserAccountType($userId, $accountType)){
            UserAccountService::accountCreate($userId, $accountType);
        }

        $info = UserAccountService::getByUserAccountType( $userId, $accountType );
        //新增流水
        $data['user_id']        = $userId;
        $data['account_id']     = $info['id'];
        $data['before_quota']   = $info['current'];
        $data['change']         = $value;
        $data['current_quota']  = $info['current'] + $value;
        $res = self::save( $data );
        return $res;
    }
    /**
     * 出账逻辑
     * @param type $userId
     * @param type $accountType
     * @param type $value
     * @param type $data
     * @param type $permitNegative
     * @return type
     * @throws Exception
     */
    public static function doOutcome( $userId, $accountType, $value, $data= [] ,$permitNegative = false )
    {
        //事务校验
        self::checkTransaction();
        //账户校验
        if(!UserAccountService::getByUserAccountType($userId, $accountType)){
            self::accountCreate($userId, $accountType);
        }

        $info = UserAccountService::getByUserAccountType( $userId, $accountType );
        if( !$permitNegative && $info['current'] - abs($value) < 0 ){
            throw new Exception('账户余额不足');
        }
        //新增流水
        $data['user_id']        = $userId;
        $data['account_id']     = $info['id'];
        $data['before_quota']   = $info['current'];
        $data['change']         = -1 * abs( $value );
        $data['current_quota']  = $info['current'] - $value;
        $res = self::save( $data );
        return $res;
    }
    
    public static function extraPreSave( &$data, $uuid){
        $keys = ['account_id','change'];
        DataCheck::must($data, $keys);
        $accountId          = Arrays::value($data, 'account_id');
        $accountInfo        = UserAccountService::getInstance($accountId)->get();
        $data['user_id']    = $accountInfo['user_id'];
        $data['account_type']   = $accountInfo['account_type'];
        $data['before_quota']   = $accountInfo['current'];
        $data['current_quota']  = $accountInfo['current'] + $data['change'];
        
        return $data;
    }
    /**
     * 来源表和来源id查是否有记录：
     * 一般用于判断该笔记录是否已入账，避免重复入账
     * @param type $fromTable   来源表
     * @param type $fromTableId 来源表id
     */
    public static function hasLog( $fromTable, $fromTableId ,$con = [] )
    {
        //`from_table` varchar(255) DEFAULT '' COMMENT '来源表',
        //`from_table_id` varchar(32) DEFAULT '' COMMENT '来源表id',
        $con[] = ['from_table','=',$fromTable];
        $con[] = ['from_table_id','=',$fromTableId];
        // 20210919，考虑异步触发器；无session
        $info = self::mainModel()->where($con)->find();
        return $info ? : false;
    }

    public static function extraAfterSave(&$data, $uuid) {
        self::extraAfterUpdate($data, $uuid);
        $fromTable = Arrays::value($data, 'from_table');
        $fromTableId = Arrays::value($data, 'from_table_id');
        if($fromTable == OrderService::getTable()){
            OrderFlowNodeService::lastNodeFinishAndNext($fromTableId);
        }
    }
    
    public static function extraAfterUpdate(&$data, $uuid) {
        $info       = self::getInstance( $uuid )->get();
        $accountId  = Arrays::value($info, 'account_id');
        //更新账户余额
        UserAccountService::getInstance( $accountId )->updateRemain();
        $accountType = UserAccountService::getInstance($accountId)->fAccountType();
        //是余额的，入账一下账户余额
        $fromTable = self::mainModel()->getTable();
        if( $accountType == 'money' &&  !FinanceAccountLogService::hasLog( $fromTable, $uuid ) ){
        // if( $accountType == 'money' && $info['statement_id'] && !FinanceAccountLogService::statementHasLog( $info['statement_id'] ) ){
            self::checkTransaction();
            self::addFinanceAccountLog( $info );
        }
    }
    
    /*
     * 写入商户
     */
    public static function addFinanceAccountLog(UserAccountLog $log)
    {
        $statementId            = $log['statement_id'];
        $statement              = FinanceStatementService::getInstance( $statementId )->get();

        $data['company_id']     = $log['company_id'];
        $data['user_id']        = $log['user_id'];
        $data['customer_id']    = Arrays::value($statement, 'customer_id');
        $data['money']          = Arrays::value($log, 'change');
        $data['statement_id']   = $statementId;
        $data['reason']         = Arrays::value($log, 'change_reason');
        $data['change_type']    = $data['money'] >= 0 ? 2 : 1;  //正数应付；负数应收
        $data['account_id']     = FinanceAccountService::getIdByAccountType($log['company_id'], 'money');      //线上余额
        $data['from_table']     = self::mainModel()->getTable();
        $data['from_table_id']  = $log['id'];
        return FinanceAccountLogService::save($data);
    }    
    
    /**
     *
     */
    public function fId() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    /**
     *
     */
    public function fAppId() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    /**
     *
     */
    public function fCompanyId() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    /**
     * 用户id
     */
    public function fUserId() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    /**
     * 账户id
     */
    public function fAccountId() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    /**
     * 变动前余额
     */
    public function fBeforeQuota() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    /**
     * 余额变动值
     */
    public function fChange() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    /**
     * 变动后余额
     */
    public function fCurrentQuota() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    /**
     * 变动分类
     */
    public function fChangeCate() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    /**
     * 变动原因
     */
    public function fChangeReason() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    /**
     * 来源表
     */
    public function fFromTable() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    /**
     * 来源表id
     */
    public function fFromTableId() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    /**
     * 结算状态：0未结算、1已结算
     */
    public function fBalanceStatus() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    /**
     * 排序
     */
    public function fSort() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    /**
     * 状态(0禁用,1启用)
     */
    public function fStatus() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    /**
     * 有使用(0否,1是)
     */
    public function fHasUsed() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    /**
     * 锁定（0：未锁，1：已锁）
     */
    public function fIsLock() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    /**
     * 锁定（0：未删，1：已删）
     */
    public function fIsDelete() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    /**
     * 备注
     */
    public function fRemark() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    /**
     * 创建者，user表
     */
    public function fCreater() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    /**
     * 更新者，user表
     */
    public function fUpdater() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    /**
     * 创建时间
     */
    public function fCreateTime() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    /**
     * 更新时间
     */
    public function fUpdateTime() {
        return $this->getFFieldValue(__FUNCTION__);
    }

}
