<?php
namespace xjryanse\user\model;

/**
 * 用户标签
 */
class UserTag extends Base
{
    use \xjryanse\traits\ModelUniTrait;
    // 20230516:数据表关联字段
    public static $uniFields = [
        [
            'field'     =>'tag_id',
            // 去除prefix的表名
            'uni_name'  =>'system_tag',
            'uni_field' =>'id',
            'del_check' => true,
            'del_msg'   => '该标签有用户使用，请先解绑'
        ],
        [
            'field'     =>'user_id',
            // 去除prefix的表名
            'uni_name'  =>'user',
            'uni_field' =>'id',
            'in_list'   => false,
            'in_statics'=> true,
            'in_exist'  => true,
            'del_check' => true,
            'del_msg'   => '该用户有绑定标签，请先解绑'
        ],
    ];


}