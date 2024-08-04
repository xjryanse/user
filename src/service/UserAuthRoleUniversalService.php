<?php

namespace xjryanse\user\service;

use xjryanse\system\interfaces\MainModelInterface;
use xjryanse\logic\Arrays;
use Exception;
/**
 * 角色权限
 */
class UserAuthRoleUniversalService implements MainModelInterface {

    use \xjryanse\traits\InstTrait;
    use \xjryanse\traits\MainModelTrait;
    use \xjryanse\traits\MainModelRamTrait;
    use \xjryanse\traits\MainModelCacheTrait;
    use \xjryanse\traits\MainModelCheckTrait;
    use \xjryanse\traits\MainModelGroupTrait;
    use \xjryanse\traits\MainModelQueryTrait;

    use \xjryanse\traits\StaticModelTrait;
    
    protected static $mainModel;
    protected static $mainModelClass = '\\xjryanse\\user\\model\\UserAuthRoleUniversal';

    /**
     * 角色的数据权限id数组
     */
    public static function roleUniversalIds($roleIds,$tableName) {
        $con[] = ['role_id', 'in', $roleIds];
        $con[] = ['universal_table', '=', $tableName];
        //只查有效
        $con[] = ['status', '=', 1];
        //dump($con);
        return self::mainModel()->where($con)->distinct('universal_id')->cache(86400)->column('universal_id');
    }
    /**
     * 根据当前会话用户，获取id
     * @param type $tableName
     */
    public static function userUniversalIds($tableName){
        $roleIds = UserAuthUserRoleService::userRoleIds(session(SESSION_USER_ID));
        $universalRoleIds = self::roleUniversalIds($roleIds, $tableName);
        return $universalRoleIds;
    }
    /**
     * 20220819:页面项反查权限
     * @param type $universalTable
     * @param type $universalId
     * @return type
     */
    public static function universalRoleIds($universalTable,$universalId) {        
        $con[] = ['universal_table','=',$universalTable];
        $con[] = ['universal_id','=',$universalId];
        $con[] = ['company_id','=',session(SESSION_COMPANY_ID)];
        $lists  = self::staticConList($con);
        $roleIds = array_column($lists,'role_id');
        
        return $roleIds;
    }
    
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
    public static function universalClear($universalTable,$universalId){
        if($universalTable || $universalId){
            $con[] = ['universal_table','=',$universalTable];
            $con[] = ['universal_id','=',$universalId];
            self::where($con)->delete();
        }
    }
    /**
     * 
     * @param type $userId
     * @param type $roleIds
     * @return boolean
     */
    public static function universalRoleIdSave($universalTable,$universalId,$roleIds){
        self::checkTransaction();
        if(!$universalTable || !$universalId){
            return false;
        }
        $dataArr = [];
        foreach($roleIds as $roleId){
            $dataArr[] = ['universal_table'=>$universalTable,'universal_id'=>$universalId,'role_id'=>$roleId];
        }
        //先删再加
        $con[] = ['universal_table','=',$universalTable];
        $con[] = ['universal_id','=',$universalId];
        $con[] = ['company_id','=',session(SESSION_COMPANY_ID)];
        self::mainModel()->where($con)->delete();
        //批量添加
        self::saveAll($dataArr);        
        // 清除本表缓存
        self::staticCacheClear();
    }
    /**
     * 20240413：用角色id更新
     * @param type $param
     */
    public static function doUpdateByRoleAndId($param){
        if(!isset($param['hasAuth'])){
            throw new Exception('hasAuth参数异常');
        }
        
        $keys = ['role_id','universal_table','universal_id'];
        $data = Arrays::getByKeys($param, $keys);
        $id = self::commGetIdEG($data);
        if(!$param['hasAuth']){
            self::getInstance($id)->deleteRam();
        }
        return true;
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
