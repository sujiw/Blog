<?php 
/**
 * 用户管理控制器
 */
namespace Admin\Controller;
class UserController extends BaseController{
    public function index(){
        $this->display();
    }

    /**
     * 后台手动添加用户
     */
    public function add(){
        if(IS_POST){
            $data["username"] = I("username",'');
            $data["nickname"] = I("nickname",'');
            $data["password"] = encrypt(I('password',''));
            $count = M("User")->where("username = '".$data["username"]."'")->count();
            if( $count > 0 ){
                jsonReturn(0,"登录名已经存在，不能重复注册!");
            }
            $data["add_time"] = time();
            $res = D("User")->add($data);
            if( !$res ){
                jsonReturn(0,"操作失败！");
            }
            adminLog('添加用户');
            jsonReturn(1,"操作成功！");
        }
        $this->display();
    }

    /**
     * 修改用户
     * @return [type] [description]
     */
    public function edit(){
        if(IS_POST){
            $data["username"] = I("username",'');
            $data["nickname"] = I("nickname",'');
            $id = I('id',0);
            $count = M("User")->where("id = $id")->count();
            if( $count <= 0 ){
                jsonReturn(0,"用户不存在！");
            }
            $info = M("User")->where("username = '".$data["username"]."'")->find();
            if( $info["id"] != $id && $info["username"] == $data["username"] ){
                jsonReturn(0,"登录名已经存在，不能重复注册!");
            }
            $res = D("User")->where("id = $id")->save($data);
            if($res !== false) {
                adminLog('修改用户');
                jsonReturn(1,"操作成功！");
            }else{
                jsonReturn(0,"操作失败！");
            }
        }
        $id = I("id",0);
        $user = M("User")->where("id = $id")->find();
        if( !$user ){
            jsonReturn(0,"用户不存在！");
        }
        $this->assign("data",$user);
        $this->display();
    }

    /**
     * 修改用户密码
     * @return [type] [description]
     */
    public function editpwd(){
        if(IS_POST){
            $id = I("id",0);
            $data["password"] = encrypt(I('password',''));
            $info = M("User")->where("id = $id")->find();
            if( !$info ){
                jsonReturn(0,"用户不存在！");
            }
            $res = D("User")->where("id = $id")->save($data);
            if($res !== false) {
                adminLog('修改用户密码');
                jsonReturn(1,"操作成功！");
            }else{
                jsonReturn(0,"操作失败！");
            }
        }
        $id = I("id",0);
        $user = M("User")->where("id = $id")->find();
        if( !$user ){
            jsonReturn(0,"用户不存在！");
        }
        $this->assign("data",$user);
        $this->display();
    }

    public function lock(){
        $id = I("id",0);
        $data["status"] = 1;
        $info = M("User")->where("id = $id")->find();
        if( !$info ){
            jsonReturn(0,"用户不存在！");
        }
        $res = D("User")->where("id = $id")->save($data);
        if($res !== false) {
            adminLog('锁定用户');
            jsonReturn(1,"操作成功！");
        }else{
            jsonReturn(0,"操作失败！");
        }
    }
    public function unlock(){
        $id = I("id",0);
        $data["status"] = 0;
        $info = M("User")->where("id = $id")->find();
        if( !$info ){
            jsonReturn(0,"用户不存在！");
        }
        $res = D("User")->where("id = $id")->save($data);
        if($res !== false) {
            adminLog('解锁用户');
            jsonReturn(1,"操作成功！");
        }else{
            jsonReturn(0,"操作失败！");
        }
    }
    public function del(){
        $id = I("id",0);
        $info = M("User")->where("id = $id")->find();
        if( !$info ){
            jsonReturn(0,"用户不存在！");
        }
        $res = D("User")->where("id = $id")->delete();
        if(!$res) {
            jsonReturn(0,"操作失败！");
        }
        adminLog('删除用户');
        jsonReturn(1,"操作成功！");
    }
}