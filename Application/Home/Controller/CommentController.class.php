<?php 
namespace Home\Controller;
class CommentController extends BaseController{
    public function index(){
        //获取评论
        $p = I("p",1);
        $limit = 10;
        $comment_list = M("Comment")->alias("c")
            ->field("c.id,c.content,c.add_time,u.nickname,u.head_pic,u.`status`,c.admin_id,u.reg_type")
            ->where("c.user_id = u.id AND c.`status` = 1 AND c.pid = 0 AND c.is_message = 1")
            ->join("LEFT JOIN __USER__ AS u on c.user_id = u.id")
            ->page($p,$limit)
            ->order("c.add_time DESC")
            ->select();
        $comment_count = M("Comment")->alias("c")
            ->field("c.id,c.content,c.add_time,u.nickname,u.head_pic,u.`status`")
            ->where("c.user_id = u.id AND c.`status` = 1 AND c.pid = 0 AND c.is_message = 1")
            ->join("LEFT JOIN __USER__ AS u on c.user_id = u.id")
            ->count();
        //获取回复
        foreach ($comment_list as &$item) {
            $comment_reply_list = M("Comment")->alias("c")
                ->field("c.id,c.content,c.add_time,a.nickname,a.head_pic")
                ->where("c.`status` = 1 AND c.pid = ".$item["id"])
                ->join("LEFT JOIN __ADMIN__ AS a on c.admin_id = a.admin_id")
                ->order("c.add_time DESC")
                ->select();
            foreach ($comment_reply_list as &$comment_reply) {
                $comment_reply["str_add_time"] = date("Y-m-d H:i:s" , $comment_reply["add_time"]);
                //$comment_reply["head_pic"] = C("DOMAIN")."Public/upload/".C("admin_head_pic").$comment_reply["head_pic"];
            }
            $item["str_add_time"] = date("Y-m-d H:i:s" , $item["add_time"]);
            //如果是账号密码注册，返回的头像前面加上路径
            if( $item['reg_type'] == 0 ){
                $item["head_pic"] = C("DOMAIN")."Public/upload/".C("user_head_pic").$item["head_pic"];
            }
            $item["comment_reply_list"] = $comment_reply_list;
        }

        $Page = new \Think\Page($comment_count,$limit);
        $show = $Page->show();
        $this->assign("comment_list",$comment_list);
        $this->assign('page',$show);
        $this->display();
    }

    public function ajax_comment(){
        if(!$this->user){
            jsonReturn(0,"请先登录！");
        }
        $this->user = M("User")->where(["id"=>$this->user["id"]])->find();
        if($this->user["status"] == 1 ){
            jsonReturn(0,"抱歉，您的账号因为违规操作已经封号，解封请联系博主，谢谢合作！");
        }
        if($this->user["email"] == '' ){
            jsonReturn(2,"未绑定QQ邮箱。");
        }
        $data["content"] = I("content","");
        $data["user_id"] = $this->user["id"];
        $data["is_message"] = 1;
        $data["status"] = 1;
        $data["add_time"] = time();
        $res = D("Comment")->add($data);

        $d['res'] = $res;
        $d['sql'] = M()->getLastSql();

        if( !$res ){
            jsonReturn(0,"操作失败！",$d);
        }
        jsonReturn(1,"操作成功！");
    }
}