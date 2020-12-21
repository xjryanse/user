<?php

namespace xjryanse\user\service;

use xjryanse\system\interfaces\MainModelInterface;
use Exception;

/**
 * 用户总表
 */
class UserService implements MainModelInterface {

    use \xjryanse\traits\InstTrait;
    use \xjryanse\traits\MainModelTrait;

    protected static $mainModel;
    protected static $mainModelClass = '\\xjryanse\\user\\model\\User';

    /*
     * 手机号码取用户信息
     */

    public static function getUserInfoByPhone($phone) {
        if (!$phone) {
            return false;
        }
        $con = [];
        $con[] = ['phone', '=', $phone];
        $info = self::find($con);
        if (!$info) {
            //尝试匹配用户名中的手机号码
            $con1 = [];
            $con1[] = ['username', '=', $phone];
            $info = self::find($con1);
        }
        return $info;
    }

    /**
     * 设定用户名
     */
    public function setUserName($userName) {
        //查询用户名是否存在
        $con[] = ['username', '=', $userName];
        $con[] = ['id', '<>', $this->uuid];
        $count = self::count($con);
        //更新
        if ($count) {
            throw new Exception('用户名已存在！');
        }
        return $this->update(['username' => $userName]);
    }

    /*
     * 设定密码
     */

    public function setPassword($password) {
        return $this->update(['password' => password_hash($password, PASSWORD_DEFAULT)]);
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
     * 推荐用户id
     */
    public function fRecUserId() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    /**
     *
     */
    public function fCompanyId() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    /**
     * 用户名
     */
    public function fUsername() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    /**
     * 会员等级：normal普通会员；senior：高级会员
     */
    public function fDegree() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    /**
     * 用户头像【存id】
     */
    public function fHeadimg() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    /**
     * 密码
     */
    public function fPassword() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    /**
     * 手机
     */
    public function fPhone() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    /**
     * 平台昵称
     */
    public function fNickname() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    /**
     * 真实姓名
     */
    public function fRealname() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    /**
     * 性别(1男,2女)
     */
    public function fSex() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    /**
     * 生日
     */
    public function fBirthday() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    /**
     * 身份证号
     */
    public function fIdNo() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    /**
     * 注册来源
     */
    public function fSource() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    /**
     * 后台类型:
      '':无后台权限
      'normal':普通后台用户
      'super':系统超级管理员
      'subsuper'公司级超级管理

     */
    public function fAdminType() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    /**
     * 末次登录ip
     */
    public function fLastLoginip() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    /**
     * 末次登录时间
     */
    public function fLastLogintime() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    /**
     *
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
     * 创建者
     */
    public function fCreater() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    /**
     * 更新者
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
