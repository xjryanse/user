<?php

namespace xjryanse\user\service;

use xjryanse\system\interfaces\MainModelInterface;

/**
 * 用户验证码
 */
class UserVerifyCodeService implements MainModelInterface {

    use \xjryanse\traits\InstTrait;
    use \xjryanse\traits\MainModelTrait;
    use \xjryanse\traits\MainModelRamTrait;
    use \xjryanse\traits\MainModelCacheTrait;
    use \xjryanse\traits\MainModelCheckTrait;
    use \xjryanse\traits\MainModelGroupTrait;
    use \xjryanse\traits\MainModelQueryTrait;


    protected static $mainModel;
    protected static $mainModelClass = '\\xjryanse\\user\\model\\UserVerifyCode';

    /**
     * 使验证码失效
     */
    public static function invalid($key, $userIdentify) {
        $con[] = ['key', '=', $key];
        $con[] = ['user_identify', '=', $userIdentify];
        return self::mainModel()->where($con)->update(['status' => 0]);
    }

    /**
     * 生成验证码，默认长度6（IOSbug，输入默认复制2次，前端采用指定6位截断）
     * @param type $length
     */
    public static function generate($length = 6) {
        $min = '1' . str_repeat(0, $length - 1);
        $max = str_repeat(9, $length);
        return mt_rand($min, $max);
    }

    /**
     * 校验
     * @param type $length
     */
    public static function verify($key, $userIdentify, $code, $inSeconds = 300) {
        $con[] = ['create_time', '>=', date('Y-m-d H:i:s', time() - $inSeconds)]; //有效期内，默认5分钟
        $con[] = ['key', '=', $key];          //场景
        if ($userIdentify) {
            $con[] = ['user_identify', '=', $userIdentify];   //匹配到用户
        }

        $con[] = ['code', '=', $code];
        $con[] = ['status', '=', 1];      //有效
        $con[] = ['has_used', '=', 0];    //未使用

        $res = self::lists($con);
//        dump($res);
        if ($res->isEmpty()) {
            return false;
        }
        //更新为已使用。
        self::mainModel()->where($con)->update(['has_used' => 1]);
        return true;
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
     * 用户识别（手机号码，邮箱，openid）
     */
    public function fUserIdentify() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    /**
     * 场景key值
     */
    public function fKey() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    /**
     * 验证码
     */
    public function fCode() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    /**
     * email邮箱,sms短信,wxtplmsg微信模板消息
     */
    public function fSendBy() {
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
