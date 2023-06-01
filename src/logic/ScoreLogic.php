<?php
namespace xjryanse\user\logic;

use xjryanse\system\service\SystemRuleService;
use xjryanse\logic\Datetime;
use xjryanse\user\service\UserAccountService;
use xjryanse\user\service\UserAccountLogService;
use xjryanse\system\service\SystemCateService;
use xjryanse\system\service\SystemConditionService;
use xjryanse\logic\Arrays;
use xjryanse\logic\Debug;
use think\Db;
/**
 * 积分逻辑
 */
class ScoreLogic
{
    /**
     * 取出积分规则，根据当前用户数据，匹配规则达成则添加积分
     * @param type $userId
     */
    public static function score( $userId ){
        /*
        $con[] = ['rule_type','=','score'];
        $con[] = ['status','=',1];
        $ruleIds = SystemRuleService::ids( $con );
         */
        $ruleIds = SystemRuleService::ruleTypeIds('score');
        Debug::debug("ScoreLogic::score的规则id",$ruleIds);
        foreach( $ruleIds as $ruleId){
            //校验规则，并添加积分
            self::ruleScoreCheck($ruleId, $userId);
        }
    }
    /**
     * 积分规则校验
     */
    protected static function ruleScoreCheck( $ruleId, $userId )
    {
        //校验规则是否达成
        if(!self::isRuleReached($ruleId, $userId)){
            return false;
        }
        //条件已达成，无记录则添加，有记录跳过
        return self::addScoreWithLogCheck($ruleId, $userId);
    }
    /**
     * 规则是否达成
     * @param type $ruleId
     * @param type $userId
     * @return type
     */
    protected static function isRuleReached( $ruleId, $userId )
    {
        $data               = self::ruleGetData($ruleId, $userId);
        Debug::debug('isRuleReached的ruleGetData的值', $data);
        $isReached          = SystemRuleService::getInstance($ruleId)->isRuleReached( $data );
        return $isReached;
    }
    /**
     * 规则无记录，才添加
     */
    protected static function addScoreWithLogCheck($ruleId, $userId)
    {
        $groupKey   = 'userScoreCate';
        $rule       = SystemRuleService::getInstance( $ruleId )->get();
        // SystemCateService 的 一个实例
        $info       = SystemCateService::getByGroupKeyCateKey( $groupKey , $rule['rule_key']);   //systemCate的表：userScoreCate
        $data       = self::ruleGetData($ruleId, $userId);        //
        // 基于条件表，查找相关的数据
        $rawData    = SystemConditionService::findDataByItemKey( 'score', $rule['rule_key'], $data );
        Debug::debug('$rawData',$rawData);
        $fromTable  = Arrays::value($rawData, 'from_table');
        $fromTableId= Arrays::value($rawData['from_table_data'], 'id');
        $changeCate = Arrays::value($info, 'cate_key');
        $cond[]     = ['change_cate','=',$changeCate];
        $hasLog =  UserAccountLogService::hasLog($fromTable, $fromTableId, $cond);
        if($hasLog){
            return false;
        }
        //积分没有写入过，则写入一下
        Db::startTrans();
            $cateId = SystemCateService::keyGetId($groupKey, $rule['rule_key']);
            $logData                    = [];
            $logData['change_cate']     = $changeCate;
            $logData['change_reason']   = SystemCateService::getInstance($cateId)->fCateName(); //积分变动原因
            $logData['from_table']      = $fromTable;
            $logData['from_table_id']   = $fromTableId;
            $res = UserAccountLogService::doIncome($userId, 'score', $rule['rule_value'],$logData);
        Db::commit();
        return $res;
    }

    /**
     * 
     * @param type $ruleId
     * @param type $userId
     * @return type     
     *    from_time:出发时间
     *      to_time:到达时间
     *      user_id:用户id
     */
    protected static function ruleGetData( $ruleId, $userId )
    {
        $scoreAccount       = UserAccountService::getByUserAccountType($userId, 'score');
        $rule               = SystemRuleService::getInstance( $ruleId )->get();
        Debug::debug('ruleGetData规则信息', $rule);
        // 20230519：优化
        $scoreAccountId = Arrays::value($scoreAccount, 'id');
        $lastFromTableId    = UserAccountLogService::lastFromTableId($scoreAccountId,$rule['rule_key'] ,'');
        //签到得积分
        $data               = Datetime::periodTime($rule['period'], $rule['period_unit']);
        $data['lastFromTableId']    = $lastFromTableId;
        $data['userId']    = $userId;
        Debug::debug('数据来源scoreLogic::ruleGetData()', $data);
        return $data;
    }

}
