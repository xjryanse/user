<?php

namespace xjryanse\user\service;

use xjryanse\system\interfaces\MainModelInterface;

/**
 * 角色权限
 */
class UserAuthRoleMethodService implements MainModelInterface {

    use \xjryanse\traits\InstTrait;
    use \xjryanse\traits\MainModelTrait;
    use \xjryanse\traits\MainModelQueryTrait;
    use \xjryanse\traits\StaticModelTrait;

    protected static $mainModel;
    protected static $mainModelClass = '\\xjryanse\\user\\model\\UserAuthRoleMethod';

    /**
     * 20221215根据角色id，提取有权限的方法id
     * @param type $roleIds
     * @return type
     */
    public static function roleMethodIds($roleIds) {
        $con[] = ['role_id', 'in', $roleIds];
        //只查有效
        $con[] = ['status', '=', 1];
        return self::staticConColumn('method_id',$con);
    }
    
    /**
     * 2022-12-16
     * @param type $universalTable
     * @param type $universalId
     * @param type $roleIds
     * @return boolean
     */
    public static function methodRoleIdSave($methodId, $roleIds){
        self::checkTransaction();
        if(!$methodId ){
            return false;
        }
        $dataArr = [];
        foreach($roleIds as $roleId){
            $dataArr[] = ['method_id'=>$methodId,'role_id'=>$roleId];
        }
        //先删再加
        $con[] = ['method_id','=',$methodId];
        $con[] = ['company_id','=',session(SESSION_COMPANY_ID)];
        self::mainModel()->where($con)->delete();
        //批量添加
        self::saveAll($dataArr);        
    }
    /**
     * 删角色同步清
     * @param type $roleId
     */
    public static function roleClear($roleId){
        if($roleId){
            $con[] = ['role_id','=',$roleId];
            self::where($con)->delete();
        }
    }
    /**
     * 删方法同步清
     * @param type $methodId
     */
    public static function methodClear($methodId){
        if($methodId){
            $con[] = ['method_id','=',$methodId];
            self::where($con)->delete();
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
     * 角色id
     */
    public function fRoleId() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    /**
     * 数据权限项id
     */
    public function fDataId() {
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
