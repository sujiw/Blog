<?php
namespace Admin\Controller;
class WebsiteController extends BaseController {
    function index(){
        $this->display();
    }
    function edit(){
        if(IS_POST){
            $data = I("post.");
            if(!M('website')->where(["id"=>1])->save($data))
                jsonReturn(0,"保存失败");
            S('website',null);
            jsonReturn(1,"保存成功");
        }
        jsonReturn(1,"参数错误");
    }
}