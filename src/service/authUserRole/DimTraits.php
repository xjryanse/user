<?php

namespace xjryanse\user\service\authUserRole;

/**
 * 
 */
trait DimTraits{
    /**
     * 提取用户旗下角色
     * @param type $userId
     */
    public static function dimListByUserId($userId){
        $con[] = ['user_id', 'in', $userId];
        //只查有效
        $con[] = ['status', '=', 1];
        $lists  = self::staticConList($con);
        return $lists;
    }
            
}
