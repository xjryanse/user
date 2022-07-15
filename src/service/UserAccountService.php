<?php

namespace xjryanse\user\service;

use xjryanse\system\interfaces\MainModelInterface;
use xjryanse\logic\Arrays2d;
use xjryanse\logic\Sql;
use think\Db;

/**
 * 用户账户
 */
class UserAccountService implements MainModelInterface {

    use \xjryanse\traits\InstTrait;
    use \xjryanse\traits\MainModelTrait;

    protected static $mainModel;
    protected static $mainModelClass = '\\xjryanse\\user\\model\\UserAccount';
    //直接执行后续触发动作
    protected static $directAfter = true;  

    /**
     * 更新余额
     */
    public function updateRemain()
    {
//        $con[] = ['account_id','=',$this->uuid];
//        $money = UserAccountLogService::mainModel()->where($con)->sum('change');
//        return self::mainModel()->where('id',$this->uuid)->update(['current'=>$money]);
        
        $mainTable  =   self::getTable();
        $mainField  =   "current";
        $dtlTable   =   UserAccountLogService::getTable();
        $dtlStaticField     =   "change";
        $dtlUniField        =   "account_id";
        $dtlCon[] = ['main.id','=',$this->uuid];
        $sql = Sql::staticUpdate($mainTable, $mainField, $dtlTable, $dtlStaticField, $dtlUniField,$dtlCon);
        return Db::query($sql);
    }
    
    /**
     * 创建账户
     * @param type $userId  用户id
     * @param type $keys    键
     */
    public static function accountCreate( $userId ,$keys = [] )
    {
        //获取现有账户
        $accounts = self::getByUserId($userId);
        //兼容字符串
        if(!is_array( $keys )){
            $keys = [ $keys ];
        }
        //循环判断，未创建则创建
        foreach($keys as $key){
            if(!isset($accounts[$key])){
                //创建用户账户
                self::createUserAccount($userId, $key);
            }
        }
    }    
    
    /**
     * 按userid取值，按key替换。
     * @param type $userId
     */
    public static function getByUserId( $userId )
    {
        $res = UserService::getInstance($userId)->objAttrsList('userAccount');
        return Arrays2d::fieldSetKey($res, 'account_type');
//        $values = self::listsByField( 'user_id' , $userId );
//        $values = $values ? $values->toArray() : [] ;
//        $keys   = array_column( $values,'account_type');
//        return array_combine($keys, $values);
    }    
    
    public static function extraAfterSave(&$data, $uuid) {
        UserService::getInstance($data['user_id'])->objAttrsPush('userAccount',$data);
    }
    /*
     * 用户id和账户类型创建，一个类型只能有一个账户
     */
    public static function createUserAccount( $userId, $accountType )
    {
        $con[] = ['user_id','=',$userId];
        $con[] = ['account_type','=',$accountType];
        if(self::count($con)){
            return false;
        } else {
            $data = [];
            $data['user_id']        = $userId;
            $data['account_type']   = $accountType;
            return self::save($data);
        }
    }
    
    /**
     * 根据用户和账户类型取单条数据
     * @param type $userId
     * @param type $accountType
     * @return type
     */
    public static function getByUserAccountType($userId, $accountType) {
        $lists = UserService::getInstance($userId)->objAttrsList('userAccount');
        $con[] = ['account_type', '=', $accountType];
        return Arrays2d::listFind($lists, $con);
//        $con[] = ['user_id', '=', $userId];
//        $con[] = ['account_type', '=', $accountType];
//        return self::find($con);
    }
    /**
     * 获取用户的账户id，没id自动创建
     */
    public static function userAccountId($userId, $accountType){
        $con[] = ['user_id','=',$userId];
        $con[] = ['account_type','=',$accountType];
        $id = self::mainModel()->where($con)->value('id');
        if(!$id){
            $data['user_id']        = $userId;
            $data['account_type']   = $accountType;
            $id = self::saveGetId($data);
        }
        return $id;
    }
    /*     * *
     * 获取用户账户信息
     */

    public static function getUserAccounts($userId) {
        return UserService::getInstance($userId)->objAttrsList('userAccount');
//        $con[] = ['user_id', '=', $userId];
//        return self::lists($con);
    }

    /**
     * 入账更新
     */
    public function income($value, $updateTotal = true) {
        self::checkTransaction();
        //总得额更新（只增不减）
        if ($updateTotal) {
            self::mainModel()->where('id', $this->uuid)->setInc('total', $value);
        }
        //账户余额更新
        return self::mainModel()->where('id', $this->uuid)->setInc('current', $value);
    }

    /**
     * 资金出账更新
     */
    public function outcome($value) {
        self::checkTransaction();
        //账户余额更新
        return self::mainModel()->where('id', $this->uuid)->setDec('current', $value);
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
     * 账户名称
     */
    public function fAccountName() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    /**
     * 账户类型(score:积分,money:余额,reward:分佣奖励)
     */
    public function fAccountType() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    /**
     * 账号
     */
    public function fAccountNo() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    /**
     * 单位
     */
    public function fUnit() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    /**
     * 总累积
     */
    public function fTotal() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    /**
     * 当前剩余
     */
    public function fCurrent() {
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
