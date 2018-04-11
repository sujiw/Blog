<?php 
/**
 * 文章管理控制器
 */
namespace Admin\Controller;
use Think\Db;
class ArticleController extends BaseController{

    public function index(){
        $this->assign("type",1);
        $this->assign("is_del",0);
        $this->display();
    }
    public function waitList(){
        $this->assign("type",0);
        $this->assign("is_del",0);
        $this->display("index");
    }
    public function noPassList(){
        $this->assign("type",3);
        $this->assign("is_del",0);
        $this->display("index");
    }

    public function huishou(){
        $this->assign("type",2);
        $this->assign("is_del",1);
        $this->display();
    }

    //文章评论
    public function comment(){
        $id = I("aid",0);
        $this->assign("aid",$id);
        $this->display("comment");
    }
    /**
     * 文章评论回复
     * @return [type] [description]
     */
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
            $data["is_message"] = 0;
            $data["content"] = html_entity_decode(I("content",""));

            $info = M("Comment")->alias("c")
                        ->field("a.title,c.article_id,u.email")
                        ->where("c.id = ".$id)
                        ->join("LEFT JOIN __ARTICLE__ AS a on c.article_id = a.id")
                        ->join("LEFT JOIN __USER__ AS u on c.user_id = u.id")
                        ->find();
            $content = '<h3>你在'.$this->website['title'].'的&lt;&lt;'.$info["title"].'&gt;&gt;评论中，博主回复你啦，赶紧去看看吧。<a href="'.C('DOMAIN').$info["article_id"].'" target="_blank">点击这里前往</a></h3><p style="color: red;font-size: 22px">该邮件由系统自动发送，请勿回复。请认准站长唯一邮箱：<a href="mailto:317723531@qq.com" target="_blank">317723531@q<wbr>q.com</a></p>';
            if($info["email"] == ""){
                $res = D("Comment")->add($data);
                if(!$res){
                    jsonReturn( 0 , "操作失败！");
                }
                adminLog("回复文章评论");
                jsonReturn( 1 , "操作成功！");
            }else{
                $rs = think_send_mail($info["email"],'尊敬的用户',$this->website['title'].'回复通知！',$content);
                if( $rs == 1 ){
                    $res = D("Comment")->add($data);
                    if(!$res){
                        jsonReturn( 0 , "操作失败！");
                    }
                    adminLog("回复文章评论");
                    jsonReturn( 1 , "操作成功！");
                }else{
                    jsonReturn( 0 , $rs);
                }
            }
        }
        $id = I("id",0);
        $res = M("Comment")->where("id = $id")->find();
        $reply_list = M("Comment")->where("pid = $id")->order("add_time DESC")->select();
        foreach ($reply_list as &$item) {
            $item["str_add_time"] = date("Y-m-d H:i:s" , $item["add_time"]);
        }
        $this->assign("comment",$res);
        $this->assign("reply_list",$reply_list);
        $this->display("reply");
    }

    /**
     * 删除评论
     * @return [type] [description]
     */
    public function comment_del(){
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
                adminLog('删除评论及回复');
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
    /**
     * 删除评论回复
     * @return [type] [description]
     */
    public function comment_reply_del(){
        $id = I("id",0);
        $res = D("Comment")->where("id = $id")->delete();
        if(!$res) {
            jsonReturn(0,"操作失败！");
        }
        adminLog('删除评论的回复');
        jsonReturn(1,"操作成功！");
    }
    /**
     * 添加文章
     */
    public function add(){
        if(IS_POST){
            $article = M("Article");
			$data = I("post.","");
            if(!empty($data)){
                $data = $article->create();
            }
			else 
				$data = [];
            $sess_auth = session('auth');
            if(!sess_auth){
                $this->error("用户信息已经过期，正在跳转登录页面",U('Admin/Admin/login'),1);
            }
            $data["admin_id"] = $sess_auth["admin_id"];
            $data["add_time"] = time();
            $res = D("Article")->add($data);
            if(!$res){
                jsonReturn( 0 , "操作失败！");
            }
            adminLog('添加文章');
            jsonReturn( 1 , "操作成功！");
        }
        $cate_list = M("Category")->select();
        $this->assign("cate_list",$cate_list);
        $this->display();
    }

    /**
     * 修改文章
     * @return [type] [description]
     */
    public function edit(){
        if(IS_POST){
            $article = M("Article");
			$data = I("post.","");
            if(!empty($data)){
                $data = $article->create();
            }
			else
				$data = [];
            $sess_auth = session('auth');
            if(!sess_auth){
                $this->error("用户信息已经过期，正在跳转登录页面",U('Admin/Admin/login'),1);
            }
            $count = M("Article")->where("id = ".$data["id"])->count();
            if( $count == 0 ){
                jsonReturn(0,"无数据！");
            }
            $res = D("Article")->save($data);
            if($res !== false) {
                adminLog('修改文章');
                jsonReturn(1,"操作成功！");
            }else{
                jsonReturn(0,"操作失败！");
            }
        }
        $id = I("id",0);
        $data = M("Article")->where("id = $id")->find();
        if( !$data ){
            exit("无数据！");
        }
        if( !empty($data["thumb"]) ){
            $data["thumb_src"] = C("DOMAIN")."Public/upload/".C("article_thumb_pic").$data["thumb"];
        }
        $cate_list = M("Category")->select();
        $this->assign("cate_list",$cate_list);
        $this->assign("data",$data);
        $this->display();
    }

    /**
     * 文章加密
     * @return [type] [description]
     */
    public function jiami(){
        if(IS_POST){
            $id_arr = I("id_arr","");
            if( strlen($id_arr) > 0 ){
                $model = D("Article");
                $model->startTrans();
                try{
                    $data["is_open"] = 0;
                    $data["browse_pwd"] = I("browse_pwd","123456");
                    $where['id'] = array('in',$id_arr);
                    $res = $model->where($where)->save($data);
                    if($res !== false) {
                        adminLog('加密文章');
                        jsonReturn(1,"操作成功！");
                    }else{
                        jsonReturn(0,"操作失败！");
                    }
                } catch (Exception $e) {
                    // 回滚事务
                    $model->rollback();
                    jsonReturn(0,"操作失败！");
                }
            }else{
                jsonReturn(0 , '操作失败！');
            }
        }
        $id_arr = I("id_arr","");
        $this->assign("id_arr",$id_arr);
        $this->display();
    }

    /**
     * 取消文章加密
     * @return [type] [description]
     */
    public function qxjiami(){
        $id_arr = I("id_arr","");
        if( strlen($id_arr) > 0 ){
            $model = D("Article");
            $model->startTrans();
            try{
                $data["is_open"] = 1;
                $data["browse_pwd"] = "";
                $where['id'] = array('in',$id_arr);
                $res = $model->where($where)->save($data);
                if($res !== false) {
                    adminLog('取消文章加密');
                    jsonReturn(1,"操作成功！");
                }else{
                    jsonReturn(0,"操作失败！");
                }
            } catch (Exception $e) {
                // 回滚事务
                $model->rollback();
                jsonReturn(0,"操作失败！");
            }
        }else{
            jsonReturn(0 , '操作失败！');
        }
    }

    /**
     * 文章审核
     * @return [type] [description]
     */
    public function shenhe(){
        $id_arr = I("id_arr","");
        if( strlen($id_arr) > 0 ){
            $model = D("Article");
            $model->startTrans();
            try{
                $data["status"] = 1;
                $data["no_pass_reason"] = "";
                $where['id'] = array('in',$id_arr);
                $res = $model->where($where)->save($data);
                if($res !== false) {
                    adminLog('文章审核');
                    jsonReturn(1,"操作成功！");
                }else{
                    jsonReturn(0,"操作失败！");
                }
            } catch (Exception $e) {
                // 回滚事务
                $model->rollback();
                jsonReturn(0,"操作失败！");
            }
        }else{
            jsonReturn(0 , '操作失败！');
        }
    }

    /**
     * 文章审核不通过
     * @return [type] [description]
     */
    public function nopass(){
        if(IS_POST){
            $id_arr = I("id_arr","");
            if( strlen($id_arr) > 0 ){
                $model = D("Article");
                $model->startTrans();
                try{
                    $data["status"] = 3;
                    $data["no_pass_reason"] = I("no_pass_reason","");
                    $where['id'] = array('in',$id_arr);
                    $res = $model->where($where)->save($data);
                    if($res !== false) {
                        adminLog('拒绝文章审核');
                        jsonReturn(1,"操作成功！");
                    }else{
                        jsonReturn(0,"操作失败！");
                    }
                } catch (Exception $e) {
                    // 回滚事务
                    $model->rollback();
                    jsonReturn(0,"操作失败！");
                }
            }else{
                jsonReturn(0 , '操作失败！');
            }
        }
        $id_arr = I("id_arr","");
        $this->assign("id_arr",$id_arr);
        $this->display();
    }

    /**
     * 文章置顶
     * @return [type] [description]
     */
    public function zhiding(){
        $id_arr = I("id_arr","");
        if( strlen($id_arr) > 0 ){
            $model = D("Article");
            $model->startTrans();
            try{
                $data["is_top"] = 1;
                $where['id'] = array('in',$id_arr);
                $res = $model->where($where)->save($data);
                if($res !== false) {
                    adminLog('文章置顶');
                    jsonReturn(1,"操作成功！");
                }else{
                    jsonReturn(0,"操作失败！");
                }
            } catch (Exception $e) {
                // 回滚事务
                $model->rollback();
                jsonReturn(0,"操作失败！");
            }
        }else{
            jsonReturn(0 , '操作失败！');
        }
    }

    /**
     * 取消置顶
     * @return [type] [description]
     */
    public function qxzhiding(){
        $id_arr = I("id_arr","");
        if( strlen($id_arr) > 0 ){
            $model = D("Article");
            $model->startTrans();
            try{
                $data["is_top"] = 0;
                $where['id'] = array('in',$id_arr);
                $res = $model->where($where)->save($data);
                if($res !== false) {
                    adminLog('取消置顶');
                    jsonReturn(1,"操作成功！");
                }else{
                    jsonReturn(0,"操作失败！");
                }
            } catch (Exception $e) {
                // 回滚事务
                $model->rollback();
                jsonReturn(0,"操作失败！");
            }
        }else{
            jsonReturn(0 , '操作失败！');
        }
    }

    /**
     * 假删除
     * @return [type] [description]
     */
    public function dels(){
        $id_arr = I("id_arr","");
        if( strlen($id_arr) > 0 ){
            $model = D("Article");
            $model->startTrans();
            try{
                $data["is_del"] = 1;
                $data["del_time"] = time();
                $where['id'] = array('in',$id_arr);
                $res = $model->where($where)->save($data);
                if($res !== false) {
                    adminLog('文章假删除');
                    jsonReturn(1,"操作成功！");
                }else{
                    jsonReturn(0,"操作失败！");
                }
            } catch (Exception $e) {
                // 回滚事务
                $model->rollback();
                jsonReturn(0,"操作失败！");
            }
        }else{
            jsonReturn(0 , '操作失败！');
        }
    }

    /**
     * 真删除
     * @return [type] [description]
     */
    public function zhendels(){
        $id_arr = I("id_arr","");
        if( strlen($id_arr) > 0 ){
            $model = D("Article");
            $model->startTrans();
            try{
                $where['id'] = array('in',$id_arr);
                $res = $model->where($where)->delete();
                if($res !== false) {
                    adminLog('文章从回收站删除');
                    jsonReturn(1,"操作成功！");
                }else{
                    jsonReturn(0,"操作失败！");
                }
            } catch (Exception $e) {
                // 回滚事务
                $model->rollback();
                jsonReturn(0,"操作失败！");
            }
        }else{
            jsonReturn(0 , '操作失败！');
        }
    }

    /**
     * 文章从回收站恢复
     * @return [type] [description]
     */
    public function huifu(){
        $id_arr = I("id_arr","");
        if( strlen($id_arr) > 0 ){
            $model = D("Article");
            $model->startTrans();
            try{
                $data["is_del"] = 0;
                $data["del_time"] = 0;
                $where['id'] = array('in',$id_arr);
                $res = $model->where($where)->save($data);
                if($res !== false) {
                    adminLog('文章从回收站恢复');
                    jsonReturn(1,"操作成功！");
                }else{
                    jsonReturn(0,"操作失败！");
                }
            } catch (Exception $e) {
                // 回滚事务
                $model->rollback();
                jsonReturn(0,"操作失败！");
            }
        }else{
            jsonReturn(0 , '操作失败！');
        }
    }


    /**
     * 上传缩略图
     * @return [type] [description]
     */
    public function thumbUpload(){
        $info = uploadImage($_FILES["file"] , C("article_thumb_pic"));
        if( $info["code"] == 0 ){
            jsonReturn( 0 , $info["msg"]);
        }
        $d["src"] = C("DOMAIN")."Public/upload/".C("article_thumb_pic").$info["file"]["savename"];
        $d["name"] = $info["file"]["savename"];
        jsonReturn(1 , '操作成功！',$d);
    }

    /**
     * 文章内容图片上传
     * @return [type] [description]
     */
    public function contentImagesUpload(){
        $info = uploadImage($_FILES["file"] , C("article_content_pic"));
        if( $info["code"] == 0 ){
            $data["success"] = 0;
            $data["message"] = $info["msg"];
            $data["url"] = "";
            exit(json_encode($data));
        }
        $data["success"] = 1;
        $data["message"] = "操作成功！";
        $data["url"] = C("DOMAIN")."Public/upload/".C("article_content_pic").$info["editormd-image-file"]["savename"];
        exit(json_encode($data));
    }
}