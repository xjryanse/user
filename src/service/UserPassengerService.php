<?php

namespace xjryanse\user\service;

use xjryanse\system\interfaces\MainModelInterface;
use xjryanse\logic\DataCheck;
use xjryanse\user\service\UserService;
use xjryanse\logic\Arrays;
use Exception;
/**
 * 用户乘客
 */
class UserPassengerService implements MainModelInterface {

    use \xjryanse\traits\InstTrait;
    use \xjryanse\traits\MainModelTrait;
    use \xjryanse\traits\MainModelRamTrait;
    use \xjryanse\traits\MainModelCacheTrait;
    use \xjryanse\traits\MainModelCheckTrait;
    use \xjryanse\traits\MainModelGroupTrait;
    use \xjryanse\traits\MainModelQueryTrait;

    use \xjryanse\traits\ObjectAttrTrait;
    
    protected static $mainModel;
    protected static $mainModelClass = '\\xjryanse\\user\\model\\UserPassenger';

    public static function extraDetails( $ids ){
        return self::commExtraDetails($ids, function($lists) use ($ids){
            return $lists;
        },true);
    }
    
    public static function extraPreSave( &$data, $uuid){
        if(mb_strlen(Arrays::value($data, 'id_no')) > 18){
            throw new Exception('身份证号码太长');
        }
        
        $keys = ['user_id','phone','realname'];
        DataCheck::must($data, $keys);
        UserService::getInstance($data['user_id'])->checkUserPhone();
        //乘客校验
        self::passengerCheck($data['user_id'], $data['id_no'], $uuid);
        return $data;
    }

    public static function extraPreUpdate( &$data, $uuid){

    }
    /**
     * 乘客校验
     * @param type $userId
     * @param type $idno
     * @param type $uuid
     * @return boolean
     */
    public static function passengerCheck($userId, $idno,$uuid = ''){
        if($idno){
            $con[] = ['user_id','=',$userId];
            $con[] = ['id_no','=',$idno];
            if($uuid){
                $con[] = ['id','<>',$uuid];
            }
            $count = self::mainModel()->where($con)->count();
            if($count){
                throw new Exception('乘客信息已存在');
            }
        }
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
     *
     */
    public function fSessionId() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    /**
     *
     */
    public function fUserInfo() {
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
     * 公众号运营者对粉丝的备注，公众号运营者可在微信公众平台用户管理界面对粉丝添加备注
     */
    public function fRemark() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    /**
     * 创建者
     */
    public function fCreater() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    /**
     * 更新者
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
