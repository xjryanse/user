<?php

namespace xjryanse\user\service;

use xjryanse\system\interfaces\MainModelInterface;

/**
 * 用户推荐人绑定
 */
class UserRecBindService extends Base implements MainModelInterface {

    use \xjryanse\traits\InstTrait;
    use \xjryanse\traits\MainModelTrait;
    use \xjryanse\traits\MainModelRamTrait;
    use \xjryanse\traits\MainModelCacheTrait;
    use \xjryanse\traits\MainModelCheckTrait;
    use \xjryanse\traits\MainModelGroupTrait;
    use \xjryanse\traits\MainModelQueryTrait;


    protected static $mainModel;
    protected static $mainModelClass = '\\xjryanse\\user\\model\\UserRecBind';

    /**
     * 20230619
     * @param type $userId
     * @param type $recUserId
     * @param type $thingKey
     */
    public static function doBind($userId,$recUserId,$thingKey){
        $data['user_id']        = $userId;
        $data['rec_user_id']    = $recUserId;
        $data['thing_key']      = $thingKey;
        return self::save($data);
    }

}
