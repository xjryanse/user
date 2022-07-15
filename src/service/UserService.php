<?php

namespace xjryanse\user\service;

use xjryanse\system\service\SystemCompanyUserService;
use xjryanse\customer\service\CustomerUserService;
use xjryanse\wechat\service\WechatWePubFansUserService;
use xjryanse\wechat\service\WechatWeAppFansUserService;
use xjryanse\system\interfaces\MainModelInterface;
use xjryanse\order\service\OrderService;
use xjryanse\logic\Arrays;
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
class UserService implements MainModelInterface {

    use \xjryanse\traits\InstTrait;
    use \xjryanse\traits\MainModelTrait;
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
        'userAccount'=>[
            'class'     =>'\\xjryanse\\user\\service\\UserAccountService',
            'keyField'  =>'user_id',
            'master'    =>true
        ],
        'userAddress'=>[
            'class'     =>'\\xjryanse\\user\\service\\UserAddressService',
            'keyField'  =>'user_id',
            'master'    =>true
        ]
    ];
    
    /**
     * 20220613:获取当天生日的用户列表
     */
    public static function getBrithdayUsers($date = ''){
        $dateQuery = $date ? date('m-d',strtotime($date)) : date('m-d');
        $tableName = self::getTable();
        $sql = "select * from ".$tableName." where date_format( birthday, '%m-%d') = '" . $dateQuery . "' and company_id = '".session(SESSION_COMPANY_ID)."'";
        Debug::debug('$sql',$sql);
        $res = Db::query($sql);
        Debug::debug('$res',$res);
        return $res;
    }
    /**
     * 会话设置
     */
    public function sessionSet(){
        if(!$this->uuid){
            return false;
        }
        //用户id
        session(SESSION_USER_ID,$this->uuid);
        $userInfo           = $this->info();
        //是否公司员工；是否管理员；是否有司机角色；后勤角色
        $isCompanyUser      = SystemCompanyUserService::isCompanyUser(session(SESSION_COMPANY_ID), $this->uuid);
        $isAdminTypeMatch   = in_array($userInfo['admin_type'],['super','admin','driver']);
        $userHasRole        = UserAuthUserRoleService::userHasRoleKey($this->uuid, ['driver','logistics']);
        $userInfo['isCompanyManage']    = $isCompanyUser || $isAdminTypeMatch || $userHasRole ;
        /*20220516增加用户账户*/
        $con[] = ['user_id','=',$this->uuid];
        $userInfo['accounts']   = UserAccountService::mainModel()->where($con)->column('current','account_type');
        //20220531绑定的公众号openid
        $userInfo['wePubOpenid']   = WechatWePubFansUserService::mainModel()->where($con)->column('openid');
        //20220531绑定的小程序openid
        $userInfo['weAppOpenid']   = WechatWeAppFansUserService::mainModel()->where($con)->column('openid');
        //20220531,密码不存
        $userInfo['password']       = '';
        //用户信息
        session(SESSION_USER_INFO,$userInfo);
        //用户所属的部门id
        session(SESSION_DEPT_ID,$userInfo['dept_id']);
        //部门id信息
        /*
            $con[] = ['user_id','=',$this->uuid];
            $companyUserInfo = SystemCompanyUserService::mainModel()->where($con)->order('dept_id desc')->find();
            Debug::debug('$companyUserInfo条件',$con);
            Debug::debug('$companyUserInfo',$companyUserInfo);
            session(SESSION_DEPT_ID,$companyUserInfo ? $companyUserInfo['dept_id'] : '');
         */
    }
    
    public function info($cache=5){
        $info               = $this->get();
        $info['roleIds']    = UserAuthUserRoleService::userRoleIds($this->uuid);
        return $info;
    }
    /**
     * 20220511
     * 部门id列表，用于数据权限过滤
     */
    public function deptIdsForDataAuth(){
        $cond[]         = ['user_id','=',$this->uuid];
        $customerIds    = CustomerUserService::mainModel()->where($cond)->column('customer_id');
        $info           = $this->get();
        if($info['dept_id']){
            $customerIds[]  = $info['dept_id'];
        }
        return array_unique($customerIds);
    }

    /**
     * 验证用户是否有绑定了手机号码
     */
    public function checkUserPhone($notice=''){
        $info = self::mainModel()->where('id',$this->uuid)->find();
        //20220601: 增加用户类型判断
        if(!$info['phone'] && !in_array($info['admin_type'],['super','subSuper','admin'])){
            throw new Exception($notice ? : '请先到底部菜单“我的”绑定手机号码'.$this->uuid);
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
    public static function extraPreSave( &$data, $uuid)
    {
        $notice['username'] = '用户名必须';
        $notice['realname'] = '真实姓名必须';
        $notice['sex']      = '性别必须';
        $notice['phone']    = '手机必须';
        //数据校验
        DataCheck::must($data, ['username'], $notice);
        $adminType = Arrays::value( $data, 'admin_type');
        // 内部人员：后台；司机；业务；行政
        if(in_array($adminType,['admin','driver','busier','manager'])){
            DataCheck::must($data, ['realname','sex','phone'], $notice);
        }
        
        $phone = Arrays::value( $data, 'phone');
        //有填手机才有判断
        if($phone && mb_strlen($phone) < 8){
            throw new Exception('手机号码格式错误.'.$phone);
        }
        $conUser[] = ['username','=',$data['username']];
        $countUser = self::count( $conUser );
        if( $countUser && $data['username'] ){
            throw new Exception('用户名'.$data['username'].'已存在,请更换');
        }
        
        $con[] = ['phone','=',$phone];
        $count = self::count( $con );
        if( $count && $phone ){
            throw new Exception('该手机号码'.$phone.'已注册');
        }
        
        // 默认密码设为手机号码后6位
        if(!isset($data['password'])){
            $data['password'] = password_hash( substr($phone, -6) , PASSWORD_DEFAULT );
        }
        if(!Arrays::value($data,"busier_id")){
            $data['busier_id'] = session(SESSION_USER_ID);
        }
        //20220423保存角色
        if(isset($data['roleIds'])){
            self::checkTransaction();
            //保存角色信息
            $roleIds = Arrays::value( $data, 'roleIds',[]);
            //先删再加
            UserAuthUserRoleService::userRoleIdSave($uuid, $roleIds);
        }
        //20220613：根据身份证号码，自动填入性别；生日
        $idNo = Arrays::value($data, 'id_no');
        if(IdNo::isIdNo($idNo)){
            $data['sex']        = IdNo::getSex($idNo);
            $data['birthday']   = IdNo::getBirthday($idNo);
        }
        
        return $data;
    }

    public static function extraPreUpdate( &$data, $uuid)
    {
        $phone = Arrays::value( $data, 'phone');
        if($phone){
            $con[] = ['phone','=',$phone];
            $con[] = ['id','<>',$uuid];
            $existUserInfo = self::find( $con );
            if( $existUserInfo ){
                throw new Exception('该手机号码'.$phone.'已被“'.$existUserInfo['username'].'”注册，请更换手机号码重试');
            }            
        }
        if(isset($data['roleIds'])){
            self::checkTransaction();            
            //保存角色信息
            $roleIds = Arrays::value( $data, 'roleIds',[]);
            //先删再加
            UserAuthUserRoleService::userRoleIdSave($uuid, $roleIds);
        }
        //20220613：根据身份证号码，自动填入性别；生日
        $idNo = Arrays::value($data, 'id_no');
        if(IdNo::isIdNo($idNo)){
            $data['sex']        = IdNo::getSex($idNo);
            $data['birthday']   = IdNo::getBirthday($idNo);
        }
        
        return $data;
    }    
    
    /**
     * 分页（软删）
     * @param type $con
     * @param type $order
     * @param type $perPage
     * @param type $having
     * @return type
     */
    public static function paginate( $con = [],$order='',$perPage=10,$having = '',$field ="*")
    {
        $con[] = ['is_delete','=',0];
        return self::commPaginate($con, $order, $perPage, $having,$field);
    }
    /**
     * 软删
     * @return type
     */
    public function delete()
    {
        $data['is_delete'] = 1;
        return $this->commUpdate($data);
    }
    /**
     * 额外详情信息
     */
    /*
    protected static function extraDetail( &$item ,$uuid )
    {
        if(!$item){ return false;}
        self::commExtraDetail($item, $uuid);
        //用户账户余额
        DbExtraData::oneToMoreByKey($item, UserAccountService::mainModel()->getTable(), 'user_id', $uuid, 'account_type', 'current');
        //用户账户账号
        DbExtraData::oneToMoreByKey($item, UserAccountService::mainModel()->getTable(), 'user_id', $uuid, 'account_type', 'id');
        //①添加身份证信息
        $subInfos = UserIdnoService::getInstance( $uuid )->getSubFieldData();
        foreach($subInfos as $k=>$v){
            $item->$k = $v;
        }
        //作为业务员的用户数据统计
        $con[] = ['busier_id','=',$uuid];
        $item->SCbusier_id      = self::count( $con );
        //作为推荐人的用户数据统计
        $con1[] = ['rec_user_id','=',$uuid];
        $item->SCrec_user_id    = self::count( $con1 );
        
        return $item;
    }
     *
     */
    /**
     * 20220715:简单的用户信息保存：姓名收件
     */
    public static function simpleSave($realname,$phone){
        $data['username']   = $phone;
        $data['phone']      = $phone;
        $data['realname']   = $realname;
        $data['nickname']   = $realname;
        $res = self::save($data);
        return $res;
    }
    
    public static function extraDetails( $ids ){
        //数组返回多个，非数组返回一个
        $isMulti = is_array($ids);
        if(!is_array($ids)){
            $ids = [$ids];
        }
        $con[] = ['id','in',$ids];
        $lists = self::selectX($con, '', '',['password']);
        //推荐人查询数组
        $recArr = self::groupBatchCount('rec_user_id', $ids);
        //业务员查询数组
        $busiArr = self::groupBatchCount('busier_id', $ids);
        //乘客查询数组
        $passArr = UserPassengerService::groupBatchCount('user_id', $ids);
        //绑定公司数组
        $custArr = CustomerUserService::groupBatchCount('user_id', $ids);
        //绑定公众号账号数
        $wePubArr = WechatWePubFansUserService::groupBatchCount('user_id', $ids);
        //绑定小程序账号数
        $weAppArr = WechatWeAppFansUserService::groupBatchCount('user_id', $ids);
        //包车订单数
        $baoCon[] = ['order_type','=','bao'];
        $baoArr = OrderService::groupBatchCount('user_id', $ids,$baoCon);
        $pinCon[] = ['order_type','=','pin'];
        $pinArr = OrderService::groupBatchCount('user_id', $ids,$pinCon);
        //角色数
        //$roleArr = UserAuthUserRoleService::groupBatchCount('user_id', $ids);
        $roleArr = UserAuthUserRoleService::groupBatchSelect('user_id', $ids,'user_id,role_id');
        
        foreach($lists as &$user){
            //推荐人数
            $user['recCounts']  = Arrays::value($recArr, $user['id'],0);
            //业务人数
            $user['busiCounts']  = Arrays::value($busiArr, $user['id'],0);
            //乘客人数
            $user['passCounts']  = Arrays::value($passArr, $user['id'],0);
            //绑定客户
            $user['custCounts']  = Arrays::value($custArr, $user['id'],0);
            //绑定公众账号数
            $user['wePubCounts']  = Arrays::value($wePubArr, $user['id'],0);
            //绑定小程序账号数
            $user['weAppCounts']  = Arrays::value($weAppArr, $user['id'],0);
            //绑定包车订单数
            $user['orderBaoCounts'] = Arrays::value($baoArr, $user['id'],0);
            //绑定拼车订单数
            $user['orderPinCounts'] = Arrays::value($pinArr, $user['id'],0);
            //角色数
            $user['roleIds']        = array_column(Arrays::value($roleArr, $user['id'],[]),'role_id') ? : [];
            $user['roleArrCounts']  = count(Arrays::value($roleArr, $user['id'],[]));
            //keys
            $keys = ['recCounts','busiCounts','passCounts','custCounts','wePubCounts','weAppCounts','orderBaoCounts','orderPinCounts','roleArrCounts'];
            $user['hasData'] = array_sum(Arrays::getByKeys($user, $keys)) ? 1 : 0;
        }
        
        return $isMulti ? $lists : $lists[0];
        // return $isMulti ? Arrays2d::fieldSetKey($lists, 'id') : $lists[0];
    }

    /**
     * 额外保存身份信息
     * @param array $data
     * @return type
     */
    public static function save( $data)
    {
        $res = self::commSave($data);
        $res['user_id'] = $res['id'];
        UserIdnoService::save($data);
        return $res;
    }
    /**
     * 额外保存身份信息
     * @param array $data
     * @return type
     */
    public function update( array $data)
    {
        $res = $this->commUpdate($data);
        //有则更新，无则新增
        if(UserIdnoService::getInstance( $this->uuid )->get()){
            UserIdnoService::getInstance( $this->uuid )->update( $data );
        } else {
            $data['id']         = $this->uuid;
            $data['user_id']    = $this->uuid;

            UserIdnoService::save($data);
        }
        return $res;
    }
    /**
     * 手机号码取用户id
     */
    public static function phoneUserId( $phone )
    {
        $con = [];
        $con[] = ['phone', '=', $phone];
        if(self::mainModel()->hasField('company_id')){
            $con[] = ['company_id','=',session(SESSION_COMPANY_ID)];
        }
        return self::column('id',$con);
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
     */
    public static function phoneBindUserGetId($phone,$oldUserId){
        if(!session(SESSION_COMPANY_ID)){
            throw new Exception('入口参数异常，请联系开发.phoneBindUserGetId');
        }
        $con[] = ['phone','=',$phone];
        $info = self::find($con);
        //【1】如果手机号码已有用户，返回已有用户id；
        if($info){
            if($oldUserId){
                //删除旧的没用用户
                $oldInfo = self::getInstance($oldUserId)->get();
                //删除没用用户
                if(!$oldInfo['phone']){
                    self::getInstance($oldUserId)->delete();
                }
            }
            return $info['id'];
        }
        $oldUserInfo = self::getInstance($oldUserId)->get();
        if($oldUserInfo['phone']){
            //【2】如果手机号码没有用户，且oldUserId有手机号码；重新建一个用户；
            $data['username']   = $phone;
            $data['phone']      = $phone;
            $res = self::save($data);
            return $res['id'];
        } else {
            //【3】如果手机号码没有用户，且oldUserId无手机号码，更新oldUserId手机号码
            // 20220122用户名15个字符内，超出说明是系统随机的，替换掉
            $updData['phone'] = $phone; 
            if(mb_strlen($oldUserInfo['username']) > 15 ){
                $updData['username'] = $phone; 
            }
            self::getInstance($oldUserId)->update($updData);
            return $oldUserId;
        }
    }
    /**
     * 手机号码获取用户id：没有记录，则写一个用户
     */
    public static function phoneGetId($phone,$data = []){
        $con[] = ['company_id','=',session(SESSION_COMPANY_ID)];
        $con[] = ['phone','=',$phone];
        $id = self::mainModel()->where($con)->value('id');
        if(!$id){
            $data['username'] = $phone;
            $data['phone'] = $phone;
            $res = self::save($data);
            $id = $res['id'];
        }
        return $id;
    }
    
    /**
     * openid获取用户id：没有记录，则写一个用户
     */
    public static function openidGetId($openid,$data = []){
        $con[] = ['company_id','=',session(SESSION_COMPANY_ID)];
        $con[] = ['username','=',$openid];
        $id = self::mainModel()->where($con)->value('id');
        if(!$id){
            $data['username'] = $openid;
            //2022-02-24
            if(isset($data['phone']) && !$data['phone']){
                $data['phone'] = null;
            }

            $res = self::save($data);
            $id = $res['id'];
        }
        return $id;
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
     * 推荐用户id
     */
    public function fRecUserId() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    /**
     *
     */
    public function fCompanyId() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    /**
     * 用户名
     */
    public function fUsername() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    /**
     * 会员等级：normal普通会员；senior：高级会员
     */
    public function fDegree() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    /**
     * 用户头像【存id】
     */
    public function fHeadimg() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    /**
     * 密码
     */
    public function fPassword() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    /**
     * 手机
     */
    public function fPhone() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    /**
     * 平台昵称
     */
    public function fNickname() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    /**
     * 真实姓名
     */
    public function fRealname() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    /**
     * 性别(1男,2女)
     */
    public function fSex() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    /**
     * 生日
     */
    public function fBirthday() {
        return $this->getFFieldValue(__FUNCTION__);
    }
    
    public function fBusierId() {
        return $this->getFFieldValue(__FUNCTION__);
    }
    /**
     * 身份证号
     */
    public function fIdNo() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    /**
     * 注册来源
     */
    public function fSource() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    /**
     * 后台类型:
      '':无后台权限
      'normal':普通后台用户
      'super':系统超级管理员
      'subsuper'公司级超级管理

     */
    public function fAdminType() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    /**
     * 末次登录ip
     */
    public function fLastLoginip() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    /**
     * 末次登录时间
     */
    public function fLastLogintime() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    /**
     *
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
     * 创建者
     */
    public function fCreater() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    /**
     * 更新者
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
