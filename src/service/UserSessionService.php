<?php
namespace xjryanse\user\service;

use app\common\interfaces\MainModelInterface;

/**
 * session中用户表
 */
class UserSessionService implements MainModelInterface
{
    use \xjryanse\traits\InstTrait;
    use \xjryanse\traits\MainModelTrait;

    protected static $mainModel;
    protected static $mainModelClass    = '\\xjryanse\\user\\model\\UserSession';

}
