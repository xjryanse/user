<?php
namespace xjryanse\user\service;

use xjryanse\system\interfaces\MainModelInterface;

/**
 * 用户验证码
 */
class UserVerifyCodeService implements MainModelInterface
{
    use \xjryanse\traits\InstTrait;
    use \xjryanse\traits\MainModelTrait;

    protected static $mainModel;
    protected static $mainModelClass    = '\\xjryanse\\user\\model\\UserVerifyCode';
    
    /**
     * 使验证码失效
     */
    public static function invalid($key,$userIdentify)
    {
        $con[] = ['key','=',$key];
        $con[] = ['user_identify','=',$userIdentify ];
        return self::mainModel()->where($con)->update(['status'=>0]);
    }
    
    /**
     * 生成验证码，默认长度6（IOSbug，输入默认复制2次，前端采用指定6位截断）
     * @param type $length
     */
    public static function generate($length = 6)
    {
        $min = '1' . str_repeat(0, $length - 1);
        $max = str_repeat(9, $length);
        return mt_rand($min, $max);
    }
    
    /**
     * 校验
     * @param type $length
     */
    public static function verify($key , $userIdentify, $code, $inSeconds = 300)
    {
        $con[] = ['create_time','>=',date('Y-m-d H:i:s',time() - $inSeconds )]; //有效期内，默认5分钟
        $con[] = ['key','=',$key];          //场景
        if( $userIdentify ){
            $con[] = ['user_identify','=',$userIdentify];   //匹配到用户
        }
        
        $con[] = ['code','=',$code];    
        $con[] = ['status','=',1];      //有效
        $con[] = ['has_used','=',0];    //未使用
        
        $res = self::lists( $con );
//        dump($res);
        if($res->isEmpty()){
            return false;
        }
        //更新为已使用。
        self::mainModel()->where($con)->update(['has_used'=>1]);
        return true;
    }
    
}
