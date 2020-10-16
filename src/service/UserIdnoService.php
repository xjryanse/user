<?php
namespace xjryanse\user\service;

use app\common\interfaces\MainModelInterface;

/**
 * 用户实名
 */
class UserIdnoService implements MainModelInterface
{
    use \xjryanse\traits\InstTrait;
    use \xjryanse\traits\MainModelTrait;

    protected static $mainModel;
    protected static $mainModelClass    = '\\xjryanse\\user\\model\\UserIdno';

}
