<?php
// +----------------------------------------------------------------------
// | LTHINK [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2015-2016 http://LTHINK.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 涛哥 <liangtao.gz@foxmail.com>
// +----------------------------------------------------------------------
// TypeEvent.class.php 2015-12-02

namespace Home\Event;

use Think\Controller;
use LT\ThinkSDK\ThinkOauth;

Header('Content-Type: text/html; charset=utf-8');

class TypeEvent extends Controller{
    //登录成功，获取腾讯QQ用户信息
    public function qq($token){
        $qq = ThinkOauth::getInstance('qq', $token);
        $data = $qq->call('user/get_user_info');
        if ($data['ret'] == 0) {
            $user = M("User")->where("openid = '".$token['openid']."'")->find();
            if( !$user ){
                $userInfo['nickname'] = $data['nickname'];
                $userInfo['head_pic'] = $data['figureurl_qq_2'];
                $userInfo['reg_type'] = 1;
                $userInfo['openid'] = $token['openid'];
                $userInfo['last_login'] = time();
                $userInfo['last_ip'] = getIP();
                $userInfo['add_time'] = time();
                $res = D("User")->add($userInfo);

                $session_data["id"] = $res;
                $session_data["nickname"] = $userInfo["nickname"];
                $session_data["head_pic"] = $userInfo["head_pic"];
                $session_data["is_relation"] = $userInfo["is_relation"];
                session("user",$session_data);

                // if( !$res ){
                //     jsonReturn(0,"登录失败，请重新授权！");
                // }
                // jsonReturn(1,"登录成功111！".session('url'));
            }else{
                $d["last_login"] = time();
                $d["last_ip"] = getIP();
                D("User")->where("id = ".$user["id"])->save($d);
                
                $session_data["id"] = $user["id"];
                $session_data["nickname"] = $user["nickname"];
                $session_data["head_pic"] = $user["head_pic"];
                $session_data["is_relation"] = $user["is_relation"];
                session("user",$session_data);
            }

            $url = session('url');
            header("Location:$url");
            //redirect($url,1);
            // if($res !== false) {
            //     jsonReturn(1,"登录成功2222！".session('url'));
            // }else{
            //     jsonReturn(0,"登录失败！");
            // }
        } else {
            //jsonReturn(0,"登录失败，请重新授权！");
            throw_exception("获取腾讯QQ用户信息失败：{$data['msg']}");
        }
    }

    //登录成功，获取腾讯微博用户信息
    public function tencent($token)
    {
        $tencent = ThinkOauth::getInstance('tencent', $token);
        $data = $tencent->call('user/info');

        if ($data['ret'] == 0) {
            $userInfo['type'] = 'TENCENT';
            $userInfo['name'] = $data['data']['name'];
            $userInfo['nick'] = $data['data']['nick'];
            $userInfo['head'] = $data['data']['head'];
            return $userInfo;
        } else {
            throw_exception("获取腾讯微博用户信息失败：{$data['msg']}");
        }
    }

    //登录成功，获取新浪微博用户信息
    public function sina($token)
    {
        $sina = ThinkOauth::getInstance('sina', $token);
        $data = $sina->call('users/show', "uid={$sina->openid()}");

        if ($data['error_code'] == 0) {
            $userInfo['type'] = 'SINA';
            $userInfo['name'] = $data['name'];
            $userInfo['nick'] = $data['screen_name'];
            $userInfo['head'] = $data['avatar_large'];
            return $userInfo;
        } else {
            throw_exception("获取新浪微博用户信息失败：{$data['error']}");
        }
    }

    //登录成功，获取网易微博用户信息
    public function t163($token)
    {
        $t163 = ThinkOauth::getInstance('t163', $token);
        $data = $t163->call('users/show');

        if ($data['error_code'] == 0) {
            $userInfo['type'] = 'T163';
            $userInfo['name'] = $data['name'];
            $userInfo['nick'] = $data['screen_name'];
            $userInfo['head'] = str_replace('w=48&h=48', 'w=180&h=180', $data['profile_image_url']);
            return $userInfo;
        } else {
            throw_exception("获取网易微博用户信息失败：{$data['error']}");
        }
    }

    //登录成功，获取人人网用户信息
    public function renren($token)
    {
        $renren = ThinkOauth::getInstance('renren', $token);
        $data = $renren->call('users.getInfo');

        if (!isset($data['error_code'])) {
            $userInfo['type'] = 'RENREN';
            $userInfo['name'] = $data[0]['name'];
            $userInfo['nick'] = $data[0]['name'];
            $userInfo['head'] = $data[0]['headurl'];
            return $userInfo;
        } else {
            throw_exception("获取人人网用户信息失败：{$data['error_msg']}");
        }
    }

    //登录成功，获取360用户信息
    public function x360($token)
    {
        $x360 = ThinkOauth::getInstance('x360', $token);
        $data = $x360->call('user/me');

        if ($data['error_code'] == 0) {
            $userInfo['type'] = 'X360';
            $userInfo['name'] = $data['name'];
            $userInfo['nick'] = $data['name'];
            $userInfo['head'] = $data['avatar'];
            return $userInfo;
        } else {
            throw_exception("获取360用户信息失败：{$data['error']}");
        }
    }

    //登录成功，获取豆瓣用户信息
    public function douban($token)
    {
        $douban = ThinkOauth::getInstance('douban', $token);
        $data = $douban->call('user/~me');

        if (empty($data['code'])) {
            $userInfo['type'] = 'DOUBAN';
            $userInfo['name'] = $data['name'];
            $userInfo['nick'] = $data['name'];
            $userInfo['head'] = $data['avatar'];
            return $userInfo;
        } else {
            throw_exception("获取豆瓣用户信息失败：{$data['msg']}");
        }
    }

    //登录成功，获取Github用户信息
    public function github($token)
    {
        $github = ThinkOauth::getInstance('github', $token);
        $data = $github->call('user');

        if (empty($data['code'])) {
            $userInfo['type'] = 'GITHUB';
            $userInfo['name'] = $data['login'];
            $userInfo['nick'] = $data['name'];
            $userInfo['head'] = $data['avatar_url'];
            return $userInfo;
        } else {
            throw_exception("获取Github用户信息失败：{$data}");
        }
    }

    //登录成功，获取Google用户信息
    public function google($token)
    {
        $google = ThinkOauth::getInstance('google', $token);
        $data = $google->call('userinfo');

        if (!empty($data['id'])) {
            $userInfo['type'] = 'GOOGLE';
            $userInfo['name'] = $data['name'];
            $userInfo['nick'] = $data['name'];
            $userInfo['head'] = $data['picture'];
            return $userInfo;
        } else {
            throw_exception("获取Google用户信息失败：{$data}");
        }
    }

    //登录成功，获取Google用户信息
    public function msn($token)
    {
        $msn = ThinkOauth::getInstance('msn', $token);
        $data = $msn->call('me');

        if (!empty($data['id'])) {
            $userInfo['type'] = 'MSN';
            $userInfo['name'] = $data['name'];
            $userInfo['nick'] = $data['name'];
            $userInfo['head'] = '微软暂未提供头像URL，请通过 me/picture 接口下载';
            return $userInfo;
        } else {
            throw_exception("获取msn用户信息失败：{$data}");
        }
    }

    //登录成功，获取点点用户信息
    public function diandian($token)
    {
        $diandian = ThinkOauth::getInstance('diandian', $token);
        $data = $diandian->call('user/info');

        if (!empty($data['meta']['status']) && $data['meta']['status'] == 200) {
            $userInfo['type'] = 'DIANDIAN';
            $userInfo['name'] = $data['response']['name'];
            $userInfo['nick'] = $data['response']['name'];
            $userInfo['head'] = "https://api.diandian.com/v1/blog/{$data['response']['blogs'][0]['blogUuid']}/avatar/144";
            return $userInfo;
        } else {
            throw_exception("获取点点用户信息失败：{$data}");
        }
    }

    //登录成功，获取淘宝网用户信息
    public function taobao($token)
    {
        $taobao = ThinkOauth::getInstance('taobao', $token);
        $fields = 'user_id,nick,sex,buyer_credit,avatar,has_shop,vip_info';
        $data = $taobao->call('taobao.user.buyer.get', "fields={$fields}");

        if (!empty($data['user_buyer_get_response']['user'])) {
            $user = $data['user_buyer_get_response']['user'];
            $userInfo['type'] = 'TAOBAO';
            $userInfo['name'] = $user['user_id'];
            $userInfo['nick'] = $user['nick'];
            $userInfo['head'] = $user['avatar'];
            return $userInfo;
        } else {
            throw_exception("获取淘宝网用户信息失败：{$data['error_response']['msg']}");
        }
    }

    //登录成功，获取百度用户信息
    public function baidu($token)
    {
        $baidu = ThinkOauth::getInstance('baidu', $token);
        $data = $baidu->call('passport/users/getLoggedInUser');

        if (!empty($data['uid'])) {
            $userInfo['type'] = 'BAIDU';
            $userInfo['name'] = $data['uid'];
            $userInfo['nick'] = $data['uname'];
            $userInfo['head'] = "http://tb.himg.baidu.com/sys/portrait/item/{$data['portrait']}";
            return $userInfo;
        } else {
            throw_exception("获取百度用户信息失败：{$data['error_msg']}");
        }
    }

    //登录成功，获取开心网用户信息
    public function kaixin($token)
    {
        $kaixin = ThinkOauth::getInstance('kaixin', $token);
        $data = $kaixin->call('users/me');

        if (!empty($data['uid'])) {
            $userInfo['type'] = 'KAIXIN';
            $userInfo['name'] = $data['uid'];
            $userInfo['nick'] = $data['name'];
            $userInfo['head'] = $data['logo50'];
            return $userInfo;
        } else {
            throw_exception("获取开心网用户信息失败：{$data['error']}");
        }
    }

    //登录成功，获取搜狐用户信息
    public function sohu($token)
    {
        $sohu = ThinkOauth::getInstance('sohu', $token);
        $data = $sohu->call('i/prv/1/user/get-basic-info');

        if ('success' == $data['message'] && !empty($data['data'])) {
            $userInfo['type'] = 'SOHU';
            $userInfo['name'] = $data['data']['open_id'];
            $userInfo['nick'] = $data['data']['nick'];
            $userInfo['head'] = $data['data']['icon'];
            return $userInfo;
        } else {
            throw_exception("获取搜狐用户信息失败：{$data['message']}");
        }
    }

}