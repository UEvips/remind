<?php
namespace Remind\Controller;

use Think\Controller;

class IndexController extends CommonController {

    public $statusarr = array(
        -1 => '未确认',
        '未开始',
        '正在做',
        '待审核',
        '已完成'
    );

    /**
     * 用户登录
     */
    public function _initialize() {
        //ACTIVE
        $m = session('openid');
//        if (empty($m)) {
//            IS_AJAX && $this->ajaxRtn('请登录后操作');
//            $this->display('remind/login');
//            die;
//        }
        ignore_user_abort(false); //客户端断开终止代码
    }

    public function index() {
        $dd = $this->getDingTalk();
        $ulist = $dd->getAlluser();
        $mywork = M('work_user');
        if ($_GET['code']) {
            $dduser = $dd->userinfo($_GET['code']);
//            print_r($dduser);die;
            session('ddinfo', $dduser);
            $user = M('user_work')->where(array('openid' => array('eq', $dduser['user_info']['openid'])))->find();
            if (empty($user)) {
                $type = "1";
                $this->type = $type;
                $this->display();
                exit();
            } else {
                session('m', $user);
            }
        }
        $user_id = $_SESSION['m']['uid'];
        //获取主要任务
        $where['tb_work_user.status'] = 0;
        $where['tb_work_user.user_id'] = $user_id;
        $work = $mywork->field('tb_work_user.id,tb_work_user.user_id,tb_work_user.user_name,b.title,b.jd')->join('JOIN tb_work b on  tb_work_user.work_id=b.id')->where($where)->limit(2)->select();
//        echo $mywork->getlastsql();die;
        $this->work = $work;
//        print_r($work);die;
        //获取长线任务
        $where4['b.etime'] = "每天必做";
        $where['tb_work_user.user_id'] = $user_id;
        $cwork = $mywork->field('tb_work_user.id,tb_work_user.user_id,tb_work_user.user_name,b.title,b.jd')->join('JOIN tb_work b on  tb_work_user.work_id=b.id')->where($where1)->order('b.etime desc')->limit(3)->select();
        $this->cwork = $cwork;
        //获取部门当月目标
        //获取本月第一天
//        $day = date('Y-m-01');
        $enddat = date('Y-m-d 23:59:59', strtotime("$mouth +1 month -1 day"));
//        $year = date('Y');
        //获取剩余时间
        $nums = date('t')-date('d');
        $target = M('target')->select();
        $this->target = $target;
        $this->nums = $nums;
        //获取最新公告
        $message = M('message')->where($map)->order('id desc')->find();
        $this->message = $message;
        //获取项目流
        $data = M('work')->limit($limit['pagelast'], $limit['pagenext'])->select();
        //获取部门
        $department = M('department')->where(array('status' => array('eq', 1)))->select();
//        print_r($data);die;
        //获取所有用户
        $user = M('user_work')->field('uid,username,depart')->select();
        foreach ($user as $k => $v) {
            $user[$v['username']] = $v;
            unset($user[$k]);
        }
//        print_r($user);die;
        foreach ($data as &$row) {
            $row['cyr'] = explode('，', $row['cyr']);
            foreach ($row['cyr'] as &$vo) {
                $vo = $user[$vo];
            }
        }
        foreach ($department as $k => $v) {
            foreach ($data as &$vo) {
                foreach ($vo['cyr'] as &$vo1) {
//                   echo $v['name'];
                    if ($v['name'] == $vo1['depart']) {
                        $dep[$v['name']][] = $vo;
                    }
                }
            }
        }
        $this->department = $dep;
        //获取任务流
//        print_r($department);
//        print_r($user);die;
        foreach ($department as $k => $v) {
            foreach ($user as $k1 => $v1) {
                if ($v['name'] == $v1['depart']) {
                    $rw[$v['name']][] = $v1;
                }
            }
        }
        foreach ($rw as $k => $v) {
            foreach ($v as $k1 => $v1) {
                $where1['tb_work_user.user_id'] = $v1['uid'];
                $where2['tb_work_user.status'] = 1;
                $where2['tb_work_user.ctime'] = array('ELT', time());
                $where3['tb_work_user.status'] = 0;
                $rwl[$k][$v1['username']] = $mywork->field('tb_work_user.id,tb_work_user.user_id,tb_work_user.user_name,b.title,b.jd')->join('JOIN tb_work b on  tb_work_user.work_id=b.id')->where($where1)->where($where3)->order('b.etime desc')->limit(2)->select();
                $rwl[$k][$v1['username']][2] = $mywork->field('tb_work_user.id,tb_work_user.user_id,tb_work_user.user_name,b.title,b.jd')->join('JOIN tb_work b on  tb_work_user.work_id=b.id')->where($where1)->where($where2)->order('tb_work_user.ctime desc')->find();
//                echo $mywork->getlastsql();die;
            }
        }
//        print_r($rwl);die;
        $this->rwl = $rwl;
        $this->display();
    }

    //excel文件上传
    public function phpexcel1() {
//
        if (!empty($_FILES ['up'] ['name'])) {
            $path = "excel/" . date('d');
            $info = $this->upload($path);
            import('ORG.Util.ExcelToArrary'); //导入excelToArray类
            $ExcelToArrary = new \ExcelToArrary(); //实例化  
            $res = $ExcelToArrary->read('./Public/' . $info['up']['savepath'] . $info['up']['savename'], "UTF-8", $info['up']['ext']); //传参,判断office2007还是office2003
            if(empty($res)){
                exit("导入失败！！！");
            }
            M('target')->where('1')->delete();
            M('work')->where('1')->delete();
            M('work_user')->where('1')->delete();
            $work = array_slice($res[1], 1);
            if (!empty($work)) {
                foreach ($work as $k => $v1) {
                    $work1[$k]['depart'] = $v1 [0] ? $v1 [0] : " "; //部门
                    $work1[$k]['target'] = $v1 [1]; //目标下达
                    $work1[$k]['jd'] = $v1 [2] ? $v1 [2] : " "; //完成进度
                    $work1[$k]['etime'] = $v1 [3]; //剩余时间
                }
                M('target')->addall($work1);
            }
            $row = array_slice($res[0], 2); //为了去掉Excel里的表头,也就是$res数组里的$res[0];
            foreach ($row as $k1 => $v1) {
                $data[$k1]['work'] = $v1 [0] ? $v1 [0] : " "; //项目
                $data[$k1]['decompose'] = $v1 [1]; //项目分解
                $data[$k1]['title'] = $v1 [2] ? $v1 [2] : " "; //详细信息
                $data[$k1]['cyr'] = $v1 [3]; //参与人
                $data[$k1]['etime'] = $v1 [4]; //计划完成时间
                $data[$k1]['jd'] = $v1 [5] ? $v1 [5] : "无"; //进度
                $data[$k1]['content'] = $v1 [6] ? $v1 [6] : "无"; //备注
                $data[$k1]['fzr'] = $v1 [7]; //负责人
                $data[$k1]['status'] = $v1 [5] == "已完成" ? "1" : "0"; //状态 
            }
            foreach ($data as $k => $v) {
//                print_r($v);die;
                $result = M('work')->add($v);
                if ($result) {
                    $cyr = explode('，', $v['cyr']);
                    foreach ($cyr as $k1 => $v1) {
                        $user = M('user_work')->where(array('username' => array('eq', $v1)))->find();
                        if (empty($user)) {
                            $userinfo['uid'] = substr(time(), 7) . rand(100000, 999999);
                            $userinfo['username'] = $v1;
                            $userinfo['pwd'] = md5('123456');
                            M('user_work')->add($userinfo); //添加新用户
                        }
                        $workuser['user_id'] = empty($user) ? $userinfo['uid'] : $user['uid'];
                        $workuser['user_name'] = $v1;
                        $workuser['work_id'] = $result;
                        $workuser['status'] = $v['status'];
//                        print_r($workuser);die;
                        M('work_user')->add($workuser); //工作用户关联表
                    }
                }
            }
            if (!$result) {
                $this->error('导入数据库失败');
                exit();
            } else {
                $filename = './Public/' . $info['up']['savepath'] . $info['up']['savename']; //上传文件绝对路径,unlink()删除文件函数
                if (unlink($filename)) {
                    $this->success('导入成功');
                } else {
                    $this->error('缓存删除失败');
                }
            }
        } else {
            $this->error('(⊙o⊙)~没传数据就导入?!你在逗我?!');
        }
    }

    public function tixing() {
        if (!empty($_POST['op'])) {
            $uid = I('post.uid');
            $wid = I('post.wid');
            $wm = M('work_user')->where(array('work_id' => array('eq', $wid)))->find();
            if (empty($wm)) {
                $msg['msg'] = "该项目不存在";
            }
//            print_r($wm);die;
            if ($wm['status'] == 1) {
                $msg['msg'] = "该项目已完成";
            } else {
                $info['uid'] = $uid;
                $info['work_id'] = $wid;
                $info['time'] = time();
                $info['title'] = $wm['title'];
                $info['ststus'] = 1;
                $res = M('tixing')->add($info);
                if ($res){
                    $msg['msg'] = "提醒发送成功";
                }
            }

            echo json_encode($msg);
            $dd = $this->getDingTalk();
            $message = $dd->ddmessage();
            exit();
        }
        $model = M('work_user');
        $sum = I('get.sum') ? I('get.sum') : "10";
        $p = I('get.page') ? I('get.page') : "1";
        $limit = $this->limit($sum, $p);
        $data = $model->field('tb_work_user.work_id,tb_work_user.user_id,tb_work_user.user_name,b.title,b.jd')->join('JOIN tb_work b on  tb_work_user.work_id=b.id')->limit($limit['pagelast'], $limit['pagenext'])->select();
        $num = count($model->select());
        $url = U('Remind/work') . '?page=';
        $this->list = $data;
        $this->page = $this->page($url, $num, $sum, $p);
//        print_r($data);die;
        $this->display();
    }

//    部门管理
    public function department() {
        $this->list = M('department')->where(array('status' => array('eq', 1)))->select();

        $this->display();
    }

    //公告管理
    public function message() {
        $model = M('message');
        if (IS_POST) {
            $data['title'] = I('post.title');
            $data['content'] = I('post.content');
            $data['time'] = time();
            $data['uid'] = session('uid');
            $res = $model->add($data);
            if ($res) {
                exit($this->success('新增成功', 'message'));
            }
        }
        $this->list = $model->order('id desc')->find();
        $this->display();
    }

    public function messagelist() {
        $this->list = M('message')->order('id desc')->select();
        $this->display();
    }

    //任务管理
    public function work() {
        $model = M('work');
        $sum = I('get.sum') ? I('get.sum') : "10";
        $p = I('get.page') ? I('get.page') : "1";
        $limit = $this->limit($sum, $p);
        $data = $model->order('id desc')->limit($limit['pagelast'], $limit['pagenext'])->select();
        $num = count($model->select());
        $url = U('Remind/work') . '?page=';
        $this->page = $this->page($url, $num, $sum, $p);
        //获取部门
        $department = M('department')->where(array('status' => array('eq', 1)))->select();
        foreach ($department as $k => $v) {
            $department[$v['id']] = $v;
            unset($department[0]);
        }
        //获取所有用户
        $user = M('user_work')->field('uid,username')->select();
        foreach ($user as $k => $v) {
            $user[$v['username']] = $v;
            unset($user[$k]);
        }
        foreach ($data as &$row) {
            $row['bm'] = $department[$row['department']]['name'];
            $row['cyr'] = explode('，', $row['cyr']);
            foreach ($row['fzr'] as &$vo) {
                $vo = $user[$vo];
            }
        }
//        print_r($data);die;
        $this->list = $data;
        $this->display();
    }

    //权限管理
    public function login() {
        $dd = $this->getDingTalk();
        $data = $dd->changman();
        $dduser = $dd->getlogin();
        $dat = I('param.');
        $db = M('user_work');
        //获取最新公告
        $message = M('message')->where($map)->order('id desc')->find();
        $this->message = $message;
        if (IS_AJAX) {
            $uname = I('post.name');
            $pwd = I('post.pwd');
            if (is_numeric($uname)) {
                $user = $db->where(array('moblie' => array('eq', $uname)))->find();
            } else {
                $user = $db->where(array('username' => array('eq', $uname)))->find();
            }
            if (!empty($user)) {
                if (md5($pwd) == $user['pwd']) {
                    session('logoimg', $user ['logoimg']);
                    session('openid', $user['openid']);
                    session('m', $user);
                    //日志
                    $log = get_log_data('user_work');
                    $log['cont'] = $log['desc'] = '登录了系统';
                    add_log($log);
                    $msg['err'] = 1;
                    $msg['url'] = U('remind/index');
                } else {
                    $msg['err'] = 0;
                    $msg['msg'] = "密码错误！";
                }
            } else {
                $msg['err'] = 0;
                $msg['msg'] = "该用户不存在！";
            }
            echo json_encode($msg);
        } else {
            $this->url = urlencode("https://oapi.dingtalk.com/connect/oauth2/sns_authorize?appid=" . $dduser['appid'] . "&response_type=code&scope=snsapi_login&state=STATE&redirect_uri=" . $dduser['huiurl']);
            $this->url1 = "https://oapi.dingtalk.com/connect/oauth2/sns_authorize?appid=" . $dduser['appid'] . "&response_type=code&scope=snsapi_login&state=STATE&redirect_uri=" . $dduser['huiurl'];
            $this->display();
        }
    }

    //退出
    public function loginout() {
        //日志
        $log = get_log_data('customer');
        $log['cont'] = $log['desc'] = '退出了系统';
        $log['type'] = -1;
        $user = session('u');
        $where['id'] = $user['openid'];
        $updata['loginouttime'] = time();
        //echo $updata['loginouttime'];die;
        M('customer')->where($where)->save($updata);
        add_log($log);
        session_unset();
        session_destroy();
        if (!IS_AJAX) {
            $this->redirect('remind/login');
        }
    }

    /**
     * ajax数据返回，继承 ajaxReturn
     *
     * @param int $code
     *        	错误码
     * @param string $msg
     *        	错误信息
     * @param string|array $dat
     *        	返回数据
     */
    public function ajaxRtn($msg = 'error', $code = false, $dat = '') {
        $data = array(
            'errorCode' => $code,
            'errorMsg' => $msg,
            'data' => $dat
        );
        $this->ajaxReturn($data);
    }

//   权限管理
    public function rolelist() {
        if (IS_AJAX) {
            //开启关闭角色
            $data['states'] = M('remind_role')->field('id,status')->save(I('param.'));
            $data['p'] = I('param.');
            $this->ajaxReturn($data);
        } else {
            $this->list = M('remind_role')->order('pid,id,name,remark')->select();
            $this->display();
        }
    }

    //用户列表
    public function userlist() {
        $dat = I('param.');
        if ($dat['sub'] == 'addrole') {
            foreach ($dat['uid'] as $v) {
                foreach ($dat['rid'] as $r) {
                    $role[] = array(
                        'remind_role_id' => $r,
                        'user_id' => $v
                    );
                }
            }
            $db = M('remind_role_user');
            $where['user_id'] = array('in', $dat['uid']);
            $db->where($where)->delete();
            $db->addAll($role);
            exit($this->success('新增成功', 'userlist'));
        }
        $this->role = M('remind_role')->order('pid,id,name,remark')->select();
        $this->dep = M('department')->where(array('ststus' => array('eq', 1)))->select();
        $this->user = D('RemindUserRelation')->relation(true)->select();
        $this->display();
    }

    //节点列表
    public function nodelist() {
        $field = 'id,title,name,pid,level';
        $node = M('node')->field($field)->order('pid,sort,title')->select();
        $this->list = node_merge($node); //重组节点
        $this->display();
    }

    //添加角色
    public function addRole() {
        if (IS_AJAX) {
            $role = M('remind_role');
            $role->field('name,pid,status,remark')->create(I('param.'));
            $data['states'] = $role->add();
            $this->ajaxReturn($data);
        } else {
            $this->pid = I('pid', 0);
            $this->display();
        }
    }

    //添加用户
    public function addUser() {
        $user = array(
            'username' => I('username'),
            'depart' => I('depart'),
            'mobile' => I('mobile'),
            'logintime' => time(),
            'loginip' => get_client_ip()
        );
        $uid = I('post.uid');
        if (empty($uid)) {
            $pwd = I('pwd') ? I('pwd') : "123456";
            $user['pwd'] = md5($pwd);
            $user['uid'] = substr(time(), 7) . rand(100000, 999999);
            $res = M('user_work')->add($user);
        } else {
            $pwd = I('pwd');
            if (empty($pwd)) {
                $user['pwd'] = md5($pwd);
            }
            $res = M('user_work')->where(array('uid' => array('eq', $uid)))->save($user);
        }
        if ($res) {
            exit($this->success('更新成功', 'userlist'));
        }
    }

    //删除用户
    public function delUser() {
        $uid = I('post.uid');

        $row = M('remind_role_user')->where(array('user_id' => array('eq', $uid)))->delete(); //删除该用户角色
        $res = M('user_work')->where(array('uid' => array('eq', $uid)))->delete(); //删除提醒用户
        if ($res) {
            $msg['msg'] = "删除成功";
        } else {
            $msg['msg'] = "删除失败";
        }
        echo json_encode($msg);
    }

    //添加节点
    public function addNode() {
        if (IS_AJAX) {
            $node = M('node');
            $da = I('param.');
            //节点上级，上级节点等级+1
            $da['level'] = $node->where(array('id' => $da['pid']))->getField('level') + 1;
            $data['states'] = $node->field('name,pid,status,title,sort,level')->add($da);
            $this->ajaxReturn($data);
        } else {
            $this->pid = I('pid', 0); //上级节点id
            $this->title = '添加' . I('title', '应用');
            $this->display();
        }
    }

    //配置角色权限
    public function setAccess() {
        $rid = I('rid', 0, 'intval');
        if (IS_AJAX) {
            $ac = M('remind_access');
            $da = I('param.', '', 'intval');
            //节点等级
            $da['level'] = M('node')->where(array('id' => $da['node_id']))->getField('level');
            $field = 'node_id,role_id,level';
            //$ac->field('node_id,role_id,level')->create($da);
            //设置权限，节点被选中则为角色设置权限，否删除权限
            I('chk') ? $ac->field($field)->add($da) : $ac->field($field)->where($da)->delete();
            $this->ajaxReturn($da);
        } else {
            //角色拥有权限的几点
            $access = M('remind_access')->where(array('role_id' => $rid))->getField('node_id', true);
            $field = 'id,title,name,pid';
            //全部节点
            $node = M('node')->field($field)->order('pid,sort,title')->select();
            $this->rid = $rid;
            $this->list = node_merge($node, $access); //重组节点有权限则checked
            $this->display();
        }
    }

    //任务确定
    public function updwork() {
        $wid['id'] = I('post.wid');
        $data['status'] = "1";
        $res = M('work_user')->where($wid)->save($data);
        if ($res) {
            $where['tb_work_user.status'] = 0;
//        $where['tb_work_user.user_id'] = $user_id;
            $work = M('work_user')->field('tb_work_user.id,tb_work_user.user_id,tb_work_user.user_name,b.title,b.jd')->join('JOIN tb_work b on  tb_work_user.work_id=b.id')->where($where)->limit(2)->select();
            echo json_encode($work);
        }
    }

    //绑定验证码
    public function yzm() {
        $yzm = I('post.yzm');
        $user = M('user_work')->where(array('uid' => array('eq', $yzm)))->find();
        if (!empty($user)) {
            $openid = $_SESSION['ddinfo']['user_info']['openid'];
            $data['openid'] = $openid;
            M('user_work')->where(array('uid' => array('eq', $yzm)))->save($data);
            $userinfo = M('user_work')->where(array('uid' => array('eq', $yzm)))->find();
            session('m', $userinfo);
            $msg['err'] = 1;
            $msg['msg'] = U('remind/index');
        } else {
            $msg['err'] = 0;
            $msg['msg'] = "验证码错误！！！";
        }
        echo json_encode($msg);
    }
    public function tixing_home(){
        $uid =  $_SESSION['m']['uid'];
        $where['uid']=$_SESSION['m']['uid'];
        $where['ststus'] = 1;
        $data = M('tixing')->where($where)->select();
        if(!empty($data)){
             echo json_encode($data);
        }
    }
}
