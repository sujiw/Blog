<?php 
function encrypt($str){
    return md5(C("AUTH_CODE").$str);
}
// 定义一个函数getIP() 客户端IP，
function getIP(){            
    if (getenv("HTTP_CLIENT_IP"))
        $ip = getenv("HTTP_CLIENT_IP");
    else if(getenv("HTTP_X_FORWARDED_FOR"))
        $ip = getenv("HTTP_X_FORWARDED_FOR");
    else if(getenv("REMOTE_ADDR"))
        $ip = getenv("REMOTE_ADDR");
    else $ip = "Unknow";

    if(preg_match('/^((?:(?:25[0-5]|2[0-4]\d|((1\d{2})|([1-9]?\d)))\.){3}(?:25[0-5]|2[0-4]\d|((1\d{2})|([1 -9]?\d))))$/', $ip))          
        return $ip;
    else
        return '';
}
// 服务器端IP
function serverIP(){   
    return gethostbyname($_SERVER["SERVER_NAME"]);   
}
function jsonReturn($status = 0,$msg = '',$data = ''){
    if(empty($data)){
        $data = '';
    }

    if($status == 0 && $msg == ''){
        $msg = "操作失败";
    }
    $info['status'] = $status;
    $info['msg'] = $msg;
    $info['result'] = $data;
    exit(json_encode($info));
}
/**
 * banse64数据转换成图片
 * @param $data
 * @param $path
 * @return bool|string
 */
function uploadImage($data ,$path){
    $upload = new \Think\Upload();// 实例化上传类
    $upload->maxSize   =     3145728 ;// 设置附件上传大小
    $upload->exts      =     array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
    $upload->rootPath  =     "./Public/upload/".$path; // 设置附件上传根目录
    $upload->saveName  =     array('uniqid','');
    $upload->autoSub   =     false;
    // 上传单个文件 
    $info   =   $upload->upload();
    if(!$info) {// 上传错误提示错误信息
        $info["code"] = 0;
        $info["msg"] = $upload->getError();
    }else{// 上传成功 获取上传文件信息
        $info["code"] = 1;
    }
    return $info;
}
function think_send_mail($to, $name, $subject = '', $body = '', $attachment = null){

    $config = C('THINK_EMAIL');

    vendor('PHPMailer.class#phpmailer'); //从PHPMailer目录导class.phpmailer.php类文件
    vendor('PHPMailer.class#smtp');
    $mail = new PHPMailer(); //PHPMailer对象

    $mail->CharSet = 'UTF-8'; //设定邮件编码，默认ISO-8859-1，如果发中文此项必须设置，否则乱码
    $mail->IsSMTP(); // 设定使用SMTP服务
    $mail->SMTPDebug = 0; // 关闭SMTP调试功能
    $mail->SMTPAuth = true; // 启用 SMTP 验证功能
    $mail->SMTPSecure = 'ssl'; // 使用安全协议
    $mail->Host = $config['SMTP_HOST']; // SMTP 服务器
    $mail->Port = $config['SMTP_PORT']; // SMTP服务器的端口号
    $mail->Username = $config['SMTP_USER']; // SMTP服务器用户名
    $mail->Password = $config['SMTP_PASS']; // SMTP服务器密码
    $mail->SetFrom($config['FROM_EMAIL'], $config['FROM_NAME']);
    $replyEmail = $config['REPLY_EMAIL']?$config['REPLY_EMAIL']:$config['FROM_EMAIL'];
    $replyName = $config['REPLY_NAME']?$config['REPLY_NAME']:$config['FROM_NAME'];
    $mail->AddReplyTo($replyEmail, $replyName);
    $mail->Subject = $subject;
    $mail->AltBody = "为了查看该邮件，请切换到支持 HTML 的邮件客户端"; 
    $mail->MsgHTML($body);
    $mail->AddAddress($to, $name);
    if(is_array($attachment)){ // 添加附件
        foreach ($attachment as $file){
            is_file($file) && $mail->AddAttachment($file);
        }
    }
    return $mail->Send() ? true : $mail->ErrorInfo;
}