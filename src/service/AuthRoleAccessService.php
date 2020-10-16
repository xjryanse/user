<?php
namespace xjryanse\user\service;

use xjryanse\system\interfaces\MainModelInterface;

/**
 * 角色权限
 */
class AuthRoleAccessService implements MainModelInterface
{
    use \xjryanse\traits\InstTrait;
    use \xjryanse\traits\MainModelTrait;

    protected static $mainModel;
    protected static $mainModelClass    = '\\xjryanse\\user\\model\\AuthRoleAccess';

    /**
     * 角色的权限id数组
     */
    public static function roleAccessIds( $roleIds )
    {
        $con[] = ['role_id','in',$roleIds];
        //只查有效
        $con[] = ['status','=',1];
        $con[] = ['app_id','=',session('scopeAppId')];
        
        return self::mainModel()->where( $con )->distinct('access_id')->column('access_id');
    }
    
}
