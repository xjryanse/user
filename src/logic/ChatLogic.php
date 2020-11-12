<?php
namespace xjryanse\user\logic;

use xjryanse\user\service\UserChatLogService;
use think\facade\Cache;
/**
 * 聊天逻辑
 */
class ChatLogic
{
    //主聊用户id
    use \xjryanse\traits\InstTrait;
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
        return "YDZB_".explode('-', $array );
    }
    /*
     * TODO发送消息
     */
    public function onMessageSend( $chatWithId )
    {
        $key = $this->chatKeyGenerate( $chatWithId );
        //存缓存
        
    }
    
            
            


}
