<?php 
/**
 * 用户组控制器
 */
namespace Admin\Controller;
class AuthGroupController extends BaseController{
    public function index(){
        $this->display();
    }
    /**
     * 添加用户组
     */
    public function add(){
        if(IS_POST){
            $data["rules"] = I("str_id",'');
            $data["title"] = I("title",'');
            $data["status"] = I("status",1);
            $count = M("AuthGroup")->where("title = '".$data["title"]."'")->count();
            if( $count > 0 ){
                jsonReturn(0,"该用户组已经存在！");
            }
            $res = D("AuthGroup")->add($data);
            if( !res ){
                jsonReturn(0,"操作失败！");
            }
            adminLog('添加用户组');
            jsonReturn(1,"操作成功！");
        }
        $authRule_list = M("AuthRule")->where("status = 1")->select();
        $this->assign("authRule_list",$authRule_list);
        $this->display();
    }
    /**
     * 编辑用户组
     * @return [type] [description]
     */
    public function edit(){
        if(IS_POST){
            $data["rules"] = I("str_id",'');
            $data["title"] = I("title",'');
            $data["status"] = I("status",1);
            $id = I("id",0);

            $info = M("AuthGroup")->where("id = $id")->find();
            if( !$info ){
                jsonReturn(0,"操作失败！");
            }
            if( $info["title"] != $data["title"] ){
                $count = M("AuthGroup")->where("title = '".$data["title"]."'")->count();
                if( $count > 0 ){
                    jsonReturn(0,"该用户组已经存在！");
                }
            }
            
            $res = D("AuthGroup")->where("id = $id")->save($data);
            adminLog('修改用户组');
            if($res !== false) {
                jsonReturn(1,"操作成功！");
            }else{
                jsonReturn(0,"操作失败！");
            }
        }
        $id = I("id",0);
        $data = M("AuthGroup")->where("id = $id")->find();
        if( !$data ){
            exit("无数据！");
        }
        if( $data["rules"] != "" ){
           $id_str = rtrim($data["rules"],",");
           $id_arr = explode(",",$id_str);
        }
        $authRule_list = M("AuthRule")->where("status = 1")->select();
        $this->assign("authRule_list",$authRule_list);
        $this->assign("id_arr",$id_arr);
        $this->assign("data",$data);
        $this->display();
    }
    /**
     * 删除用户组
     * @return [type] [description]
     */
    public function del(){
        $id = I("id",0);
        $info = M("AuthGroup")->where("id = $id")->find();
        if( !$info ){
            jsonReturn(0,"操作失败！");
        }
        $res = D("AuthGroup")->where("id = $id")->delete();
        if( !res ){
            jsonReturn(0,"操作失败！");
        }
        adminLog('删除用户组');
        jsonReturn(1,"操作成功！");
    }
}