<?php
namespace xjryanse\user\model;

/**
 * 用户跳链
 */
class UserJump extends Base
{
    use \xjryanse\traits\ModelUniTrait;
    // 20230516:数据表关联字段
    public static $uniFields = [
        [
            'field'     =>'jump_key',
            'uni_name'  =>'system_jump_key',
            'uni_field' =>'jump_key',
            'in_statics'=> true,
            'del_check' => true
        ],
    ];

}