<?php

namespace xjryanse\user\service;

use xjryanse\system\interfaces\MainModelInterface;
use xjryanse\system\service\SystemConditionService;
use xjryanse\user\service\UserCouponService;
use xjryanse\order\service\OrderService;
use xjryanse\logic\Debug;
use xjryanse\logic\Arrays;
use Exception;
/**
 * 用户优惠券模板
 */
class UserCouponTemplateService extends Base implements MainModelInterface {

    use \xjryanse\traits\InstTrait;
    use \xjryanse\traits\MainModelTrait;
    use \xjryanse\traits\MainModelQueryTrait;

    protected static $mainModel;
    protected static $mainModelClass = '\\xjryanse\\user\\model\\UserCouponTemplate';
    /**
     * 是否可使用当前优惠券
     * 判断某订单是否可使用当前的优惠券
     */
    public function couponCanIUse( $orderId ){
        $order = OrderService::getInstance($orderId)->get();
        $param = $order ? $order->toArray() : []; 
        $param[ 'orderId' ]     = $orderId;     //订单id
        $matchConditionKey = $this->fMatchCondition();
        Debug::debug('$matchConditionKey',$matchConditionKey);
        //优惠券类型使用coupon
        return SystemConditionService::isReachByItemKey( 'coupon', $matchConditionKey, $param );
    }
    /**
     * 获取优惠金额
     */
    public function getCouponMoney( $orderPrize ){
        
        
        
    }
    /**
     * 用户领取优惠券
     * @param type $userId
     */
    public function userGetCoupon( $userId ,$extraData = []){
        $coupon     = $this->get();
        if(!$coupon){
            throw new Exception('优惠券不存在');
        }
        Debug::debug('$coupon',$coupon);
        $coupArr    = $coupon ? $coupon->toArray() : []; 
        $userCoup   = array_merge( $extraData, Arrays::getByKeys( $coupArr , ['company_id','coupon_value','discount','min_order_prize','deduction_type']));
        $userCoup['coupon_tpl_id']  = $this->uuid;
        $userCoup['user_id']        = $userId;
        $userCoup['start_time']     = date('Y-m-d H:i:s');
        $userCoup['exprire_time']   = date('Y-m-d H:i:s',strtotime("+".$coupon['expire_days']." days"));
        Debug::debug('$userCoup',$userCoup);
        return UserCouponService::save($userCoup);
    }

    /**
     * 钩子-保存前
     */
    public static function extraPreSave(&$data, $uuid) {
        
    }

    /**
     * 钩子-保存后
     */
    public static function extraAfterSave(&$data, $uuid) {
        
    }

    /**
     * 钩子-更新前
     */
    public static function extraPreUpdate(&$data, $uuid) {
        
    }

    /**
     * 钩子-更新后
     */
    public static function extraAfterUpdate(&$data, $uuid) {
        
    }

    /**
     * 钩子-删除前
     */
    public function extraPreDelete() {
        
    }

    /**
     * 钩子-删除后
     */
    public function extraAfterDelete() {
        
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
    public function fCompanyId() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    /**
     * 优惠券标题
     */
    public function fCouponTitle() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    /**
     * 优惠券说明
     */
    public function fCouponContent() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    /**
     * 过期天数
     */
    public function fExpireDays() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    /**
     * 适应订单类型
     */
    public function fOrderType() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    /**
     * 折扣类型(money抵用金额，amount抵用数量)
     */
    public function fDeductionType() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    /**
     * 优惠券面值
     */
    public function fCouponValue() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    /**
     * 优惠券面值单位
     */
    public function fValueUnit() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    /**
     * 最低使用金额
     */
    public function fMinUsePrize() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    /**
     * 匹配条件key：system_condition表
     */
    public function fMatchCondition() {
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
