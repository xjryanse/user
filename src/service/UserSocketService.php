<?php
namespace xjryanse\user\service;

use xjryanse\system\interfaces\MainModelInterface;

/**
 * 用户socket对接
 */
class UserSocketService implements MainModelInterface
{
    use \xjryanse\traits\InstTrait;
    use \xjryanse\traits\MainModelTrait;

    protected static $mainModel;
    protected static $mainModelClass    = '\\xjryanse\\user\\model\\UserSocket';

}
