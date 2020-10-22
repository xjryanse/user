<?php
namespace xjryanse\user\logic;

use think\facade\Request;
/**
 * session逻辑
 */
class SessionLogic
{
    /**
     * session初始化，一般用于兼容小程序无法识别session
     */
    public static function sessionInit()
    {
        if ( Request::header('sessionid') || Request::param('sessionid') ) {
            $sessionid  = Request::param('sessionid') ? : Request::header('sessionid');
            if(!session_id()){
                session_id( $sessionid );
            }
        }
    }
}
