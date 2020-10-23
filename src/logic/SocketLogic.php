<?php
namespace xjryanse\user\logic;

use xjryanse\curl\Query;
use xjryanse\user\service\UserSocketService;
/**
 * 与socket系统的对接逻辑
 */
class SocketLogic
{

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
     * 获取远端code
     * @return type
     */
    private static function remoteCode()
    {
        $data['appid']  = config('xiesemi.socket.appid');
        $data['secret'] = config('xiesemi.socket.secret');
        $url            = config('xiesemi.socket.url');
        return Query::posturl($url, $data);
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
     * 获取需发送消息的连接id数组
     * @param type $typeCode    消息码
     */
    public static function getToConnectIds( $typeCode ,$companyId = 0  )
    {
        //根据消息类型获取待发送用户
        $toUsers = MessageService::getToUsersByType('socket', $typeCode ,$companyId );

        //获取id
        $con[] = ['user_type','=',1];
        $con[] = ['user_id','in',$toUsers['front']];
        $con[] = ['status','in',[0,1]];
        
        $con2[] = ['user_type','=',2];
        $con2[] = ['user_id','in',$toUsers['admin']];
        $con2[] = ['status','in',[0,1]];
        $res    = UserSocketService::mainModel()->where( $con )->whereNotNull('connect_id')->column('connect_id');
        $res2   = UserSocketService::mainModel()->where( $con2 )->whereNotNull('connect_id')->column('connect_id');
        
        return array_merge( $res , $res2 );
    }
    
    public static function getToConnectIdsByUserIds( $userIds )
    {
        $con[] = ['user_id','in',$userIds];
        $con[] = ['create_time','>=',date('Y-m-d H:i:s',strtotime('-2 days'))];
        $con[] = ['status','in',[0,1]];
        
        $res = UserSocketService::mainModel()->where( $con )->whereNotNull('connect_id')->column('connect_id');

        return $res;
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
