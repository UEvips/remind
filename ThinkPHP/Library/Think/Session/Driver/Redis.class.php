<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2014 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
namespace Think\Session\Driver;
  /**
    *自定义redis处理 驱动
    */
Class SessionRedis {
  //	REDIS连接对象
  Private $redis;
  
  //	SESSION有效时间
  Private $expire;
  
  //	functions.php有定义默认执行方法为execute
  //	具体代码可参考Common/functions.php中，搜索session，可查询到相关session自动执行的方法
  Public function execute () {
  session_set_save_handler(
    array(&$this,"open"),
    array(&$this,"close"),
    array(&$this,"read"),
    array(&$this,"write"),
    array(&$this,"destroy"),
    array(&$this,"gc"));
  }

    /**
     * 打开Session 
     * @access public 
     * @param string $savePath 
     * @param mixed $sessName  
     */
  Public function open ($path, $name) {
    $this->expire = C('SESSION_EXPIRE') ? C('SESSION_EXPIRE') : ini_get('session.gc_maxlifetime');
    $this->redis = new Redis();
    return $this->redis->connect(C('REDIS_HOST'),C('REDIS_PORT'));
  }
  
    /**
     * 关闭Session 
     * @access public 
     */
  Public function close () {
    return $this->redis->close();
  }
  
    /**
     * 读取Session 
     * @access public 
     * @param string $sessID 
     */
  Public function read ($id) {
    $id = C('SESSION_PREFIX').$id;
    $data = $this->redis->get($id);
    return $data ? $data : '';
  }
  
    /**
     * 写入Session 
     * @access public 
     * @param string $sessID 
     * @param String $sessData  
     */
  Public function write ($id, $data) {
    $id = C('SESSION_PREFIX').$id;
    return $this->redis->set($id, $data, $this->expire);
  }
  
    /**
     * 删除Session 
     * @access public 
     * @param string $sessID 
     * 销毁SESSION
     */	
  Public function destroy ($id) {
    $id = C('SESSION_PREFIX').$id;
    return $this->redis->delete($id);
  }
  
    /**
     * Session 垃圾回收
     * @access public 
     * @param string $sessMaxLifeTime 
     */
  Public function gc ($maxLifeTime) {
    return true;
  }
}