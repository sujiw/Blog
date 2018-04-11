<?php 
namespace Home\Controller;
use Think\Controller;

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
        if($this->isLogin()){
            $this->assign('user',$this->user);
        }
    }
    public function isLogin(){
        $this->user = session("user");
        if(is_array($this->user)){
            return true;
        }
        $this->user = false;
        return false;
    }
}