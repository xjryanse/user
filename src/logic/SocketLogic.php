<?php
namespace xjryanse\user\logic;

use xjryanse\curl\Query;
use xjryanse\user\service\UserSocketService;
/**
 * 与socket系统的对接逻辑
 */
class SocketLogic
{
    //应用id
    protected $socketAppId ;
    //应用secret
    protected $socketSecret ;
    //应用secret
    protected $socketCodeUrl = "http://socket.xiesemi.cn/connect/app/code" ;
    //socket发送地址
    protected $socketSendUrl = "https://travel.xiesemi.cn:9581";
    /**
     * socket初始实例化
     * @param type $socketAppId     应用appid
     * @param type $socketSecret    应用密钥
     */
    public function __construct( $socketAppId, $socketSecret, $socketCodeUrl="" ,$socketSendUrl = "") {
        //应用appid
        $this->socketAppId  = $socketAppId;
        //应用密钥
        $this->socketSecret = $socketSecret;
        if( $socketCodeUrl ){
            $this->socketCodeUrl    = $socketCodeUrl;
        }
        if( $socketSendUrl ){
            $this->socketSendUrl    = $socketSendUrl;
        }
    }

    /*
     * 获取socket连接code
     */
    public static function code( $type, $userInfo )
    {
        //获取远端连接code
        $remote = self::remoteCode();
        if($remote['code'] == 0){
            //将以前的同user_id数据设置为下线
            self::setOff($userInfo['id']);
            //记录当前信息
            self::log( $type, $userInfo ,$remote['data']['code'] );
        }
        return $remote;
    }
    /**
     * 更新socket连接用户信息
     */
    public static function updateUser( $code,$connectId)
    {
        if(!$code || !$connectId){
            return false;
        }
        
        $con[] = ['code','=',$code];
        $info = UserSocketService::mainModel()->where( $con )->whereNull('connect_id')->find();
        if($info){
            return UserSocketService::getInstance( $info['id'] )->update(['connect_id'=>$connectId]);
        }
    }
    /**
     * socket消息直发
     * @param type $connectIds  用户连接id
     * @param type $message     发送的消息内容
     * @return type
     */
    public static function send( $connectIds, $message )
    {
        $data['to_user'] = !is_array($connectIds) ? implode(',',$connectIds ) : $connectIds;
        $data['message'] = $message ;

        return Query::posturl( $this->socketSendUrl , $data );
    }

    /**
     * 获取远端code
     * @return type
     */
    private static function remoteCode()
    {
        $data['appid']  = $this->socketAppId;
        $data['secret'] = $this->socketSecret;
        return Query::posturl( $this->socketCodeUrl, $data);
    }
    /**
     * 记录本地日志
     * @param type $type
     * @param type $userInfo
     * @param type $code
     * @return type
     */
    private static function log( $type, $userInfo ,$code )
    {
        $data['code']       = $code;
        $data['user_type']  = $type;
        $data['user_id']    = $userInfo ? $userInfo['id'] : 0 ;
        $data['status']     = 0;    //待接入
        return UserSocketService::save( $data );
    }

    /**
     * 设用户id为off
     * @param type $userId  用户id
     */
    public static function setOff( $userId )
    {
        $con[] = ['user_id','=',$userId];
        $con[] = ['create_time','<=',date('Y-m-d H:i:s',strtotime('-1 minute'))];

        UserSocketService::mainModel()->where( $con )->update(['status'=>2]);   //2为离线状态。
    }
}
