<?php
namespace MyClass;
class Sms{
	
public function send($mobile,$content){
	
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); 
header("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT"); 
header("Cache-Control: no-cache, must-revalidate"); 
header("Pramga: no-cache"); 

		 
	    // echo 1003;
// $randStr = str_shuffle('1234567890');
// $rand = substr($randStr,0,4); 
// $_SESSION["mbvfcode"]=$rand;
$url='http://61.147.98.117:9015';//系统接口地址
//$content=urlencode("【超宝验证】您的验证码是:".$rand.",5分钟后过期，请您及时验证!");
//echo  $content;die;
$content =iconv( "UTF-8", "GBK", $content );
$content=urlencode($content);
$username="18628284721";//用户名
$password="bGlzdGVyMTIz";//密码百度BASE64加密后密文
// $username="15982290690";//用户名
// $password="MTIzMTIz";//密码百度BASE64加密后密文
// $mobile="13866194291";
$url=$url."/servlet/UserServiceAPI?method=sendSMS&extenno=&isLongSms=0&username=".$username."&password=".$password."&smstype=1&mobile=".$mobile."&content=".$content;
//echo $url;
$html = file_get_contents($url);
//echo $html.'0001';
//var_dump(strpos($html,"success"));
if(strpos($html,"success")!==false){
   return  true ;
}else{
	return false;
}
}
function isMobile($mobile) {
    if (!is_numeric($mobile)) {
        return false;
    }
    return preg_match('#^13[\d]{9}$|^14[5,7]{1}\d{8}$|^15[^4]{1}\d{8}$|^17[0,6,7,8]{1}\d{8}$|^18[\d]{9}$#', $mobile) ? true : false;
}
}