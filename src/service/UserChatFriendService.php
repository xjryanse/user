<?php

namespace xjryanse\user\service;

use xjryanse\system\interfaces\MainModelInterface;
use xjryanse\user\service\UserService;

/**
 * 好友关系表
 */
class UserChatFriendService implements MainModelInterface {

    use \xjryanse\traits\InstTrait;
    use \xjryanse\traits\MainModelTrait;
    use \xjryanse\traits\MainModelQueryTrait;

    protected static $mainModel;
    protected static $mainModelClass = '\\xjryanse\\user\\model\\UserChatFriend';

    /*
     * 获取聊天好友列表
     */

    public static function getFriends($userId) {
        $con[] = ['user_id', '=', $userId];
        $lists = self::lists($con);
        foreach ($lists as &$v) {
            $v['friendInfo'] = UserService::getInstance($v['friend_user_id'])->get();
        }
        return $lists;
    }

    /**
     * 
     * @param type $userId          主好友
     * @param type $friendUserId    添加好友
     */
    public static function addFriend($userId, $friendUserId) {
        if (self::isFriend($userId, $friendUserId)) {
            return true;
        }
        //用户为空，则不操作
        if (!$userId || !$friendUserId) {
            return false;
        }
        $data['user_id'] = $userId;
        $data['friend_user_id'] = $friendUserId;
        return self::save($data);
    }

    /*
     * 是否好友（单向）
     * @param type $userId
     * @param type $friendUserId
     * @return bool
     */

    public static function isFriend($userId, $friendUserId): bool {
        $con[] = ['user_id', '=', $userId];
        $con[] = ['friend_user_id', '=', $friendUserId];
        return self::find($con) ? true : false;
    }

    /**
     * 设定末条已读消息id
     * @param type $userId          用户id
     * @param type $friendUserId    好友id
     * @param type $lastReadId      末条已读消息id
     */
    public static function setLastReadId($userId, $friendUserId, $lastReadId) {
        $con[] = ['user_id', '=', $userId];
        $con[] = ['friend_user_id', '=', $friendUserId];
        $info = self::find($con);
        if ($info) {
            $data['last_read_id'] = $lastReadId;
            return self::getInstance($info['id'])->update($data);
        }
        return false;
    }

    /**
     *
     */
    public function fId() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    /**
     *
     */
    public function fAppId() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    /**
     *
     */
    public function fCompanyId() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    /**
     * 用户id
     */
    public function fUserId() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    /**
     * 朋友id
     */
    public function fFriendUserId() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    /**
     * 末条已读消息id：(超过该id的为未读消息)
     */
    public function fLastReadId() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    /**
     * 分组id
     */
    public function fGroupId() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    /**
     * 用户对好友的备注
     */
    public function fMark() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    /**
     * 排序
     */
    public function fSort() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    /**
     * 状态(0禁用,1启用)
     */
    public function fStatus() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    /**
     * 有使用(0否,1是)
     */
    public function fHasUsed() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    /**
     * 锁定（0：未锁，1：已锁）
     */
    public function fIsLock() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    /**
     * 锁定（0：未删，1：已删）
     */
    public function fIsDelete() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    /**
     * 备注
     */
    public function fRemark() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    /**
     * 创建者，user表
     */
    public function fCreater() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    /**
     * 更新者，user表
     */
    public function fUpdater() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    /**
     * 创建时间
     */
    public function fCreateTime() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    /**
     * 更新时间
     */
    public function fUpdateTime() {
        return $this->getFFieldValue(__FUNCTION__);
    }

}
