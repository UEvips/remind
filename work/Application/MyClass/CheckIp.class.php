<?php
class CheckIp{
	private  $ip;
	private  $iparr;
	private  $error;	
	public function __construct($iparr='',$ip=false){
		$this->setiparr($iparr);
		$this->setip($ip);
	}
	/**
	 * ip是否在限制内
	 */
	public function IpIsOk(){
		$iparr=$this->getiparr();
		$ip=$this->getip();
		foreach ($iparr as $row){
			$sip=@$row[0];
			$eip=@$row[1];
			$isok=$this->CheckOne($sip, $eip, $ip);
			if($isok){
				$this->seterror(@$row[2]);
				return true;
			}
		}
		return false;
	}
	/**
	 * 判断ip范围，
	 */
	public function CheckOne($sip,$eip,$ip){
		//只有sip or eip 则只限制单独ip
		empty($sip)&&$sip=$eip;
		empty($eip)&&$eip=$sip;
		if($sip==$eip&&$sip==$ip){
			return true;
		}
		$sips=$this->explodeip($sip);
		$eips=$this->explodeip($eip);
		$ips=$this->explodeip($ip);
		$end=count($sips);
		for($i=0;$i<$end;$i++){
			$sp=intval($sips[$i]);
			$ep=intval($eips[$i]);
			$p=intval($ips[$i]);
			$isok=($p >=$sp && $p <=$ep);
			if(!$isok){
				return false;
			}elseif($sp!=$ep){
				return true;
			}		
		}
		return true;
	}
/**
 *IP地区限制
 * @param unknown $address
 * @param unknown $city
 * @return boolean
 */
	public function checkaddres($city='',$address=''){
		empty($address)&&$address=$this->getipplaces();
		$tcity3 = "%**#".$address;
		$city=trim($city);
		empty($city)&&$city='*';
		if(strpos($tcity3,$city) === false){
			return false;
		}else{
			return true;
		}
	}
	/**
	 *将ip拆成数组 
	 */
	public function explodeip($ip){
		$ip=explode('.', $ip);
		return $ip;
	}			
	/**
	 * 设置需限制的ip段数组
	 * @param array $iparr
	 */
	public function setiparr($iparr){
		!is_array($iparr)&&$iparr=array();
		foreach ($iparr as &$row){
			$arr='';
			foreach ($row as $k=>$v){
				$arr[]=$v;
			}
			$row=$arr;
		}
		$this->iparr=$iparr;
	}
	/**
	 * 获取需限制的ip段数组
	 */
	public function getiparr(){
		return $this->iparr;
	}
	/**
	 * 获取当前ip
	 * 
	 */
	public function get_real_ip(){
		$ip=false;
		if(!empty($_SERVER['HTTP_CLIENT_IP'])){
			$ip=$_SERVER['HTTP_CLIENT_IP'];
		}
			if(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){
				$ips=explode (', ', $_SERVER['HTTP_X_FORWARDED_FOR']);
				if($ip){ array_unshift($ips, $ip); $ip=FALSE; }
				for ($i=0; $i < count($ips); $i++){
					if(!eregi ('^(10│172.16│192.168).', $ips[$i])){
						$ip=$ips[$i];
						break;
					}
				}
			}
			return ($ip ? $ip : $_SERVER['REMOTE_ADDR']);
	}
	/**
	 * 获取ip地区
	 * @param string $ip
	 * @return string
	 */
	public function getipplaces($ip='')
	{
		$ip = empty($ip)?$this->getip():$ip;
		$ipInfos = $this->getrealplace($ip);
		$str=trim($ipInfos['province']).trim($ipInfos['city']).trim($ipInfos['district']).' '.trim($ipInfos['carrier']);
		$str=str_replace(array('none','None'), array('',''), $str);
		return $str;
	}
	/**
	 * 获取ip地区
	 * @param string $ip
	 * @return boolean|Ambigous <string, mixed>
	 */
	function getrealplace($ip=''){
		$ip = empty($ip)?$this->getip():$ip;
		//message($ip);
		$ch = curl_init();
		$url = "http://apis.baidu.com/apistore/iplookupservice/iplookup?ip=$ip";
		$header = array(
				'apikey: e667007d15913fb46aeaf9bb3f866349',
		);
		// 添加apikey到header
		curl_setopt($ch, CURLOPT_HTTPHEADER  , $header);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		// 执行HTTP请求
		curl_setopt($ch , CURLOPT_URL , $url);
		$res = curl_exec($ch);
		$real = json_decode($res,true);
		return $real = $real['retData'];
		//var_dump(json_decode($res)).die();
	}
	/**
	 * 获取ip地区
	 * @param string $ip
	 * @return boolean|Ambigous <string, mixed>
	 */
	public function GetIpLookup($ip = ''){
		$ip = empty($ip)?$this->getip():$ip;
		$res = @file_get_contents('http://int.dpool.sina.com.cn/iplookup/iplookup.php?format=js&ip=' . $ip);
		if(empty($res)){ return false; }
		$jsonMatches = array();
		preg_match('#\{.+?\}#', $res, $jsonMatches);
		if(!isset($jsonMatches[0])){ return false; }
		$json = json_decode($jsonMatches[0], true);
		if(isset($json['ret']) && $json['ret'] == 1){
			$json['ip'] = $ip;
			unset($json['ret']);
		}else{
			return false;
		}
		return $json;
	}
	/**
	 * 获取错误
	 * 
	 */
	public function geterror(){
		$error=$this->error;
		return $error;
	}
	/**
	 * 设置错误
	 * @param string $error
	 */
	public function seterror($error){
		$this->error=$error?$error:'主动屏蔽，您涉嫌违规行为已被管理员屏蔽';
	}
	/**
	 * 获取ip
	 * 
	 */
	public function getip(){
		$ip=$this->ip;
		$ip=$ip?$ip:$this->get_real_ip();
		return $ip;
	}
	/**
	 * 设置ip
	 * @param string $ip
	 */
	public function setip($ip=false){
		$this->ip=$ip?$ip:$this->getip();
	}
}