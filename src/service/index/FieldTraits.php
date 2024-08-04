<?php

namespace xjryanse\user\service\index;

/**
 * 分页复用列表
 */
trait FieldTraits{
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

    public function fNamePhone() {
        return $this->fieldValue('namePhone');
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

    public function fBusierId() {
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

    public function fSign() {
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
