<?php

namespace xjryanse\user\service;

use xjryanse\staff\service\StaffLogService;
use xjryanse\system\interfaces\MainModelInterface;
// use xjryanse\view\service\ViewStaffService;
use xjryanse\user\service\UserService;
use xjryanse\system\service\SystemAuthJobRoleService;
use xjryanse\system\service\SystemCompanyUserService;
use xjryanse\logic\Arrays;
use xjryanse\logic\Arrays2d;
/**
 * 用户角色
 */
class UserAuthUserRoleService implements MainModelInterface {

    use \xjryanse\traits\InstTrait;
    use \xjryanse\traits\MainModelTrait;
    use \xjryanse\traits\MainModelRamTrait;
    use \xjryanse\traits\MainModelCacheTrait;
    use \xjryanse\traits\MainModelCheckTrait;
    use \xjryanse\traits\MainModelGroupTrait;
    use \xjryanse\traits\MainModelQueryTrait;

    use \xjryanse\traits\StaticModelTrait;
    use \xjryanse\traits\ObjectAttrTrait;

    protected static $mainModel;
    protected static $mainModelClass = '\\xjryanse\\user\\model\\UserAuthUserRole';

    use \xjryanse\user\service\authUserRole\TriggerTraits;
    
    public static function extraDetails( $ids ){
        return self::commExtraDetails($ids, function($lists) use ($ids){
            return $lists;
        },true);
    }
    
    /**
     * 保存用户的角色信息
     */
    public static function userRoleIdSave($userId,$roleIds){
        self::checkTransaction();
        if(!$userId){
            return false;
        }
        $dataArr = [];
        foreach($roleIds as $roleId){
            $dataArr[] = ['user_id'=>$userId,'role_id'=>$roleId];
        }
        //先删再加
        self::mainModel()->where('user_id',$userId)->delete();
        self::saveAll($dataArr);
        //20220811:如果权限中有员工权限，则在员工表中添加入职记录
        if(UserAuthRoleService::hasStaffRoles($roleIds)){
            StaffLogService::setJoin($userId);
        }
        // 清除本表缓存
        self::staticCacheClear();
        // 20230108
        UserService::clearCommExtraDetailsCache($userId);
    }
    /**
     * 用户的角色id数组
     */
    public static function userRoleIds($userId) {
        $con[] = ['user_id', 'in', $userId];
        //只查有效
        $con[] = ['status', '=', 1];
        if (self::mainModel()->hasField('app_id')) {
            $con[] = ['app_id', '=', session(SESSION_APP_ID)];
        }
        $lists  = self::staticConList($con);
        $roleIds = array_column($lists,'role_id');
        //20220817：兼容客户？？？
        
        $isStaff = self::isStaff($userId);
        // if(!ViewStaffService::isStaff($userId) && !$roleIds){
        if(!$isStaff && !$roleIds){
            //TODO:客户定制权限处理
            //如果用户不是员工，取客户通用权限            
            $keys = ['customer'];
            $roleIds = UserAuthRoleService::keysToIds($keys);
        }
        
        return $roleIds;
    }
    /**
     * 带岗位的权限id
     */
    public static function userRoleIdsWithJob($userId){
        // 用户id，提取岗位id
        $jobIds         = SystemCompanyUserService::dimJobIdsByUserId($userId);
        // 岗位id，提取角色id
        $jobRoleIds     = SystemAuthJobRoleService::dimRoleIdsByJobId($jobIds);
        // 用户id，提取角色id
        $userRoleIds    = self::userRoleIds($userId);
        return Arrays::uniqueMerge($jobRoleIds, $userRoleIds);
    }

    /**
     * 查看用户是否有某个权限
     */
    public static function userHasRole( $userId, $roleId )
    {
        $con[] = ['user_id','in',$userId];
        $con[] = ['role_id','in',$roleId];
        // dump($con);
        return self::staticConCount($con);
    }
    /**
     * 查询用户是否有某个角色key的权限
     */
    public static function userHasRoleKey($userId, $roleKey){
        $ids = UserAuthRoleService::keysToIds($roleKey);
        return self::userHasRole($userId, $ids);
    }
    /**
     * 20220512
     * 清除用户的角色：用于离职删除权限
     * TODO清缓存？？
     */
    public static function clearRole($userId){
        $con[] = ['user_id','=',$userId];
        $res = self::mainModel()->where($con)->delete();
        // 清除本表缓存
        self::staticCacheClear();
        // 20230108
        UserService::clearCommExtraDetailsCache($userId);
        return $res;
    }
    /**
     * 用户的所属角色：用于数据权限控制
     */
    public static function userRoleKeyForDataAuth($userId){
        // 权限组1：后台管理员
        if(self::userHasRoleKey($userId, 'logistics')){
            return 'logistics';
        }
        // 权限组2：驾驶员
        if(self::userHasRoleKey($userId, 'driver')){
            return 'driver';
        }
        // 客户
        return 'customer';
    }
    /**
     * 替代视图viewStaff同名方法
     * 2023-10-08是否员工？
     */
    public static function isStaff ($userId) {
        // 20231208:超级管理员也行,默认是员工
        $userInfo   = UserService::getInstance($userId)->get();
        $admType    = Arrays::value($userInfo,'admin_type');
        if($admType == 'super' || $admType == 'subSuper'){
            return true;
        }

        $staffRoleKeys = ['logistics','driver'];
        
        return self::userHasRoleKey($userId, $staffRoleKeys);
    }
    /**
     * 20231228:全部员工的用户id
     */
    public static function staffUserIds($con = []){
        $roleKey = ['logistics','driver'];
        $roleIds = UserAuthRoleService::keysToIds($roleKey);

        $con[]      = ['role_id','in',$roleIds];
        $lists      = self::staticConList($con);
        return Arrays2d::uniqueColumn($lists, 'user_id');
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
    public function fUserId() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    /**
     *
     */
    public function fRoleId() {
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
