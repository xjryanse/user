<?php
namespace xjryanse\user\logic;

use xjryanse\user\service\UserChatLogService;
use xjryanse\logic\SnowFlake;
use Redis;
use think\Db;
/**
 * 聊天逻辑
 */
class ChatLogic
{
    //主聊用户id
    use \xjryanse\traits\InstTrait;
    
    protected $redis;
    /**
     * 注入redis依赖
     * @param Redis $redis  已经连接好的redis 实例
     */
    public function setRedis(Redis $redis)
    {
        $this->redis = $redis;
    }
    
    /**
     * 获取单用户聊天记录
     * @param type $chatWithId  用户id或群组id
     * @param type $type        single:单聊；group:群聊
     * @param type $con
     * @param type $orderBy
     * @param type $perPage
     * @return type
     */
    public function getLog( $chatWithId ,$type="single" ,$con = [], $orderBy="id desc",$perPage = 20 )
    {
        //redis中的聊天记录搬到数据库
        $this->writeToDb($chatWithId);
        //单聊
        if( $type == "single"){
            //当前用户在前
            $concats[]  = [$this->uuid .'-'.$chatWithId ];
            //当前用户在后
            $concats[]  = [$chatWithId .'-'.$this->uuid ];
            //查询聊天记录条件
            $con[]      = ['concat( from_user_id,receiver_id )','in', $concats ];
        } 
        //群聊
        if( $type == "group"){
            //查询聊天记录条件：接收人为群id
            $con[]      = ['receiver_id','in', $chatWithId ];
        }
        $res        = UserChatLogService::paginate( $con, $orderBy, $perPage );
        return $res;
    }
    /**
     * 生成聊天key
     * @param type $chatWithId  聊天对象id
     */
    public function chatKeyGenerate( $chatWithId )
    {
        $array = [ $this->uuid, $chatWithId ];
        sort( $array );
        return "YDZB_".implode('-', $array );
    }
    /*
     * TODO发送消息
     * @param type $chatWithId
     * @param type $message     ['message','msg_type','event']
     * @return type
     */
    public function onMessageSend( $chatWithId , array $message )
    {
        $key = $this->chatKeyGenerate( $chatWithId );
        $message['from_user_id']    = $this->uuid;
        $message['receiver_id']     = $chatWithId;
        $message['id']              = SnowFlake::generateParticle();
        $message['msg_time']     = date('Y-m-d H:i:s');

        //存缓存
        return $this->redis->lpush( $key, json_encode($message,JSON_UNESCAPED_UNICODE) );
    }
    
    /**
     * 聊天记录从redis搬到数据库
     */
    public function writeToDb( $chatWithId )
    {
        $key    = $this->chatKeyGenerate( $chatWithId );
        $data   = [];
        $index  = 1;
        //每次只取100条
        while( $index <= 100){
            $tmpData = $this->redis->rpop( $key );
            //只处理json格式的数据包
            if($tmpData && is_array(json_decode($tmpData,true))){
                $data[] = json_decode($tmpData,true);
            }
            $index++;
        }
        if(!$data){
            return false;
        }
        //开事务保存，保存失败数据恢复redis
        Db::startTrans();
        try {
            UserChatLogService::saveAll($data);
            // 提交事务
            Db::commit();
        } catch (\Exception $e) {
            // 回滚事务
            Db::rollback();
            //数据恢复到redis
            while( count($data) ){
                $tmpData = array_pop($data);
                //推回redis
                $this->redis->rpush( $key , json_encode($tmpData,JSON_UNESCAPED_UNICODE) );
            }
        }
    }

}
