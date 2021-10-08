<?php

namespace xjryanse\user\service;

use xjryanse\system\interfaces\MainModelInterface;

/**
 * 角色权限
 */
class UserAuthRoleAccessService implements MainModelInterface {

    use \xjryanse\traits\InstTrait;
    use \xjryanse\traits\MainModelTrait;

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

        return self::mainModel()->where($con)->distinct('access_id')->cache(86400)->column('access_id');
    }
    /**
     * 保存角色的权限id
     * @param type $roleId      角色id
     * @param type $accessIds   权限id
     */
    public static function saveRoleAccess( $roleId, $accessIds ){
        self::checkTransaction();
        $con[] = ['role_id','=',$roleId];
        //先删
        self::mainModel()->where($con)->delete();
        //再加
        $tempArr = [];
        foreach( $accessIds as &$accessId ){
            $tempArr[] = ['role_id'=>$roleId,'access_id'=>$accessId];
        }
        return self::saveAll($tempArr);
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
