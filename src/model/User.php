<?php
namespace xjryanse\user\model;

use xjryanse\system\service\SystemFileService;
/**
 * 用户总表
 */
class User extends Base
{
    /**
     * 小程序图标
     * @param type $value
     * @return type
     */
    public function getHeadimgurlAttr( $value )
    {
        return $value ? SystemFileService::getInstance( $value )->get() : $value ;
    }
    

}