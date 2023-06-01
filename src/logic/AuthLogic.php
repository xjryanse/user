<?php
namespace xjryanse\user\logic;

use xjryanse\user\service\UserAuthAccessService;
use xjryanse\user\service\UserAuthRoleAccessService;
use xjryanse\user\service\UserAuthUserRoleService;
use xjryanse\user\service\UserAuthRoleService;
use xjryanse\user\service\UserAuthDataService;
use xjryanse\user\service\UserAuthRoleDataService;
use xjryanse\logic\Debug;
use xjryanse\view\service\ViewStaffService;
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
    public static function getMenu( $userId,$admType='',$con = [] )
    {
        $roleIds    = UserAuthUserRoleService::userRoleIds($userId);
        // Debug::debug('$roleIds',$roleIds);
        return self::roleMenus($roleIds,$admType,$con);
    }
    
    /**
     * 
     * @param type $roleIds
     * @param type $admType 管理类型
     * @return type
     */
    public static function roleMenus( $roleIds ,$admType,$con=[])
    {
        //超管直接展示全部菜单：20210402
        if( $admType == 'super' || $admType == 'subSuper'){
            $accesses   = UserAuthAccessService::listsInfo( $con );
        } else {
            //获取角色的权限
            $accessIds  = UserAuthRoleAccessService::roleAccessIds( $roleIds );
            $con[] = ['id','in',$accessIds];
            if( UserAuthAccessService::mainModel()->hasField('only_role')){
                $con[] = ['only_role','in',['',$admType]];
            }
            Debug::debug(__CLASS__.__FUNCTION__.'$con',$con);
            $accesses   = UserAuthAccessService::listsInfo($con);
        }
        // Debug::debug(__METHOD__.'$accesses',$accesses);
        return (new self())->makeTree($accesses);
    }
    /**
     * 数据权限过滤查询条件
     * @param type $userId
     * @param type $tableName
     * @param type $strict      硬性过滤
     * @return type
     */
    public static function dataCon( $userId , $tableName ,$strict = false)
    {
        if(!$userId){
            return [];
        }
        //获取用户的角色
        $roleIds    = UserAuthUserRoleService::userRoleIds($userId);
        //获取角色的数据权限
        $dataIds  = UserAuthRoleDataService::roleDataIds( $roleIds );
        if(!$dataIds){
            return [];
        }
        $con[] = ['id','in',$dataIds];
        $con[] = ['table_name','in',$tableName];
        if($strict){
            $con[] = ['strict','=',1];
        }
        //不用lists，避免死循环
        $authData = UserAuthDataService::mainModel()->where( $con )->cache(86400)->select();
        if(!$authData){
            return [];
        }

        $resCon = [];
        foreach($authData as $v){
            $jsonStr = str_replace('{$sessionUserId}', $userId, $v['field_con']);
            $resCon = array_merge( $resCon,json_decode($jsonStr));
        }
        return $resCon;
    }
}
