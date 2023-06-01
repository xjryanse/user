<?php

namespace xjryanse\user\service;

use xjryanse\system\interfaces\MainModelInterface;
use xjryanse\logic\Arrays;
use xjryanse\logic\Strings;
use xjryanse\logic\Url;
use Exception;

/**
 * 权限
 */
class UserAuthAccessService implements MainModelInterface {

    use \xjryanse\traits\InstTrait;
    use \xjryanse\traits\MainModelTrait;
    use \xjryanse\traits\StaticModelTrait;

    protected static $mainModel;
    protected static $mainModelClass = '\\xjryanse\\user\\model\\UserAuthAccess';

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
     * 将url链接进行了转化
     */

    public static function listsInfo($con = [], $order = 'sort') {
        if (self::mainModel()->hasField('app_id')) {
            $con[] = ['app_id', '=', session(SESSION_APP_ID)];
        }
        if (self::mainModel()->hasField('company_id')) {
            $con[] = ['company_id', '=', session(SESSION_COMPANY_ID)];
        }
        //只取启用的
        $con[] = ['status', '=', 1];
        // index 兼容elementui
        /*
        $lists = self::mainModel()->where($con)->field('id,pid,name,icon,access_group,access_type,show_type,url')->order($order)->cache(86400)->select();
        if($lists){
            $lists = $lists->toArray();
        }
         */
        // 20220814优化
        $keys = ['id','pid','name','icon','access_group','access_type','show_type','url'];
        $lists = self::staticConList($con, session(SESSION_COMPANY_ID), $order, $keys);
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
     *
     */
    public function fId() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    /**
     *
     */
    public function fAppId() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    /**
     *
     */
    public function fCompanyId() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    /**
     * 父id，-1表示开发模式下的菜单
     */
    public function fPid() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    /**
     * 权限名称
     */
    public function fName() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    /**
     * 图标
     */
    public function fIcon() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    /**
     * 类型：
      page非菜单页面，
      menu侧边菜单页，
      api api接口、
      home首页菜单
     */
    public function fAccessType() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    /**
     * 权限地址
     */
    public function fUrl() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    /**
     * 权限串，优先于url判断
     */
    public function fAuthstr() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    /**
     * 1嵌套页，2跳链
     */
    public function fShowType() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    /**
     * 排序
     */
    public function fSort() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    /**
     * 状态(0禁用,1启用)
     */
    public function fStatus() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    /**
     * 有使用(0否,1是)
     */
    public function fHasUsed() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    /**
     * 锁定（0：未锁，1：已锁）
     */
    public function fIsLock() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    /**
     * 锁定（0：未删，1：已删）
     */
    public function fIsDelete() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    /**
     * 备注
     */
    public function fRemark() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    /**
     * 创建者，user表
     */
    public function fCreater() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    /**
     * 更新者，user表
     */
    public function fUpdater() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    /**
     * 创建时间
     */
    public function fCreateTime() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    /**
     * 更新时间
     */
    public function fUpdateTime() {
        return $this->getFFieldValue(__FUNCTION__);
    }

}
