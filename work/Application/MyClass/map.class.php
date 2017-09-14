<?php

/**
 * @author xialei <xialeistudio@gmail.com>
*/
class map
{
	//private static $_instance;
	private  $ip;
	private  $key='Msok7YsCeGRMsBcOduzFomShxMg5WUDv';
	private  $ippos;
	private  $gpspos;
	const REQ_GET = 1;
	const REQ_POST = 2;

	/**
	 * 单例模式
	 * @return map
	 // */
	// public static function instance()
	// {
		// if (!self::$_instance instanceof self)
		// {
			// self::$_instance = new self;
		// }
		// return self::$_instance;
	// }

	/**
	 * 执行CURL请求
	 * @author: xialei<xialeistudio@gmail.com>
	 * @param $url
	 * @param array $params
	 * @param bool $encode
	 * @param int $method
	 * @return mixed
	 */
	private function async($url, $params = array(), $encode = true, $method = self::REQ_GET)
	{
		$ch = curl_init();
		if ($method == self::REQ_GET)
		{
			$url = $url . '?' . http_build_query($params);
			$url = $encode ? $url : urldecode($url);
			curl_setopt($ch, CURLOPT_URL, $url);
		}
		else
		{
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
		}
		curl_setopt($ch, CURLOPT_REFERER, '百度地图referer');
		curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (iPhone; CPU iPhone OS 7_0 like Mac OS X; en-us) AppleWebKit/537.51.1 (KHTML, like Gecko) Version/7.0 Mobile/11A465 Safari/9537.53');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$resp = curl_exec($ch);
		curl_close($ch);
		return $resp;
	}

	/**
	 * ip定位
	 * @param string $ip
	 * @return array
	 * @throws Exception
	 */
	public function locationByIP($ip='')
	{
		$ip=$ip?$ip:$this->getip();
		//检查是否合法IP
		if (!filter_var($ip, FILTER_VALIDATE_IP))
		{
			//throw new Exception('ip地址不合法');
		}
		$params = array(
				'ak' => $this->getkey(),
				'ip' => $ip,
				'coor' => 'bd09ll'//百度地图GPS坐标
		);
		$api = 'http://api.map.baidu.com/location/ip';
		$resp = $this->async($api, $params);
		$data = json_decode($resp, true);
		//有错误
		if ($data['status'] != 0)
		{
			//throw new Exception($data['message']);
			return $params ;
		}
		//返回地址信息
		$pos= array(
				'address' => $data['content']['address'],
				'province' => $data['content']['address_detail']['province'],
				'city' => $data['content']['address_detail']['city'],
				'district' => $data['content']['address_detail']['district'],
				'street' => $data['content']['address_detail']['street'],
				'street_number' => $data['content']['address_detail']['street_number'],
				'city_code' => $data['content']['address_detail']['city_code'],
				'lng' => $data['content']['point']['x'],
				'lat' => $data['content']['point']['y'],
				'ip' => $ip
		);
		$this->setippos($pos);
		return $this->getippos();
	}


	/**
	 * GPS定位
	 * @param $lng
	 * @param $lat
	 * @return array
	 * @throws Exception
	 */
	public function locationByGPS($lng, $lat)
	{
		$params = array(
				'coordtype' => 'wgs84ll',
				'location' => $lat . ',' . $lng,
				'ak' => $this->getkey(),
				'output' => 'json',
				'pois' => 0
		);
		$resp = $this->async('http://api.map.baidu.com/geocoder/v2/', $params, false);
		$data = json_decode($resp, true);
		if ($data['status'] != 0)
		{
			throw new Exception($data['message']);
		}
		$pos= array(
				'address' => $data['result']['formatted_address'],
				'province' => $data['result']['addressComponent']['province'],
				'city' => $data['result']['addressComponent']['city'],
				'street' => $data['result']['addressComponent']['street'],
				'street_number' => $data['result']['addressComponent']['street_number'],
				'city_code'=>$data['result']['cityCode'],
				'lng'=>$data['result']['location']['lng'],
				'lat'=>$data['result']['location']['lat']
		);
		$this->setgpspos($pos);
		return $this->getgpspos();
	}
	/**
	 * 获取当前ip,真实ip
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
	 * 获取ip
	 *
	 */
	public function getip(){
		$this->ip=trim($this->ip);
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
		$this->ip=trim($this->ip);
	}
	/**
	 * 获取key
	 *
	 */
	public function getkey(){
		$this->key=trim($this->key);
		$key=$this->key;
		return $key;
	}
	/**
	 * 设置key
	 * @param string $key
	 */
	public function setkey($key=false){
		$this->key=$key?$key:$this->getkey();
		$this->key=trim($this->key);
	}
	/**
	 * 获取ip position
	 *
	 */
	public function getippos(){
		//$this->key=trim($this->key);
		$position=$this->ippos;
		return $position;
	}
	/**
	 * 设置ip position
	 * @param array $position
	 */
	public function setippos($position=false){
		$this->ippos=$position?$position:$this->getippos();
		//$this->key=trim($this->key);
	}
	/**
	 * 获取gps position
	 *
	 */
	public function getgpspos(){
		//$this->key=trim($this->key);
		$position=$this->gpspos;
		return $position;
	}
	/**
	 * 设置gps position
	 * @param array $position
	 */
	public function setgpspos($position=false){
		$this->gpspos=$position?$position:$this->getgpspos();
		//$this->key=trim($this->key);
	}
}