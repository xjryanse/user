<?php

namespace xjryanse\user\service;

use xjryanse\system\interfaces\MainModelInterface;
use xjryanse\logic\Cachex;
/**
 * 用户跳链
 */
class UserJumpService implements MainModelInterface {

    use \xjryanse\traits\InstTrait;
    use \xjryanse\traits\MainModelTrait;
    use \xjryanse\traits\MainModelRamTrait;
    use \xjryanse\traits\MainModelCacheTrait;
    use \xjryanse\traits\MainModelCheckTrait;
    use \xjryanse\traits\MainModelGroupTrait;
    use \xjryanse\traits\MainModelQueryTrait;

    // use \xjryanse\traits\SubServiceTrait;
    use \xjryanse\traits\StaticModelTrait;
    use \xjryanse\traits\ObjectAttrTrait;

    protected static $mainModel;
    protected static $mainModelClass = '\\xjryanse\\user\\model\\UserJump';
    
    public static function getUrl($userId,$key){
        $con[] = ['user_id','=',$userId];
        $con[] = ['jump_key','=',$key];
        $info = self::staticConFind($con);
        return $info ? $info['jump_url'] : '';
    }

}
