<?php
namespace xjryanse\user\service;

use xjryanse\system\interfaces\MainModelInterface;

/**
 * 聊天记录表
 */
class UserChatLogService implements MainModelInterface
{
    use \xjryanse\traits\InstTrait;
    use \xjryanse\traits\MainModelTrait;

    protected static $mainModel;
    protected static $mainModelClass    = '\\xjryanse\\user\\model\\UserChatLog';

    /**
     * 未读消息统计数量
     * @param type $userId          用户id
     * @param type $friendId        好友id
     * @param type $lastReadMsgId   末条已读消息id
     */
    public static function noReadCount( $userId, $friendId, $lastReadMsgId )
    {
        //当前用户在前
        $concats[]  = '\'' .$userId .'_'.$friendId . '\'' ;
        //当前用户在后
        $concats[]  = '\'' .$friendId .'_'.$userId . '\'';
        //查询聊天记录条件
        if($lastReadMsgId){
            $con[] = ['id','>',$lastReadMsgId];
        }
        $count = self::mainModel()->whereRaw("concat( from_user_id,'_',receiver_id ) in (".implode(",",$concats).")" )->where($con)->count();

        return $count;
    }
}
