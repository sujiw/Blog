<?php
namespace Home\Controller;
class IndexController extends BaseController {

    /**
     * 首页列表（文章，分类，用户数量）
     * @return [type] [description]
     */
    public function index(){
        $keywords = I("keywords",'');
        $p = I("p",1);
        $cate_id = I("cate_id",0);
        $limit = 10;
        $order = "a.is_top DESC,a.id DESC";
        //菜单分类
        $cate_list = M("Category")->select();
        //用户总数
        $user_count = M("User")->count();
        //文章总数
        $article_count = M("Article")->where("is_del = 0 AND status = 1")->count();
        //留言总数
        $cmt_count = M("Comment")->where("pid = 0 AND is_message = 1 AND status = 1")->count();
        //热门文章 8条
        $hot_article_list = M("Article")->alias(a)
            ->field("a.id,COUNT(a.id) AS cnt,a.title")
            ->where("c.pid = 0 AND c.`status` = 1 AND c.is_message = 0")
            ->join("LEFT JOIN __COMMENT__ AS c on a.id = c.article_id")
            ->page(1,8)
            ->group("a.id")
            ->order("cnt DESC")
            ->select();
        //点击排行8条
        $click_article_list = M("Article")->page(1,8)->order("click DESC")->select();
        //滚动图
        $img_article_list = M("Article")->where("thumb != ''")->page(1,3)->order("id DESC")->select();
        //搜索
        if( $cate_id == 0 ){
            $where = "is_del = 0 AND a.status = 1 AND a.title LIKE '%".$keywords."%'";
        }else{
            $where = "is_del = 0 AND a.status = 1 AND a.cate_id = $cate_id AND a.title LIKE '%".$keywords."%'";
        }
        $list = M("Article")->alias("a")
            ->field("a.id,admin.nickname,cate.title AS cate_title,a.title,a.thumb,a.abstract,a.click,a.is_top,a.add_time")
            ->where($where)
            ->join("LEFT JOIN __ADMIN__ AS admin on a.admin_id = admin.admin_id")
            ->join("LEFT JOIN __CATEGORY__ AS cate on a.cate_id = cate.id")
            ->page($p,$limit)
            ->order($order)
            ->select();
        $article_list_count = M("Article")->alias("a")
            ->field("a.id,admin.nickname,cate.title AS cate_title,a.title,a.thumb,a.abstract,a.click,a.is_top,a.add_time")
            ->where($where)
            ->join("LEFT JOIN __ADMIN__ AS admin on a.admin_id = admin.admin_id")
            ->join("LEFT JOIN __CATEGORY__ AS cate on a.cate_id = cate.id")
            ->count();
        foreach ($list as &$item) {
            $comment_count = M("Comment")->where("status = 1 AND pid = 0 AND article_id = ".$item["id"])->count();
            if( !empty($item["thumb"]) ){
                $item["thumb"] = C("DOMAIN")."Public/upload/".C("article_thumb_pic").$item["thumb"];
            }
            $item["str_add_time"] = date("Y-m-d H:i:s" , $item["add_time"]);
            $item["comment_count"] = $comment_count;
        }
        $this->assign("list",$list);
        $Page = new \Think\Page($article_list_count,$limit);
        $show = $Page->show();

        $this->assign('page',$show);
        $this->assign('article_list_count',$article_list_count);
        $this->assign('click_article_list',$click_article_list);
        $this->assign('img_article_list',$img_article_list);

        $this->assign('p',$p);
        $this->assign("keywords",$keywords);
        $this->assign("article_count",$article_count);
        $this->assign("comment_count",$cmt_count);
        $this->assign("user_count",$user_count);
        $this->assign("cate_list",$cate_list);
        $this->assign("hot_article_list",$hot_article_list);
        $this->assign("cate_id",$cate_id);
        $this->display();
    }

    public function group(){
        $list = M("Group")->select();
        $this->assign("list",$list);
        $this->display();
    }
    public function resume(){
        $this->display();
    }
}