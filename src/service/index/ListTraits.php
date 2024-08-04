<?php
namespace xjryanse\user\service\index;

use xjryanse\logic\ModelQueryCon;
use xjryanse\system\service\SystemCompanyUserService;
use xjryanse\bus\service\BusDriverAbilityService;
use xjryanse\wechat\service\WechatWePubFansUserService;
use xjryanse\staff\service\StaffLogService;
use xjryanse\sql\service\SqlService;
use xjryanse\logic\Arrays2d;
use xjryanse\logic\Arrays;
use xjryanse\logic\Debug;
use think\Db;
/**
 * 触发复用
 */
trait ListTraits{

    /**
     * 20231229：员工信息带证件
     */
    public static function listStaffDriverWithCert($param){
        $param              = Arrays::unsetEmpty($param);
        $fields             = [];
        $fields['equal']    = ['dept_id'];
        $fields['like']     = ['realname','phone','id_no','address','sex'];

        $con        = ModelQueryCon::queryCon($param, $fields);        

        $sqlCert    = self::mainModel()->driverCertSql();
        // $sqlCert = SqlService::keyBaseSql('driverCertSql');
        // $driverSql  = SystemCompanyUserService::mainModel()->driverSql();
        // 员工的列表
        $driverSql = SqlService::keyBaseSql('staffList');
        // 
        // $licencePlateSql = BusDriverAbilityService::mainModel()->driverLicencePlateSql();
        $licencePlateSql = SqlService::keyBaseSql('driverLicencePlate');
        // Debug::dump($licencePlateSql);
        
        $staffSql = StaffLogService::mainModel()->field('user_id as uid,join_date,contract_final_time')->group('user_id')->buildSql();
        // $staffSql = SqlService::keyBaseSql('staffLogList');
        // dump($staffSql);exit;
        $sqlTable   = '(select * from '.$driverSql.' as aae'
                . ' left join '.$sqlCert.' as bbe on aae.user_id = bbe.certUserId'
                . ' left join '.$staffSql.' as stf on stf.uid = aae.user_id'
                . ' left join '.$licencePlateSql.' as cce on aae.user_id = cce.driver_id) as mainTable';
        // 20240220
        $con[] = ['role_key','=','driver'];
        $arr        = Db::table($sqlTable)->where($con)->order('sort')->select();
        // 部门处理
        $userIds    = Arrays2d::uniqueColumn($arr, 'user_id');
        // 查询微信是否绑定？
        $conWx[]    = ['user_id','in',$userIds];
        $userBindCountObj = WechatWePubFansUserService::where($conWx)->group('user_id')->column('count(1) as num','user_id');

        foreach($arr as &$v){
            // 特殊处理驾驶证日期长期的问题；
            $driverLimitTime = strtotime($v['driverLimitTime']);
            $maxTime         = strtotime('2099-12-31');
            if($driverLimitTime > $maxTime){
                // 20240103:长期
                $v['driverLimitTime'] = '长期';
            }
            
            $v['wxWePubBinds']      = Arrays::value($userBindCountObj, $v['user_id']);
            $v['hasWxWePubBind']    = $v['wxWePubBinds'] ? 1:0;
        }
        
        return $arr;
    }
    
    /**
     * 20240104:专门用于管理证件的到期时间？？？
     */
    public static function listCert(){
        // 线路牌sql:TODO
        $sql="(SELECT
            a.cert_key,a.cert_limit_time,
            b.id AS user_id,
            c.deptName
        FROM
            w_cert AS a
            INNER JOIN w_user AS b ON a.belong_table_id = b.id
        inner join (SELECT
            be.user_id ,GROUP_CONCAT(ce.dept_name) as deptName
        FROM
            w_system_company_job AS ae
            INNER JOIN w_system_company_user AS be ON ae.id = be.job_id 
            left join w_system_company_dept as ce on ae.dept_id = ce.id
        WHERE
            ae.role_key = 'driver'
            group by be.user_id) as c on b.id = c.user_id) as MainTable";


        // 提取员工需要哪些资质
        $sqlC = '(SELECT
                a.id,
                a.user_id,
                b.cert_key 
            FROM
                w_system_company_user AS a
                INNER JOIN w_system_company_job_cert_key AS b ON a.job_id = b.job_id 
            GROUP BY
                user_id,
                cert_key)';

        // 提取员工的部门
        $sqlD = "(SELECT
            bD.user_id ,GROUP_CONCAT(cD.dept_name) as deptName
        FROM
            w_system_company_job AS aD
            INNER JOIN w_system_company_user AS bD ON aD.id = bD.job_id 
            left join w_system_company_dept as cD on aD.dept_id = cD.id
        WHERE
            aD.role_key = 'driver'
            group by bD.user_id)";

        // 员工，证件+部门
        $sqlStaff = '( select aStaff.*,bStaff.deptName from '.$sqlC.' as aStaff inner join '.$sqlD.' as bStaff on aStaff.user_id = bStaff.user_id) ';
        
        // dump($sqlStaff);
        // 员工需要管理的资质加上证件进行左联；
        $sqlFinal="(SELECT finalA.deptName,finalA.user_id,finalA.cert_key,finalB.time_manage,finalB.cert_limit_time FROM ".$sqlStaff." as finalA left join w_cert as finalB"
                . " on finalA.user_id = finalB.belong_table_id"
                . " and finalA.cert_key = finalB.cert_key ) as MainTable";
        // dump($sqlFinal);exit;
        
        $lists = Db::table($sqlFinal)->order('cert_limit_time')->select();

        foreach($lists as $k=>&$v){
            if(strtotime($v['cert_limit_time']) - time() > 86400 * 30 ){
                // 到期30日以上，正常
                $v['certStatus'] = 1;
            } else if(time() > strtotime($v['cert_limit_time'])){
                // 已过期
                $v['certStatus'] = 3;
            } else {
                // 即将到期
                $v['certStatus'] = 2;
            }
            // 20240428
            if(!$v['time_manage']){
                unset($lists[$k]);
            }
        }
        
        return $lists;
    }
    

}
