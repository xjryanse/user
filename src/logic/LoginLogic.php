<?php
namespace xjryanse\user\logic;

use xjryanse\logic\Arrays;
use xjryanse\user\service\UserService;
use xjryanse\user\service\UserLoginLogService;
use think\facade\Cache;
use think\facade\Session;
use Exception;
/**
 * 登录逻辑
 */
class LoginLogic
{
    /**
     * 
     * @return type
     */
    public static function login( $param )
    {
        //取用户信息
        $userName = Arrays::value($param, 'username');
        //取密码
        $password = Arrays::value($param, 'password');
        
        $con[] = ['username','=', $userName ];
        $con[] = ['company_id','in',session('scopeCompanyId')];

        $userInfo = UserService::find( $con );
        Cache::set('tmpCon',$con);
        if(!$userInfo){
            //系统级的超级管理员账户
            $con1[] = ['username','=',Request::param('username','')];
            $con1[] = ['admin_type','=','super'];       //超级管理账号
            Cache::set('tmpCon1',$con1);
            $userInfo = UserService::find( $con1 );
        }
            Cache::set('lastSql',UserService::mainModel()->getLastSql());        

        if(!$userInfo){
            throw new Exception('用户不存在');
        }
        if(!password_verify($password, $userInfo['password'] )){
            throw new Exception('密码错误!');
        }
        if($userInfo['status'] != 1){
            throw new Exception('用户状态异常');
        }
//        Session::clear();        
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
    
}
