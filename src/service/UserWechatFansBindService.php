<?php
namespace xjryanse\user\service;

use app\common\interfaces\MainModelInterface;

/**
 * 微信公众号粉丝绑定用户表
 */
class UserWechatFansBindService implements MainModelInterface
{
    use \app\common\traits\InstTrait;
    use \app\common\traits\MainModelTrait;

    protected static $mainModel;
    protected static $mainModelClass    = '\\app\\user\\model\\UserWechatFansBind';

}
