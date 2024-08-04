<?php
namespace xjryanse\user\model;

/**
 * 角色跳链
 */
class UserAuthRoleJump extends Base
{
    use \xjryanse\traits\ModelUniTrait;
    // 20230516:数据表关联字段
    public static $uniFields = [
        [
            'field'     =>'role_id',
            // 去除prefix的表名
            'uni_name'  =>'user_auth_role',
            'uni_field' =>'id',
            'del_check' => true,
            'del_msg'   => '该角色有跳链配置，请先解除'
        ],
        [
            'field'     =>'jump_key',
            'uni_name'  =>'system_jump_key',
            'uni_field' =>'jump_key',
            'in_statics'=> true,
            'del_check' => true
        ],
    ];

}