<?php

namespace xjryanse\user\service;

use xjryanse\system\interfaces\MainModelInterface;
use xjryanse\logic\Arrays;
use xjryanse\logic\Arrays2d;
use xjryanse\user\service\UserAuthUserRoleService;
use xjryanse\user\service\UserAuthRoleAccessService;
use Exception;

/**
 * 角色
 */
class UserAuthRoleService implements MainModelInterface {

    use \xjryanse\traits\InstTrait;
    use \xjryanse\traits\MainModelTrait;
    use \xjryanse\traits\StaticModelTrait;

    protected static $mainModel;
    protected static $mainModelClass = '\\xjryanse\\user\\model\\UserAuthRole';
    //直接执行后续触发动作
    protected static $directAfter = true; 
    
//    protected static function extraDetail( &$item ,$uuid )
//    {
//        //获取权限
//        $con[] = ['role_id','=',$uuid];
//        $item->sAccessIds      = UserAuthRoleAccessService::mainModel()->where($con)->column('distinct access_id');
//        return $item;
//    }
    public static function extraDetails( $ids ){
        //数组返回多个，非数组返回一个
        $isMulti = is_array($ids);
        if(!is_array($ids)){
            $ids = [$ids];
        }
        $con[] = ['id','in',$ids];
        $listRaw = self::mainModel()->where($con)->select();
        $lists = $listRaw ? $listRaw->toArray() : [];
        $userCountArr = UserAuthUserRoleService::groupBatchCount('role_id', $ids);
        $accessCountArr = UserAuthRoleAccessService::groupBatchCount('role_id', $ids);
        $universalCountArr = UserAuthRoleUniversalService::groupBatchCount('role_id', $ids);
        //权限id数组
        $accessIdObjs   = UserAuthRoleAccessService::mainModel()->where([['role_id','in',$ids]])->select();
        $accessIdArrs = $accessIdObjs ? $accessIdObjs->toArray() : [];
        
        foreach($lists as &$v){
            //用户人数
            $v['userCount']     = Arrays::value($userCountArr, $v['id'],0);
            //权限数
            $v['accessCount']   = Arrays::value($accessCountArr, $v['id'],0);
            // 页面权限数
            $v['universalCount']   = Arrays::value($universalCountArr, $v['id'],0);
            //权限id
            $con    = [];
            $con[]  = ['role_id','=',$v['id']];
            $v['sAccessIds']    = array_column(Arrays2d::listFilter($accessIdArrs, $con),'access_id');
        }
        
        return $isMulti ? $lists : $lists[0];
        // return $isMulti ? Arrays2d::fieldSetKey($lists, 'id') : $lists[0];
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

    }
    
    public function extraAfterDelete() {
        UserAuthRoleUniversalService::roleClear($this->uuid);
        UserAuthRoleMethodService::roleClear($this->uuid);
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
     * 20220811：所选的角色id，是否有员工角色（用于判断是否员工）
     * @param type $ids
     */
    public static function hasStaffRoles($ids){
        //驾驶员-driver；后勤-logistics
        $con[] = ['role_key','in',['driver','logistics']];
        $con[] = ['id','in',$ids];
        $count = self::mainModel()->where($con)->count();
        return $count ? true : false;
    }
    /**
     * 角色key，转角色id
     * @param type $keys
     * @return type
     */
    public static function keysToIds($keys){
        $con[] = ['role_key','in',$keys];
        $lists = self::staticConList($con);
        return array_column($lists,'id');
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
