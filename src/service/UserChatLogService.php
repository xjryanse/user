<?php

namespace xjryanse\user\service;

use xjryanse\system\interfaces\MainModelInterface;

/**
 * 聊天记录表
 */
class UserChatLogService implements MainModelInterface {

    use \xjryanse\traits\InstTrait;
    use \xjryanse\traits\MainModelTrait;
    use \xjryanse\traits\MainModelRamTrait;
    use \xjryanse\traits\MainModelCacheTrait;
    use \xjryanse\traits\MainModelCheckTrait;
    use \xjryanse\traits\MainModelGroupTrait;
    use \xjryanse\traits\MainModelQueryTrait;


    protected static $mainModel;
    protected static $mainModelClass = '\\xjryanse\\user\\model\\UserChatLog';

    /**
     * 未读消息统计数量
     * @param type $userId          用户id
     * @param type $friendId        好友id
     * @param type $lastReadMsgId   末条已读消息id
     */
    public static function noReadCount($userId, $friendId, $lastReadMsgId) {
        //当前用户在前
        $concats[] = '\'' . $userId . '_' . $friendId . '\'';
        //当前用户在后
        $concats[] = '\'' . $friendId . '_' . $userId . '\'';
        //查询聊天记录条件
        if ($lastReadMsgId) {
            $con[] = ['id', '>', $lastReadMsgId];
        }
        $count = self::mainModel()->whereRaw("concat( from_user_id,'_',receiver_id ) in (" . implode(",", $concats) . ")")->where($con)->count();

        return $count;
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
     *
     */
    public function fMessage() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    /**
     * 消息类型
     */
    public function fMsgType() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    /**
     * 事件名称
     */
    public function fEvent() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    /**
     * 发送人id
     */
    public function fFromUserId() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    /**
     * 接收人id/群id
     */
    public function fReceiverId() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    /**
     * 消息发送时间
     */
    public function fMsgTime() {
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
