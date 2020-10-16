<?php
namespace xjryanse\user\model;

use app\db\logic\DbLogic;
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
        if($value){
            return DbLogic::get('w_file', $value);
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
        if($value){
            return DbLogic::get('w_file', $value);
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
        if($value){
            return DbLogic::get('w_file', $value);
        }
        return $value;
    }    

}