<?php

namespace xjryanse\user\service;

use xjryanse\system\interfaces\MainModelInterface;
use xjryanse\logic\Arrays;
use Exception;

/**
 * 角色
 */
class UserAuthRoleService implements MainModelInterface {

    use \xjryanse\traits\InstTrait;
    use \xjryanse\traits\MainModelTrait;

    protected static $mainModel;
    protected static $mainModelClass = '\\xjryanse\\user\\model\\UserAuthRole';
    
    protected static function extraDetail( &$item ,$uuid )
    {
        //获取权限
        $con[] = ['role_id','=',$uuid];
        $item->sAccessIds      = UserAuthRoleAccessService::mainModel()->where($con)->column('distinct access_id');
        return $item;
    }
    /**
     * 额外保存信息
     * @param type $data
     * @param type $uuid
     */
    public static function extraPreSave( &$data, $uuid){
        //有传权限数据，则保存权限
        $sAccessIds = Arrays::value($data,'sAccessIds',[]);
        if( $sAccessIds ){
            UserAuthRoleAccessService::saveRoleAccess($uuid, $sAccessIds);
        }
    }
    
    public static function extraPreUpdate( &$data, $uuid){
        //有传权限数据，则保存权限
        $sAccessIds = Arrays::value($data,'sAccessIds',[]);
        if( $sAccessIds ){
            UserAuthRoleAccessService::saveRoleAccess($uuid, $sAccessIds);
        }
    }
    /**
     * 默认的权限
     * @return type
     */
    public static function defaultRoleIds()
    {
        $con[] = ['is_default','=',1];
        $con[] = ['company_id','=',session(SESSION_COMPANY_ID)];
        return self::mainModel()->where($con)->cache(86400)->column('id');
    }
    public static function saveCheck(array $data) {
        if (!arrayHasValue($data, 'name')) {
            throw new Exception('角色名不能为空');
        }
    }

    public function extraPreDelete(){
        $con[]      = ['role_id','=',$this->uuid];
        $userRole   = UserAuthUserRoleService::find( $con );
        if( $userRole ){
            $userName = UserService::getInstance( $userRole['user_id'] )->fUsername();
            throw new Exception($userName.'已使用该角色，不可操作');
        }
    }
    
    /**
     * 有用户使用该角色
     */
    public function hasUser()
    {
        $con[]      = ['role_id','=',$this->uuid];
        $roleCount  = UserAuthUserRoleService::mainModel()->where( $con )->count();
        return $roleCount ? true : false;
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
     * 角色名
     */
    public function fName() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    /**
     * 权限类型：normal普通，subsuper二级超管
     */
    public function fType() {
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
