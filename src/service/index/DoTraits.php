<?php

namespace xjryanse\user\service\index;

use xjryanse\system\service\SystemImportTemplateService;
use xjryanse\logic\Arrays;
use xjryanse\user\service\UserAuthRoleService;
use xjryanse\system\service\SystemCompanyJobService;
/**
 * 分页复用列表
 */
trait DoTraits{
    
    /**
     * 20231230:带证件信息，导入
     * @param type $param
     */
    public static function doImportWithCerts($param){
        $roleKey            = Arrays::value($param, 'role_key');
        $param['role_id']   = UserAuthRoleService::keyToId($roleKey);
        // 20240102
        $deptId             = Arrays::value($param, 'dept_id');
        $param['job_id']    = SystemCompanyJobService::deptKeyToId($deptId, $roleKey);

        $tplKey     = 'hxDriver';
        $templateId = SystemImportTemplateService::keyToId($tplKey);
        // 处理导入数据
        SystemImportTemplateService::getInstance($templateId)->doImportData($param);

        return true;
    }
    
    
}
