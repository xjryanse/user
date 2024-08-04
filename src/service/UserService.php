<?php

namespace xjryanse\user\service;

use xjryanse\customer\service\CustomerUserService;
use xjryanse\wechat\service\WechatWeAppFansService;
use xjryanse\wechat\service\WechatWePubFansService;
use xjryanse\wechat\service\WechatWePubFansUserService;
use xjryanse\wechat\service\WechatWeAppFansUserService;
use xjryanse\system\interfaces\MainModelInterface;
// use xjryanse\view\service\ViewStaffService;
use xjryanse\user\service\UserAuthUserRoleService;
use xjryanse\system\service\SystemFileService;
use xjryanse\universal\service\UniversalPageService;
use xjryanse\logic\Arrays;
use xjryanse\logic\Arrays2d;
use xjryanse\logic\Strings;
use xjryanse\logic\DataCheck;
use xjryanse\logic\Debug;
use xjryanse\logic\IdNo;
use think\Db;
use Exception;

/**
 * 用户总表
 * 几个注册来源：
 * 【1】后台添加
 * 【2】公众号注册，默认写
 * 【3】小程序注册，默认写
 */
class UserService extends Base implements MainModelInterface {

    use \xjryanse\traits\InstTrait;
    use \xjryanse\traits\MainModelTrait;
    use \xjryanse\traits\MainModelRamTrait;
    use \xjryanse\traits\MainModelCacheTrait;
    use \xjryanse\traits\MainModelCheckTrait;
    use \xjryanse\traits\MainModelGroupTrait;
    use \xjryanse\traits\MainModelQueryTrait;

    use \xjryanse\traits\SubServiceTrait;
    use \xjryanse\traits\ObjectAttrTrait;

    protected static $mainModel;
    protected static $mainModelClass = '\\xjryanse\\user\\model\\User';
    // 是否缓存get数据
    protected static $getCache = true;
    ///从ObjectAttrTrait中来
    // 定义对象的属性
    protected $objAttrs = [];
    // 定义对象是否查询过的属性
    protected $hasObjAttrQuery = [];
    // 定义对象属性的配置数组
    protected static $objAttrConf = [
        'userAccount' => [
            'class' => '\\xjryanse\\user\\service\\UserAccountService',
            'keyField' => 'user_id',
            'master' => true
        ],
        'userAddress' => [
            'class' => '\\xjryanse\\user\\service\\UserAddressService',
            'keyField' => 'user_id',
            'master' => true
        ],
        //20220814 financeManageAccount
        'financeManageAccount' => [
            'class' => '\\xjryanse\\finance\\service\\FinanceManageAccountService',
            'keyField' => 'belong_table_id',
            'master' => true
        ],
    ];
    //直接执行后续触发动作
    protected static $directAfter = true;
    
    // 20230710：开启方法调用统计
    protected static $callStatics = true;

    use \xjryanse\user\service\index\DoTraits;
    use \xjryanse\user\service\index\FieldTraits;
    use \xjryanse\user\service\index\ListTraits;
    use \xjryanse\user\service\index\TriggerTraits;
    
    //2023-01-08：
    public static function extraAfterUpdate(&$data, $uuid) {
        // 20230710：统计方法执行次数改造
        // 有则更新，无则新增
        if (UserIdnoService::getInstance($uuid)->get()) {
            UserIdnoService::getInstance($uuid)->update($data);
        } else {
            $data['id']         = $uuid;
            $data['user_id']    = $uuid;

            UserIdnoService::save($data);
        }
        
        UserService::clearCommExtraDetailsCache($uuid);
        // 20230415：修复bug
        // ViewStaffService::staticCacheClear();
    }

    public function extraAfterDelete($data) {
        //2023-01-08：
        UserService::clearCommExtraDetailsCache($this->uuid);
        // 20230415:修复bug
        // ViewStaffService::staticCacheClear();
    }

    /**
     * 20220613:获取当天生日的用户列表
     * @param type $date
     * @return type
     */
    public static function getBrithdayUsers($date = '') {
        $dateQuery = $date ? date('m-d', strtotime($date)) : date('m-d');
        $tableName = self::getTable();
        $sql = "select * from " . $tableName . " where date_format( birthday, '%m-%d') = '" . $dateQuery . "' and company_id = '" . session(SESSION_COMPANY_ID) . "'";
        Debug::debug('$sql', $sql);
        $res = Db::query($sql);
        Debug::debug('$res', $res);
        return $res;
    }

    /**
     * 会话设置
     * @useFul 1
     */
    public function sessionSet() {
        if (!$this->uuid) {
            return false;
        }
        //用户id
        session(SESSION_USER_ID, $this->uuid);
        // $userInfo = $this->info();
        /// 20231026：尝试仅使用get??
        $userInfo = $this->get();
        // 20231026
        $userInfo['roleIds'] = UserAuthUserRoleService::userRoleIds($this->uuid);
        //是否公司员工；是否管理员；是否有司机角色；后勤角色
        // $isCompanyUser      = SystemCompanyUserService::isCompanyUser(session(SESSION_COMPANY_ID), $this->uuid);
        // $isStaff = ViewStaffService::isStaff($this->uuid);
        // 20231028替换
        $isStaff = UserAuthUserRoleService::isStaff($this->uuid);
        // 注意：可能导致权限泄露！！
        $isAdminTypeMatch = in_array($userInfo['admin_type'], ['super']);
        // $userHasRole        = UserAuthUserRoleService::userHasRoleKey($this->uuid, ['driver','logistics']);
        // $userInfo['isCompanyManage']    = $isStaff || $isAdminTypeMatch || $userHasRole ;
        $userInfo['isCompanyManage'] = $isStaff || $isAdminTypeMatch;
        /* 20220516增加用户账户 */
        $con[] = ['user_id', '=', $this->uuid];
        $userInfo['accounts'] = UserAccountService::mainModel()->where($con)->column('current', 'account_type');
        //20220531绑定的公众号openid
        $userInfo['wePubOpenid'] = WechatWePubFansUserService::mainModel()->where($con)->column('openid');
        //20220531绑定的小程序openid
        $userInfo['weAppOpenid'] = WechatWeAppFansUserService::mainModel()->where($con)->column('openid');
        //20220531,密码不存
        $userInfo['password'] = '';
        // throw new Exception('测试');
        // dump($userInfo);exit;
        //用户信息
        session(SESSION_USER_INFO, $userInfo);
        //用户所属的部门id
        session(SESSION_DEPT_ID, $userInfo['dept_id']);
        //20220820：增加客户id
        session(SESSION_CUSTOMER_ID, $userInfo['customer_id']);
        // 20240101
        // dump('sessionSet');dump($userInfo);
        return $userInfo;
    }


    /**
     * 20220511
     * 部门id列表，用于数据权限过滤
     */
    public function deptIdsForDataAuth() {
        $cond[] = ['user_id', '=', $this->uuid];
        $customerIds = CustomerUserService::mainModel()->where($cond)->column('customer_id');
        $info = $this->get();
        if ($info['dept_id']) {
            $customerIds[] = $info['dept_id'];
        }
        return array_unique($customerIds);
    }

    /**
     * 验证用户是否有绑定了手机号码
     * @useFul 1
     */
    public function checkUserPhone($notice = '') {
        $info = self::mainModel()->where('id', $this->uuid)->find();
        //20220601: 增加用户类型判断
        if ($info && !$info['phone'] && !in_array($info['admin_type'], ['super', 'subSuper', 'admin'])) {
            throw new Exception($notice ?: '请先到底部菜单“我的”绑定手机号码' . $this->uuid);
        }
    }

    /**
     * 额外保存信息
     * 有用户名，无手机号码
     * 有用户名，有手机号码
     * 无用户名，有手机号码
     * @param type $data
     * @param type $uuid
     */
    public static function extraPreSave(&$data, $uuid) {
        $notice['username'] = '用户名必须';
        $notice['realname'] = '真实姓名必须';
        $notice['sex'] = '性别必须';
        $notice['phone'] = '手机必须';
        //数据校验
        DataCheck::must($data, ['username'], $notice);
        $adminType = Arrays::value($data, 'admin_type');
        // 内部人员：后台；司机；业务；行政
        if (in_array($adminType, ['admin', 'driver', 'busier', 'manager'])) {
            DataCheck::must($data, ['realname', 'sex', 'phone'], $notice);
        }

        $phone = Arrays::value($data, 'phone');
        //有填手机才有判断
        if ($phone && mb_strlen($phone) < 8) {
            throw new Exception('手机号码格式错误.' . $phone);
        }
        $conUser[] = ['username', '=', $data['username']];
        $countUser = self::count($conUser);
        if ($countUser && $data['username']) {
            throw new Exception('用户' . $data['username'] . '已存在');
        }

        $con[] = ['phone', '=', $phone];
        $count = self::count($con);
        if ($count && $phone) {
            throw new Exception('该手机号码' . $phone . '已注册');
        }

        // 默认密码设为手机号码后6位
        if (!isset($data['password'])) {
            $data['password'] = password_hash(substr($phone, -6), PASSWORD_DEFAULT);
        }
        if (!Arrays::value($data, "busier_id")) {
            $data['busier_id'] = session(SESSION_USER_ID);
        }
        //20220423保存角色
        if (isset($data['roleIds'])) {
            self::checkTransaction();
            //保存角色信息
            $roleIds = Arrays::value($data, 'roleIds', []);
            //先删再加
            UserAuthUserRoleService::userRoleIdSave($uuid, $roleIds);
        }
        //20220613：根据身份证号码，自动填入性别；生日
        $idNo = Arrays::value($data, 'id_no');
        if (IdNo::isIdNo($idNo)) {
            $data['sex'] = IdNo::getSex($idNo);
            $data['birthday'] = IdNo::getBirthday($idNo);
        }

        return $data;
    }

    public static function extraPreUpdate(&$data, $uuid) {
        $phone = Arrays::value($data, 'phone');
        if ($phone) {
            $con[] = ['phone', '=', $phone];
            $con[] = ['id', '<>', $uuid];
            $existUserInfo = self::find($con);
            if ($existUserInfo) {
                throw new Exception('该手机号码' . $phone . '已被“' . $existUserInfo['username'] . '”注册，请更换手机号码重试');
            }
        }
        if (isset($data['roleIds'])) {
            self::checkTransaction();
            //保存角色信息
            $roleIds = Arrays::value($data, 'roleIds', []);
            //先删再加
            UserAuthUserRoleService::userRoleIdSave($uuid, $roleIds);
            // 20221109:增加清除用户页面缓存的动作
            UniversalPageService::clearUserPageCache($uuid);
        }
        //20220613：根据身份证号码，自动填入性别；生日
        $idNo = Arrays::value($data, 'id_no');
        if (IdNo::isIdNo($idNo)) {
            $data['sex'] = IdNo::getSex($idNo);
            $data['birthday'] = IdNo::getBirthday($idNo);
        }
        return $data;
    }

    /*
     * 获取签名图片路径
     * @useFul 1
     */
    public function getSignPath() {
        $sign = self::mainModel()->where('id', $this->uuid)->value('sign');
        $path = SystemFileService::getInstance($sign)->fFilePath();
        return $path;
    }

    /**
     * 2022-12-01 获取签名，带更新
     */
    public function getSignWithUpdate($signInfo) {
        $userId = $this->uuid;
        if (!$signInfo) {
            $signInfo = UserService::mainModel()->where('id', $userId)->value('sign');
        } else {
            $userUpdate['sign'] = $signInfo;
            $this->update($userUpdate);
        }
        return $signInfo;
    }

    /**
     * 20220715:简单的用户信息保存：姓名收件
     * @useFul 1
     */
    public static function simpleSave($realname, $phone, $data = []) {
        $con[] = ['phone', '=', $phone];
        $id = self::where($con)->value('id');
        $data['realname'] = $realname;
        if ($id) {
            if (mb_strlen($phone) == 11) {
                //更新用户的姓名
                return self::getInstance($id)->update($data);
            } else {
                // 防杂牌号码重复
                throw new Exception($phone . '已存在');
            }
        }
        $data['phone'] = $phone;
        $data['username'] = $phone;
        $data['nickname'] = $realname;
        $res = self::save($data);
        return $res;
    }

    public static function extraDetails($ids) {
        return self::commExtraDetails($ids, function($lists) use ($ids) {
                    //推荐人查询数组
                    $recArr = self::groupBatchCount('rec_user_id', $ids);
                    //业务员查询数组
                    $busiArr = self::groupBatchCount('busier_id', $ids);
                    //角色数
                    //$roleArr = UserAuthUserRoleService::groupBatchCount('user_id', $ids);
                    $roleArr = UserAuthUserRoleService::groupBatchSelect('user_id', $ids, 'user_id,role_id');
                    // 账户数
                    $userAccountArr = UserAccountService::groupBatchCount('user_id', $ids);

                    foreach ($lists as &$user) {
                        // 2022-11-24:账户数
                        $user['accountCount']   = Arrays::value($userAccountArr, $user['id'], 0);
                        // 20220924:该用户是否有手机号码
                        $user['hasPhone']       = Strings::isPhone($user['phone']) ? 1 : 0;
                        //推荐人数
                        $user['recCounts']      = Arrays::value($recArr, $user['id'], 0);
                        //业务人数
                        $user['busiCounts']     = Arrays::value($busiArr, $user['id'], 0);
                        //角色数
                        $user['roleIds']        = array_column(Arrays::value($roleArr, $user['id'], []), 'role_id') ?: [];
                        $user['roleCount']      = count(Arrays::value($roleArr, $user['id'], []));
                        //keys
                        $keys                   = ['recCounts', 'busiCounts', 'passCounts', 'custCounts', 'wePubCounts', 'weAppCounts', 'orderBaoCounts', 'orderPinCounts', 'roleCount', 'baoBusDriverCounts'];
                        $user['hasData']        = array_sum(Arrays::getByKeys($user, $keys)) ? 1 : 0;
                        // 20220914:是否有签名：用于前端显示
                        $user['hasSign']        = $user['sign'] ? 1 : 0;
                        // 20240416 是否员工
                        $user['isStaff']        = Arrays::value($user, 'uniSystemCompanyUserCount') ? 1: 0;
                    }
                    return $lists;
                },true);
    }

    /**
     * 20230515：删除前，校验数据是否可删除
     * @param type $info    由$this->info信息来
     */
    protected static function delPreCheck($info) {
        $hasValRule['recCounts'] = '该用户名下有推荐人不可删';
        $hasValRule['busiCounts'] = '该业务员名下有用户不可删';
        $hasValRule['custCounts'] = '该用户有绑定客户，需要先解绑';
        DataCheck::hasValue($info, $hasValRule);

    }
    
    /**
     * 20230710：因统计函数调用次数改造
     * @param type $data
     * @param type $uuid
     * @return type
     */
    public static function extraAfterSave(&$data, $uuid) {
        $res = $data;
        $res['user_id'] = $res['id'];
        UserIdnoService::saveGetId($data);
        return $res;
    }

    /**
     * 手机号码取用户id；没用户不写入
     * @useFul 1
     */
    public static function phoneUserId($phone) {
        $con = [];
        $con[] = ['phone', '=', $phone];
        // 2023-02-27: 手机号码提取用户id
        return self::where($con)->cache(1)->value('id');
    }

    /*
     * 手机号码取用户信息
     */

    public static function getUserInfoByPhone($phone) {
        if (!$phone) {
            return false;
        }
        $con = [];
        $con[] = ['phone', '=', $phone];
        $info = self::find($con);
        if (!$info) {
            //尝试匹配用户名中的手机号码
            $con1 = [];
            $con1[] = ['username', '=', $phone];
            $info = self::find($con1);
        }
        return $info;
    }

    /**
     * 设定用户名
     */
    public function setUserName($userName) {
        //查询用户名是否存在
        $con[] = ['username', '=', $userName];
        $con[] = ['id', '<>', $this->uuid];
        $count = self::count($con);
        //更新
        if ($count) {
            throw new Exception('用户名已存在！');
        }
        return $this->update(['username' => $userName]);
    }

    /*
     * 设定密码
     */

    public function setPassword($password) {
        return $this->update(['password' => password_hash($password, PASSWORD_DEFAULT)]);
    }

    /**
     * 20211216
     * 手机号码绑定用户
     * 【1】如果手机号码已有用户，返回已有用户id；
     * 【2】如果手机号码没有用户，且oldUserId有手机号码；重新建一个用户；
     * 【3】如果手机号码没有用户，且oldUserId无手机号码，更新oldUserId手机号码
     * @useFul 1
     */
    public static function phoneBindUserGetId($phone, $oldUserId) {
        if (!session(SESSION_COMPANY_ID)) {
            throw new Exception('入口参数异常，请联系开发.phoneBindUserGetId');
        }
        $con[] = ['phone', '=', $phone];
        $info = self::find($con);
        //【1】如果手机号码已有用户，返回已有用户id；
        if ($info) {
            if ($oldUserId) {
                //删除旧的没用用户
                $oldInfo = self::getInstance($oldUserId)->get();
                //删除没用用户
                if (!$oldInfo['phone'] && $oldInfo) {
                    self::getInstance($oldUserId)->delete();
                }
            }
            return $info['id'];
        }
        $oldUserInfo = self::getInstance($oldUserId)->get();
        if ($oldUserInfo['phone']) {
            //【2】如果手机号码没有用户，且oldUserId有手机号码；重新建一个用户；
            $data['username'] = $phone;
            $data['phone'] = $phone;
            $res = self::save($data);
            return $res['id'];
        } else {
            //【3】如果手机号码没有用户，且oldUserId无手机号码，更新oldUserId手机号码
            // 20220122用户名15个字符内，超出说明是系统随机的，替换掉
            $updData['phone'] = $phone;
            if (mb_strlen($oldUserInfo['username']) > 15) {
                $updData['username'] = $phone;
            }
            self::getInstance($oldUserId)->update($updData);
            return $oldUserId;
        }
    }

    /**
     * 手机号码获取用户id：没有记录，则写一个用户
     */
    public static function phoneGetId($phone, $data = []) {
        $con[] = ['company_id', '=', session(SESSION_COMPANY_ID)];
        $con[] = ['phone', '=', $phone];
        $info = self::mainModel()->where($con)->find();
        $id = $info ? $info['id'] : '';
        if (!$info) {
            $data['username'] = $phone;
            $data['phone'] = $phone;
            $res = self::save($data);
            $id = $res['id'];
        } else if ((!$info['realname'] || mb_strlen($info['realname']) < 2 ) && Arrays::value($data, 'realname')) {
            self::mainModel()->where('id', $info['id'])->update(['realname' => $data['realname']]);
        }
        return $id;
    }

    /**
     * openid获取用户id：没有记录，则写一个用户
     */
    public static function openidGetId($openid, $data = []) {
        $con[] = ['company_id', '=', session(SESSION_COMPANY_ID)];
        $con[] = ['username', '=', $openid];
        $id = self::mainModel()->where($con)->value('id');
        if (!$id) {
            $data['username'] = $openid;
            //2022-02-24
            if (isset($data['phone']) && !$data['phone']) {
                $data['phone'] = null;
            }

            $res = self::save($data);
            $id = $res['id'];
        }
        return $id;
    }

    /**
     * 20220910:微信自动绑定逻辑
     */
    public static function wxAutoBind($phone) {
        $unionid = WechatWeAppFansService::phoneToUnionid($phone);
        if (!$unionid) {
            throw new Exception('系统没有找到小程序用户' . $phone);
        }
        $pubOpenId = WechatWePubFansService::unionidToOpenid($unionid);
        if (!$pubOpenId) {
            throw new Exception('系统没有找到小程序对应的公众号用户' . $phone);
        }
        if (WechatWePubFansUserService::openidHasBind($pubOpenId)) {
            throw new Exception('公众号已有绑用户' . $pubOpenId);
        }
        //提取用户id
        $userId = self::phoneGetId($phone);
        $info = WechatWePubFansUserService::changeBind($pubOpenId, $userId);
        return $info;
    }
    /**
     * 判断是否当前用户
     * @param type $userId
     */
    public static function isMe($userId){
        return session(SESSION_USER_ID) && $userId == session(SESSION_USER_ID) ? 1 : 0;
    }

    /**
     * 20231118:端口初始化时带公司初始化
     */
    public static function compUserInit($companyId, $phone){
        $data['company_id'] = $companyId;
        $data['username']   = $phone;
        $data['phone']      = $phone;
        // 默认密码为手机号码后6位
        $data['password']   = password_hash(substr($phone, -6), PASSWORD_DEFAULT);
        $data['admin_type'] = 'subSuper';

        $res = self::saveRam($data);
        return $res;
    }
    /**
     * 员工姓名转id，仅支持当前员工
     */
    public static function staffRealnameToId($realname){
        // 全部员工id
        $staffUserIds = UserAuthUserRoleService::staffUserIds();
        return self::realnameToId($realname, $staffUserIds);
    }
    /**
     * 姓名转id
     * @param type $realname 姓名
     * @param type $ids      有效的id数组
     * @return type
     * @throws Exception
     */
    public static function realnameToId($realname, $ids){
        $con[]  = ['id','in',$ids];
        $con[]  = ['status','=',1];
        $lists  = self::where($con)->field('id,realname')->cache(1)->select();
        $arr    = $lists ? $lists->toArray() : [];
        
        $conName[] = ['realname','=',$realname];
        $res = Arrays2d::listFilter($arr, $conName);
        if(count($res) > 1){
            throw new Exception($realname.'有多个,无法匹配');
        }
        return $res ? $res[0]['id']: '';
    }

}
