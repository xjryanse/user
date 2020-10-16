<?php
namespace xjryanse\user\service;

use app\common\interfaces\MainModelInterface;

/**
 * 用户常用地址
 */
class UserAddressService implements MainModelInterface
{
    use \app\common\traits\InstTrait;
    use \app\common\traits\MainModelTrait;

    protected static $mainModel;
    protected static $mainModelClass    = '\\xjryanse\\user\\model\\UserAddress';

}
