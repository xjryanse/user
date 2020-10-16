<?php
namespace xjryanse\user\service;

use app\common\interfaces\MainModelInterface;

/**
 * 用户总表
 */
class UserService implements MainModelInterface
{
    use \xjryanse\traits\InstTrait;
    use \xjryanse\traits\MainModelTrait;

    protected static $mainModel;
    protected static $mainModelClass    = '\\xjryanse\\user\\model\\User';

}
