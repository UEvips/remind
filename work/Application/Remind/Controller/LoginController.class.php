<?php

namespace Remind\Controller;

use Think\Controller;

class LoginController extends CommonController {

    public function login() {

        session_start();
//        print_r($_COOKIE);die;
        $db = M('user');
        if (IS_AJAX) {
            $uname = I('post.user');
            $pwd = I('post.pwd');
            if (is_numeric($uname)){
                $user = $db->where(array('phone' => array('eq', $uname)))->find();
            } else {
                $user = $db->where(array('username' => array('eq', $uname)))->find();
            }
            if (!empty($user)) {
                if (md5($pwd . KEY) === $user['pwd']) {
                    session('logoimg', $user ['logoimg']);
                    session('openid', $user['openid']);
                    session('m', $user);
                    $this->setcookie($user['iscookie'], $pwd, $uname);
                    $log = get_log_data('user');
                    $log['cont'] = $log['desc'] = '登录了系统';
                    add_log($log);

                    show_json(1, array('url' => U('index/index')));
                } else {
                    show_json(0, "密码错误!");
                }
            } else {
                show_json(0, "该用户不存在");
            }
        } else if (isset($_COOKIE['user']) && $_COOKIE['user']['isc']=="1"){
            $uname = $_COOKIE['user']['user'];
            $pwd   = $_COOKIE['user']['pwd'];
            if (is_numeric($uname)) {
                $user = $db->where(array('phone' => array('eq', $uname)))->find();
            } else {
                $user = $db->where(array('username' => array('eq', $uname)))->find();
            }
            if (!empty($user)) {
                if (md5($pwd . KEY) === $user['pwd']) {
                    session('logoimg', $user ['logoimg']);
                    session('openid', $user['openid']);
                    session('m', $user);
                    $this->setcookie($user['iscookie'], $pwd, $uname);
                    $log = get_log_data('user');
                    $log['cont'] = $log['desc'] = '登录了系统';
                    add_log($log);
                    $this->redirect('index/index');
                } else {
                   $this->display();
                }
            } else {
                $this->display();
            }
        }
        $this->display();
    }



    function setcookie($iscookie, $pwd, $uname) {
        $db = M('user');
        if (I('post.iscookie') == "1") {
            if ($iscookie != "1") {
                setcookie("user[user]", $uname, time() + 3600 * 24 * 30, '/');
                setcookie("user[pwd]", $pwd, time() + 3600 * 24 * 30, '/');
                setcookie("user[isc]", 1, time() + 3600 * 24 * 30, '/');
                $db->where(array('username' => array('eq', $uname)))->setField('iscookie', '1');
            }
        }else{
                setcookie("user[user]", $uname, time() - 3600 * 24 * 30, '/');
                setcookie("user[pwd]", $pwd, time() - 3600 * 24 * 30, '/');
                setcookie("user[isc]", 0, time() - 3600 * 24 * 30, '/');
                $db->where(array('username' => array('eq', $uname)))->setField('iscookie', '0');
            }
        return;
    }

    public function DDlogin() {
        $dd = $this->getDingTalk();
        $data = $dd->changman();
        $dduser = $dd->getlogin();
        $url['url'] = urlencode("https://oapi.dingtalk.com/connect/oauth2/sns_authorize?appid=" . $dduser['appid'] . "&response_type=code&scope=snsapi_login&state=STATE&redirect_uri=" . $dduser['huiurl']);
        $url['url1'] = "https://oapi.dingtalk.com/connect/oauth2/sns_authorize?appid=" . $dduser['appid'] . "&response_type=code&scope=snsapi_login&state=STATE&redirect_uri=" . $dduser['huiurl'];
        echo $_GET['jsoncallback'] . "(" . json_encode($url) . ")";
    }

    public function loginout() {
        
        //日志
        $log = get_log_data('customer');
        $log['cont'] = $log['desc'] = '退出了系统';
        $log['type'] = -1;
        $log['user'] =  $_SESSION['m']['username'];
        M('user')->where(array('id' => array('eq', $_SESSION['m']['id'])))->setField('iscookie', '1');
        add_log($log);
        session_unset ();
        session_destroy ();
        $this->display('login/login');
    }

}
