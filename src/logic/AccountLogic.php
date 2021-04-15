<?php
namespace xjryanse\user\logic;

use xjryanse\system\interfaces\AccountLogicInterface;

use xjryanse\user\service\UserAccountService;
use xjryanse\user\service\UserAccountLogService;
use Exception;
/**
 * 账户逻辑
 */
class AccountLogic implements AccountLogicInterface
{
    /**
     * 【逐步淘汰】按userid取值，按key替换。
     * @param type $userId
     */
    public static function getByUserId( $userId )
    {
        $values = UserAccountService::listsByField( 'user_id' , $userId );
        $values = $values ? $values->toArray() : [] ;
        $keys   = array_column( $values,'account_type');
        return array_combine($keys, $values);
    }
    /**
     * 【逐步淘汰】创建账户
     * @param type $userId  用户id
     * @param type $keys    键
     */
    public static function accountCreate( $userId ,$keys = [] )
    {
        //获取现有账户
        $accounts = self::getByUserId($userId);
        //兼容字符串
        if(!is_array( $keys )){
            $keys = [ $keys ];
        }
        //循环判断，未创建则创建
        foreach($keys as $key){
            if(!isset($accounts[$key])){
                //创建用户账户
                UserAccountService::createUserAccount($userId, $key);
            }
        }
    }

    /**
     * 【TODO逐步淘汰】入账逻辑
     * @param type $userId      用户id
     * @param type $accountType 账户类型
     * @param type $value       变动值
     * @param type $data        额外数据
     * @return type
     */
    public static function doIncome( $userId, $accountType, $value, $data= [] )
    {
        //事务校验
        UserAccountService::checkTransaction();
        //账户校验
        if(!UserAccountService::getByUserAccountType($userId, $accountType)){
            self::accountCreate($userId, $accountType);
        }

        $info = UserAccountService::getByUserAccountType( $userId, $accountType );
        //新增流水
        $data['user_id']        = $userId;
        $data['account_id']     = $info['id'];
        $data['before_quota']   = $info['current'];
        $data['change']         = $value;
        $data['current_quota']  = $info['current'] + $value;
        $res = UserAccountLogService::save( $data );
        //更新总账
        UserAccountService::getInstance( $info['id'] )->income( $value );
        return $res;
    }

    /*
     * 【逐步淘汰】出账逻辑
     * @param type $userId      用户id
     * @param type $accountType 账户类型
     * @param type $value       变动值
     * @param type $data        额外数据
     * @param type $permitNegative  是否允许账户余额负值
     * @return type
     */
    public static function doOutcome( $userId, $accountType, $value, $data= [] ,$permitNegative = false )
    {
        //事务校验
        UserAccountService::checkTransaction();
        //账户校验
        if(!UserAccountService::getByUserAccountType($userId, $accountType)){
            self::accountCreate($userId, $accountType);
        }

        $info = UserAccountService::getByUserAccountType( $userId, $accountType );
        if( !$permitNegative && $info['current'] - abs($value) < 0 ){
            throw new Exception('账户余额不足');
        }
        //新增流水
        $data['user_id']        = $userId;
        $data['account_id']     = $info['id'];
        $data['before_quota']   = $info['current'];
        $data['change']         = -1 * abs( $value );
        $data['current_quota']  = $info['current'] - $value;
        $res = UserAccountLogService::save( $data );
        //更新总账
        UserAccountService::getInstance( $info['id'] )->outcome( abs($value) );
        return $res;
    }
}
