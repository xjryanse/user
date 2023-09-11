<?php
namespace xjryanse\user\model;

/**
 * 用户乘客表
 */
class UserPassenger extends Base
{
    use \xjryanse\traits\ModelUniTrait;
    // 20230516:数据表关联字段
    public static $uniFields = [
        [
            'field'     =>'user_id',
            // 去除prefix的表名
            'uni_name'  =>'user',
            'uni_field' =>'id',
            'in_list'   => true,
            'in_statics'=> true,
            'in_exist'  => true,
            'del_check' => true,
            'del_msg'   => '该用户有乘客，请先删除'
        ],
    ];

}