<?php 
/**
 * 权限规则控制器
 */
namespace Admin\Controller;
class AuthRuleController extends BaseController{
    public function index(){
        $this->display();
    }
    /**
     * 添加权限规则
     */
    public function add(){
        if(IS_POST){
            $data["name"] = I("name");
            $data["title"] = I("title");
            $count = M("AuthRule")->where("name = '".$data["name"]."'")->count();
            if( $count > 0 ){
                jsonReturn(0,"该权限规则已经存在！");
            }
            $res = D("AuthRule")->add($data);
            if( !res ){
                jsonReturn(0,"操作失败！");
            }
            adminLog('添加权限');
            jsonReturn(1,"操作成功！");
        }
        $planPath = APP_PATH.'Admin/Controller';
        $planList = array();
        $dirRes   = opendir($planPath);
        while($dir = readdir($dirRes)){
            if(!in_array($dir,array('.','..','.svn'))){
                $xcontroller = basename($dir,'.class.php');
                $planList[] = explode("Controller",$xcontroller)[0];
            }
        }
        $this->assign('planList',$planList);
        $this->display();
    }
    /**
     * 修改权限规则
     * @return [type] [description]
     */
    public function edit(){
        if(IS_POST){
            $data["name"] = I("name");
            $data["title"] = I("title");
            $id = I("id",0);

            $info = M("AuthRule")->where("id = $id")->find();
            if( !$info ){
                jsonReturn(0,"操作失败！");
            }
            if( $info["name"] != $data["name"] ){
                $count = M("AuthRule")->where("name = '".$data["name"]."'")->count();
                if( $count > 0 ){
                    jsonReturn(0,"该权限规则已经存在！");
                }
            }
            
            $res = D("AuthRule")->where("id = $id")->save($data);
            if($res !== false) {
                adminLog('修改权限');
                jsonReturn(1,"操作成功!");
            }else{
                jsonReturn(0,"操作失败!");
            }
        }
        $id = I("id",0);
        $data = M("AuthRule")->where("id = $id")->find();
        if( !$data ){
            exit("无数据！");
        }
        $planPath = APP_PATH.'Admin/Controller';
        $planList = array();
        $dirRes   = opendir($planPath);
        while($dir = readdir($dirRes)){
            if(!in_array($dir,array('.','..','.svn'))){
                $xcontroller = basename($dir,'.class.php');
                $planList[] = explode("Controller",$xcontroller)[0];
            }
        }
        $this->assign('planList',$planList);
        $this->assign('data',$data);
        $this->display();
    }
    /**
     * 删除权限规则
     * @return [type] [description]
     */
    public function del(){
        $id = I("id",0);
        $info = M("AuthRule")->where("id = $id")->find();
        if( !$info ){
            jsonReturn(0,"操作失败！");
        }
        $res = D("AuthRule")->where("id = $id")->delete();
        if( !res ){
            jsonReturn(0,"操作失败！");
        }
        adminLog('删除权限规则');
        jsonReturn(1,"操作成功！");
    }
    /**
     * ajax获取对应控制器下的方法
     * @return [type] [description]
     */
    function ajax_get_action(){
        $control = I('controller');
        $advContrl = get_class_methods("Admin\\Controller\\".$control."Controller");
        $baseContrl = get_class_methods('Admin\Controller\BaseController');
        $diffArray  = array_diff($advContrl,$baseContrl);
        $html = '';
        foreach ($diffArray as $val){
            $html .= "<option value='".$val."'>".$val."</option>";
        }
        exit($html);
    }
}