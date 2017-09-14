<?php
namespace MyClass;
class Download{
	//下载文件检测
	public function FileExists($path=''){
        $path =trim($path);
        if(empty($path)){
			 throw new Exception("The file path is null");return false;
		}		
		//检查文件是否存在   	
        if (! file_exists ($path)){  		
           throw new Exception("The file does not exist(".$path.')');return false;
        }
		return true;
	}
	//获取文件名
	public function getFileName($path='',$file_name=''){
		$path=trim($path);
        $Suffix=strrchr($path,'.');
		$file_name=trim($file_name);
		$file_name=empty($file_name)?strrchr($path,'/'):$file_name.$Suffix;
		$file_name=str_replace(array('/',$Suffix),array('',''),$file_name);
		$file_name=$file_name.$Suffix;
		return trim($file_name);
		
	}
	//fread 方式下载
	public function FreadDown($path='',$file_name=''){
		$path=trim($path);
		$file_name=$this->getFileName($path,$file_name);
		try{
		   $this->FileExists($path);
		}catch(Exception $e) {
		   $ex=$e->getMessage();
           throw new Exception($ex);return false;
        }
        //打开文件    
        $file = fopen ($path, "r" );    
        //输入文件标签     
        Header ( "Content-type: application/octet-stream" );    
        Header ( "Accept-Ranges: bytes" );    
        Header ( "Accept-Length: " . filesize ($path) ); 
        //处理中文文件名
        $ua = $_SERVER["HTTP_USER_AGENT"];
        $encoded_filename = rawurlencode($file_name);
        if (preg_match("/MSIE/", $ua)) {
           header('Content-Disposition: attachment; filename="' . $encoded_filename . '"');
        } else if (preg_match("/Firefox/", $ua)) {
           header("Content-Disposition: attachment; filename*=\"utf8''" . $file_name . '"');
        } else {
           header('Content-Disposition: attachment; filename="' . $file_name . '"');
        }   
        //输出文件内容     
        //读取文件内容并直接输出到浏览器    
        echo fread ( $file, filesize ( $path) );    
        fclose ( $file );    
        exit ();    
	}
	//header 方式下载，不安全
	public function HeaderDown($path=''){
		$path=trim($path);
		try{
		   $this->FileExists($path);
		}catch(Exception $e) {
		   $ex=$e->getMessage();
           throw new Exception($ex);return false;
        }
		//重新定向浏览器指向    
        Header("Location: ".$_SERVER['REQUEST_SCHEME']."://".$_SERVER['SERVER_NAME'].':'.$_SERVER['SERVER_PORT'].'/'.$path);    
        exit; 
	}
	//readfile 方式下载
	public function ReadfileDown($path='',$file_name=''){ 
		$path=trim($path);
		$file_name=$this->getFileName($path,$file_name);
		try{
		   $this->FileExists($path);
		}catch(Exception $e) {
		   $ex=$e->getMessage();
           throw new Exception($ex);return false;
        } 
        header("Content-type: application/octet-stream");   
        Header ( "Accept-Ranges: bytes" );  
        //处理中文文件名
        $ua = $_SERVER["HTTP_USER_AGENT"];
        $encoded_filename = rawurlencode($file_name);
        if (preg_match("/MSIE/", $ua)) {
           header('Content-Disposition: attachment; filename="' . $encoded_filename . '"');
        } else if (preg_match("/Firefox/", $ua)) {
           header("Content-Disposition: attachment; filename*=\"utf8''" . $file_name . '"');
        } else {
           header('Content-Disposition: attachment; filename="' . $file_name . '"');
        } 
        header("Content-Length: ". filesize($path));
        readfile($path);exit; 
	}
	//让Xsendfile发送文件,需apache支持，有bug暂不能下载
	public function XsendfileDown($path='',$file_name=''){
		$path=trim($path);
		$file_name=$this->getFileName($path,$file_name);
		try{
		   $this->FileExists($path);
		}catch(Exception $e) {
		   $ex=$e->getMessage();
           throw new Exception($ex);return false;
        } 
        header("Content-type: application/octet-stream");   
        Header ( "Accept-Ranges: bytes" );  
        //处理中文文件名
        $ua = $_SERVER["HTTP_USER_AGENT"];
        $encoded_filename = rawurlencode($file_name);
        if (preg_match("/MSIE/", $ua)) {
           header('Content-Disposition: attachment; filename="' . $encoded_filename . '"');
        } else if (preg_match("/Firefox/", $ua)) {
           header("Content-Disposition: attachment; filename*=\"utf8''" . $file_name . '"');
        } else {
           header('Content-Disposition: attachment; filename="' . $file_name . '"');
        }    
		//让Xsendfile发送文件
         header("X-Sendfile: ".$path);
		
	}
}