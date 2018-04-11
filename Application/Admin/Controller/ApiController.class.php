<?php 
namespace Admin\Controller;
class ApiController extends BaseController{
    /**
     * 规则列表，搜索
     * @return [type] [description]
     */
    public function getAuthRuleList(){
        $keywords = I("keywords",'');
        $page = I("page",1);
        $limit = I("limit",10);
        if( !empty($keywords) ){
            $list = M("AuthRule")->where("title LIKE '%".$keywords."%'")->page($page,$limit)->select();
            $count = M("AuthRule")->where("title LIKE '%".$keywords."%'")->count();
        }else{
            $list = M("AuthRule")->page($page,$limit)->select();
            $count = M("AuthRule")->count();
        }
        $data["code"]=0;
        $data["msg"]="操作成功！";
        $data["count"]=$count;
        $data["data"]=$list;
        if( $count == 0 ){
            $data["msg"]="暂无数据！";
        }
        exit(json_encode($data));
    }
    /**
     * 用户组列表，搜索
     * @return [type] [description]
     */
    public function getAuthGroupList(){
        $keywords = I("keywords",'');
        $page = I("page",1);
        $limit = I("limit",10);
        if( !empty($keywords) ){
            $list = M("AuthGroup")->where("title LIKE '%".$keywords."%'")->page($page,$limit)->select();
            $count = M("AuthGroup")->where("title LIKE '%".$keywords."%'")->count();
        }else{
            $list = M("AuthGroup")->page($page,$limit)->select();
            $count = M("AuthGroup")->count();
        }
        $data["code"]=0;
        $data["msg"]="操作成功！";
        $data["count"]=$count;
        $data["data"]=$list;
        if( $count == 0 ){
            $data["msg"]="暂无数据！";
        }
        exit(json_encode($data));
    }

    /**
     * 管理员列表
     * @return [type] [description]
     */
    public function getAdminList(){
        $keywords = I("keywords",'');
        $page = I("page",1);
        $limit = I("limit",10);
        if( !empty($keywords) ){
            $list = M("Admin")->where("login_name LIKE '%".$keywords."%'")->page($page,$limit)->select();
            $count = M("Admin")->where("login_name LIKE '%".$keywords."%'")->count();
        }else{
            $list = M("Admin")->page($page,$limit)->select();
            $count = M("Admin")->count();
        }
        $auth_group_list = M("auth_group")->select();
        $auth_group_access_list = M("auth_group_access")->select();
        foreach ($list as &$item) {
            $title = M("auth_group")->alias("ag")
                                    ->field("ag.title")
                                    ->where("a.admin_id = ".$item["admin_id"])
                                    ->join("LEFT JOIN __AUTH_GROUP_ACCESS__ AS aga on aga.group_id = ag.id")
                                    ->join("LEFT JOIN __ADMIN__ AS a on a.admin_id = aga.uid")
                                    ->find();

            $item["str_time"] = date("Y-m-d H:i:s" , $item["add_time"]);
            $item["auth_group_title"] = $title["title"];
        }
        $data["code"]=0;
        $data["msg"]="操作成功！";
        $data["count"]=$count;
        $data["data"]=$list;
        if( $count == 0 ){
            $data["msg"]="暂无数据！";
        }
        exit(json_encode($data));
    }

    /**
     * 管理员日志列表
     * @return [type] [description]
     */
    public function getAdminLogList(){
        $keywords = I("keywords",'');
        $page = I("page",1);
        $limit = I("limit",10);
        if( !empty($keywords) ){
            $list = M("admin_log")->alias("al")
                        ->field("al.log_id,al.log_info,al.log_ip,al.log_time,a.nickname")
                        ->where("a.nickname LIKE '%".$keywords."%'")
                        ->join("LEFT JOIN __ADMIN__ AS a on a.admin_id = al.admin_id")
                        ->page($page,$limit)
                        ->order("al.log_time desc")
                        ->select();
            $count = M("admin_log")->alias("al")
                        ->field("al.log_id,al.log_info,al.log_ip,al.log_time,a.nickname")
                        ->where("a.nickname LIKE '%".$keywords."%'")
                        ->join("LEFT JOIN __ADMIN__ AS a on a.admin_id = al.admin_id")
                        ->count();
        }else{
            $list = M("admin_log")->alias("al")
                        ->field("al.log_id,al.log_info,al.log_ip,al.log_time,a.nickname")
                        ->join("LEFT JOIN __ADMIN__ AS a on a.admin_id = al.admin_id")
                        ->page($page,$limit)
                        ->order("al.log_time desc")
                        ->select();
            $count = M("admin_log")->alias("al")
                        ->field("al.log_id,al.log_info,al.log_ip,al.log_time,a.nickname")
                        ->join("LEFT JOIN __ADMIN__ AS a on a.admin_id = al.admin_id")
                        ->count();
        }
        foreach ($list as &$item) {
            $item["str_time"] = date("Y-m-d H:i:s" , $item["log_time"]);
        }
        $data["code"]=0;
        $data["msg"]="操作成功！";
        $data["count"]=$count;
        $data["data"]=$list;
        if( $count == 0 ){
            $data["msg"]="暂无数据！";
        }
        exit(json_encode($data));
    }

    /**
     * 文章分类
     * @return [type] [description]
     */
    public function getCategoryList(){
        $keywords = I("keywords",'');
        $page = I("page",1);
        $limit = I("limit",10);
        if( !empty($keywords) ){
            $list = M("Category")->where("title LIKE '%".$keywords."%'")->page($page,$limit)->select();
            $count = M("Category")->where("title LIKE '%".$keywords."%'")->count();
        }else{
            $list = M("Category")->page($page,$limit)->select();
            $count = M("Category")->count();
        }
        $data["code"]=0;
        $data["msg"]="操作成功！";
        $data["count"]=$count;
        $data["data"]=$list;
        if( $count == 0 ){
            $data["msg"]="暂无数据！";
        }
        exit(json_encode($data));
    }

    /**
     * 文章列表
     * @return [type] [description]
     */
    public function getArticleList(){
        $type = I("type",0);
        $is_del = I("is_del",0);
        $keywords = I("keywords",'');
        $page = I("page",1);
        $limit = I("limit",10);
        $order = "a.is_top DESC,a.id DESC";
        if( !empty($keywords) ){
            if( $is_del == 1 ){
                $where = "is_del = ".$is_del." AND a.title LIKE '%".$keywords."%'";
                $order = "a.del_time DESC,a.id DESC";
            }else{
                $where = "is_del = ".$is_del." AND status = ".$type." AND a.title LIKE '%".$keywords."%'";
            }
            $list = M("Article")->alias("a")
                        ->field("a.id,admin.nickname,cate.title AS cate_title,a.title,a.`status`,a.is_open,a.no_pass_reason,a.browse_pwd,a.is_comment,a.is_top,a.add_time,a.is_del,a.del_time")
                        ->where($where)
                        ->join("LEFT JOIN __ADMIN__ AS admin on a.admin_id = admin.admin_id")
                        ->join("LEFT JOIN __CATEGORY__ AS cate on a.cate_id = cate.id")
                        ->page($page,$limit)
                        ->order($order)
                        ->select();
            $count = M("Article")->alias("a")
                        ->field("a.id,admin.nickname,cate.title AS cate_title,a.title,a.`status`,a.is_open,a.no_pass_reason,a.browse_pwd,a.is_comment,a.is_top,a.add_time,a.is_del,a.del_time")
                        ->where($where)
                        ->join("LEFT JOIN __ADMIN__ AS admin on a.admin_id = admin.admin_id")
                        ->join("LEFT JOIN __CATEGORY__ AS cate on a.cate_id = cate.id")
                        ->order("a.is_top DESC,a.id DESC")
                        ->count();
        }else{
            if( $is_del == 1 ){
                $where = "is_del = ".$is_del;
                $order = "a.del_time DESC,a.id DESC";
            }else{
                $where = "is_del = ".$is_del." AND status = ".$type;
            }
            $list = M("Article")->alias("a")
                        ->field("a.id,admin.nickname,cate.title AS cate_title,a.title,a.`status`,a.is_open,a.no_pass_reason,a.browse_pwd,a.is_comment,a.is_top,a.add_time,a.is_del,a.del_time")
                        ->where($where)
                        ->join("LEFT JOIN __ADMIN__ AS admin on a.admin_id = admin.admin_id")
                        ->join("LEFT JOIN __CATEGORY__ AS cate on a.cate_id = cate.id")
                        ->page($page,$limit)
                        ->order($order)
                        ->select();
            $count = M("Article")->alias("a")
                        ->field("a.id,admin.nickname,cate.title AS cate_title,a.title,a.`status`,a.is_open,a.no_pass_reason,a.browse_pwd,a.is_comment,a.is_top,a.add_time,a.is_del,a.del_time")
                        ->where($where)
                        ->join("LEFT JOIN __ADMIN__ AS admin on a.admin_id = admin.admin_id")
                        ->join("LEFT JOIN __CATEGORY__ AS cate on a.cate_id = cate.id")
                        ->order($order)
                        ->count();
        }
        foreach ($list as &$item) {
            $item["str_time"] = date("Y-m-d H:i:s" , $item["add_time"]);
            $item["str_del_time"] = date("Y-m-d H:i:s" , $item["del_time"]);
        }
        $data["code"]=0;
        $data["msg"]="操作成功！";
        $data["count"]=$count;
        $data["data"]=$list;
        if( $count == 0 ){
            $data["msg"]="暂无数据！";
        }
        exit(json_encode($data));
    }

    /**
     * 用户列表
     * @return [type] [description]
     */
    public function getUserList(){
        $keywords = I("keywords",'');
        $page = I("page",1);
        $limit = I("limit",10);
        $order = "id DESC,add_time DESC";
        if( !empty($keywords) ){
            $list = M("User")->where("nickname LIKE '%".$keywords."%' OR username LIKE '%".$keywords."%'")->page($page,$limit)->order($order)->select();
            $count = M("User")->where("nickname LIKE '%".$keywords."%' OR username LIKE '%".$keywords."%'")->count();
        }else{
            $list = M("User")->page($page,$limit)->order($order)->select();
            $count = M("User")->count();
        }
        foreach ($list as &$item) {
            if( $item['reg_type'] == 0 ){
                $item["head_pic"] = C("DOMAIN")."Public/upload/".C("user_head_pic").$item["head_pic"];
            }
            $item["str_last_login"] = date("Y-m-d H:i:s" , $item["last_login"]);
            $item["str_add_time"] = date("Y-m-d H:i:s" , $item["add_time"]);
        }
        $data["code"]=0;
        $data["msg"]="操作成功！";
        $data["count"]=$count;
        $data["data"]=$list;
        if( $count == 0 ){
            $data["msg"]="暂无数据！";
        }
        exit(json_encode($data));
    }

    /**
     * 获取留言板的留言列表
     * @return [type] [description]
     */
    public function getCommentList(){
        $keywords = I("keywords",'');
        $page = I("page",1);
        $limit = I("limit",10);
        
        $order = "c.add_time DESC,c.id DESC";
        if( !empty($keywords) ){
            $where = "c.pid = 0 AND c.is_message = 1 AND c.`status` = 1 AND c.content LIKE '%".$keywords."%'";
        }else{
            $where = "c.pid = 0 AND c.is_message = 1 AND c.`status` = 1";
        }
        $list = M("Comment")->alias(c)
            ->field("c.id,u.head_pic,u.reg_type,u.nickname,u.`status`,c.content,c.admin_id,c.add_time")
            ->where($where)
            ->join("LEFT JOIN __USER__ AS u on c.user_id = u.id")
            ->page($page,$limit)
            ->order($order)
            ->select();
        $count = M("Comment")->alias(c)
            ->field("c.id,u.head_pic,u.reg_type,u.nickname,u.`status`,c.content,c.admin_id,c.add_time")
            ->where($where)
            ->join("LEFT JOIN __USER__ AS u on c.user_id = u.id")
            ->order($order)
            ->select();
        foreach ($list as &$item) {
            if( $item['reg_type'] == 0 ){
                $item["head_pic"] = C("DOMAIN")."Public/upload/".C("user_head_pic").$item["head_pic"];
            }
            $item["str_add_time"] = date("Y-m-d H:i:s" , $item["add_time"]);
        }
        $data["code"]=0;
        $data["msg"]="操作成功！";
        $data["count"]=$count;
        $data["data"]=$list;
        if( $count == 0 ){
            $data["msg"]="暂无数据！";
        }
        exit(json_encode($data));
    }

    /**
     * 获取文章的评论列表
     * @return [type] [description]
     */
    public function getArticleCommentList(){
        $keywords = I("keywords",'');
        $page = I("page",1);
        $limit = I("limit",10);
        $aid = I("aid",0);
        $order = "c.add_time DESC,c.id DESC";
        if( !empty($keywords) ){
            $where = "c.article_id = ".$aid." AND c.pid = 0 AND c.is_message = 0 AND c.`status` = 1 AND c.content LIKE '%".$keywords."%'";
        }else{
            $where = "c.article_id = ".$aid." AND c.pid = 0 AND c.is_message = 0 AND c.`status` = 1";
        }
        $list = M("Comment")->alias(c)
            ->field("c.id,u.head_pic,u.reg_type,u.nickname,u.`status`,c.content,c.admin_id,c.add_time")
            ->where($where)
            ->join("LEFT JOIN __USER__ AS u on c.user_id = u.id")
            ->page($page,$limit)
            ->order($order)
            ->select();
        $count = M("Comment")->alias(c)
            ->field("c.id,u.head_pic,u.reg_type,u.nickname,u.`status`,c.content,c.admin_id,c.add_time")
            ->where($where)
            ->join("LEFT JOIN __USER__ AS u on c.user_id = u.id")
            ->order($order)
            ->select();
        foreach ($list as &$item) {
            if( $item['reg_type'] == 0 ){
                $item["head_pic"] = C("DOMAIN")."Public/upload/".C("user_head_pic").$item["head_pic"];
            }
            $item["str_add_time"] = date("Y-m-d H:i:s" , $item["add_time"]);
        }
        $data["code"]=0;
        $data["msg"]="操作成功！";
        $data["count"]=$count;
        $data["data"]=$list;
        if( $count == 0 ){
            $data["msg"]="暂无数据！";
        }
        exit(json_encode($data));
    }

    /**
     * 获取前端圈子的列表
     * @return [type] [description]
     */
    public function getGroupList(){
        $keywords = I("keywords",'');
        $page = I("page",1);
        $limit = I("limit",10);
        $order = "id DESC,add_time DESC";
        if( !empty($keywords) ){
            $list = M("Group")->where("title LIKE '%".$keywords."%'")->page($page,$limit)->order($order)->select();
            $count = M("Group")->where("title LIKE '%".$keywords."%'")->count();
        }else{
            $list = M("Group")->page($page,$limit)->order($order)->select();
            $count = M("Group")->count();
        }
        foreach ($list as &$item) {
            $item["str_add_time"] = date("Y-m-d H:i:s" , $item["add_time"]);
        }
        $data["code"]=0;
        $data["msg"]="操作成功！";
        $data["count"]=$count;
        $data["data"]=$list;
        if( $count == 0 ){
            $data["msg"]="暂无数据！";
        }
        exit(json_encode($data));
    }
}