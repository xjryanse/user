<?php

namespace xjryanse\user\service;

use xjryanse\system\interfaces\MainModelInterface;

/**
 * 角色权限
 */
class UserAuthRoleAccessService implements MainModelInterface {

    use \xjryanse\traits\InstTrait;
    use \xjryanse\traits\MainModelTrait;
    use \xjryanse\traits\MainModelRamTrait;
    use \xjryanse\traits\MainModelCacheTrait;
    use \xjryanse\traits\MainModelCheckTrait;
    use \xjryanse\traits\MainModelGroupTrait;
    use \xjryanse\traits\MainModelQueryTrait;

    use \xjryanse\traits\StaticModelTrait;
    use \xjryanse\traits\MiddleModelTrait;
    // use \xjryanse\traits\MainModelComCateLevelQueryTrait;
    
    protected static $mainModel;
    protected static $mainModelClass = '\\xjryanse\\user\\model\\UserAuthRoleAccess';

    /**
     * 角色的权限id数组
     */
    public static function roleAccessIds($roleIds) {
        $con[] = ['role_id', 'in', $roleIds];
        //只查有效
        $con[] = ['status', '=', 1];
        if (self::mainModel()->hasField('app_id')) {
            $con[] = ['app_id', '=', session(SESSION_APP_ID)];
        }

        $res = self::mainModel()->where($con)->distinct('access_id')->cache(86400)->column('access_id');
        // 20240226:父级id也提取
        $conf   = [];
        $conf[] = ['id','in',$res];
        $conf[] = ['pid','<>',''];
        $pids   = UserAuthAccessService::mainModel()->where($conf)->distinct('pid')->cache(86400)->column('pid');

        // $pids   = [];
        return array_merge($res, $pids);
    }
    /**
     * 保存角色的权限id
     * 20240413：使用以下方法替代
     * 
     * UserAuthRoleAccessService::middleBindRam('role_id', $uuid, 'access_id', $sAccessIds, true);
     * 
     * @param type $roleId      角色id
     * @param type $accessIds   权限id
     */
    public static function saveRoleAccess( $roleId, $accessIds ){
        self::checkTransaction();
        $con[] = ['role_id','=',$roleId];
        //先删
        self::mainModel()->where($con)->delete();
        // 20240412把所有父级一并写一下
        $conA[] = ['id','in',$accessIds];
        $conA[] = ['pid','<>',''];
        $pids = UserAuthAccessService::where($conA)->whereNotNull('pid')->column('pid');
        $nAccessIds = array_unique(array_merge($accessIds, $pids));
        //再加
        $tempArr = [];
        foreach( $nAccessIds as &$accessId ){
            $tempArr[] = ['role_id'=>$roleId,'access_id'=>$accessId];
        }
        return self::saveAll($tempArr);
    }
    /**
     * 第一个页面key
     */
    public static function firstPageKey( $userId ){
        $roleIds    = UserAuthUserRoleService::userRoleIdsWithJob($userId);
        $accessIds  = self::roleAccessIds( $roleIds );
        
        $con        = [];
        $con[]      = ['id','in',$accessIds];
        $key        = UserAuthAccessService::where($con)->whereNotNull('page_key')->order('sort')->value('page_key');

        return $key;
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
    public function fRoleId() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    /**
     *
     */
    public function fAccessId() {
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
