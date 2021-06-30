<?php

namespace xjryanse\user\model;

/**
 * 用户总表
 */
class UserDegreeExt extends Base {
    /**
     * 延期前会员到期时间
     * @param type $value
     * @return type
     */
    public function setPreExtEndTimeAttr($value) {
        return self::setTimeVal($value);
    }    
    /**
     * 延期后会员到期时间
     * @param type $value
     * @return type
     */
    public function setAfterExtEndTime($value) {
        return self::setTimeVal($value);
    }    
}
