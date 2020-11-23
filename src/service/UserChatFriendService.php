<?php
namespace xjryanse\user\service;

use xjryanse\system\interfaces\MainModelInterface;
use xjryanse\user\service\UserService;

/**
 * 好友关系表
 */
class UserChatFriendService implements MainModelInterface
{
    use \xjryanse\traits\InstTrait;
    use \xjryanse\traits\MainModelTrait;

    protected static $mainModel;
    protected static $mainModelClass    = '\\xjryanse\\user\\model\\UserChatFriend';

    /*
     * 获取聊天好友列表
     */
    public static function getFriends( $userId )
    {
        $con[] = ['user_id','=',$userId];
        $lists = self::lists( $con );
        foreach( $lists as &$v){
            $v['friendInfo']    = UserService::getInstance( $v['friend_user_id'] )->get();
            //未读消息条数
            $v['noReadsCount']  = 0 ;
        }
        return $lists;
    }
    /**
     * 
     * @param type $userId          主好友
     * @param type $friendUserId    添加好友
     */
    public static function addFriend( $userId, $friendUserId )
    {
        if(self::isFriend($userId, $friendUserId)){
            return true;
        }
        $data['user_id']        = $userId;
        $data['friend_user_id'] = $friendUserId;
        return self::save($data);
    }
    /*
     * 是否好友（单向）
     * @param type $userId
     * @param type $friendUserId
     * @return bool
     */
    public static function isFriend( $userId, $friendUserId ) :bool
    {
        $con[] = ['user_id','=',$userId];
        $con[] = ['friend_user_id','=',$friendUserId];
        return self::find( $con ) ? true : false;
    }
    /**
     * 设定末条已读消息id
     * @param type $userId          用户id
     * @param type $friendUserId    好友id
     * @param type $lastReadId      末条已读消息id
     */
    public static function setLastReadId( $userId,$friendUserId,$lastReadId)
    {
        $con[] = ['user_id','=',$userId];
        $con[] = ['friend_user_id','=',$friendUserId];
        $info = self::find( $con );
        if( $info ){
            $data['last_read_id'] = $lastReadId;
            return self::getInstance( $info['id'] )->update( $data );
        }
        return false;
    }
}
