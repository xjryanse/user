<?php
namespace xjryanse\user\model;

use app\db\logic\DbLogic;
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
        if($value){
            return DbLogic::get('w_file', $value);
        }
        return $value;
    }
    

}