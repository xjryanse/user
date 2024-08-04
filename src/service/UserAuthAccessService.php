<?php

namespace xjryanse\user\service;

use xjryanse\system\interfaces\MainModelInterface;
use xjryanse\system\service\SystemCompanyService;
use xjryanse\logic\Arrays;
use xjryanse\logic\Strings;
use xjryanse\logic\Debug;
use xjryanse\logic\Url;
use Exception;

/**
 * 权限
 */
class UserAuthAccessService implements MainModelInterface {

    use \xjryanse\traits\InstTrait;
    use \xjryanse\traits\MainModelTrait;
    use \xjryanse\traits\MainModelRamTrait;
    use \xjryanse\traits\MainModelCacheTrait;
    use \xjryanse\traits\MainModelCheckTrait;
    use \xjryanse\traits\MainModelGroupTrait;
    use \xjryanse\traits\MainModelQueryTrait;

    use \xjryanse\traits\StaticModelTrait;
    use \xjryanse\traits\MainModelComCateLevelQueryTrait;

    protected static $mainModel;
    protected static $mainModelClass = '\\xjryanse\\user\\model\\UserAuthAccess';

    use \xjryanse\user\service\authAccess\FieldTraits;
    use \xjryanse\user\service\authAccess\ListTraits;

    public static function extraDetails( $ids ){
        return self::commExtraDetails($ids, function($lists) use ($ids){
            $roleCounts = UserAuthRoleAccessService::groupBatchCount('access_id', $ids);
            foreach($lists as &$v){
                $v['roleCount']       = Arrays::value($roleCounts, $v['id'],0);
            }

            return $lists;
        });
    }
    
    public static function saveCheck(array $data) {
        if (!arrayHasValue($data, 'name')) {
            throw new Exception('权限名称不能为空');
        }
    }
    /*
     * 提取公司全部的可用菜单id
     */
    public static function compAllAccessIds(){
        // 【1】按company_id提取
        $ids        = self::ids();
        // 【2】按comp_cate提取
        $cIds       = self::comCateLevelIds();
        
        return array_merge($ids, $cIds);
    }
    /*
     * 将url链接进行了转化
     */

    public static function listsInfo($con = [], $order = 'sort') {
        // 20220814优化
        $keys = ['id','pid','name','icon','access_group','access_type','show_type','url'];

        $ids    = self::compAllAccessIds();
        $con[] = ['status', '=', 1];
        $con[] = ['id', 'in', $ids];
        $lists  = self::mainModel()
                ->where($con)
                ->order($order)
                ->field(implode(',',$keys))
                ->select();
        //20220515增加替换参数
        $replaceData['year'] = date('Y');
        $replaceData['yearmonth'] = date('Y-m');
        $replaceData['date'] = date('d');

        foreach ($lists as &$v) {
            //兼容vue，节点默认不展开
            $v['vue_expand'] = false;
            if (!$v['url']) {
                continue;
            }
            if($v['access_group'] == 'admin'){
                $v['url'] = Url::addParam($v['url'], ['comKey' => session(SESSION_COMPANY_KEY), 'sessionid' => session_id()]);
            }
            // 嵌套页面添加key
            if(in_array($v['access_group'],['adminx','manage']) && $v['show_type'] == 1){
                $v['url'] = '/'.session(SESSION_COMPANY_KEY).$v['url'];
            }
            // 20220515增加替换参数
            $v['url'] = Strings::dataReplace($v['url'],$replaceData);
        }
        return $lists;
    }
    /**
     * 添加菜单
     * @createTime 2023-09-11
     * @param type $name    菜单名称
     * @param type $url     菜单url
     * @param type $group   菜单分组
     * @param type $data    其他数据
     */
    public static function addAccess($name,$url, $group='manage', $data = []){
        $data['name']           = $name;
        $data['url']            = $url;
        $data['access_group']   = $group;
        
        return self::save($data);
    }

}
