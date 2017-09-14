<?php

namespace Remind\Controller;

use Think\Controller;

class MemberController extends CommonController {
    
    public function index(){
        $db = M('user');
        $sum = I('get.sum') ? I('get.sum') : "1";
        $p = I('get.page') ? I('get.page') : "1";
        if(!empty($_GET['username'])){
            $where .=" username ='{$_GET['username']}'";
        }
        $this->list = $db->where($where)->order('id desc')->limit((($p - 1) * $sum),$sum)->select();
        $num = count($db->where($where)->select());
        $url = U('Remind/work') . '?page=';
        $this->page = $this->page($url, $num, $sum, $p);
        $this->display();
    }
    public function post(){
        $db = M('user');
        $id =I('post.id');
        $data['username'] = I('post.username');
        $data['pwd'] = md5(I('post.pwd').KEY);
        $data['phone'] = I('post.phone');
        $data['department'] = I('post.department');
        $data['role'] = I('post.role');
        $data['status'] = I('post.status');
        if($id){
            $db->where(array('id'=>array('eq',$id)))->save($data);
        }else{
            $db->add($data);
        }
        show_json(1,array('url'=>U('member/index')));
    }
    public function department(){
        $db = M('department');
        $list = $db->where('status','1')->order('id desc')->select();
        show_json(1,$list);
    }
    public function role(){
        $db = M('role');
        $list = $db->where('status','1')->order('id desc')->select();
        show_json(1,$list);
    }
    
   

    
}
