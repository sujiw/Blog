<?php 
namespace Admin\Controller;
use Think\Controller;
use Think\Auth;
class BaseController extends Controller{

    function __construct(){
        parent::__construct();
    }

    public function _initialize(){
        $this->website = S('website');
        if(!$this->website){
            $this->website = M('website')->find();
            S('website',$this->website,86400);
        }
        $this->assign('website',$this->website);
        $this->assign('action',ACTION_NAME);
        //过滤不需要登陆的行为
        if(in_array(ACTION_NAME,array('login','logout','vertify')) || in_array(CONTROLLER_NAME,array('Ueditor','Uploadify'))){
            //return;
        }else{
            $sess_auth = session('auth');
            if($sess_auth['admin_id'] > 0 ){
                $this->check_priv();//检查管理员菜单操作权限
            }else{
                $this->error('请先登陆',U('Admin/Admin/login'),1);
            }
        }
    }
    
    //权限验证函数(TP), 入口初始化执行
    public function check_priv(){
        $sess_auth = session('auth');
        if(!sess_auth){
            $this->error("用户信息已经过期，正在跳转登录页面",U('Admin/Admin/login'),1);
        }
        if ($sess_auth['admin_id'] == 1) {
            return true;
        }
        $auth = new Auth();
        //$auth->check(MODULE_NAME.DS.CONTROLLER_NAME.DS.ACTION_NAME,$sess_auth['admin_id'])
        if( !$auth->check(CONTROLLER_NAME."/".ACTION_NAME,$sess_auth['admin_id']) ){
            exit("您没有操作权限,请联系超级管理员分配权限");
            //$this->error('您没有操作权限,请联系超级管理员分配权限',U('Admin/Index/main'),1);
        }
    }
    
}