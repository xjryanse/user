<?php

namespace xjryanse\user\service;

use xjryanse\system\interfaces\MainModelInterface;

/**
 * 角色分类
 */
class UserAuthCateService implements MainModelInterface {

    use \xjryanse\traits\InstTrait;
    use \xjryanse\traits\MainModelTrait;
    use \xjryanse\traits\MainModelRamTrait;
    use \xjryanse\traits\MainModelCacheTrait;
    use \xjryanse\traits\MainModelCheckTrait;
    use \xjryanse\traits\MainModelGroupTrait;
    use \xjryanse\traits\MainModelQueryTrait;


    protected static $mainModel;
    protected static $mainModelClass = '\\xjryanse\\user\\model\\UserAuthCate';
    //直接执行后续触发动作
    protected static $directAfter = true;

}
