<?php

namespace xjryanse\user\service;

use xjryanse\system\interfaces\MainModelInterface;
use think\facade\Request;

/**
 * 用户登录日志
 */
class UserLoginLogService implements MainModelInterface {

    use \xjryanse\traits\InstTrait;
    use \xjryanse\traits\MainModelTrait;
    use \xjryanse\traits\MainModelQueryTrait;
    use \xjryanse\traits\RedisModelTrait;

    protected static $mainModel;
    protected static $mainModelClass = '\\xjryanse\\user\\model\\UserLoginLog';

    /**
     * 记录登录日志
     */
    public static function loginLog($userInfo) {
        $data['user_id'] = $userInfo['id'];
        $data['username'] = $userInfo['username'];
        $data['login_ip'] = Request::ip();
        $data['login_time'] = date('Y-m-d H:i:s');
        $data['domain_name'] = Request::server('HTTP_HOST');
        //登录日志
        //self::save($data);
        // 20221026
        self::redisLog($data);
        //末次登录时间更新
        $data2['id'] = $userInfo['id'];
        $data2['last_loginip'] = Request::ip();
        $data2['last_logintime'] = date('Y-m-d H:i:s');
        UserService::mainModel()->where('id',$userInfo['id'])->update($data2);
        // UserService::getInstance($userInfo['id'])->update($data2);
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
    public function fUserId() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    /**
     *
     */
    public function fUsername() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    /**
     *
     */
    public function fLoginIp() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    /**
     *
     */
    public function fLoginTime() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    /**
     *
     */
    public function fDomainName() {
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
