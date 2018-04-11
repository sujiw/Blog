<?php 
namespace Home\Controller;
use Think\Verify;
use LT\ThinkSDK\ThinkOauth;
class UserController extends BaseController{
    public function login(){
        if(IS_POST){
            $verify = new Verify();
            if (!$verify->check(I('vertify',''), "user_login")) {
                jsonReturn(0,"验证码错误");
            }

            $data["username"] = I("username","");
            $data["password"] = I("password","");
            if( !empty($data["username"]) && !empty($data["password"]) ){
                $data["password"] = encrypt($data['password']);
                $res = M("User")->where($data)->find();
                if( !$res ){
                    jsonReturn(0,"用户名或密码错误！");
                }
                $session_data["id"] = $res["id"];
                $session_data["nickname"] = $res["nickname"];
                $session_data["head_pic"] = C("DOMAIN")."Public/upload/".C("user_head_pic").$res["head_pic"];
                $session_data["is_relation"] = $res["is_relation"];
                session("user",$session_data);
                //$url = I("url","") ? I("url","") : U('Home/Index/index');
                jsonReturn(1,"登录成功！");
            }else{
                jsonReturn(0,"用户名或密码不能为空！");
            }
        }
        $this->display();
    }
    public function reg(){
        if(IS_POST){
            $verify = new Verify();
            if (!$verify->check(I('vertify',''), "user_login")) {
                jsonReturn(0,"验证码错误");
            }
            $data["username"] = I("username","");
            $data["password"] = I("password","");
            $data["nickname"] = I("nickname","");
            if( !empty($data["username"]) && !empty($data["nickname"]) && !empty($data["password"]) ){
                $count = M("User")->where(['username'=>$data["username"]])->count();
                if( $count > 0 ){
                    jsonReturn(0,"登录名已经被占用！");
                }
                $data["password"] = encrypt($data['password']);
                $num = rand(1,10);
                $data["head_pic"] = $num.'.png';
                $data["reg_type"] = 0;
                $data["last_login"] = time();
                $data["last_ip"] = getIP();
                $data["add_time"] = time();
                $res = D("User")->add($data);
                if( !$res ){
                    jsonReturn(0,"注册失败，服务器错误！");
                }
                $session_data["id"] = $res;
                $session_data["nickname"] = $data["nickname"];
                $session_data["head_pic"] = C("DOMAIN")."Public/upload/".C("user_head_pic").$data["head_pic"];
                $session_data["is_relation"] = 0;
                session("user",$session_data);
                jsonReturn(1,"注册成功！");
            }else{
                jsonReturn(0,"用户名、昵称或密码不能为空！");
            }
        }
        $this->display();
    }
    public function vertify(){
        $config = array(
            'fontSize' => 30,
            'length' => 4,
            'useCurve' => true,
            'useNoise' => false,
            'reset' => false
        );    
        $Verify = new Verify($config);
        $Verify->entry("user_login");
    }
    public function logout(){
        session('user',null);
        jsonReturn(1,"已退出！");
    }
    public function qqlogin($type = null,$url = ''){
        session('url',$url);
        empty($type) && $this->error('参数错误');
        //加载ThinkOauth类并实例化一个对象
        $sns = ThinkOauth::getInstance($type);
        //exit($sns->getRequestCodeURL());
        $asd = file_get_contents($sns->getRequestCodeURL());
        //跳转到授权页面
        redirect($sns->getRequestCodeURL());
    }
    //授权回调地址
    public function callback($type = null, $code = null){
        (empty($type) || empty($code)) && $this->error('参数错误');

        //加载ThinkOauth类并实例化一个对象
        $sns = ThinkOauth::getInstance($type);

        //腾讯微博需传递的额外参数
        $extend = null;
        if ($type == 'tencent') {
            $extend = array('openid' => $this->_get('openid'), 'openkey' => $this->_get('openkey'));
        }

        //请妥善保管这里获取到的Token信息，方便以后API调用
        //调用方法，实例化SDK对象的时候直接作为构造函数的第二个参数传入
        //如： $qq = ThinkOauth::getInstance('qq', $token);
        $token = $sns->getAccessToken($code, $extend);

        //获取当前登录用户信息
        if (is_array($token)) {
            $user_info = A('Type', 'Event')->$type($token);

            echo("<h1>恭喜！使用 {$type} 用户登录成功</h1><br>");
            echo("授权信息为：<br>");
            dump($token);
            echo("当前登录用户信息为：<br>");
            dump($user_info);
        }
    }
}