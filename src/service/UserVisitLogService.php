<?php

namespace xjryanse\user\service;

use xjryanse\customer\service\CustomerService;
use xjryanse\system\interfaces\MainModelInterface;
use xjryanse\logic\Arrays;
use xjryanse\logic\DataCheck;

/**
 * 用户来访登记
 */
class UserVisitLogService implements MainModelInterface {

    use \xjryanse\traits\InstTrait;
    use \xjryanse\traits\MainModelTrait;
    use \xjryanse\traits\MainModelQueryTrait;
    use \xjryanse\traits\SubServiceTrait;

    protected static $mainModel;
    protected static $mainModelClass = '\\xjryanse\\user\\model\\UserVisitLog';

    /**
     * 用户在多少分钟内是否有登记记录（避免重复登记）
     * @param type $customerId
     * @param type $userId
     * @param type $minutes
     */
    public static function hasVisitWithin($customerId, $userId, $minutes = 15) {
        $cond = [];
        $cond[] = ['user_id', '=', $userId];
        $cond[] = ['customer_id', '=', $customerId];
        $cond[] = ['create_time', '>=', date('Y-m-d H:i:s', '-' . $minutes . ' minute')];
        return UserVisitLogService::find($cond);
    }

    /**
     * 额外输入信息
     */
    public static function extraPreSave(&$data, $uuid) {
        DataCheck::must($data, ['user_id', 'customer_id']);
        //用户信息
        $userId = Arrays::value($data, 'user_id');
        $userInfo = UserService::getInstance($userId)->get();
        $data['realname'] = Arrays::value($userInfo, 'realname');
        $data['user_customer_id'] = Arrays::value($userInfo, 'customer_id');
        $userCustomerInfo = CustomerService::getInstance(Arrays::value($userInfo, 'customer_id'))->get();
        //公司信息
        $customerId = Arrays::value($data, 'customer_id');
        $customerInfo = CustomerService::getInstance($customerId)->get();
        $describe = '';
        if ($userCustomerInfo) {
            $describe = $userCustomerInfo['customer_name'] . ' 的 ';
        }
        $describe .= $data['realname'] . ' 于' . date('Y-m-d H:i:s') . '到访' . $customerInfo['customer_name'];
        $data['describe'] = $describe;
        return $data;
    }

    /**
     * 额外输入信息
     */
    public static function extraAfterSave(&$data, $uuid) {
        
    }

    /**
     * 额外输入信息
     */
    public static function extraAfterUpdate(&$data, $uuid) {
        
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
     * 手机号码
     */
    public function fPhone() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    /**
     * 真实姓名
     */
    public function fRealname() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    /**
     * 身份证号码
     */
    public function fIdNo() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    /**
     * 身份证地址
     */
    public function fAddress() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    /**
     * 生日
     */
    public function fBirthday() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    /**
     * 性别：1男2女
     */
    public function fSex() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    /**
     * 民族
     */
    public function fNation() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    /**
     * 排序
     */
    public function fSort() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    /**
     * 身份证正面
     */
    public function fPicFace() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    /**
     * 身份证反面
     */
    public function fPicBack() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    /**
     * 人脸照片
     */
    public function fRealFace() {
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
