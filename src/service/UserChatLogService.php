<?php
namespace xjryanse\user\service;

use xjryanse\system\interfaces\MainModelInterface;

/**
 * 聊天记录表
 */
class UserChatLogService implements MainModelInterface
{
    use \xjryanse\traits\InstTrait;
    use \xjryanse\traits\MainModelTrait;

    protected static $mainModel;
    protected static $mainModelClass    = '\\xjryanse\\user\\model\\UserChatLog';

}
