<?php
namespace xjryanse\user\service;

use xjryanse\system\interfaces\MainModelInterface;
use Exception;

/**
 * 角色
 */
class UserAuthRoleService implements MainModelInterface
{
    use \xjryanse\traits\InstTrait;
    use \xjryanse\traits\MainModelTrait;

    protected static $mainModel;
    protected static $mainModelClass    = '\\xjryanse\\user\\model\\UserAuthRole';

    public static function saveCheck(array $data)
    {
        if(!arrayHasValue($data, 'name')){
            throw new Exception('角色名不能为空');
        }
    }    
}
