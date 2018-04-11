<?php
namespace Admin\Controller;
class IndexController extends BaseController {

    function index(){
        $sess_auth = session("auth");
        $this->assign('admin',$sess_auth);
        $this->display();
    }

    function main(){
        $sess_auth = session("auth");
        $data['article_conut'] = M("Article")->where('status = 1 AND is_del = 0')->count();
        $data['comment_conut'] = M("Comment")->where('status = 1 AND is_message = 1 AND pid = 0')->count();
        $data['article_daishenhe_conut'] = M("Article")->where('status = 0 AND is_del = 0')->count();
        $data['user_conut_qq'] = M("User")->where("reg_type = 1")->count();
        $data['user_conut_zc'] = M("User")->where("reg_type = 0")->count();
        $data["last_ip"] = $sess_auth["last_ip"];
        $data["last_login"] = $sess_auth["last_login"];
        $url='http://ip.taobao.com/service/getIpInfo.php?ip='.$data["last_ip"];
        $result = file_get_contents($url);
        $result = json_decode($result,true);
        $data["address"] = $result["data"]["country"].$result["data"]["region"].$result["data"]["city"];
        $this->assign('data',$data);
        $this->display();
    }
}