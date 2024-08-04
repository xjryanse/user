<?php

namespace xjryanse\user\service\authAccess;

use Exception;
use xjryanse\logic\Arrays2d;
use xjryanse\logic\Arrays;
use xjryanse\user\service\UserAuthRoleAccessService;
use xjryanse\user\service\UserAuthRoleService;
use xjryanse\user\service\UserAuthUserRoleService;
use xjryanse\system\service\SystemCompanyJobService;
use xjryanse\system\service\SystemAuthJobRoleService;
use xjryanse\system\service\SystemCompanyUserService;
use xjryanse\user\service\UserService;

/**
 * 分页复用列表
 */
trait ListTraits{
    /**
     * 20230806：uniqid，组织树状
     */
    public static function listAccessGroupTree($param) {
        $accGroup   = Arrays::value($param,'access_group','manage');
        $con        = [];
        $con[]      = ['access_group','=',$accGroup];

        $all    = self::where($con)->order('sort')->select();
        $allArr = $all ? $all->toArray() : [];

        return Arrays2d::makeTree($allArr,'','pid','subLists');
    }

    
    /**
     * 20240311：客户展示：只看不该
     */
    public static function listCustTree($param) {
        $accGroup   = Arrays::value($param,'access_group','manage');
        $con        = [];
        $con[]      = ['access_group','=',$accGroup];
        $con[]      = ['status','=',1];

        $all    = self::lists($con);
        $allArr = $all ? $all->toArray() : [];
        // 全量角色表；
        $allRoleAccess  = UserAuthRoleAccessService::staticConList();
        // 全量岗位表；
        $allJobRole     = SystemAuthJobRoleService::staticConList();
        // 全量用户-角色；
        $allCompanyUser = SystemCompanyUserService::staticConList();
        $allUserIds     = Arrays2d::uniqueColumn($allCompanyUser, 'user_id');
        
        $conU           = [['id','in',$allUserIds]];
        $userLists      = UserService::where($conU)->field('id,realname')->select();
        $userListsArr   = $userLists ? $userLists->toArray() : [];
        // 岗位关联用户
        $allRoleUser    = UserAuthUserRoleService::staticConList();
        
        foreach($allArr as &$v){
            // 角色串
            $con            = [['access_id','=',$v['id']]];
            $roleIds        = Arrays2d::uniqueColumn(Arrays2d::listFilter($allRoleAccess, $con), 'role_id');            
            $v['roleStr']   = UserAuthRoleService::idNames($roleIds);
            // 岗位串
            $conJob         = [['role_id','in',$roleIds]];
            $jobIds         = Arrays2d::uniqueColumn(Arrays2d::listFilter($allJobRole, $conJob), 'job_id');            
            $v['jobStr']    = SystemCompanyJobService::idJobNames($jobIds);
            // 人员串（岗位）
            $conUser        = [['job_id','in',$jobIds]];
            $userIds        = Arrays2d::uniqueColumn(Arrays2d::listFilter($allCompanyUser, $conUser), 'user_id');
            $conUe          = [['id','in',$userIds]];
            $userNames      = array_column(Arrays2d::listFilter($userListsArr, $conUe),'realname');
            $v['jobUserStr'] = implode(',',$userNames);
            // 人员串(角色)
            $conRUser        = [['role_id','in',$roleIds]];
            $rUserIds        = Arrays2d::uniqueColumn(Arrays2d::listFilter($allRoleUser, $conRUser), 'user_id');
            $conRUe          = [['id','in',$rUserIds]];
            $rUserNames      = array_column(Arrays2d::listFilter($userListsArr, $conRUe),'realname');
            $v['roleUserStr'] = implode(',',$rUserNames);
        }
        
        return Arrays2d::makeTree($allArr,'','pid','subLists');
    }
}
