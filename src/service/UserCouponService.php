<?php

namespace xjryanse\user\service;

use xjryanse\system\interfaces\MainModelInterface;
use xjryanse\order\service\OrderService;
use Exception;

/**
 * 用户优惠券
 */
class UserCouponService extends Base implements MainModelInterface {

    use \xjryanse\traits\InstTrait;
    use \xjryanse\traits\MainModelTrait;
    use \xjryanse\traits\MainModelRamTrait;
    use \xjryanse\traits\MainModelCacheTrait;
    use \xjryanse\traits\MainModelCheckTrait;
    use \xjryanse\traits\MainModelGroupTrait;
    use \xjryanse\traits\MainModelQueryTrait;


    protected static $mainModel;
    protected static $mainModelClass = '\\xjryanse\\user\\model\\UserCoupon';

    /**
     * 添加优惠券
     * @param type $userId
     * @param type $counponTplId
     */
    public static function couponAdd($userId, $counponTplId, $data = []) {
        $couponTplInfo = UserCouponTemplateService::getInstance($counponTplId)->get();
        $data['user_id'] = $userId;
        $data['coupon_tpl_id'] = $counponTplId;
        $data['coupon_title'] = Arrays::value($couponTplInfo, 'coupon_title');
        $data['start_time'] = date('Y-m-d H:i:s');
        $data['exprire_time'] = date('Y-m-d H:i:s', time() + 86400 * intval($couponTplInfo['expire_days']));

        return self::save($data);
    }

    /**
     * 是否有券
     */
    public static function hasCoupon($fromTable, $fromTableId, $con = []) {
        $con[] = ['from_table', '=', $fromTable];
        $con[] = ['from_table_id', '=', $fromTableId];
        return self::mainModel()->where($con)->count();
    }

    /**
     * 优惠券使用
     */
    public function use($useTable, $useTableId) {
        $con[] = ['use_table', '=', $useTable];
        $con[] = ['use_table_id', '=', $useTableId];
        if (self::count($con)) {
            throw new Exception('该使用事项已有用券记录');
        }

        $info = $this->get(0);
        if ($info['use_status']) {
            $rData[1] = '已使用';
            $rData[2] = '已过期';
            throw new Exception('优惠券' . $rData[$info['use_status']]);
        }
        if (date('Y-m-d H:i:s') >= $info['exprire_time']) {
            self::mainModel()->where('id', $this->uuid)->update(['use_status' => 2]);
            throw new Exception('优惠券过期了');
        }

        $data['use_table'] = $useTable;
        $data['use_table_id'] = $useTableId;
        $data['use_time'] = date('Y-m-d H:i:s');
        $data['use_status'] = 1;
        $cond[] = ['id', '=', $this->uuid];
        $cond[] = ['use_status', '=', 0];
        $res = self::mainModel()->where($cond)->update($data);
        if (!$res) {
            throw new Exception('该券已使用');
        }
        return $res;
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
     * 优惠券模板id
     */
    public function fCouponTplId() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    /**
     * 持有用户id
     */
    public function fUserId() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    /**
     * 过期时间
     */
    public function fExprireTime() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    /**
     * 开始时间
     */
    public function fStartTime() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    /**
     * 使用订单id
     */
    public function fOrderId() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    /**
     * 使用订单号
     */
    public function fOrderSn() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    /**
     * 使用状态（todo未使用，finish已使用，expire已过期）
     */
    public function fUseStatus() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    /**
     * 使用时间
     */
    public function fUseTime() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    /**
     * 优惠券标题
     */
    public function fCouponTitle() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    /**
     * 适用订单类型
     */
    public function fOrderType() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    /**
     * 抵扣类型(money抵扣金额，amount抵用数量)
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
     * 来源表
     */
    public function fFromTable() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    /**
     * 来源表id
     */
    public function fFromTableId() {
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
