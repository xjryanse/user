<?php
namespace xjryanse\user\service;

use app\common\interfaces\MainModelInterface;

/**
 * session中用户表
 */
class UserSessionService implements MainModelInterface
{
    use \app\common\traits\InstTrait;
    use \app\common\traits\MainModelTrait;

    protected static $mainModel;
    protected static $mainModelClass    = '\\xjryanse\\user\\model\\UserSession';

}
