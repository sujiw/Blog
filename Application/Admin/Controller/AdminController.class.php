<?php 
namespace Admin\Controller;
use Think\Db;
class AdminController extends BaseController{

    public function index(){
        $this->display();
    }
    public function login(){
        if(session('?admin_id') && session('admin_id')>0){
            $this->error("您已登录",U('Admin/Index/index'));
        }
        if(IS_POST){
            $login_name = I('post.username','');
            $password = I('post.password','');
            if(!empty($login_name) && !empty($password)){
                $password2 = encrypt($password);
                //查询登录用户信息
                $admin_info = M('Admin')->where(array('login_name'=>$login_name,'password'=>$password2))->find();
                if(is_array($admin_info)){
                    //session用户id
                    //session('admin_id',$admin_info['admin_id']);
                    session('auth', $admin_info);
                    //获取admin_log表里的最后登录时间
                    $last_login_time = M('admin_log')->where("admin_id = ".$admin_info['admin_id']." and log_info = '后台登录'")->order('log_id desc')->limit(1)->getField('log_time');
                    //更新admin表里的最后登录时间和ip
                    M('admin')->where("admin_id = ".$admin_info['admin_id'])->save(array('last_login'=>time(),'last_ip'=>  getIP()));

                    //后台操作记录
                    adminLog('后台登录');
                    $url = session('from_url') ? session('from_url') : U('Admin/Index/index');
                    exit(json_encode(array('status'=>1,'url'=>$url)));
                }else{
                    exit(json_encode(array('status'=>0,'msg'=>'账号密码不正确')));
                }
            }else{
                exit(json_encode(array('status'=>0,'msg'=>'请填写账号密码')));
            }
        }
        $this->display();
    }
    public function logout(){
        session('auth',null);
        $this->success("退出成功",U('Admin/Admin/login'));
    }

    /**
     * 添加管理员
     */
    public function add(){
        if(IS_POST){
            $data["login_name"] = I('login_name','');
            $data["nickname"] = I('nickname','');
            $data["password"] = encrypt(I('password',''));
            $data["add_time"] = time();
            $data2["group_id"] = I('group_id',0);

            $count = M("Admin")->where(array('login_name'=>$data["login_name"]))->count();
            if( $count > 0 ){
                jsonReturn(0,"用户名已经被占用！");
            }

            $model = D("Admin");
            $model->startTrans();
            try{
                $res = $model->add($data);
                $data2["uid"] = $model->getLastInsID();
                $res2 = D("auth_group_access")->add($data2);
                adminLog('添加管理员');
                if( $res && $res2 ){
                   $model->commit();
                }
                jsonReturn(1,"操作成功！");
            } catch (Exception $e) {
                // 回滚事务
                $model->rollback();
                jsonReturn(0,"操作失败！");
            }

        }
        $auth_group_list = M("AuthGroup")->where("status = 1")->select();
        $this->assign("auth_group_list",$auth_group_list);
        $this->display();
    }

    /**
     * 修改管理员
     * @return [type] [description]
     */
    public function edit(){
        if(IS_POST){
            $data["login_name"] = I('login_name','');
            $data["nickname"] = I('nickname','');
            
            $admin_id = I("admin_id",0);
            $data2["uid"] = $admin_id;
            $data2["group_id"] = I('group_id',0);
            if( $admin_id <= 0 ){
                jsonReturn(0,"无数据！");
            }
            $info = M("Admin")->where(array('login_name'=>$data["login_name"]))->find();
            if( $info["admin_id"] != $admin_id && $info["login_name"] == $data["login_name"] ){
                jsonReturn(0,"用户名已经被占用！");
            }
            $model = D("Admin");
            $model->startTrans();
            try{
                $res = $model->where("admin_id = $admin_id")->save($data);
                $res2 = D("auth_group_access")->where("uid = $admin_id")->save($data2);
                adminLog('修改管理员');
                $model->commit();
                jsonReturn(1,"操作成功！");
            } catch (Exception $e) {
                // 回滚事务
                $model->rollback();
                jsonReturn(0,"操作失败！");
            }
        }
        $admin_id = I("admin_id",0);
        $data = M("Admin")->where("admin_id = $admin_id")->find();
        if( !$data ){
            exit("无数据！");
        }
        $group_id = M("auth_group_access")->where("uid = $admin_id")->getField("group_id");
        $auth_group_list = M("auth_group")->where("status = 1")->select();
        $this->assign("data",$data);
        $this->assign("auth_group_list",$auth_group_list);
        $this->assign("group_id",$group_id);
        $this->display();
    }

    /**
     * 删除管理员以及auth_group_access表中相对应的数据
     * @return [type] [description]
     */
    public function del(){
        $admin_id = I("admin_id",0);
        $info = M("Admin")->where("admin_id = $admin_id")->find();
        if( !$info ){
            jsonReturn(0,"无数据！");
        }
        $model = D("Admin");
        $model->startTrans();
        try{
            $res = $model->where("admin_id = $admin_id")->delete();
            $res2 = D("auth_group_access")->where("uid = $admin_id")->delete();
            adminLog('删除管理员');
            if( $res && $res2 ){
               $model->commit();
            }
            jsonReturn(1,"操作成功！");
        } catch (Exception $e) {
            // 回滚事务
            $model->rollback();
            jsonReturn(0,"操作失败！");
        }
    }

    /**
     * 修改密码
     * @return [type] [description]
     */
    public function editpwd(){
        if(IS_POST){
            $admin_id = I("admin_id",0);
            $data["password"] = encrypt(I('password',''));
            $info = M("Admin")->where("admin_id = $admin_id")->find();
            if( !$info ){
                jsonReturn(0,"无数据！");
            }
            $res = D("Admin")->where("admin_id = $admin_id")->save($data);
            if($res !== false) {
                adminLog('修改管理员密码');
                jsonReturn(1,"操作成功！");
            }else{
                jsonReturn(0,"操作失败！");
            }
        }
        $admin_id = I("admin_id",0);
        $info = M("Admin")->where("admin_id = $admin_id")->find();
        if( !$info ){
            jsonReturn(0,"无数据！");
        }
        $this->assign("admin_id",$admin_id);
        $this->display();
    }

    public function log(){
        $this->display();
    }
}