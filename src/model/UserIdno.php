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
     * 图片修改器，图片带id只取id
     * @param type $value
     * @throws \Exception
     */
    public function setPicFaceAttr( $value )
    {
        if((is_array($value)|| is_object($value)) && isset( $value['id'])){
            $value = $value['id'];
        }
        return $value;
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
     * 图片修改器，图片带id只取id
     * @param type $value
     * @throws \Exception
     */
    public function setPicBackAttr( $value )
    {
        if((is_array($value)|| is_object($value)) && isset( $value['id'])){
            $value = $value['id'];
        }
        return $value;
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
    /**
     * 图片修改器，图片带id只取id
     * @param type $value
     * @throws \Exception
     */
    public function setRealFaceAttr( $value )
    {
        if((is_array($value)|| is_object($value)) && isset( $value['id'])){
            $value = $value['id'];
        }
        return $value;
    } 
}