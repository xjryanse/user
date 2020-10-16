<?php
namespace xjryanse\user\service;

use xjryanse\system\interfaces\MainModelInterface;

/**
 * 用户角色
 */
class AuthUserRoleService implements MainModelInterface
{
    use \xjryanse\traits\InstTrait;
    use \xjryanse\traits\MainModelTrait;

    protected static $mainModel;
    protected static $mainModelClass    = '\\xjryanse\\user\\model\\AuthUserRole';
    /**
     * 用户的角色id数组
     */
    public static function userRoleIds( $userId )
    {
        $con[] = ['user_id','in',$userId];
        //只查有效
        $con[] = ['status','=',1];
        $con[] = ['app_id','=',session('scopeAppId')];
        
        return self::mainModel()->where( $con )->distinct('role_id')->column('role_id');
    }
}
