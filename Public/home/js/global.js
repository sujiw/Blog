layui.use(["jquery","layer",'util','form','element','carousel'],function(){
    var $ = layui.jquery
        form = layui.form,
        element = layui.element,
        layer = layui.layer;
        util = layui.util,
        carousel = layui.carousel;
    $(".yzmi-btn").click(function () {
        $(this).attr('src',vertifys+"?_="+Math.random());
    });
    carousel.render({
        elem: '#test1'
        ,width: '100%' //设置容器宽度
        ,arrow: 'always' //始终显示箭头
    });
    form.verify({
        nickname:function(value,item){
            if( $.trim(value) == '' ){
                return '昵称不能为空！';
            }
        },
        username:function(value,item){
            if( $.trim(value) == '' ){
                return '登录名不能为空！';
            }
        },
        vertify:function(value,item){
            if( $.trim(value) == '' ){
                return '验证码不能为空！';
            }
        },
        password:[/^[\S]{6,12}$/,'密码必须6到12位，且不能出现空格!']
    });
    //注册
    form.on("submit(reg)",function(data){
        var post = {};
        post.username = data.field.username;
        post.nickname = data.field.nickname;
        post.password = data.field.password;
        post.vertify = data.field.vertify;
        $.ajax({
            url:data.field.url,
            data:post,
            type:"POST",
            dataType:'json',
            success:function(data){
                if( data.status != 1 ){
                    layer.msg(data.msg,{icon:5});
                    return false;
                }
                layer.msg(data.msg,{icon:6});
                setTimeout(function(){
                    location.reload();
                },1000);
            },
            error:function(){
                layer.msg("网络失败，请刷新页面后重试",{icon:5});
            }
        });
    });
    //登录
    form.on("submit(login)",function(data){
        var post = {};
        post.username = data.field.username;
        post.password = data.field.password;
        post.vertify = data.field.vertify;
        $.ajax({
            url:data.field.url,
            data:post,
            type:"POST",
            dataType:'json',
            success:function(data){
                if( data.status != 1 ){
                    layer.msg(data.msg,{icon:5});
                    return false;
                }
                layer.msg(data.msg,{icon:6});
                setTimeout(function(){
                    location.reload();
                },1000);
            },
            error:function(){
                layer.msg("网络失败，请刷新页面后重试",{icon:5});
            }
        });
    });
    //QQ登录
    $(".qqlogin").on("click",function(){
        layer.msg('正在通过QQ登入', {icon:16, shade: 0.1, time:0})
        window.location.href = $(this).data('url');
    });
    //退出登录
    $(".logout").on('click',function(){
        $.ajax({
            url:$(this).data('url'),
            dataType:'json',
            success:function(data){
                layer.msg(data.msg,{icon:6});
                setTimeout(function(){
                    location.reload();
                },1000);
            },
            error:function(){
                layer.msg("网络失败，请刷新页面后重试",{icon:5});
            }
        });
    });
    //切换验证码
    $(".yzmi-btn").on('click',function(){
        $(this).attr('src',$(this).data('url'));
    });
    util.fixbar({});
    //登录窗口
    $(".userLoginBtn").on("click",function(){
        $("#userLogin input[type!='hidden']").val("");
        $("#userReg input[type!='hidden']").val("");
        $("#userLogin").removeClass('layui-hide');
        $("#userReg").addClass('layui-hide');

    });
    //注册窗口
    $(".userRegBtn").on("click",function(){
        $("#userLogin input[type!='hidden']").val("");
        $("#userReg input[type!='hidden']").val("");
        $("#userLogin").addClass('layui-hide');
        $("#userReg").removeClass('layui-hide');
    });
    //关闭窗口
    $(".model-box .cls").on("click",function(){
        $("#userLogin input[type!='hidden']").val("");
        $("#userReg input[type!='hidden']").val("");
        $(this).parents('.model-box').addClass("layui-hide")
    });
    //我的信息
    $(".myInfo").on("click",function(){
        $(".sm_myInfo").removeClass("layui-hide");
        $("body").css({"overflow-y":"hidden"});
    });
    $(".sm_myInfo .cls").on("click",function(){
        $(".sm_myInfo").addClass("layui-hide");
        $("body").css({"overflow-y":"auto"})
    });
    //头部菜单
    $(".sm_btn").on("click",function(){
        if( $(this).attr("class").indexOf("open") > 0 ){
            $(this).removeClass("open");
            $(".menu-nav").removeClass("open");
            $("body").removeClass("out");
            $("header").removeClass("out");
        }else{
            $(this).addClass("open");
            $(".menu-nav").addClass("open");
            $("body").addClass("out");
            $("header").addClass("out");
        }
    });
    //分类菜单
    $(".sm_cate_btn").on("click",function(){
        if( $(this).attr("class").indexOf("open") > 0 ){
            $(this).removeClass("open");
            $(".sm_cate").removeClass("open");
        }else{
            $(this).addClass("open");
            $(".sm_cate").addClass("open");
        }
    });

    //搜索
    $(".searchBtn").on('click',function(){
        $("#search-box").removeClass("layui-hide");
        $("#search-box").removeClass("layui-anim-fadeout");
        $("#search-box").addClass("layui-anim-fadein");
    });
    $("#search-box #bg").on('click',function(){
        $('#search-box').addClass("layui-anim-fadeout");
        $('#search-box').addClass("layui-hide");
        $("#search-box").removeClass("layui-anim-fadein");
    });
    $("#search-box input").on('keydown',function(e){
        if( e.keyCode == 13 ){
            if($.trim($(this).val()) == ''){
                return false;
            }
            window.location.href = $('#search-url').val()+"/"+$(this).val();
        }
    });
    
    //标题
    document.addEventListener("visibilitychange",function(){
        document.title=document.hidden?"出BUG了,快看!":title
    });
    //复制
    document.body.addEventListener("copy",function(evn){
        var clipboardData = evn.clipboardData||window.clipboardData,
            str = window.getSelection().toString();
        if(clipboardData && str && !(str.length<18) ){
            evn.preventDefault();
            var text=["作者：SuJiWen","来自：SuJiWenBlog","链接："+window.location.href,"",str];
            clipboardData.setData("text/html",text.join("<br>"));
            clipboardData.setData("text/plain",text.join("\n"));
        }
    });
    //console.log()
    try{
        console.log("一个人到底多无聊\r\n 才会把 console 当成玩具\r\n一个人究竟多堕落\r\n 才会把大好青春荒废在博客上\r\n\r\n\r\n%cfollow me %c http://blog.mdaogo.cn","color:red","color:green")
    }catch(evn){}
    //百度自动推送
    var bp = document.createElement('script');
    var curProtocol = window.location.protocol.split(':')[0];
    if (curProtocol === 'https'){
       bp.src = 'https://zz.bdstatic.com/linksubmit/push.js';
    }
    else{
      bp.src = 'http://push.zhanzhang.baidu.com/push.js';
    }
    var s = document.getElementsByTagName("script")[0];
    s.parentNode.insertBefore(bp, s);
});