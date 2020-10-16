<?php
namespace xjryanse\user\service;

use app\common\interfaces\MainModelInterface;

/**
 * 签到奖励表
 */
class UserSignAwardService implements MainModelInterface
{
    use \app\common\traits\InstTrait;
    use \app\common\traits\MainModelTrait;

    protected static $mainModel;
    protected static $mainModelClass    = '\\xjryanse\\user\\model\\UserSignAward';

}
