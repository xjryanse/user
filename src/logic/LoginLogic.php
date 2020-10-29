<?php
namespace xjryanse\user\logic;

use xjryanse\user\service\UserService;
use xjryanse\user\service\UserLoginLogService;
use think\facade\Session;
use Exception;
/**
 * 登录逻辑
 */
class LoginLogic
{
    /**
     * 登录信息
     * @param type $userName    用户名
     * @param type $password    密码
     * @return type
     * @throws Exception
     */
    public static function login( $userName, $password )
    {
        $con[] = ['username','=', $userName ];
        $con[] = ['company_id','in',session('scopeCompanyId')];

        $userInfo = UserService::find( $con );

        if(!$userInfo){
            //系统级的超级管理员账户：跨了公司
            $con1[] = ['username','=',$userName];
            $con1[] = ['admin_type','=','super'];       //超级管理账号
            $userInfo = UserService::find( $con1 );
        }

        if(!$userInfo){
            throw new Exception('用户不存在');
        }
        if(!password_verify($password, $userInfo['password'] )){
            throw new Exception('密码错误!');
        }
        if($userInfo['status'] != 1){
            throw new Exception('用户状态异常');
        }
        //记录登录日志
        UserLoginLogService::loginLog( $userInfo );
        
        //登录信息存session
        session('scopeUserId',$userInfo['id']);
        session('scopeUserInfo',$userInfo);
        $userInfo['sessionId'] = session_id();
        //是微信浏览器环境，同时更新用户的默认登录
//        if(WxBrowser::isWxBrowser() && session('myOpenid')){
//            WechatUserBindService::updateUserIdByOpenid( session('myOpenid') , $userInfo['id']);
//        }
        unset($userInfo['password']);
        return $userInfo;
    }
    /**
     * 退出
     */
    public static function logout()
    {
//        $comKey = session('scopeCompanyKey'); 
//        //是微信浏览器环境，同时更新用户的默认登录
//        if(WxBrowser::isWxBrowser() && session('myOpenid')){
//            //将用户设为0;
//            WechatUserBindService::updateUserIdByOpenid( session('myOpenid') , 0);
//        }
        //清空session用户信息
        Session::clear();

        return true;
    }
    /**
     * 用户session
     */
    public static function userSession()
    {
        $data['userInfo']   = session('scopeUserInfo');
        $data['userId']     = session('scopeUserId');
        $data['sessionId']  = session_id();
        return $data;
    }
    /**
     * 直接设定了当前用户的信息
     */
    public static function sessionUserSet( $userId )
    {
        //从数据库直接拿
        $userInfo = UserService::getInstance( $userId )->get( 0 );
        session( 'scopeUserInfo', $userInfo );
        session( 'scopeUserId', $userInfo['id'] );
        return $userInfo;
    }
}
