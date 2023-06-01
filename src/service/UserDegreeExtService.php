<?php

namespace xjryanse\user\service;

use xjryanse\system\interfaces\MainModelInterface;
use xjryanse\logic\Arrays;
use xjryanse\logic\DataCheck;
use Exception;

/**
 * 用户实名
 */
class UserDegreeExtService implements MainModelInterface {

    use \xjryanse\traits\InstTrait;
    use \xjryanse\traits\MainModelTrait;
    use \xjryanse\traits\SubServiceTrait;

    protected static $mainModel;
    protected static $mainModelClass = '\\xjryanse\\user\\model\\UserDegreeExt';

    public static function extraPreSave(&$data, $uuid) {
        DataCheck::must($data, ['user_id','degree','ext_days']);
        $userId     = Arrays::value($data, 'user_id');
        $degree     = Arrays::value($data, 'degree');
        $extDays    = Arrays::value($data, 'ext_days');
        
        $userInfo   = UserService::getInstance( $userId )->get(0);
        if(!$userInfo){
            throw new Exception('用户不存在');
        }
        if($userInfo['degree'] == $degree && $userInfo['degree_end_time'] > date('Y-m-d H:i:s') ){
            $data['pre_ext_end_time'] = $userInfo['degree_end_time'];
        } else {
            $data['pre_ext_end_time'] = date('Y-m-d H:i:s');
        }
        $data['after_ext_end_time'] = date('Y-m-d H:i:s',strtotime($data['pre_ext_end_time']) + 86400 * $extDays);
        return $data;
    }
    
    public static function extraAfterSave(&$data, $uuid) {
        $userId         = Arrays::value($data, 'user_id');
        $userDegreeInfo = self::getUserDegreeInfo( $userId );
        //更新
        if( $userDegreeInfo ){
            $updData['degree']          = Arrays::value($userDegreeInfo, 'degree');
            $updData['degree_end_time'] = Arrays::value($userDegreeInfo, 'after_ext_end_time');
            UserService::getInstance( $userId )->update($updData);
        }
        return $data;
    }
    
    /**
     * 获取用户的等级时间
     */
    protected static function getUserDegreeInfo( $userId ){
        $con[] = ['user_id','=',$userId];
        return self::mainModel()->where($con)->order('after_ext_end_time desc')->find();
    }
    
    public static function hasExtLog($fromTable,$fromTableId){
        $con[] = ['from_table','=',$fromTable];
        $con[] = ['from_table_id','=',$fromTableId];
        return self::count($con);
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
    public function fDegree() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    /**
     * 延期前会员到期时间
     */
    public function fPreExtEndTime() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    /**
     * 续费后等级截止时间
     */
    public function fAfterExtEndTime() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    /**
     * 生日
     */
    public function fExtDays() {
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
