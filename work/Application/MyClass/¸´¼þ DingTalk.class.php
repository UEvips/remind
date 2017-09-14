<?php

namespace MyClass;
/**
 * 钉钉接口
 * @author Administrator
 *
 */
class DingTalk {
	private $corpid = 'dingea7bff30eaa1d1d4';
	private $corpsecret = 'sFuh97hXWQaYusMc0czsLLCFTOnOMgR7T-yErga4vzt1frMIwjdWb0yLFh0-hZ2u';
	private $userid = '';
	private $AgentID='14448391';
	
	public function getConf(){
		return array(
		    'corpid'=>$this->corpid,
		    'corpsecret'=>$this->corpsecret,
		    'AgentID'=>$this->AgentID,
		);
	}
	/**
	 * 获取token
	 */
	public function getAccess_Token($info=7200) {
		$serch = array (
				'%CORPID%',
				'%SECRECT%' 
		);
		// 请求token地址
		$url = 'https://oapi.dingtalk.com/gettoken?corpid=%CORPID%&corpsecret=%SECRECT%';
		$rep = array (
				'corpid' => $this->corpid,
				'corpsecret' => $this->corpsecret 
		);
		$url = str_replace ( $serch, $rep, $url );
		//读取缓存
		$token=$this->getCache($url,'access_token',$info);
		return $token ['access_token'];
	}
	/**
	 * 网页授权，获取临时授权码code
	 */
	public function oauth2($uri=false) {
		$uri =$uri?$uri:$this->get_url();
		$uri=urlencode($uri);
		$rep = array (
				'corpid' => $this->getCorpid (),
				'redirect_uri' => $uri 
		);
		$serch = array (
				'%CORPID%',
				'%REDIRECT_URI%' 
		);
		$url = 'https://oapi.dingtalk.com/connect/oauth2/authorize?appid=%CORPID%&redirect_uri=%REDIRECT_URI%&response_type=code&scope=SCOPE&state=STATE';
		$url = str_replace ( $serch, $rep, $url );
		header ( "Location: $url" );
		die ();
	}
	/**
	 * 获取部门列表 or 部门详情
	 *
	 * @param int $id   	
	 * @return objc
	 */
	public function getDep($id='') {
		$rep = array (
				'ID' => $id 
		);
		$serch = array (
				'%ID%' 
		);
		$url = "https://oapi.dingtalk.com/department/list";
		if ($id!='') {
			$url = 'https://oapi.dingtalk.com/department/get?id=%ID%';
		}
		$url = $this->Token_replace($serch, $rep, $url);
		//读取缓存
		$list=$this->getCache($url,'dep_'.$id);
		//print_r($list);
		if (!$id) {
		  $list=$list['department'];
		  $tmp=array();
		  foreach ($list as $row){
			 $tmp[$row['id']]=$row;
		  }
		  $list=$tmp;
		}
		return $list;
	}
	/**
	 * 获取部门员工
	 * @param int $dep_id
	 * @param Boolean $info 是否详情，默认false
	 * @return objc
	 */
	public function getDepUser($dep_id,$info=false){
		$rep = array (
				'ID' => $dep_id 
		);
		$serch = array (
				'%ID%' 
		);
		$url = 'https://oapi.dingtalk.com/user/simplelist?department_id=%ID%';
		if ($info) {
			$url = 'https://oapi.dingtalk.com/user/list?department_id=%ID%';
		}
		$url = $this->Token_replace($serch, $rep, $url);
		//读取缓存
		$name='dep_user_'.$dep_id.'_'.($info?'1':'0');
		$list = $this->getCache($url,$name);
		$list=$list['userlist'];
		foreach ($list as &$row){
			if($info){
				$dep_id=$row['department'];
				$row['department']='';
				$dep=$this->getDep();
				foreach ($dep_id as $k =>$v){
					$row['department'][$v]=$dep[$v];
				}
			}else {
			$dep=$this->getDep();		
			$row['department'][0]=$dep[$dep_id];
			$row['department'][$dep_id]=$dep[$dep_id];
			}
		}
		return $list;
	}
	/**
	 * 获取多部门成员
	 * @param array $deparr 部门数组 ，默认为空获取全部
	 * @param Boolean $info 是否详情，默认false
	 * @return array:
	 */
	public function getDepUserArray($deparr='',$info=false){
		if (empty($deparr)) {
			$deparr=$this->getDep();
			foreach ($deparr as &$row ){
				$row=$row['id'];
			}
		}
		//获取多部门成员
		$temp=array();
		foreach ($deparr as $dep){
			$tmp=$this->getDepUser($dep,$info);
			$temp=array_merge($temp,$tmp);
		}
		//去重复	
		$list=array();	
		foreach ($temp as $row){
			    $tmp=$list[$row['userid']];
			    unset($row['department'][0]);
			    if(is_array($tmp)){
			    	//$row['department']=array_diff_key($row['department'],$tmp['department']);
			    	foreach ($row['department'] as $key=> $v){
			    		$tmp['department'][$key]=$v;
			    	}
			    	$row=$tmp;
			    }
				$list[$row['userid']]=$row;		
		}
		return $list;
	}
	/**
	 * 获取多部门成员
	 * @param array $deparr 部门数组 ，默认为空获取全部
	 * @param Boolean $info 是否详情，默认false
	 * @return array: 继承getDepUserArray
	 */
	public function getAlluser($deparr='',$info=false){
		$list=$this->getDepUserArray($deparr,$info);
		foreach ($list as &$row){
			$tmp=array();
			foreach ($row['department'] as &$d){
				$tmp[]=$d['name'];
			}
			$row['department']=implode(',', $tmp);
		}
		return $list;
	}
	/**
	 * 获取所在部门员工
	 * @param $userid 员工ID
	 * @param Boolean $info 是否详情，默认false
	 * @return array: 继承getAlluser
	 */
	public function getAtDepUser($userid,$info=false){
		$u=$this->getTUser($userid);
		$dep=array();
		foreach($u['department'] as $row){
			$dep[]=$row['id'];
		}
		$list=$this->getAlluser($dep,$info);
		return $list;
	}
	//免登录
	/**
	 * 通过code换取用户身份
	 * @return array
	 */
	public function getTUserInfo($code) {
		$rep = array (
				'CODE' => $code
		);
		$serch = array (
				'%CODE%'
		);
		$url='https://oapi.dingtalk.com/user/getuserinfo?code=%CODE%';
		$url = $this->Token_replace($serch, $rep, $url);
		$list = $this->gethttps ( $url );
		return $list;
	}
	/**
	 * 获取成员详情
	 */
	public function getTUser($userid) {
		$rep = array (
				'USERID' => $userid
		);
		$serch = array (
				'%USERID%'
		);
		$url='https://oapi.dingtalk.com/user/get?userid=%USERID%';
		$url = $this->Token_replace($serch, $rep, $url);
		//读取缓存
		$list = $this->getCache($url,'user_'.$userid);
		$dep_id=$list['department'];
		$list['department']='';
		$dep=$this->getDep();
		foreach ($dep_id as $k =>$v){
			$list['department'][$v]=$dep[$v];
		}
		return $list;
	}
	//免登录end
	/**
	 * 获取用户ID
	 * @return string
	 */
	public function getUserid() {
		return $this->userid;
	}
	/**
	 * 获取企业ID
	 * @return string
	 */
	public function getCorpid() {
		return $this->corpid;
	}
	/**
	 * 获取应用ID
	 * @return string
	 */
	public function getAgentID() {
		return $this->AgentID;
	}
	/**
	 * 获取应用密钥
	 * @return string
	 */
	public function getCorpsecret() {
		return $this->corpsecret;
	}
	/**
	 * 设置用户ID
	 * @param string $userid
	 */
	public function setUserid($userid){		 
		 $this->userid=$userid;
	}
	/**
	 * 设置业ID
	 * @param string $corpid
	 */
	public function setCorpid($corpid) {
		 $this->corpid=$corpid;
	}
	/**
	 * 设置应用ID
	 * @param string $corpid
	 */
	public function setAgentID($AgentID) {
		 $this->AgentID=$AgentID;
	}
	/**
	 * 设置应用密钥
	 * @param string $corpsecret
	 */
	public function setCorpsecret($corpsecret) {
		 $this->corpsecret=$corpsecret;
	}
	/**
	 * 检查是否包含字符
	 * @param string $str
	 * @param string $vchar
	 * @return boolean
	 */
	public function checkstring($str,$vchar){
		$tcity3 = "%*^#" . $vchar;
		$str = trim ( $str );
		if (strpos ( $tcity3, $str ) === false) {
			return false;
		} else {
			return true;
		}
	}
	/**
	 * 无缓存则发出http请求
	 * @param string $url 请求地址
	 * @param int $ctime 缓存时间 默认7200秒
	 * @return array 
	 */
	public function  getCache($url,$name=false,$ctime=7200){
		$time=time();
		$tmp=$name?F($name):array();
		unset($tmp['list']['errcode']);
		unset($tmp['list']['errmsg']);
		if(empty($tmp['list'])||$tmp['time']<$time || !$name||$ctime===true){
			$ctime===true&&$ctime=7200;
			$list = $this->gethttps ($url);			
			$tmp['list']=$list;
			$ctime=$list['errcode']>0?0:$ctime;
			unset($list['errcode']);
			unset($list['errmsg']);
			empty($list)&&$ctime=0;
			$tmp['time']=$time+$ctime;
			$name&&F($name,$tmp);
		}
		return $tmp['list'];
	}
	/**
	 * 替换时给url带上token
	 * @param array $serch
	 * @param array $rep
	 * @param string $url
	 * @return string
	 */
	public function Token_replace($serch,$rep,$url){
		$befor=strstr($url,"?",true); 
		$url.= $befor ?'&':'?';
		$url.='access_token=%ACCESS_TOKEN%';
		$rep1 = array (
				'ACCESS_TOKEN' => $this->getAccess_Token (),
		);
		$serch1 = array (
				'%ACCESS_TOKEN%',
		);
		$url = str_replace ($serch1, $rep1, $url );
		$url = str_replace ($serch, $rep, $url );
		return $url;
	}
	/**
	 * HTTPS GET 请求
	 * 
	 * @param string $url        	
	 * @return objc
	 */
	public function gethttps($url,$info=true) {
		$ch = curl_init ();
		// 设置你需要抓取的URL
		curl_setopt ( $ch, CURLOPT_URL, $url );
		// 设置header
		curl_setopt ( $ch, CURLOPT_HEADER, 0 );
		curl_setopt ( $ch, CURLOPT_SSL_VERIFYHOST, FALSE );
		curl_setopt ( $ch, CURLOPT_SSL_VERIFYPEER, FALSE );
		// 设置cURL 参数，要求结果保存到字符串中还是输出到屏幕上。
		curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
		// 运行cURL，请求网页
		$data = curl_exec ( $ch );
		// 关闭URL请求
		curl_close ( $ch );
		// return $data;
		$list=json_decode ( $data,$info );	
		if($list['errcode']==40014){$this->getAccess_Token(true);};	
		return $list;
	}
	/**
	 * 通过POST方式请求json数据
	 * @param string $url
	 * @param array $data
	 * @param string $header
	 * @param int $post
	 * @return Object
	 */
	function posthttps($url, $data,$header, $info=true) {
		$ch = curl_init ();
		$res = curl_setopt ( $ch, CURLOPT_URL, $url );
		curl_setopt ( $ch, CURLOPT_SSL_VERIFYHOST, FALSE );
		curl_setopt ( $ch, CURLOPT_SSL_VERIFYPEER, FALSE );
		curl_setopt ( $ch, CURLOPT_HEADER, 0 );
		curl_setopt ( $ch, CURLOPT_POST, 1 );
		curl_setopt ( $ch, CURLOPT_POSTFIELDS, $data );
		curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt ( $ch, CURLOPT_HTTPHEADER, $header );
		$result = curl_exec ( $ch );
		curl_close($ch);
		return json_decode ( $result,$info );
	}
	/**
	 * 获取当前完整url
	 * @return string
	 */
	public function get_url() {
		$sys_protocal = isset ( $_SERVER ['SERVER_PORT'] ) && $_SERVER ['SERVER_PORT'] == '443' ? 'https://' : 'http://';
		$php_self = $_SERVER ['PHP_SELF'] ? $_SERVER ['PHP_SELF'] : $_SERVER ['SCRIPT_NAME'];
		$path_info = isset ( $_SERVER ['PATH_INFO'] ) ? $_SERVER ['PATH_INFO'] : '';
		$relate_url = isset ( $_SERVER ['REQUEST_URI'] ) ? $_SERVER ['REQUEST_URI'] : $php_self . (isset ( $_SERVER ['QUERY_STRING'] ) ? '?' . $_SERVER ['QUERY_STRING'] : $path_info);
		return $sys_protocal . (isset ( $_SERVER ['HTTP_HOST'] ) ? $_SERVER ['HTTP_HOST'] : '') . $relate_url;
	}
	private function error() {
		die ( "<script>location.href='__PUBLIC__/404.html';</script>" );
	}
	//发送消息star
	/**
	 * 消息发送接口地址
	* @param int $type    会话类型，1:默认企业会话,2:普通会话,3:群会话
	 * @return Ambigous <string, mixed>
	 */
	private function getsendurl($type=1){
		$rep=$serch=array();
		//群会话地址 3
		$urlqun='https://oapi.dingtalk.com/chat/send';
		$urlqun=$this->Token_replace($serch, $rep, $urlqun);
		//普通会话地址 2
		$urlone='https://oapi.dingtalk.com/message/send_to_conversation';
		$urlone=$this->Token_replace($serch, $rep, $urlone);
		//企业会话地址  1
		$urlmore='https://oapi.dingtalk.com/message/send';
		$urlmore=$this->Token_replace($serch, $rep, $urlmore);
		switch ($type){
			case 3:
				return $urlqun;
				break;
			case 2:
				return $urlone;
				break;
			default:
				return $urlmore;
				break;
		}
	}
	/**
	 * 发送会话消息
	 * @param array $cont
	 * @param number $type  会话类型，1:默认企业会话,2:普通会话,3:群会话
	 * @return Ambigous <object, mixed>
	 */
	public function send($cont,$type=1){
		switch ($type){
			case 3:
				unset($cont['touser']);
				unset($cont['toparty']);
				unset($cont['cid']);
				break;
			case 2:
				unset($cont['touser']);
				unset($cont['toparty']);
				unset($cont['chatid']);
				break;
			default:
				$cont['agentid']=$this->getAgentID();
				$cont['touser']=implode('|', @$cont['touser']);
				$cont['toparty']=implode('|', @$cont['toparty']);
				unset($cont['sender']);
				unset($cont['cid']);
				unset($cont['chatid']);
				break;
		}
		$url=$this->getsendurl($type);
		$data=json_encode($cont);
		//echo $data;die;
		$header=array('Content-Type: application/json', 'Content-Length: ' . strlen($data));	
		return $this->posthttps($url, $data,$header);
	}
	/**
	 * 发送会话消息
	 * @param array $cont
	 * @param string $msgtype 默认文本消息，image：voice：file：link：oa：text
	 * @param int $type    会话类型，1:默认企业会话,2:普通会话,3:群会话
	 * @return Ambigous <object, mixed>
	 */
	public function sendMsg($cont,$msgtype='text',$type=1){
		switch ($msgtype){
			case 'image':
				return $this->sendImgMsg($cont,$type);
				break;
			case 'voice':
				return $this->sendVoiceMsg($cont,$type);
				break;
			case 'file':
				return $this->sendFileMsg($cont,$type);
				break;
			case 'link':
				return $this->sendLinkMsg($cont,$type);
				break;
			case 'oa':
				return $this->sendOAMsg($cont,$type);
				break;
			default:
				return  $this->sendTxtMsg($cont,$type);
				break;
		}
	}
	/**
	 * 发送文本消息
	 * @param array $cont
	 * @param int $type    会话类型，1:默认企业会话,2:普通会话,3:群会话
	 * @return Ambigous <object, mixed>
	 */
	public function sendTxtMsg($cont,$type=1){
		$cont['msgtype']='text';
 		$cont['text'] =array('content'=>$cont['content']);
 		unset($cont['content']);
		return $this->send($cont,$type);	
	}
	/**
	 * 发送图片消息
	 * @param array $cont
	 * @param int $type    会话类型，1:默认企业会话,2:普通会话,3:群会话
	 * @return Ambigous <object, mixed>
	 */
	public function sendImgMsg($cont,$type=1){
		$cont['msgtype']='image';
		$cont['image'] =array('media_id'=>$cont['media_id']);
 		unset($cont['media_id']);
		return $this->send($cont,$type);
	}
	/**
	 * 发送文件消息
	 * @param array $cont
	 * @param int $type    会话类型，1:默认企业会话,2:普通会话,3:群会话
	 * @return Ambigous <object, mixed>
	 */
	public function sendFileMsg($cont,$type=1){
		$cont['msgtype']='image';
 		empty($cont['media_id'])&&$cont['media_id']='@lALOACZwe2Rk';//默认图片
		$cont['file'] =array('media_id'=>$cont['media_id']);
 		unset($cont['media_id']);
		return $this->send($cont,$type);
	}
	/**
	 * 发送声音消息
	 * @param array $cont
	  * @param int $type    会话类型，1:默认企业会话,2:普通会话,3:群会话
	 * @return Ambigous <object, mixed>
	 */
	public function sendVoiceMsg($cont,$type=1){
		$cont['msgtype']='image';
		$cont['voice'] =array(
				'media_id'=>$cont['media_id'],
				'duration'=>10
		);
 		unset($cont['media_id']);
		return $this->send($cont,$type);
	}
	/**
	 * 发送链接消息
	 * @param array $cont
     * @param int $type    会话类型，1:默认企业会话,2:普通会话,3:群会话
	 * @return Ambigous <object, mixed>
	 */
	public function sendLinkMsg($cont,$type=1){
		$link=array(
			'messageUrl'=>$cont['messageUrl'],
			'picUrl'=>$cont['picUrl'],
			'title'=>$cont['title'],
			'text'=>$cont['text']
		);
		$cont['msgtype']='link';
		$cont=array(
			'touser'=>$cont['touser'],//企业会话
		    'toparty'=>$cont['toparty'],//企业会话
			'sender'=>$cont['sender'],//群会话，普通会话
			'cid'=>$cont['cid'],//普通会话
			'chatid'=>$cont['chatid'],//群会话
			'msgtype'=>'link',
			'link'=>$link
		);
		return $this->send($cont,$type);
	}
	
	/**
	 * 发送OA消息
	 * @param array $cont
	 * @param int $type    会话类型，1:默认企业会话,2:普通会话,3:群会话
	 * @return Ambigous <object, mixed>
	 */
	public function sendOAMsg($cont,$type=1){
		//默认公司名称作为消息标题
		$dep=$this->getDep();
		empty($cont['title'])&&$cont['title']=$dep[1]['name'];
		$oa=array();
		//oa head
		$head['bgcolor']=!empty($cont['bgcolor'])?$cont['bgcolor']:"FF1f72cd";
		$head['text']=!empty($cont['text'])?$cont['text']:$cont['title'];
		//oa body
		$body['title']=$cont['title'];
		!empty($cont['form'])&&$body['form']=$cont['form'];
		!empty($cont['content'])&&$body['content']=$cont['content'];
		!empty($cont['author'])&&$body['author']=$cont['author'];
		!empty($cont['rich'])&&$body['rich']=$cont['rich'];
		//oa
		!empty($cont['message_url'])&&$oa['message_url']=$cont['message_url'];
		!empty($cont['pc_message_url'])&&$oa['pc_message_url']=$cont['pc_message_url'];
		$oa['head']=$head;
		$oa['body']=$body;
		//content
		$cont['msgtype']='oa';
		$cont['oa']=$oa;
		$cont=array(
			   'touser'=>$cont['touser'],//企业会话
		       'toparty'=>$cont['toparty'],//企业会话
			   'sender'=>$cont['sender'],//群会话，普通会话
			   'cid'=>$cont['cid'],//普通会话
			   'chatid'=>$cont['chatid'],//群会话
			   'msgtype'=>'oa',
			   'oa'=>$oa
		);
		return $this->send($cont,$type);	
	}
	//发送消息 end

	//文件上传 star
	public function uploads($data,$type){
		$media='{'.$data['filename'].','.$data['Length'].',multipart/form-data}';
		$rep = array (
				'TYPE' => $data['type'],
				'FILENAME' => $data['filename'],
				'LENGTH' => $data['Length']
		);
		$data=array(
			    'media'	=> $media
		);
		//print_r($data);die;
		$rep = array (
				'TYPE' => $type
		);
		$serch = array (
				'%TYPE%'
		);
		$url = 'https://oapi.dingtalk.com/media/upload?type=%TYPE%';
		$url=$this->Token_replace($serch, $rep, $url);
		$header=array('Content-Type: multipart/form-data');
		//$data=json_encode($data);
		//echo $data;
		$list=$this->posthttps($url, $data, $header);
		return $list;
	}
}