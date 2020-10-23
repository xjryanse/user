<?php
namespace xjryanse\user\model;

use xjryanse\system\service\SystemFileService;
/**
 * 用户实名
 */
class UserIdno extends Base
{
    /**
     * 身份证正面
     * @param type $value
     * @return type
     */
    public function getPicFaceAttr( $value )
    {
        return $value ? SystemFileService::getInstance( $value )->get() : $value ;
    }
    /**
     * 身份证反面
     * @param type $value
     * @return type
     */
    public function getPicBackAttr( $value )
    {
        return $value ? SystemFileService::getInstance( $value )->get() : $value ;
    }
    /**
     * 人脸实拍
     * @param type $value
     * @return type
     */
    public function getRealFaceAttr( $value )
    {
        return $value ? SystemFileService::getInstance( $value )->get() : $value ;
    }    

}