<?php
namespace Remind\Controller;

use Think\Controller;

class IndexController extends CommonController {
        public function _initialize() {
        //ACTIVE
        $m = session('m');
        if (empty($m)) {
            IS_AJAX && $this->ajaxRtn('请登录后操作');
            $this->redirect('login/login');
            die;
        }
        ignore_user_abort(false); //客户端断开终止代码
    }
    
    
    public function index(){
        $dd = $this->getDingTalk();
        $mywork = M('user');
        if ($_GET['code']) {
            $dduser = $dd->userinfo($_GET['code']);
            print_r($dduser);die;
            $user = M('user')->where(array('openid' => array('eq', $dduser['user_info']['openid'])))->find();
            if(!empty($user)){
                
            }else{
                
            }
            if (empty($user)) {
                $type = "1";
                $this->type = $type;
                $this->display();
                exit();
            } else {
                session('m', $user);
            }
        }
        $this->display();
    }
    public function userwork(){
        
        $this->display();
    }
    public function tuanwork(){
        
        $this->display();
    }
    
    public function phpexcel1(){
         $this->display();
    }
    
}