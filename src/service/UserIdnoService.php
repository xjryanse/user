<?php
namespace xjryanse\user\service;

use xjryanse\system\interfaces\MainModelInterface;

/**
 * 用户实名
 */
class UserIdnoService implements MainModelInterface
{
    use \xjryanse\traits\InstTrait;
    use \xjryanse\traits\MainModelTrait;

    protected static $mainModel;
    protected static $mainModelClass    = '\\xjryanse\\user\\model\\UserIdno';

    public static function getByUserId( $userId )
    {
        $con[] = ['user_id','=',$userId];
        return self::find( $con );
    }
}
