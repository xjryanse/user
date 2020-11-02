<?php
namespace xjryanse\user\service;

use xjryanse\system\interfaces\MainModelInterface;
use Exception;

/**
 * 用户总表
 */
class UserService implements MainModelInterface
{
    use \xjryanse\traits\InstTrait;
    use \xjryanse\traits\MainModelTrait;

    protected static $mainModel;
    protected static $mainModelClass    = '\\xjryanse\\user\\model\\User';

    /*
     * 手机号码取用户信息
     */
    public static function getUserInfoByPhone( $phone )
    {
        if(!$phone){
            return false;
        }
        $con    = [];
        $con[]  = ['phone','=',$phone];
        $info   = self::find( $con );
        if(!$info){
            //尝试匹配用户名中的手机号码
            $con1   = [];
            $con1[] = ['username','=',$phone];
            $info   = self::find( $con1 );
        }
        return $info;
    }
    /**
     * 设定用户名
     */
    public function setUserName( $userName )
    {
        //查询用户名是否存在
        $con[] = ['username','=',$userName];
        $con[] = ['id','<>',$this->uuid ];
        $count = self::count($con);
        //更新
        if($count){
            throw new Exception('用户名已存在！');
        }
        return $this->update(['username'=>$userName]);
    }
    /*
     * 设定密码
     */
    public function setPassword( $password )
    {
        return $this->update(['password'=>password_hash( $password, PASSWORD_DEFAULT )]);
    }
}
