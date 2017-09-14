<?php
namespace Remind\Controller;

use Think\Controller;

class WorkController extends CommonController {
        public function _initialize() {
        ignore_user_abort(false); //客户端断开终止代码
    }
    
    
    public function index(){
        $dd = $this->getDingTalk();
        $ulist = $dd->getAlluser();
        $mywork = M('user');
        if ($_GET['code']) {
            $dduser = $dd->userinfo($_GET['code']);
            session('ddinfo', $dduser);
            $user = M('work')->where(array('openid' => array('eq', $dduser['user_info']['openid'])))->find();
            if (empty($user)) {
                $type = "1";
                $this->type = $type;
                $this->display();
                exit();
            } else {
                session('m', $user);
            }
        }
        $this->rwl = $rwl;
        $this->display();
    }
    public function jxzwork(){
        
        $this->display();
    }
    public function dfpwork(){
        
        $this->display();
    }
    public function ywcwork(){
        
        $this->display();
    }
    
    public function phpexcel1(){
         $this->display();
    }
    
}