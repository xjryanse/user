<?php
namespace xjryanse\user\service;

use xjryanse\system\interfaces\MainModelInterface;

/**
 * 角色权限
 */
class UserAuthRoleDataService implements MainModelInterface
{
    use \xjryanse\traits\InstTrait;
    use \xjryanse\traits\MainModelTrait;

    protected static $mainModel;
    protected static $mainModelClass    = '\\xjryanse\\user\\model\\UserAuthRoleData';

    /**
     * 角色的数据权限id数组
     */
    public static function roleDataIds( $roleIds )
    {
        $con[] = ['role_id','in',$roleIds];
        //只查有效
        $con[] = ['status','=',1];
        $con[] = ['app_id','=',session(SESSION_APP_ID)];
        
        return self::mainModel()->where( $con )->distinct('data_id')->column('data_id');
    }
    
}
