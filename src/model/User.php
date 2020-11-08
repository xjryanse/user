<?php
namespace xjryanse\user\model;

use xjryanse\system\service\SystemFileService;
/**
 * 用户总表
 */
class User extends Base
{
    /**
     * 用户头像图标
     * @param type $value
     * @return type
     */
    public function getHeadimgAttr( $value )
    {
        return $value ? SystemFileService::getInstance( $value )->get() : $value ;
    }
    

}