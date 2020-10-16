<?php
namespace xjryanse\user\service;

use app\common\interfaces\MainModelInterface;

/**
 * 微信公众号粉丝表
 */
class UserWechatFansService implements MainModelInterface
{
    use \app\common\traits\InstTrait;
    use \app\common\traits\MainModelTrait;

    protected static $mainModel;
    protected static $mainModelClass    = '\\xjryanse\\user\\model\\UserWechatFans';

}
