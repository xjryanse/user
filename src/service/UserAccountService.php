<?php
namespace xjryanse\user\service;

use xjryanse\system\interfaces\MainModelInterface;

/**
 * 用户账户
 */
class UserAccountService implements MainModelInterface
{
    use \xjryanse\traits\InstTrait;
    use \xjryanse\traits\MainModelTrait;

    protected static $mainModel;
    protected static $mainModelClass    = '\\xjryanse\\user\\model\\UserAccount';

    /**
     * 根据用户和账户类型取单条数据
     * @param type $userId
     * @param type $accountType
     * @return type
     */
    public static function getByUserAccountType( $userId,$accountType)
    {
        $con[] = ['user_id','=',$userId ];
        $con[] = ['account_type','=',$accountType ];

        return self::find( $con );
    }
}
