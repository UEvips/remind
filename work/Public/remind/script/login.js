$(function() {
    //输入框图标染色
    $('input.txt').on('focus', function() {
        $(this).siblings('.icon').css('color', '#6db738');
    });
    $('input.txt').on('blur', function() {
        $(this).siblings('.icon').css('color', '#6e6e6e');
    });
    //登录方式切换
    $('.form_head li').on('click', function() {
        $(this).addClass('active').siblings('li').removeClass('active'); //样式
        if ($(this).data('id') == '1') {
            $('.form_box form').css('display', 'block');
            $('.code_box').css('display', 'none');
        } else {
            $('.form_box form').css('display', 'none');
            $('.code_box').css('display', 'block');
            //获取二维码 实例
            $('#login_container').html('<div id="loading"><span></span><span></span><span></span><span></span><span></span><p>loading..</p></div>'); //等待动画
            $.ajax({
                async: false,
                url: 'http://work.cdxtime.com/remind.php/login/DDlogin.html',
                type: "GET",
                dataType: 'jsonp',
                jsonp: 'jsoncallback',
                timeout: 5000,
                success: function(ret) {
                    var url = ret.url,
                        url1 = ret.url1;
                    var obj = DDLogin({
                        id: "login_container",
                        goto: url,
                        style: "border:none;background-color:#fff;color:#fff;font-size:18px;width:260px;margin:0 auto;height:100%;border-radius:0;",
                        href: "",
                        width: "670",
                        height: "307"
                    });
                    var hanndleMessage = function(event) {
                        var origin = event.origin;
                        var loginTmpCode = event.data; //拿到loginTmpCode后就可以在这里构造跳转链接进行跳转了
                        location.href = url1 + "&loginTmpCode=" + loginTmpCode;
                    };
                    if (typeof window.addEventListener != 'undefined') {
                        window.addEventListener('message', hanndleMessage, false);
                        $('#loading').remove(); //取消动画
                    } else if (typeof window.attachEvent != 'undefined') {
                        window.attachEvent('onmessage', hanndleMessage);
                        $('#loading').remove(); //取消动画
                    }
                }
            });
        }
    })
    //账号密码登录
    $('button[type=submit]').on('click', function(){
        var user = $('#user').val(),
            pwd = $('#pwd').val();
        user&&pwd?ajaxForm(user,pwd):back();
    })
    function ajaxForm(user,pwd){
            $.ajax({
                    type: "POST",
                    url: "./login/login.html",
                    data:$("#form").serialize(),
                    dataType: "json",
                    success: function(ret){
                        if(ret.status=="1"){
                            location.href=ret.result.url;
                        }else{
                            
                        }
                    }
                })

    }
    function back(){
        return;
    }
    //忘记密码
    var bol = true;
    $('#forget').on('click',function(){
        if(bol){
            $('.toast').addClass('active');
            bol = false;
            setTimeout(function(){
                $('.toast').removeClass('active');
                bol = true;
            },1200) 
        }
    })
})