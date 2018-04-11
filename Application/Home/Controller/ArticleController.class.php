<?php 
namespace Home\Controller;
class ArticleController extends BaseController{
    /**
     * 文章详情
     * @return [type] [description]
     */
    public function index(){
        $id = I("id",0);
        $data = M("Article")->where(["status"=>1,"is_del"=>0,"id"=>$id])->find();
        $ip = get_client_ip()."_".$id;
        $ip_lib = S($ip);
        if(!$ip_lib){
            //更新点击次数
            $d["click"] = $data["click"] + 1;
            D("Article")->where(["id"=>$id])->save($d);
            $ip_lib[] = true;
            S($ip,true,86400);
        }

        if( !$data ){
            //throw_exception("404");
            // header("HTTP/1.0 404 Not Found");
            // $this->display();
            //exit("无数据！");
            $this->error('无数据',1);
        }
        // if( $data["is_open"] == 0 ){
        //     $data["content"] = "";
        // }
        if( !empty($data["thumb"]) ){
            $data["thumb"] = C("DOMAIN")."Public/upload/".C("article_thumb_pic").$data["thumb"];
        }
        if( $data ){
            $data["str_add_time"] = date("Y-m-d H:i:s" , $data["add_time"]);
        }
        //获取评论
        $p = I("p",1);
        $limit = 10;
        $comment_list = M("Comment")->alias("c")
            ->field("c.id,c.content,c.add_time,u.nickname,u.head_pic,u.`status`,c.admin_id,u.reg_type")
            ->where(["c.`status`"=>1,"c.pid"=>0,"c.is_message"=>0,"c.article_id"=>$id])
            ->join("LEFT JOIN __USER__ AS u on c.user_id = u.id")
            ->page($p,$limit)
            ->order("c.add_time DESC")
            ->select();
        $comment_count = M("Comment")->alias("c")
            ->field("c.id,c.content,c.add_time,u.nickname,u.head_pic,u.`status`")
            ->where(["c.`status`"=>1,"c.pid"=>0,"c.is_message"=>0,"c.article_id"=>$id])
            ->join("LEFT JOIN __USER__ AS u on c.user_id = u.id")
            ->count();
        //获取回复
        foreach ($comment_list as &$item) {
            $comment_reply_list = M("Comment")->alias("c")
                ->field("c.id,c.content,c.add_time,a.nickname,a.head_pic")
                ->where(["c.`status`"=>1,"c.pid"=>$item["id"]])
                ->join("LEFT JOIN __ADMIN__ AS a on c.admin_id = a.admin_id")
                ->order("c.add_time DESC")
                ->select();
            foreach ($comment_reply_list as &$comment_reply) {
                $comment_reply["str_add_time"] = date("Y-m-d H:i:s" , $comment_reply["add_time"]);
                //$comment_reply["head_pic"] = C("DOMAIN")."Public/upload/".C("admin_head_pic").$comment_reply["head_pic"];
            }
            $item["str_add_time"] = date("Y-m-d H:i:s" , $item["add_time"]);
            if( $item['reg_type'] == 0 ){
                $item["head_pic"] = C("DOMAIN")."Public/upload/".C("user_head_pic").$item["head_pic"];
            }
            $item["comment_reply_list"] = $comment_reply_list;
        }
        $Page = new \Think\Page($comment_count,$limit);
        $show = $Page->show();
        $this->assign("comment_list",$comment_list);
        $this->assign('page',$show);
        $this->assign("data",$data);
        $this->display();
    }

    /**
     * 评论
     * @return [type] [description]
     */
    public function comment(){
        if(!$this->user){
            jsonReturn(0,"请先登录！");
        }
        $this->user = M("User")->where(["id"=>$this->user["id"]])->find();
        if( $this->user["status"] == 1 ){
            jsonReturn(0,"抱歉，您的账号因为违规操作已经封号，解封请联系博主，谢谢合作！");
        }
        if( $this->user["email"] == '' ){
            jsonReturn(2,"未绑定QQ邮箱。");
        }
        $data["article_id"] = I("article_id",0);
        $data["content"] = I("content","");
        $data["user_id"] = $this->user["id"];
        $data["status"] = 1;
        $data["add_time"] = time();
        $res = D("Comment")->add($data);
        if( !$res ){
            jsonReturn(0,"操作失败！");
        }
        jsonReturn(1,"操作成功！");
    }

    //绑定邮箱
    public function email(){
        if(!$this->user)
            jsonReturn(0,"请先登录");
        if(IS_POST){
            $data["email"] = I("email","");
            $res = M("User")->where(["id"=>$this->user["id"]])->save($data);
            if( !$res ){
                jsonReturn(0,"操作失败！");
            }
            jsonReturn(1,"操作成功！");
        }
        $this->display();
    }
}