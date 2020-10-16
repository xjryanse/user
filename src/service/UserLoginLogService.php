<?php
namespace xjryanse\user\service;

use app\common\interfaces\MainModelInterface;
use think\facade\Request;
/**
 * 用户登录日志
 */
class UserLoginLogService implements MainModelInterface
{
    use \app\common\traits\InstTrait;
    use \app\common\traits\MainModelTrait;

    protected static $mainModel;
    protected static $mainModelClass    = '\\app\\user\\model\\UserLoginLog';

        /**
     * 记录登录日志
     */
    public static function loginLog( $userInfo )
    {
        $data['uid']        = $userInfo['id'];
        $data['username']   = $userInfo['username'];
        $data['login_ip']   = Request::ip();
        $data['login_time'] = date('Y-m-d H:i:s');
        $data['domain_name']= Request::server('HTTP_HOST');
        //登录日志
        self::save( $data );
        //末次登录时间更新
        $data2['id']             = $userInfo['id'];
        $data2['last_loginip']   = Request::ip();
        $data2['last_logintime'] = date('Y-m-d H:i:s');
        UserService::getInstance($userInfo['id'])->update( $data2 );
    }
}
