<?php

namespace xjryanse\user\service;

use xjryanse\system\interfaces\MainModelInterface;
use xjryanse\logic\DataCheck;
use xjryanse\logic\Strings;
use Exception;
/**
 * 用户常用地址
 */
class UserAddressService implements MainModelInterface {

    use \xjryanse\traits\InstTrait;
    use \xjryanse\traits\MainModelTrait;
    use \xjryanse\traits\MainModelRamTrait;
    use \xjryanse\traits\MainModelCacheTrait;
    use \xjryanse\traits\MainModelCheckTrait;
    use \xjryanse\traits\MainModelGroupTrait;
    use \xjryanse\traits\MainModelQueryTrait;


    protected static $mainModel;
    protected static $mainModelClass = '\\xjryanse\\user\\model\\UserAddress';

    public static function userAddressCount($userId){
        $con[] = ['user_id','=',$userId];
        return self::mainModel()->where($con)->count();
    }
    
    public static function extraPreSave( &$data, $uuid){
        $keys = ['realname','phone','area_code','address'];
        $notices['realname'] = '姓名必须';
        $notices['phone'] = '手机号码必须';
        $notices['area_code'] = '地区必须';
        $notices['address'] = '详细地址必须';
        
        DataCheck::must($data, $keys, $notices);
        if(!Strings::isPhone($data['phone'])){
            throw new Exception('手机号码格式错误');
        }
        
        return $data;
    }
    
    public static function extraPreUpdate( &$data, $uuid){
        if($data['phone'] && !Strings::isPhone($data['phone'])){
            throw new Exception('手机号码格式错误');
        }
        return $data;
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
     * 用户id
     */
    public function fUserId() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    /**
     * 收件(货)人姓名
     */
    public function fRealname() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    /**
     * 手机
     */
    public function fPhone() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    /**
     * 省
     */
    public function fProvince() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    /**
     * 市
     */
    public function fCity() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    /**
     * 县
     */
    public function fCounty() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    /**
     * 收件(货)地址
     */
    public function fAddress() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    /**
     * 默认地址(0否；1是)
     */
    public function fIsDefault() {
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
