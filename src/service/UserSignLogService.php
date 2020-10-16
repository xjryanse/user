<?php
namespace xjryanse\user\service;

use app\common\interfaces\MainModelInterface;

/**
 * 签到数据表
 */
class UserSignLogService implements MainModelInterface
{
    use \app\common\traits\InstTrait;
    use \app\common\traits\MainModelTrait;

    protected static $mainModel;
    protected static $mainModelClass    = '\\app\\user\\model\\UserSignLog';

}
