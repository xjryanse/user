<?php

namespace xjryanse\user\service\authAccess;

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
     *
     */
    public function fCompanyId() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    /**
     * 父id，-1表示开发模式下的菜单
     */
    public function fPid() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    /**
     * 权限名称
     */
    public function fName() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    /**
     * 图标
     */
    public function fIcon() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    /**
     * 类型：
      page非菜单页面，
      menu侧边菜单页，
      api api接口、
      home首页菜单
     */
    public function fAccessType() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    /**
     * 权限地址
     */
    public function fUrl() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    /**
     * 权限串，优先于url判断
     */
    public function fAuthstr() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    /**
     * 1嵌套页，2跳链
     */
    public function fShowType() {
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
