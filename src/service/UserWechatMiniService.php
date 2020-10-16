<?php
namespace xjryanse\user\service;

use app\common\interfaces\MainModelInterface;

/**
 * 小程序用户表
 */
class UserWechatMiniService implements MainModelInterface
{
    use \app\common\traits\InstTrait;
    use \app\common\traits\MainModelTrait;

    protected static $mainModel;
    protected static $mainModelClass    = '\\xjryanse\\user\\model\\UserWechatMini';

}
