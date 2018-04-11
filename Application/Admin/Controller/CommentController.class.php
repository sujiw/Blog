<?php 
namespace Admin\Controller;
use Think\Db;
class CommentController extends BaseController{
    public function index(){
        $this->display();
    }
    public function reply(){
        if(IS_POST){
            $id = I("id",0);
            $sess_auth = session('auth');
            if(!sess_auth){
                $this->error("用户信息已经过期，正在跳转登录页面",U('Admin/Admin/login'),1);
            }
            $data["admin_id"] = $sess_auth["admin_id"];
            $data["add_time"] = time();
            $data["pid"] = $id;
            $data["is_message"] = 1;
            $data["content"] = html_entity_decode(I("content",""));

            $info = M("Comment")->alias("c")
                        ->field("c.content,u.email")
                        ->where("c.id = ".$id)
                        ->join("LEFT JOIN __USER__ AS u on c.user_id = u.id")
                        ->find();
            $content = '<h3>你在'.$this->website['title'].'留言板中的留言，博主回复你啦，赶紧去看看吧。<a href="'.C('DOMAIN').'message" target="_blank">点击这里前往</a></h3><p style="color: red;font-size: 22px">该邮件由系统自动发送，请勿回复。请认准站长唯一邮箱：<a href="mailto:317723531@qq.com" target="_blank">317723531@q<wbr>q.com</a></p>';

            if($info["email"] == ""){
                $res = D("Comment")->add($data);
                if(!$res){
                    jsonReturn( 0 , "操作失败！");
                }
                adminLog("回复留言");
                jsonReturn( 1 , "操作成功！");
            }else{
                $rs = think_send_mail($info["email"],'尊敬的用户',$this->website['title'].'留言板回复通知！',$content);
                if( $rs == 1 ){
                    $res = D("Comment")->add($data);
                    if(!$res){
                        jsonReturn( 0 , "操作失败！");
                    }
                    adminLog("回复留言");
                    jsonReturn( 1 , "操作成功！");
                }else{
                    jsonReturn( 0 , $rs);
                }
            }
        }
        $id = I("id",0);
        $res = M("Comment")->where("id = $id")->find();
        if( !$res ){
            exit("无数据！");
        }
        $reply_list = M("Comment")->where("pid = $id")->order("add_time DESC")->select();
        foreach ($reply_list as &$item) {
            $item["str_add_time"] = date("Y-m-d H:i:s" , $item["add_time"]);
        }
        $this->assign("comment",$res);
        $this->assign("reply_list",$reply_list);
        $this->display("reply");
    }
    public function del_reply(){
        $id = I("id",0);
        $res = D("Comment")->where("id = $id")->delete();
        if(!$res) {
            jsonReturn(0,"操作失败！");
        }
        adminLog('删除留言的回复');
        jsonReturn(1,"操作成功！");
    }
    public function del(){
        $id = I("id",0);
        $list = M("Comment")->where("pid = $id")->select();
        $arr_id = '';
        foreach ($list as &$item) {
            $arr_id .= $item["id"] .",";
        }
        $id_arr = $arr_id.$id;
        $model = D("Comment");
        $model->startTrans();
        try{
            $where['id'] = array('in',$id_arr);
            $res = $model->where($where)->delete();
            if($res !== false) {
                adminLog('删除留言及回复');
                jsonReturn(1,"操作成功！");
            }else{
                jsonReturn(0,"操作失败！");
            }
        } catch (Exception $e) {
            // 回滚事务
            $model->rollback();
            jsonReturn(0,"操作失败！");
        }
    }
}