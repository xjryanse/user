<?php

namespace xjryanse\user\model;

/**
 * 用户总表
 */
class User extends Base {
    public static $picFields = ['headimg','sign'];

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

    /**
     * 用户签名
     * @param type $value
     * @return type
     */
    public function getSignAttr($value) {
        return self::getImgVal($value);
    }

    /**
     * 用户签名，图片带id只取id
     * @param type $value
     * @throws \Exception
     */
    public function setSignAttr($value) {
        return self::setImgVal($value);
    }    
    
    public function setLastLogintimeAttr($value) {
        return self::setTimeVal($value);
    }    
    /**
     * 20240101：司机
     * @return string
     */
    public static function driverCertSql(){
        // driver:驾驶证；qualify:从业资格证
        $certKeys = ['driver','qualify'];

        $sql = "(SELECT
            `aa`.`id` AS `certUserId`,";
        foreach($certKeys as $k){
            $sql .= "max( ( CASE `bb`.`cert_key` WHEN '".$k."' THEN date_format( `bb`.`cert_limit_time`, '%Y-%m-%d' ) ELSE NULL END ) ) AS `".$k."LimitTime`,
                max( ( CASE `bb`.`cert_key` WHEN '".$k."' THEN `bb`.`cert_time` ELSE NULL END ) ) AS `".$k."CertTime`,
                max( ( CASE `bb`.`cert_key` WHEN '".$k."' THEN `bb`.`certStatus` ELSE NULL END ) ) AS `".$k."CertStatus`,
                max( ( CASE `bb`.`cert_key` WHEN '".$k."' THEN `bb`.`cert_gov` ELSE NULL END ) ) AS `".$k."CertGov`,
                max( ( CASE `bb`.`cert_key` WHEN '".$k."' THEN `bb`.`cert_no2` ELSE NULL END ) ) AS `".$k."certNo2`,
                max( ( CASE `bb`.`cert_key` WHEN '".$k."' THEN `bb`.`cert_level` ELSE NULL END ) ) AS `".$k."CertLevel`,
                max( ( CASE `bb`.`cert_key` WHEN '".$k."' THEN `bb`.`cert_no` ELSE NULL END ) ) AS `".$k."CertNo`,";
        }

        $sql .= "1 AS `t` FROM ( `w_user` `aa` INNER JOIN `w_view_cert_driver` `bb` ON ( ( `aa`.`id` = `bb`.`belong_table_id` ) ) ) GROUP BY `aa`.`id`)";

        return $sql;
    }
}
