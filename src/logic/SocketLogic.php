<?php
namespace xjryanse\user\logic;

use xjryanse\curl\Query;
use xjryanse\user\service\UserSocketService;
use Exception;
/**
 * 与socket系统的对接逻辑
 */
class SocketLogic
{
    const SOCKET_TOCONNECT  = 0;//socket待接入
    const SOCKET_ONLINE     = 1;//在线
    const SOCKET_OFFLINE    = 2;//离线
    //应用id
    protected $socketAppId ;
    //应用secret
    protected $socketSecret ;
    //应用secret
    protected $socketCodeUrl = "http://socket.xiesemi.cn/connect/app/code" ;
    //socket发送地址
    protected $socketSendUrl = "https://travel.xiesemi.cn:9581";
    
    protected static $socketInst;
    
    /**
     * socket初始实例化
     * @param type $socketAppId     应用appid
     * @param type $socketSecret    应用密钥
     */
    protected function __construct( $socketAppId, $socketSecret, $socketCodeUrl="" ,$socketSendUrl = "") {
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

    /**
     * 获取socket连接实例
     * @return type
     */
    public static function getSocketInst()
    {
        if(!self::$socketInst){
            self::$socketInst = new SocketLogic( 
                    config('xiesemi.socket.appid')
                    , config('xiesemi.socket.secret')
                    , config('xiesemi.socket.codeUrl')
                    , config('xiesemi.socket.sendUrl')
                );
        }
        return self::$socketInst ;
    }

    /**
     * 获取socket连接地址（一次性）
     * @param type $userId    用户信息
     * @return type
     * @throws Exception
     */
    public static function getWssUrl( $userId )
    {
        $socketInst = self::getSocketInst();
        $codeInfo = $socketInst->code( '', $userId);
        if($codeInfo['code']!=0){
            throw new Exception('socket启动失败:'.$codeInfo['message']);
        }
        $code = $codeInfo['data']['code'];
        //socket连接url
        $connectUrl = str_replace( "=CODE", '='.$code,  config('xiesemi.socket.connectUrl') );
        return str_replace( "=APPID",'='.config('xiesemi.socket.appid'),  $connectUrl);
    }    
    
    /*
     * 获取socket连接code
     */
    public function code( $type, $userId )
    {
        //获取远端连接code
        $remote = $this->remoteCode();
        if($remote['code'] == 0){
            //将以前的同user_id数据设置为下线
            self::setOff($userId);
            //记录当前信息
            self::log( $type, $userId ,$remote['data']['code'] );
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
        $info = UserSocketService::mainModel()->where( $con )->find();
        if($info){
            $data['connect_id']     = $connectId;
            $data['connect_status'] = self::SOCKET_ONLINE;    //在线
            
            return UserSocketService::getInstance( $info['id'] )->update( $data );
        }
    }
    /**
     * socket消息直发
     * @param type $connectIds  用户连接id
     * @param type $message     发送的消息内容
     * @return type
     */
    public function send( $connectIds, $message )
    {
        if(!is_array( $connectIds )){
            $connectIds = [ $connectIds ];
        }
        $data['to_user'] = implode(',',$connectIds );
        $data['message'] = $message ;

        return Query::post( $this->socketSendUrl , $data );
    }

    /**
     * 获取远端code
     * @return type
     */
    private function remoteCode()
    {
        $data['appid']  = $this->socketAppId;
        $data['secret'] = $this->socketSecret;
        return Query::posturl( $this->socketCodeUrl, $data);
    }
    /**
     * 记录本地日志
     * @param type $type
     * @param type $userId
     * @param type $code
     * @return type
     */
    private static function log( $type, $userId ,$code )
    {
        $data['code']       = $code;
        $data['user_type']  = $type;
        $data['user_id']    = $userId ?  : '' ;
        $data['connect_status'] = self::SOCKET_TOCONNECT;    //待接入
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

        $data['connect_status'] = self::SOCKET_OFFLINE;    //离线
        UserSocketService::mainModel()->where( $con )->update( $data );   //2为离线状态。
    }
    /**
     * 获取连接id
     */
    public static function connectId( $userId )
    {
        //查指定用户
        $con[] = ['user_id',        '=',$userId];
        //查在线
        $con[] = ['connect_status', '=',self::SOCKET_ONLINE];

        $info = UserSocketService::mainModel()->where( $con )->order('id desc')->find();
        return $info ? $info['connect_id'] : "";
    }
    /**
     * 多用户取连接id
     */
    public static function connectIds ( $userIds )
    {
        //查指定用户
        $con[] = ['user_id',        'in',   $userIds ];
        //查在线
        $con[] = ['connect_status', '=',self::SOCKET_ONLINE];
        
        return UserSocketService::column("connect_id", $con);
    }
}
