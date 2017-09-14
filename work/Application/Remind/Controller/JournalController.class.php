<?php
namespace Remind\Controller;

use Think\Controller;
//日志
class JournalController extends CommonController {
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

    public function addournal(){
        
        if(IS_AJAX){
                $dd=$this->getDingTalk();	
		$u=$this->getU();
		$userid=$u['userid'];
		$dat=I('param.');
		$this->checkdata($dat);
		$db=M('drecord');
		$dbc=M('drecordComment');
		$rid=intval($dat['rid']);
		if($dat['op']=='add'){			
		    $last=$db->where(array('uid'=>$userid))->order('id desc')->find();
		    $today_total=$db->where(array('uid'=>$userid,'citme'=>array('gt',strtotime(date('Y-m-d 00:00:00')))))->order('id desc')->count();
			$m=1;
			if(time()-intval($last['ctime'])<$m*60){
				$this->message('两次日志提交时间间隔，不能低于'.$m.'分钟哦！',false,U('lists'));
			}
			$data=array(
			   'uid'=>$u['userid'],
			   'avatar'=>$u['avatar'],
			   'uname'=>$u['name'],
			   'tomorrow'=>$dat['tomorrow'],
			   'img'=>serialize($dat['img'])
			);
			foreach($dat['sxmtitle'] as $k=>$v){
				$data['morning'][]=array(
				      'title'=>strtoupper($v),
				      'type'=>strtoupper($dat['sxmtype'][$k]),
				      'name'=>strtoupper($v.date('ymd').$dat['sxmtype'][$k]),
				      'content'=>$dat['sxmcontent'][$k],
				      'percent'=>$dat['sxmpercent'][$k]
				);
			}
			$data['morning']=serialize($data['morning']);
			foreach($dat['xxmtitle'] as $k=>$v){
				$data['afternoon'][]=array(
				      'title'=>strtoupper($v),
				      'type'=>strtoupper($dat['xxmtype'][$k]),
				      'name'=>strtoupper($v.date('ymd').$dat['xxmtype'][$k]),
//                                      'name'=>strtoupper($v."170318".$dat['xxmtype'][$k]),
				      'content'=>$dat['xxmcontent'][$k],
				      'percent'=>$dat['xxmpercent'][$k]
				);
			}
			$data['afternoon']=serialize($data['afternoon']);
//                        print_r($data);die;
			if($rid){
				$data['utime']=time();
				$res=$db->where(array('id'=>$rid))->save($data);
			}else{
				$data['ctime']=time();
				$data['touser']=serialize($dat['userid']);
				$res=$db->add($data);
				if($res){
					//$touser=array('03116450208489');//$this->getTouser(array('5854791'));
					foreach($dat['userid'] as $v){
						if(!empty($v)){
							$touser[]=$v;
						}
					}
					$url=get_siteroot().U('comment',array('rid'=>$res));
					$cont=array(
							'touser'=>$touser,
							'toparty'=>array(),
							"message_url"=>$url,
							"pc_message_url"=>$url,
							'text'=>'日志',
							'title'=>$data['uname'].'的日报',
					);
					$data['morning']=unserialize($data['morning']);
					$data['afternoon']=unserialize($data['afternoon']);
					$cont['form'][0]=array('key'=>'今日上午完成工作');
					$cont['form'][1]=array('key'=>'今日下午完成工作');
					$cont['form'][2]=array('key'=>'明日工作安排');
					foreach($data['morning'] as $k=>$v){
						$cont['form'][0]['value'].=($k+1).'、'.$v['name'].' '.$v['content'].'('.$v['percent'].'%)';
					}
					foreach($data['afternoon'] as $k=>$v){
						$cont['form'][1]['value'].=($k+1).'、'.$v['name'].' '.$v['content'].'('.$v['percent'].')';
					}
					$cont['form'][2]['value']=$data['tomorrow'];
					$json=$dd->sendMsg($cont,'oa');	
                    // print_r($json);die;					
				}
			}
			$rid=$res;
			$this->message($res?'操作成功':'操作失败',$res?true:false,U('lists'));
		}
        }
        
         $this->display();
    }
    public function myjournal(){
        
        $this->display();
    }
    public function getjournal(){
        
        $this->display();
    }
    

    
}