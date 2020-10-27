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
    
    /**
     * 入账更新
     */
    public function income( $value ,$updateTotal = true)
    {
        self::checkTransaction();
        //总得额更新（只增不减）
        if( $updateTotal ){
            self::mainModel()->where('id',$this->uuid)->setInc('total',$value );
        }
        //账户余额更新
        return self::mainModel()->where('id',$this->uuid)->setInc('current',$value );
    }
    /**
     * 资金出账更新
     */
    public function outcome( $value )
    {
        self::checkTransaction();
        //账户余额更新
        return self::mainModel()->where('id',$this->uuid)->setDec('current',$value );
    }
    
}
