<?php 
namespace Admin\Controller;
class GroupController extends BaseController{
    public function idnex(){
        $this->display();
    }
    public function add(){
        if(IS_POST){
            $data["head_pic"] = I("head_pic","");
            $data["url"] = I("url","");
            $data["title"] = I("title","");
            $data["content"] = I("content","");
            $data["add_time"] = time();

            $count = M("Group")->where("title = '". $data["title"] ."'")->count();
            if( $count > 0 ){
                jsonReturn(0,"该标题已经被占用！");
            }

            $res = D("Group")->add($data);
            if(!$res){
                jsonReturn(0,"操作失败！");
            }
            adminLog('添加圈子');
            jsonReturn(1,"操作成功！");
        }
        $this->display();
    }
    public function edit(){
        if(IS_POST){
            $id = I("id",0);
            $count = M("Group")->where("id = $id")->count();
            if( $count == 0 ){
                jsonReturn(0,"无数据！");
            }
            
            $data["head_pic"] = I("head_pic","");
            $data["url"] = I("url","");
            $data["title"] = I("title","");
            $data["content"] = I("content","");

            $info = M("Group")->where("title = '". $data["title"] ."'")->find();
            if( $info["id"] != $id && $info["title"] == $data["title"] ){
                jsonReturn(0,"该标题已经被占用！");
            }

            $res = D("Group")->where("id = $id")->save($data);
            if($res !== false) {
                adminLog('修改圈子');
                jsonReturn(1,"操作成功！");
            }else{
                jsonReturn(0,"操作失败！");
            }
        }
        $id = I("id",0);
        $info = M("Group")->where("id = $id")->find();
        if( !$info ){
            jsonReturn(0,"无数据！");
        }
        $this->assign("data",$info);
        $this->display();
    }

    public function del(){
        $id = I("id",0);
        $res = D("Group")->where("id = $id")->delete();
        if(!$res) {
            jsonReturn(0,"操作失败！");
        }
        adminLog('删除圈子');
        jsonReturn(1,"操作成功！");
    }
}