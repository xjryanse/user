<?php
namespace xjryanse\user\logic;

use PHPMailer\PHPMailer\PHPMailer;
use xjryanse\user\service\UserEmailServerService;
use xjryanse\user\service\UserEmailLogService;

/**
 * 发送邮件逻辑
 */
class EmailLogic
{
    //发送server id
    use \xjryanse\traits\InstTrait;
    
    protected $mail;
    
    protected function getMail(){
        if(!$this->mail){
            $this->mail = new PHPMailer(true);                              // Passing `true` enables exceptions
            $this->mail->CharSet ="UTF-8";                     //设定邮件编码
            $this->mail->isSMTP();                             // 使用SMTP
            $this->mail->Host       = UserEmailServerService::getInstance($this->uuid)->fHost();            // SMTP服务器
            $this->mail->SMTPAuth   = true;                      // 允许 SMTP 认证
            $this->mail->Username   = UserEmailServerService::getInstance($this->uuid)->fUsername();        // SMTP 用户名  即邮箱的用户名
            $this->mail->Password   = UserEmailServerService::getInstance($this->uuid)->fPassword();        // SMTP 密码  部分邮箱是授权码(例如163邮箱)
            $this->mail->SMTPSecure = 'ssl';                    // 允许 TLS 或者ssl协议
            $this->mail->Port       = UserEmailServerService::getInstance($this->uuid)->fPort();        // 服务器端口 25 或者465 具体要看邮箱服务器支持
            $from                   = UserEmailServerService::getInstance($this->uuid)->fFrom();
            $fromName               = UserEmailServerService::getInstance($this->uuid)->fFromName();
            $this->mail->setFrom($from, $fromName);  //发件人
            $this->mail->addReplyTo($from, $fromName);  //发件人
        }
        return $this->mail;
    }
    /**
     * 发送逻辑，可能失败，请在外部捕获异常
     * @param type $toMail
     * @return type
     */
    public function send($toMail, $subject, $body, $attachments = [])
    {
        $mail = $this->getMail();
        if(is_string($toMail)){
            $toMail = [$toMail];
        }
        foreach($toMail as $receiver){
            $mail->addAddress( $receiver );  // 添加多个收件人
        }        
        foreach($attachments as $file){
            if(is_string($file)){
                $file = ["path"=>$file,"name"=>""];
            }
            $mail->addAttachment( $file['path'], $file['name']);    // 发送附件并且重命名
        }
        //Content
        $mail->isHTML(true);                                  // 是否以HTML文档格式发送  发送后客户端可直接显示对应HTML内容
        $mail->Subject = $subject;
        $mail->Body    = $body;
        $mail->AltBody = '如果邮件客户端不支持HTML则显示此内容';

        $res = $mail->send();
        //记录日志
        $this->log($toMail, $subject, $body, $attachments,$res);
        return $res;
    }
    
    protected function log($toMail, $subject, $body, $attachments,$result){
        $data['server_id']  = $this->uuid;
        $data['to_mail']    = json_encode($toMail,JSON_UNESCAPED_UNICODE);
        $data['subject']    = $subject;
        $data['body']       = $body;
        $data['attachments']    = json_encode($attachments,JSON_UNESCAPED_UNICODE);
        $data['send_result']    = booleanToNumber($result);
        return UserEmailLogService::save($data);
    }

}
