<?php

namespace MyClass;

/**
 * 钉钉接口
 * @author Administrator
 *
 */
class DingTalk {

    private $corpid = 'ding8b106a8e950b756b35c2f4657eb6378f';
    private $corpsecret = 'WWhQV7ALONGXpX1vjHpxwoRyBvMGqyztxzNAYWuF1kLaOsIqGZWEkJXCewQE0HOp';
    private $userid = '';
    private $AgentID = '78750268';
    private $DpgentID = '76756575';
    private $appid = 'dingoauiv6cd1wwgup0xgn';
    private $appSecret = "aYzyjGJ0IGCW1RFE8bpKcTiUX9Swq-dXXHL6hSstc7YYCsV6M7Pmi5mbHlXxHkif";
    private $huiurl = "http://work.cdxtime.com/remind.php/index/index";

    public function getConf() {
        return array(
            'corpid' => $this->corpid,
            'corpsecret' => $this->corpsecret,
            'AgentID' => $this->AgentID,
        );
    }

    public function getlogin() {
        return array(
            'appid' => $this->appid,
            'appSecret' => $this->appSecret,
            'huiurl' => $this->huiurl,
        );
    }

    /**
     * 获取token
     */
    public function getAccess_Token($info = 7200) {
        $serch = array(
            '%CORPID%',
            '%SECRECT%'
        );
        // 请求token地址
        $url = 'https://oapi.dingtalk.com/gettoken?corpid=%CORPID%&corpsecret=%SECRECT%';
        $rep = array(
            'corpid' => $this->corpid,
            'corpsecret' => $this->corpsecret
        );
        $url = str_replace($serch, $rep, $url);
        //读取缓存
        $token = $this->getCache($url, 'access_token', $info);
        return $token ['access_token'];
    }

    /**
     * 网页授权，获取临时授权码code
     */
    public function oauth2($uri = false) {
        $uri = $uri ? $uri : $this->get_url();
        $uri = urlencode($uri);
        $rep = array(
            'corpid' => $this->getCorpid(),
            'redirect_uri' => $uri
        );
        $serch = array(
            '%CORPID%',
            '%REDIRECT_URI%'
        );
        $url = 'https://oapi.dingtalk.com/connect/oauth2/authorize?appid=%CORPID%&redirect_uri=%REDIRECT_URI%&response_type=code&scope=SCOPE&state=STATE';
        $url = str_replace($serch, $rep, $url);
        header("Location: $url");
        die();
    }

    /**
     * 获取部门列表 or 部门详情
     *
     * @param int $id   	
     * @return objc
     */
    public function getDep($id = '') {
        $rep = array(
            'ID' => $id
        );
        $serch = array(
            '%ID%'
        );
        $url = "https://oapi.dingtalk.com/department/list";
        if ($id != '') {
            $url = 'https://oapi.dingtalk.com/department/get?id=%ID%';
        }
        $url = $this->Token_replace($serch, $rep, $url);
        //读取缓存
        $list = $this->getCache($url, 'dep_' . $id);
        //print_r($list);
        if (!$id) {
            $list = $list['department'];
            $tmp = array();
            foreach ($list as $row) {
                $tmp[$row['id']] = $row;
            }
            $list = $tmp;
        }
        return $list;
    }

    /**
     * 获取部门员工
     * @param int $dep_id
     * @param Boolean $info 是否详情，默认false
     * @return objc
     */
    
    public function getDepUser($dep_id, $info = false) {
        $rep = array(
            'ID' => $dep_id
        );
        $serch = array(
            '%ID%'
        );
        $url = 'https://oapi.dingtalk.com/user/simplelist?department_id=%ID%';
        if ($info) {
            $url = 'https://oapi.dingtalk.com/user/list?department_id=%ID%';
        }
        $url = $this->Token_replace($serch, $rep, $url);
        //读取缓存
        $name = 'dep_user_' . $dep_id . '_' . ($info ? '1' : '0');
        $list = $this->getCache($url, $name);
        $list = $list['userlist'];
        foreach ($list as &$row) {
            if ($info) {
                $dep_id = $row['department'];
                $row['department'] = '';
                $dep = $this->getDep();
                foreach ($dep_id as $k => $v) {
                    $row['department'][$v] = $dep[$v];
                }
            } else {
                $dep = $this->getDep();
                $row['department'][0] = $dep[$dep_id];
                $row['department'][$dep_id] = $dep[$dep_id];
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
    public function getDepUserArray($deparr = '', $info = false) {
        if (empty($deparr)) {
            $deparr = $this->getDep();
            foreach ($deparr as &$row) {
                $row = $row['id'];
            }
        }
        //获取多部门成员
        $temp = array();
        foreach ($deparr as $dep) {
            $tmp = $this->getDepUser($dep, $info);
            $temp = array_merge($temp, $tmp);
        }
        //去重复	
        $list = array();
        foreach ($temp as $row) {
            $tmp = $list[$row['userid']];
            unset($row['department'][0]);
            if (is_array($tmp)) {
                //$row['department']=array_diff_key($row['department'],$tmp['department']);
                foreach ($row['department'] as $key => $v) {
                    $tmp['department'][$key] = $v;
                }
                $row = $tmp;
            }
            $list[$row['userid']] = $row;
        }
        return $list;
    }

    /**
     * 获取多部门成员
     * @param array $deparr 部门数组 ，默认为空获取全部
     * @param Boolean $info 是否详情，默认false
     * @return array: 继承getDepUserArray
     */
    public function getAlluser($deparr = '', $info = false) {
        $list = $this->getDepUserArray($deparr, $info);
        foreach ($list as &$row) {
            $tmp = array();
            foreach ($row['department'] as &$d) {
                $tmp[] = $d['name'];
            }
            $row['department'] = implode(',', $tmp);
        }
        return $list;
    }

    /**
     * 获取所在部门员工
     * @param $userid 员工ID
     * @param Boolean $info 是否详情，默认false
     * @return array: 继承getAlluser
     */
    public function getAtDepUser($userid, $info = false) {
        $u = $this->getTUser($userid);
        $dep = array();
        foreach ($u['department'] as $row) {
            $dep[] = $row['id'];
        }

        $list = $this->getAlluser($dep, $info);
        return $list;
    }

    //免登录
    /**
     * 通过code换取用户身份
     * @return array
     */
    public function getTUserInfo($code) {
        $rep = array(
            'CODE' => $code
        );
        $serch = array(
            '%CODE%'
        );
        $url = 'https://oapi.dingtalk.com/user/getuserinfo?code=%CODE%';
        $url = $this->Token_replace($serch, $rep, $url);
        $list = $this->gethttps($url);
        return $list;
    }

    /**
     * 获取成员详情
     */
    public function getTUser($userid) {
        $rep = array(
            'USERID' => $userid
        );
        $serch = array(
            '%USERID%'
        );
        $url = 'https://oapi.dingtalk.com/user/get?userid=%USERID%';
        $url = $this->Token_replace($serch, $rep, $url);
        //读取缓存
        $list = $this->getCache($url, 'user_' . $userid);
        $dep_id = $list['department'];
        $list['department'] = '';
        $dep = $this->getDep();
        foreach ($dep_id as $k => $v) {
            $list['department'][$v] = $dep[$v];
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
    public function setUserid($userid) {
        $this->userid = $userid;
    }

    /**
     * 设置业ID
     * @param string $corpid
     */
    public function setCorpid($corpid) {
        $this->corpid = $corpid;
    }

    /**
     * 设置应用ID
     * @param string $corpid
     */
    public function setAgentID($AgentID) {
        $this->AgentID = $AgentID;
    }

    /**
     * 设置应用密钥
     * @param string $corpsecret
     */
    public function setCorpsecret($corpsecret) {
        $this->corpsecret = $corpsecret;
    }

    /**
     * 检查是否包含字符
     * @param string $str
     * @param string $vchar
     * @return boolean
     */
    public function checkstring($str, $vchar) {
        $tcity3 = "%*^#" . $vchar;
        $str = trim($str);
        if (strpos($tcity3, $str) === false) {
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
    public function getCache($url, $name = false, $ctime = 7200, $info = true) {
        $time = time();
        $tmp = $name ? F($name) : array();
        unset($tmp['list']['errcode']);
        unset($tmp['list']['errmsg']);
        if (empty($tmp['list']) || $tmp['time'] < $time || !$name || $ctime === true) {
            $ctime === true && $ctime = 7200;
            $list = $this->gethttps($url, $info);
            $tmp['list'] = $list;
            $ctime = $list['errcode'] > 0 ? 0 : $ctime;
            unset($list['errcode']);
            unset($list['errmsg']);
            empty($list) && $ctime = 0;
            $tmp['time'] = $time + $ctime;
            $name && F($name, $tmp);
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
    public function Token_replace($serch, $rep, $url) {
        $befor = strstr($url, "?", true);
        $url.= $befor ? '&' : '?';
        $url.='access_token=%ACCESS_TOKEN%';
        $rep1 = array(
            'ACCESS_TOKEN' => $this->getAccess_Token(),
        );
        $serch1 = array(
            '%ACCESS_TOKEN%',
        );
        $url = str_replace($serch1, $rep1, $url);
        $url = str_replace($serch, $rep, $url);
        return $url;
    }

    /**
     * HTTPS GET 请求
     * 
     * @param string $url        	
     * @return objc
     */
    public function gethttps($url, $info = true) {
        $ch = curl_init();
        // 设置你需要抓取的URL
        curl_setopt($ch, CURLOPT_URL, $url);
        // 设置header
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        // 设置cURL 参数，要求结果保存到字符串中还是输出到屏幕上。
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        // 运行cURL，请求网页
        $data = curl_exec($ch);
        // 关闭URL请求
        curl_close($ch);
        // return $data;	
        if ($info === -1) {
            $list = $data;
        } else {
            $list = json_decode($data, $info);
            if ($list['errcode'] == 40014) {
                $this->getAccess_Token(true);
            };
        }
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
    function posthttps($url, $data, $header, $info = true) {
        $ch = curl_init();
        $res = curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        $result = curl_exec($ch);
        curl_close($ch);
        return json_decode($result, $info);
    }

    /**
     * 获取当前完整url
     * @return string
     */
    public function get_url() {
        $sys_protocal = isset($_SERVER ['SERVER_PORT']) && $_SERVER ['SERVER_PORT'] == '443' ? 'https://' : 'http://';
        $php_self = $_SERVER ['PHP_SELF'] ? $_SERVER ['PHP_SELF'] : $_SERVER ['SCRIPT_NAME'];
        $path_info = isset($_SERVER ['PATH_INFO']) ? $_SERVER ['PATH_INFO'] : '';
        $relate_url = isset($_SERVER ['REQUEST_URI']) ? $_SERVER ['REQUEST_URI'] : $php_self . (isset($_SERVER ['QUERY_STRING']) ? '?' . $_SERVER ['QUERY_STRING'] : $path_info);
        return $sys_protocal . (isset($_SERVER ['HTTP_HOST']) ? $_SERVER ['HTTP_HOST'] : '') . $relate_url;
    }

    private function error() {
        die("<script>location.href='__PUBLIC__/404.html';</script>");
    }

    //发送消息star
    /**
     * 消息发送接口地址
     * @param int $type    会话类型，1:默认企业会话,2:普通会话,3:群会话
     * @return Ambigous <string, mixed>
     */
    private function getsendurl($type = 1) {
        $rep = $serch = array();
        //群会话地址 3
        $urlqun = 'https://oapi.dingtalk.com/chat/send';
        $urlqun = $this->Token_replace($serch, $rep, $urlqun);
        //普通会话地址 2
        $urlone = 'https://oapi.dingtalk.com/message/send_to_conversation';
        $urlone = $this->Token_replace($serch, $rep, $urlone);
        //企业会话地址  1
        $urlmore = 'https://oapi.dingtalk.com/message/send';
        $urlmore = $this->Token_replace($serch, $rep, $urlmore);
        switch ($type) {
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
    public function send($cont, $type = 1) {
        switch ($type) {
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
                $cont['agentid'] = $this->getAgentID();
                $cont['touser'] = implode('|', @$cont['touser']);
                $cont['toparty'] = implode('|', @$cont['toparty']);
                unset($cont['sender']);
                unset($cont['cid']);
                unset($cont['chatid']);
                break;
        }
        $url = $this->getsendurl($type);
        $data = json_encode($cont);
        //echo $data;die;
        $header = array('Content-Type: application/json', 'Content-Length: ' . strlen($data));
        return $this->posthttps($url, $data, $header);
    }

    /**
     * 发送会话消息
     * @param array $cont
     * @param string $msgtype 默认文本消息，image：voice：file：link：oa：text
     * @param int $type    会话类型，1:默认企业会话,2:普通会话,3:群会话
     * @return Ambigous <object, mixed>
     */
    public function sendMsg($cont, $msgtype = 'text', $type = 1) {
        switch ($msgtype) {
            case 'image':
                return $this->sendImgMsg($cont, $type);
                break;
            case 'voice':
                return $this->sendVoiceMsg($cont, $type);
                break;
            case 'file':
                return $this->sendFileMsg($cont, $type);
                break;
            case 'link':
                return $this->sendLinkMsg($cont, $type);
                break;
            case 'oa':
                return $this->sendOAMsg($cont, $type);
                break;
            default:
                return $this->sendTxtMsg($cont, $type);
                break;
        }
    }

    /**
     * 发送文本消息
     * @param array $cont
     * @param int $type    会话类型，1:默认企业会话,2:普通会话,3:群会话
     * @return Ambigous <object, mixed>
     */
    public function sendTxtMsg($cont, $type = 1) {
        $cont['msgtype'] = 'text';
        $cont['text'] = array('content' => $cont['content']);
        unset($cont['content']);
        return $this->send($cont, $type);
    }

    /**
     * 发送图片消息
     * @param array $cont
     * @param int $type    会话类型，1:默认企业会话,2:普通会话,3:群会话
     * @return Ambigous <object, mixed>
     */
    public function sendImgMsg($cont, $type = 1) {
        $cont['msgtype'] = 'image';
        $cont['image'] = array('media_id' => $cont['media_id']);
        unset($cont['media_id']);
        return $this->send($cont, $type);
    }

    /**
     * 发送文件消息
     * @param array $cont
     * @param int $type    会话类型，1:默认企业会话,2:普通会话,3:群会话
     * @return Ambigous <object, mixed>
     */
    public function sendFileMsg($cont, $type = 1) {
        $cont['msgtype'] = 'image';
        empty($cont['media_id']) && $cont['media_id'] = '@lALOACZwe2Rk'; //默认图片
        $cont['file'] = array('media_id' => $cont['media_id']);
        unset($cont['media_id']);
        return $this->send($cont, $type);
    }

    /**
     * 发送声音消息
     * @param array $cont
     * @param int $type    会话类型，1:默认企业会话,2:普通会话,3:群会话
     * @return Ambigous <object, mixed>
     */
    public function sendVoiceMsg($cont, $type = 1) {
        $cont['msgtype'] = 'image';
        $cont['voice'] = array(
            'media_id' => $cont['media_id'],
            'duration' => 10
        );
        unset($cont['media_id']);
        return $this->send($cont, $type);
    }

    /**
     * 发送链接消息
     * @param array $cont
     * @param int $type    会话类型，1:默认企业会话,2:普通会话,3:群会话
     * @return Ambigous <object, mixed>
     */
    public function sendLinkMsg($cont, $type = 1) {
        $link = array(
            'messageUrl' => $cont['messageUrl'],
            'picUrl' => $cont['picUrl'],
            'title' => $cont['title'],
            'text' => $cont['text']
        );
        $cont['msgtype'] = 'link';
        $cont = array(
            'touser' => $cont['touser'], //企业会话
            'toparty' => $cont['toparty'], //企业会话
            'sender' => $cont['sender'], //群会话，普通会话
            'cid' => $cont['cid'], //普通会话
            'chatid' => $cont['chatid'], //群会话
            'msgtype' => 'link',
            'link' => $link
        );
        return $this->send($cont, $type);
    }

    /**
     * 发送OA消息
     * @param array $cont
     * @param int $type    会话类型，1:默认企业会话,2:普通会话,3:群会话
     * @return Ambigous <object, mixed>
     */
    public function sendOAMsg($cont, $type = 1) {
        //默认公司名称作为消息标题
        $dep = $this->getDep();
        empty($cont['title']) && $cont['title'] = $dep[1]['name'];
        $oa = array();
        //oa head
        $head['bgcolor'] = !empty($cont['bgcolor']) ? $cont['bgcolor'] : "FF1f72cd";
        $head['text'] = !empty($cont['text']) ? $cont['text'] : $cont['title'];
        //oa body
        $body['title'] = $cont['title'];
        !empty($cont['form']) && $body['form'] = $cont['form'];
        !empty($cont['content']) && $body['content'] = $cont['content'];
        !empty($cont['author']) && $body['author'] = $cont['author'];
        !empty($cont['rich']) && $body['rich'] = $cont['rich'];
        //oa
        !empty($cont['message_url']) && $oa['message_url'] = $cont['message_url'];
        !empty($cont['pc_message_url']) && $oa['pc_message_url'] = $cont['pc_message_url'];
        $oa['head'] = $head;
        $oa['body'] = $body;
        //content
        $cont['msgtype'] = 'oa';
        $cont['oa'] = $oa;
        $cont = array(
            'touser' => $cont['touser'], //企业会话
            'toparty' => $cont['toparty'], //企业会话
            'sender' => $cont['sender'], //群会话，普通会话
            'cid' => $cont['cid'], //普通会话
            'chatid' => $cont['chatid'], //群会话
            'msgtype' => 'oa',
            'oa' => $oa
        );
        return $this->send($cont, $type);
    }

    //发送消息 end
    //文件上传 star
    //开启文件上传事务
    public function transaction() {
        $url = "https://oapi.dingtalk.com/file/upload/transaction?agent_id=%AGENT_ID%&file_size=%FILE_SIZE%&chunk_numbers=%CHUNK_NUMBERS%";
    }

    public function single($file) {
        $url = "https://oapi.dingtalk.com/file/upload/single?agent_id=%AGENT_ID%&file_size=%FILE_SIZE%";
        $serch = array('%AGENT_ID%', '%FILE_SIZE%');
        $rep = array($this->AgentID, $file['size']);
        $url = $this->Token_replace($serch, $rep, $url);
        $header = array('Content-Type: multipart/form-data');
        //$data=json_encode($data);
        //echo $data;
        $list = $this->posthttps($url, $file, $header);
        return $list;
    }

    public function uploads($data, $type) {
        
    }

    //jsapi 
    public function get_jsapi_ticket() {
        $url = "https://oapi.dingtalk.com/get_jsapi_ticket";
        $url = $this->Token_replace(null, null, $url);
        $jsapi = $this->getCache($url, 'jsapi');
        return $jsapi;
    }

    public function get_jssdk($max = 18) {
        $str = '0123456789abcdefghijklmnopqrstuvwxyzABCEDFGHIJKLMNOPQRSTUVWXYZ';
        $max = max($max, 5);
        $nonceStr = substr(str_shuffle($str), min(rand(5, strlen($str)), $max));
        $timeStamp = time();
        $ticket = $this->get_jsapi_ticket();

        $url = $this->get_url();
        $signature = $this->get_jsapi_sign($ticket['ticket'], $nonceStr, $timeStamp, urldecode($url));

        $config = array(
            'url' => $url, //'#URL#',
            'nonceStr' => $nonceStr,
            'agentId' => $this->AgentID,
            'timeStamp' => $timeStamp,
            'corpId' => $this->corpid,
            'suite_key' => '', //SUITE_KEY,
            'signature' => $signature);
        // print_r($config);die;
        return str_replace('#URL#', urldecode($url), json_encode($config));
    }

    public function get_jsapi_sign($ticket, $nonceStr, $timeStamp, $url) {
        $plain = 'jsapi_ticket=' . $ticket .
                '&noncestr=' . $nonceStr .
                '&timestamp=' . $timeStamp .
                '&url=' . $url;
        return sha1($plain);
    }

    public function getddlogins() {
        $data = $this->getlogin();
        $appid = $data['appid'];
        $huiurl = $data['huiurl'];
        $url = "https://oapi.dingtalk.com/connect/oauth2/sns_authorize?appid=" . $appid . "&response_type=code&scope=snsapi_login&state=STATE&redirect_uri=" . $huiurl;
    }

    //获取accesstoken
    public function getaccesstoken() {
        $data = $this->getlogin();
        $url = "https://oapi.dingtalk.com/sns/gettoken?appid=" . $data['appid'] . "&appsecret=" . $data['appSecret'];
        $AccessToken = file_get_contents($url);
        $res = json_decode($AccessToken, true);
        session('accesstoken', $res['access_token']);
        session('time',time()+7000);
        return $res;
    }

    //获取用户持久码
    public function tmpauthcode($code) {
        $accesstoken = session('accesstoken');
        $time = session('time');
        $ctime = time();
        if($time<$ctime){
            $this->getaccesstoken();
        }
        $url = "https://oapi.dingtalk.com/sns/get_persistent_code?access_token=".$accesstoken;
        $datas['tmp_auth_code'] = $code;
        $data = json_encode($datas);
        $header = array('Content-Type: application/json', 'Content-Length: ' . strlen($data));
        $tmpauthcode = $this->posthttps($url,$data,$header);
        return $tmpauthcode;
    }

    //获取用户用户授权SNS_TOKEN
    public function snstoken($code) {
        $accesstoken = session('accesstoken');
        $time = session('time');
        $ctime = time();
        if($time<$ctime){
            $this->getaccesstoken();
        }
        $tmpauthcode = $this->tmpauthcode($code);
        $url = "https://oapi.dingtalk.com/sns/get_sns_token?access_token=" . $accesstoken;
        $datas['openid'] = $tmpauthcode['openid'];
        $datas['persistent_code'] = $tmpauthcode['persistent_code'];
        $data = json_encode($datas);
        $header = array('Content-Type: application/json', 'Content-Length: ' . strlen($data));
        $snstoken = $this->posthttps($url, $data,$header);
        return $snstoken;
    }

    //获取用户个人信息 
    public function userinfo($code) {
        $accesstoken = session('accesstoken');
        $time = session('time');
        $ctime = time();
        if($time<$ctime){
            $this->getaccesstoken();
        }
        $snstoken = $this->snstoken($code);
        $url = "https://oapi.dingtalk.com/sns/getuserinfo?sns_token=".$snstoken['sns_token'];
        $userinfo = file_get_contents($url);
        $res = json_decode($userinfo, true);
        return $res;
    }
    
    //连接服务窗
    public function  ChannelToken(){
        $CorpId = "ding8b106a8e950b756b35c2f4657eb6378f";
        $ChannelSecret = "ebJt5S3MHuhzFI8TkXcUZp2n4k9pKaMscxw7jU7Yfug5RhUDo6mf8W40K9lk9aSM";
        $url = "https://oapi.dingtalk.com/channel/get_channel_token?corpid=".$CorpId."&channel_secret=".$ChannelSecret;
        $ChannelToken = file_get_contents($url);
        $res = json_decode($ChannelToken, true);
        $_SESSION['ChannelToken']['access_token']=$res['access_token'];
        $_SESSION['ChannelToken']['time']=time()+7000;
        print_r($res);die;
    }
    //获取服务窗关注者
    public function changman(){
        $accesstoken = $_SESSION['ChannelToken']['access_token'];
        $time = $_SESSION['ChannelToken']['time'];
        $ctime = time();
        if($time<$ctime){
            $this->ChannelToken();
        }
        $url = "https://oapi.dingtalk.com/channel/user/list?access_token=".$accesstoken."&offset=0&size=20";
        $changman = file_get_contents($url);
        $res = json_decode($changman, true);
        return $res;
    }
    public function ddmessage(){
        $accesstoken = session('accesstoken');
        $time = session('time');
        $ctime = time();
        if($time<$ctime){
            $this->getaccesstoken();
        }
        $url = "https://oapi.dingtalk.com/channel/message/send?access_token=".$accesstoken;
        $data['channelAgentId'] = "367261";
        $data['touser'] = "openid1|openid2|openid3";
        $data['msgtype'] = "text";
        $data['text'] = array('content'=>'张三的请假申请');
 
        $data = json_encode($datas);
        $header = array('Content-Type: application/json', 'Content-Length: ' . strlen($data));
        $snstoken = $this->posthttps($url, $data,$header);
        print_r($snstoken);die;
    }

}
