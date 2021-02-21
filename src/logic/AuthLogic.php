<?php
namespace xjryanse\user\logic;

use xjryanse\user\service\UserAuthAccessService;
use xjryanse\user\service\UserAuthRoleAccessService;
use xjryanse\user\service\UserAuthUserRoleService;
use xjryanse\user\service\UserAuthDataService;
use xjryanse\user\service\UserAuthRoleDataService;

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
        return self::roleMenus($roleIds);
    }
    
    public static function roleMenus( $roleIds )
    {
        //获取角色的权限
        $accessIds  = UserAuthRoleAccessService::roleAccessIds( $roleIds );
        $con[] = ['id','in',$accessIds];
        $accesses   = UserAuthAccessService::listsInfo($con);

        return (new self())->makeTree($accesses);
    }
    /**
     * 数据权限过滤查询条件
     */
    public static function dataCon( $userId , $tableName )
    {
        //获取用户的角色
        $roleIds    = UserAuthUserRoleService::userRoleIds($userId);
        //获取角色的数据权限
        $dataIds  = UserAuthRoleDataService::roleDataIds( $roleIds );
        $con[] = ['id','in',$dataIds];
        $con[] = ['table_name','in',$tableName];
        //不用lists，避免死循环
        $authData = UserAuthDataService::mainModel()->where( $con )->select();
        if(!$authData){
            return [];
        }
        $resCon = [];
        foreach($authData as $v){
            $jsonStr = str_replace('{$user_id}', $userId, $v['field_con']);
            $resCon = array_merge( $resCon,json_decode($jsonStr));
        }
        return $resCon;
    }
}
