<?php

namespace xjryanse\user\model;

/**
 * 用户总表
 */
class User extends Base {

    /**
     * 用户头像图标
     * @param type $value
     * @return type
     */
    public function getHeadimgAttr($value) {
        return self::getImgVal($value);
    }

    /**
     * 图片修改器，图片带id只取id
     * @param type $value
     * @throws \Exception
     */
    public function setHeadimgAttr($value) {
        return self::setImgVal($value);
    }

}
