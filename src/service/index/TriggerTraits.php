<?php

namespace xjryanse\user\service\index;


use xjryanse\logic\Arrays;
use Exception;
/**
 * 分页复用列表
 */
trait TriggerTraits{
    /**
     * 钩子-保存前
     */
    public static function ramPreSave(&$data, $uuid) {
        
        if(Arrays::value($data, 'phone') && !Arrays::value($data, 'username')){
            $data['username'] = Arrays::value($data, 'phone');
        }

    }
    
}
