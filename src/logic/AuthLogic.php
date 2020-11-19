<?php
namespace xjryanse\user\logic;

use xjryanse\user\service\UserAuthAccessService;
use xjryanse\user\service\UserAuthRoleAccessService;
use xjryanse\user\service\UserAuthUserRoleService;

/**
 * 登录逻辑
 */
class AuthLogic
{
    use \xjryanse\traits\TreeTrait;
    /**
     * 角色新增
     * @return type
     */
    public static function getMenu( $userId )
    {
        //获取用户的角色
        $roleIds    = UserAuthUserRoleService::userRoleIds($userId);
        //获取角色的权限
        $accessIds  = UserAuthRoleAccessService::roleAccessIds( $roleIds );
        $con[] = ['id','in',$accessIds];
        $accesses   = UserAuthAccessService::listsInfo($con);

        return (new self())->makeTree($accesses);
    }
}
