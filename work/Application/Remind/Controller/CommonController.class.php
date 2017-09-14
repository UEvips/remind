<?php
namespace Remind\Controller;

use Think\Controller;
use MyClass\DingTalk;
use MyClass\toexcel;
use MyClass\toexcels;
use MyClass\Sms;
use MyClass\Download;

class CommonController extends Controller {
	private $dingtalk = '';
	private $User = '';
	private $xls = '';
	private $sms = '';
	public function _initialize() {

	}

        public function checkstring($str, $vchar) {
		$tcity3 = "%*^#" . $vchar;
		$str = trim ( $str );
		if (strpos ( $tcity3, $str ) === false) {
			return false;
		} else {
			return true;
		}
	}
	/**
	 * 获取钉钉接口
	 *
	 * @return \MyClass\DingTalk
	 */
	public function getDingTalk() {
		empty ( $this->dingtalk ) && $this->dingtalk = new DingTalk ();
		return $this->dingtalk;
	}
	/**
	 * 获取所在部门员工
	 *
	 * @return array
	 */
	public function getAtDepUser($info = false, $isall = false) {
		$u = $this->getU ();
		$userid = $u ['userid'];
		$dd = $this->getDingTalk ();
		if ($this->_admin ||$this->_xmb || $isall) {
			$ulist = $dd->getAlluser ( '', $info );
		} else {
			$ulist[] =$u;
		}
		return $ulist;
	}
	/**
	 * 获取消息接收人
	 * 
	 * @param string $dep        	
	 * @return array
	 */
	public function getTouser($dep = '') {
		$dd = $this->getDingTalk ();
		$ulist = $dd->getDepUserArray ( $dep );
		foreach ( $ulist as &$row ) {
			$row = $row ['userid'];
		}
		return $ulist;
	}
	/**
	 * 获取登录用户
	 *
	 * @return array
	 */
	public function getU() {
		empty ( $this->User ) && $this->User = session ( 'u' );
		return $this->User;
	}
	/**
	*获取域名
	*/
	public function getroot() {
		$root = str_replace ( __SELF__, '', get_url () );
		return $root;
	}
	public function getExcel($data = array(), $title = array(), $filename = 'report'){
		empty ( $this->xls ) && $this->xls = new toexcel();
		$xls=$this->xls;
		$xls->exportexcel($data,$title,$filename);die;
	}
	public function getExcels($p,$Callback=false){
		empty ( $this->xlss ) && $this->xlss = new toexcels();
		$xls=$this->xlss;
		$xls->exportexcel($p,$Callback);die;
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
		$data = array (
                    'errorCode' => $code,
                    'errorMsg' => $msg,
                    'data' => $dat 
		);
		$this->ajaxReturn ( $data );
	}
	/**
	 * 消息返回自动判断是否ajax，当非ajax提交时$dat为跳转地址url
	 *
	 * @param string $msg
	 *        	错误信息
	 * @param string $code
	 *        	错误码
	 * @param string|array $dat
	 *        	返回数据， 当非ajax提交时$dat为跳转地址url
	 */
	public function message($msg = 'error', $code = false, $dat = '') {
		if (IS_AJAX) {
			$this->ajaxRtn ( $msg, $code, $dat );
		} else {
			//(! is_string ( $dat )||empty($dat)) && $dat = $_SERVER['HTTP_REFERER'];
			$code ? $this->success ( $msg, $dat ) : $this->error ( $msg, $dat );
			// $this->success( 'hehe', 'javascript:history.back(-1);' );
		}
	}
	/**
	 * 跳转错误页面
	 */
	public function errors() {
		die ( "<script>location.href='__PUBLIC__/404.html';</script>" );
	}
	/**
	 * 检查数据完整性,去空格并返回是否有空值
	 *
	 * @param array $data        	
	 * @return boolean
	 */
	public function checkdata(&$data = array()){
		$isok = true;
		foreach ( $data as &$v ) {
			! is_array ( $v ) && $v = trim ( $v );
			if ( is_array ( $v )&&!empty ( $v )) {
				$isok=$this->checkdata($v);
			}elseif (empty ( $v ) && $v !== 0) {
				$isok = false;
			}
		}
		return $isok;
	}
	/**
	 * 检查数据完整性
	 *
	 * @param array $data        	
	 * @return 自动处理
	 */
	public function chkdata(&$data = array()) {
		! $this->checkdata ( $data ) && $this->message ( '数据不完整', false, $data );
	}
	/**
	 * 返回时间轴开始时间，结束时间
	 *
	 * @param string $t
	 *        	日程类型t 月:moth|m/周:work|w/日:day|d
	 * @param string $time
	 *        	//传入时间//默认当前时间Y-m-d
	 * @return array
	 */
	public function getDates($t = 'm', $time = null) {
		// 日程类型t 年:year|y/月:moth|m/周:work|w/日:day|d
		$time = empty ( $time ) ? time () : strtotime ( $time ); // 传入时间//默认当前时间Y-m-d
		$s = 24 * 60 * 60; // 每天秒数
		switch ($t) {
			case 'd' :
				$sdate = date ( 'Y-m-d 00:00:00', $time );
				$edate = date ( 'Y-m-d 00:00:00', $time + $s );
				break;
			case 'w' :
				$w = date ( 'w', $time );
				$time = $time - ($w == 0 ? 6 : $w - 1) * $s;
				$sdate = date ( 'Y-m-d 00:00:00', $time );
				$edate = date ( 'Y-m-d 00:00:00', $time + 7 * $s );
				break;
			case 'y' :
				$sdate = date ( 'Y-01-01 00:00:00', $time );
				$edate = date ( 'Y-12-31 23:59:59', $time );
				break;
			default :
				$m = intval ( date ( 'm', $time ) ) + 1;
				$y = intval ( date ( 'Y', $time ) );
				$m == 13 && $y = $y + 1;
				$m == 13 && $m = 1;
				$sdate = date ( 'Y-m-01 00:00:00', $time );
				$edate = $y . ($m < 10 ? '-0' : '-') . $m . '-01  00:00:00';
				break;
		}
		// echo $sdate.'/'.$edate;
		return array (
				$sdate,
				$edate,
				strtotime($sdate),
				strtotime($edate) 
		);
	}
	public function getDate($t = 'm', $time = null) {
		// 日程类型t 年:year|y/月:moth|m/周:work|w/日:day|d
		$time = empty ( $time ) ? time () : strtotime ( $time ); // 传入时间//默认当前时间Y-m-d
		$now=date('Y-m-d H:i:s',$time);
		$s = 24 * 60 * 60; // 每天秒数
		switch ($t) {
			case 'd' :
				$sdate = date ( 'Y-m-d 00:00:00', $time );
				break;
			case 'w' :
				$sdate=date('Y-m-d 00:00:00',strtotime("$now -1 week +1 day"));
				break;
			case 'y' :
				$sdate=date('Y-m-d 00:00:00',strtotime("$now -1 year +1 day"));
				break;
			default :
                $last=strtotime(date('Y',$time).'-'.(date('m',$time)-1).'-01 00:00:00');
                $t=date('t',$last)-1;
			    $d=intval(date('d'))-1;
                $d>$t&&$t=$d;				
				$sdate=date('Y-m-d 00:00:00',strtotime("$now -$t day"));
				break;
		}
	    $edate=date('Y-m-d 00:00:00',strtotime("+1 day"));
		// echo $sdate.'/'.$edate;
		return array (
				$sdate,
				$edate,
				strtotime($sdate),
				strtotime($edate) 
		);
	}
	/**
	*计算结束时间，自动加上周末
	*/
	public function endDay($s,$d){
		$dy=24*60*60;
		$w=(int)date('w',$s);		
		$dl=$d%5;
		$d+=intval($d/5)*2;
		if($d<0){
		    $w==0&&$w=7;
			$d+=1-$w>$dl?-2:0;
		}elseif(5-$w<$dl){
			$d+=$w==5?2:1;
		}
		$s=$d*$dy+$s;
		return array($s,$d);
	}
	/**
	*计算工时，按天,未排除周末
	*/
	public function sum($s,$e){
		$i=60;//分
		$h=60*$i;//时
		$d=24;//日
		$c=($e-$s)/$h;//时差，小时计
		$day=intval($c/$d);//时差，天计
		$c=($c-$day*$d)/8;//不足24时部分
		$day+=$c>1?1:$c;//8-24时计一天，8时制		
		return round($day,2);
	}
	// 任务详细进度
	public function getschedule() {
		$u = $this->getU ();
		$userid = $u ['userid'];
		$dd = $this->getDingTalk ();
		$dat = I ( 'param.' );
		$db = D ( 'ProjectRelation' );
		$dat ['id'] = trim ( $dat ['id'] );
		! empty ( $dat ['id'] ) && $where ['id'] = $dat ['id'];
		$list = $db->where ( $where )->relation ( true )->find ();
		$ulist = $dd->getAlluser ();
		$ismember = true;
		$id = 'a' . $row ['id'];
		$list ['uname'] = $ulist [$list ['uid']] ['name'];
		$user = array ();
		foreach ( $list ['task'] as $k => &$child ) {
			// 检测轮到谁
			$child ['ismember'] = 0;
			$child ['uname'] = $ulist [$child ['member']] ['name'];
			$user [$child ['member']] = $child ['uname'];
			if ($ismember && $child ['status'] != 2) {
				$child ['ismember'] = 1;
				$ismember = false;
			} elseif ($ismember) {
				$child ['ismember'] = 2;
			}
		}
		$list ['level'] = $list ['level'] == 2 ? '紧急' : '普通';
		$list ['member'] = implode ( ',', $user );
		//$this->index = 'active';
		$this->list = $list;
		$this->display ();
	}
	public function ceshi() {		
		  $where['id']=101;
		  //$where['status']=array('lt',2);
		  $p=D('ProjectRelation')->relation(true)->where($where)->find();
		  $time=time();//-24*60*60*2;
//		  var_dump($this->isTempty);
		  
	}
	public function getcolor($c) {
		$color=Array(
               '04072150217407'=>'#79d2c0',
               '01274768601886'=>'#d3d3d3',
               '03013522374834'=>'#23c6c8',
                '285840297385'=>'#EE33F4',
               '03013556618910'=>'#E74B4B',
               '03466845368278'=>'#E7C94B',
               '03153768615642'=>'#82E74B',
               '452765412126'=>'#57EABB',
               '03030950377639'=>'#53A3ED',
               '03013556602306'=>'#336384',
               '03013556599322'=>'#192125',
               '03116450208489'=>'#24113C',
		   );
		return $color[$c];
	}
	public function upload($path){
		$path=explode('/', $path);
		$path='Uploads/'.@$path[0].'/'.date('Y').'/'.date('m').'/'.@$path[1].'/';
		$path=str_replace('//','/',$path);
		$upload = new \Think\Upload();// 实例化上传类
		$upload->maxSize   =     0 ;// 设置附件上传大小
		$upload->exts      =     array('jpg', 'gif', 'png', 'jpeg','rar','zip','7z','xls','xlsx','csv','doc','docx','psd','cdr');// 设置附件上传类型
		$upload->rootPath  =     './Public/'; // 设置附件上传根目录
		$upload->savePath  =     $path; // 设置附件上传（子）目录
		$upload->subName   =     '';
		//$upload->saveName  =     '';
		// 上传文件
		$error=intval($_FILES['up']['error']);
		$size=intval($_FILES['up']['size']);
		if($error>0){return false;}
		if($size==0){return false;}
		$info   =  $upload->upload();
		if(!$info) {// 上传错误提示错误信息
			$info=$upload->getError();
		}
		return $info;
	}
	//下载
	public function download(){
		$dat=I('param.');
		$id=intval(trim($dat['downid']));
		$where['id']=$id;
		$file=M('upload')->where($where)->find();
		$path='Public/'.$file['src'];
		$name=$file['name'];
		$down= new Download();
        try{
          $down->ReadfileDown($path,$name);
        }catch(Exception $e) {
	      echo 'Message:'.$e->getMessage();
        }
	}
	public function getworktype(){
		return array(
		    'CP',
		    'CH',
		    'QD',
		    'WA',
		    'SJ',
		    'CX',
		    'GT',
		    'WZ',
		    'SJZ',
		    'JJ',
		    'ZT'
		);
		
	}
	/**
	*发送短信
	*/
	public function send($mobile,$cont){
		if(empty($mobile)){return false;}
		$sms=$this->getSms();
		$content='您提交的工单《'.$cont[0].'》'.$cont[1];//',详情请登录：'.$this->getroot().'/index.php/client/';
		return $sms->send($mobile,$content);
	}
	public function getSms(){
		empty ( $this->sms ) && $this->sms = new sms();
		return $this->sms;
	}

	public function page($url,$count,$per_page,$pagees){
		if($count<0){
			$page = array();
			return;
		}
		$page_num = ceil($count/$per_page); //向上取整获取总页数
                if($page_num<2){
			$page = array();
			return ;
		}
		$page_three = 3;
		$floor_num = floor($page_three/2); //向下取整获得左右数值
		$page_qian = $pagees-$floor_num;//前一页
		$page_hou= $pagees+$floor_num;//后一页
		//前一页小于1
		if($page_qian<1){
			$page_qian = 1;
			$page_hou = $page_three;
		}
		//后一页大于总页数
		if($page_hou>$page_num){
			$page_hou = $page_num;
			$page_qian = $page_num - $page_three + 1;
		}
		//中间页大于总页数
		if($page_num < $page_three){
			$page_qian = 1;
			$page_hou = $page_num;
		}
	$page = '';
	//首页
	$page .='<b class="pages" value="1"><a href='.$url.'1><img src="/Public/images/leftMore.png"></a></b>';

	//上一页
	if($pagees > 1){
		$prev_page = $pagees-1;
		$page .='<b class="pages"><a href='.$url.$prev_page.'><img src="/Public/images/leftBack.png"></a></b>';
	}
	for($i=$page_qian;$i<=$page_hou;$i++){
		 if($i == $pagees){
			 $page .='<i class="on pages"><a href='.$url.$i.'>'.$i.'</a></i>';
		 }else{
		 $page .='<i class="pages"><a href='.$url.$i.'>'.$i.'</a></i>';
		 }
	}
	//下一页
	if($pagees != $page_num){
		$next_page = $cur_page+1;
		$page .='<b class="pages"><a href='.$url.$next_page.'><img src="/Public/images/rightBack.png"></a></b>';
	}
	//尾页
	$page .='<b class="pages"><a href='.$url.$page_three.'><img src="/Public/images/rightMore.png"></a></b>';
	return $page; 		
}
    

//limit
    public function limit($num,$p) {
        $limit['limit'] = $num;
        $limit['page'] = $p;
        $limit['pagelast'] = $limit['page'] * $limit['limit'] - $limit['limit'];
        $limit['pagenext'] = $limit['limit'];
        return $limit;
    }
    //用户同步
    public function user(){
        $db = M('user');
        $data = $db->select();
        $dd = $this->getDingTalk();
        $list = $dd->getAlluser();
        foreach($list as $k=>$v){
            $arr[] = $k;
        }
        foreach($data as $k=>$v){
            $bd[''] = $v['uid'];
        }
        foreach($list as $k=>$v){
            if(!in_array($k,$arr)){
                $user['uid'] = $v['userid'];
                $user['username'] = $v['name'];
                $user['depart'] = $v['department'];
                $db->add($user);
            }
        }
        foreach($data as $k=>$v){
            if(!in_array($v['uid'],$arr)){
                M('role_user')->where('user_id = '.$v['uid'])->delete();
                $db->where('uid = '.$v['uid'])->delete();
            }
        }      
    }
}