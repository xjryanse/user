<?php

namespace xjryanse\user\service\index;

use think\Db;
/**
 * 分页复用列表
 */
trait PaginateTraits{
    /**
     * 管理端口查看用户列表
     * 20231206
     * @param type $con
     * @param type $order
     * @param type $perPage
     * @param type $having
     * @param type $field
     * @param type $withSum
     */
    public static function paginateForSlManage($con = [], $order = '', $perPage = 10, $having = '', $field = "*", $withSum = false) {

        if(session(SESSION_COMPANY_ID)!=3){
            return [];
        }

        $companyId = 29;
        $cone    = [];
        $cone[]  = ['company_id','=',$companyId];
        $res = Db::name('user')->where($cone)->paginate();
        return $res ? $res->toArray() : [];
    }

}
