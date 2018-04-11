<?php 
/**
 * 文章分类控制器
 */
namespace Admin\Controller;
class CategoryController extends BaseController{
    public function index(){
        $this->display();
    }

    public function add(){
        if(IS_POST){
            $data["title"] = I("title",'');
            $count = M("Category")->where("title = '". $data["title"] ."'")->count();
            if( $count > 0 ){
                jsonReturn(0,"该分类名称已经被占用！");
            }
            $res = D("Category")->add($data);
            if( !$res ){
                jsonReturn(0,"操作失败！");
            }
            adminLog('添加文章分类');
            jsonReturn(1,"操作成功！");
        }
        $this->display();
    }
    public function edit(){
        if(IS_POST){

            $id = I("id",0);
            $data["title"] = I("title",'');

            $count = M("Category")->where("id = $id")->count();
            if( $count == 0 ){
                jsonReturn(0,"无数据！");
            }
            $info = M("Category")->where("title = '". $data["title"] ."'")->find();
            if( $info["id"] != $id && $info["title"] == $data["title"] ){
                jsonReturn(0,"该分类名称已经被占用！");
            }

            //如果更新的数据没有变化也没有出错 返回0
            $res = D("Category")->where("id = $id")->save($data);

            if($res !== false) {
                adminLog('修改文章分类');
                jsonReturn(1,"操作成功！");
            }else{
                jsonReturn(0,"操作失败！");
            }

        }
        $id = I("id",0);
        $info = M("Category")->where("id = $id")->find();
        if( !$info ){
            jsonReturn(0,"无数据！");
        }
        $this->assign("data",$info);
        $this->display();
    }
}