<?php
namespace xjryanse\user\service;

use xjryanse\system\interfaces\MainModelInterface;
use Exception;
use think\facade\Request;
/**
 * 权限
 */
class UserAuthAccessService implements MainModelInterface
{
    use \xjryanse\traits\InstTrait;
    use \xjryanse\traits\MainModelTrait;

    protected static $mainModel;
    protected static $mainModelClass    = '\\xjryanse\\user\\model\\UserAuthAccess';

    public static function saveCheck(array $data)
    {
        if(!arrayHasValue($data, 'name')){
            throw new Exception('权限名称不能为空');
        }
    }
    /*
     * 将url链接进行了转化
     */
    public static function listsInfo( $con = [],$order='')
    {
        if(self::mainModel()->hasField('app_id')){
            $con[] = ['app_id','=',session(SESSION_APP_ID)];
        }
        $lists = self::mainModel()->where( $con )->order($order)->cache(2)->select();
        foreach($lists as &$v){
            //兼容vue，节点默认不展开
            $v['vue_expand'] = false;
            if(!$v['url']){
                continue;
            }
            $tmp    = $v['url'];
            $tmp    .= strstr($tmp, '?') ? '&' : '?';
            $v['url']  = $tmp .'comKey='.Request::param('comKey','');
        }
        return $lists;
    }
    
}
