<?php

namespace xjryanse\user\service;

use xjryanse\system\interfaces\MainModelInterface;
use xjryanse\logic\Arrays;

/**
 * 用户账户流水
 */
class UserAccountLogService extends Base implements MainModelInterface {

    use \xjryanse\traits\InstTrait;
    use \xjryanse\traits\MainModelTrait;

    protected static $mainModel;
    protected static $mainModelClass = '\\xjryanse\\user\\model\\UserAccountLog';

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
            self::accountCreate($userId, $accountType);
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
        
        return self::count($con) ? self::find( $con ) : false;
    }

    public static function extraAfterSave(&$data, $uuid) {
        $info       = self::getInstance( $uuid )->get();
        $accountId  = Arrays::value($info, 'account_id');
        //更新账户余额
        UserAccountService::getInstance( $accountId )->updateRemain();
    }
    
    public static function extraAfterUpdate(&$data, $uuid) {
        $info       = self::getInstance( $uuid )->get();
        $accountId  = Arrays::value($info, 'account_id');
        //更新账户余额
        UserAccountService::getInstance( $accountId )->updateRemain();
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
