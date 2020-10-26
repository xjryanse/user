<?php
namespace xjryanse\user\logic;

use xjryanse\user\service\UserVerifyCodeService;
/**
 * 验证码逻辑
 */
class VerifyCodeLogic
{
    /**
     * 生成验证码
     * @param type $key     校验Key
     * @param type $userId  用户id
     * @param type $length  长度
     * @return type
     */
    public static function generate($key, $userIdentify, $length=6, $data = [])
    {
        $data['code']       = BverifyCodeService::generate( $length );
        $data['has_used']   = 0; //未使用
        //相同key验证码失效
        UserVerifyCodeService::invalid( $key , $userIdentify );
        return UserVerifyCodeService::save($data);
    }
    
    /**
     * 校验
     * @return type
     */
    
    public static function verify($key,$userIdentify,$code ,$inSeconds = 300)
    {
        return UserVerifyCodeService::verify( $key , $userIdentify, $code, $inSeconds);
    }
}
