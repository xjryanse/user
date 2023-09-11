<?php

namespace xjryanse\user\service\authAccess;

use Exception;
use xjryanse\logic\Arrays2d;
use xjryanse\logic\Arrays;
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

}
