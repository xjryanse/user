<?php

namespace xjryanse\user\service\authRole;

use xjryanse\logic\Arrays;
use xjryanse\user\service\UserAuthRoleAccessService;
use xjryanse\system\service\SystemAuthJobRoleService;
use xjryanse\logic\DbOperate;

/**
 * 
 */
trait TriggerTraits{
    
    /**
     * 额外保存信息
     * @param type $data
     * @param type $uuid
     */
    public static function extraPreSave(&$data, $uuid) {
        //有传权限数据，则保存权限
        $sAccessIds = Arrays::value($data, 'sAccessIds', []);
        if ($sAccessIds) {
            UserAuthRoleAccessService::saveRoleAccess($uuid, $sAccessIds);
        }
    }

    public static function extraPreUpdate(&$data, $uuid) {
        //有传权限数据，则保存权限
        $sAccessIds = Arrays::value($data, 'sAccessIds', []);
        if ($sAccessIds) {
            UserAuthRoleAccessService::saveRoleAccess($uuid, $sAccessIds);
        }
    }
    
//    /**
//     * 钩子-保存前
//     */
//    public static function extraPreSave(&$data, $uuid) {
//
//    }
//
//    /**
//     * 钩子-保存后
//     */
//    public static function extraAfterSave(&$data, $uuid) {
//
//    }
//
//    /**
//     * 钩子-更新前
//     */
//    public static function extraPreUpdate(&$data, $uuid) {
//
//    }
//
//    /**
//     * 钩子-更新后
//     */
//    public static function extraAfterUpdate(&$data, $uuid) {
//        
//    }
//
//    /**
//     * 钩子-删除前
//     */
//    public function extraPreDelete() {
//        
//    }
//
//    /**
//     * 钩子-删除后
//     */
//    public function extraAfterDelete() {
//        
//    }
    
    /**
     * 钩子-保存前
     */
    public static function ramPreSave(&$data, $uuid) {
        //有传权限数据，则保存权限
        if (isset($data['sAccessIds'])) {
            $sAccessIds = Arrays::value($data, 'sAccessIds', []);
            // UserAuthRoleAccessService::saveRoleAccess($uuid, $sAccessIds);
            UserAuthRoleAccessService::middleBindRam('role_id', $uuid, 'access_id', $sAccessIds, true);
        }
        
        if (isset($data['roleJobIds'])) {
            $roleJobIds = Arrays::value($data, 'roleJobIds', []);
            SystemAuthJobRoleService::middleBindRam('role_id', $uuid, 'job_id', $roleJobIds, true);
        }
    }

    /**
     * 钩子-保存后
     */
    public static function ramAfterSave(&$data, $uuid) {

    }

    /**
     * 钩子-更新前
     */
    public static function ramPreUpdate(&$data, $uuid) {
        //有传权限数据，则保存权限
        if (isset($data['sAccessIds'])) {
            $sAccessIds = Arrays::value($data, 'sAccessIds', []);
            // UserAuthRoleAccessService::saveRoleAccess($uuid, $sAccessIds);
            UserAuthRoleAccessService::middleBindRam('role_id', $uuid, 'access_id', $sAccessIds, true);
        }
        
        if (isset($data['roleJobIds'])) {
            $roleJobIds = Arrays::value($data, 'roleJobIds', []);
            SystemAuthJobRoleService::middleBindRam('role_id', $uuid, 'job_id', $roleJobIds, true);
        }
    }
}
