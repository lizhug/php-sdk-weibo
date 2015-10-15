<?php
/**
 * 微博粉丝服务平台PHP-SDK，官方API
 * 
 * @author lizhug <lizhug.com>
 * @link(github, http://github.com/lizhug/weibo-php-sdk)
 * @version 1.0 [<description>]
 * @example [URI] [<description>]
 * $this->valid();
 *       $weObj = new Weibo($this->WE_OPTIONS);
 *       $type = $weObj->getRev()->getRevType();
 *
 *       switch ($type) {
 *           case \Org\Weibo::MSGTYPE_TEXT:
 *               $weObj->text($weObj->getRevContent())->reply();
 *               break;
 *           case \Org\Weibo::MSGTYPE_EVENT:
 *               $eventData = $weObj->getRevData();
 *               if ($eventData['subtype'] == \Org\Weibo::EVENT_FOLLOW) {
 *                   //关注的时候自动注册
 *                   $weObj->text("#欢迎关注租哪儿 最专业的互联网租房平台#【房东直租，0中介费】【聚焦年轻人，更多优质房源】--点击「我要找房」即可搜索心仪房源。--点击「发布房源」即可免费发布房源。网站链接：http://www.zunar.com.cn")->reply();
 *               } else if ($eventData['subtype'] == \Org\Weibo::EVENT_UNFOLLOW){
 *                   $weObj->text("感谢您使用租哪儿，最专业的互联网租房平台，期待您的再次使用。网站链接：http://www.zunar.com.cn")->reply();
 *               }
 *               break;
 *           case \Org\Weibo::MSGTYPE_MENTION:
 *               $weObj->text("您的微博租哪儿已经帮您记录，我们会及时处理，帮您转发推广。感谢您使用租哪儿 最专业的互联网租房平台。网站链接：http://www.zunar.com.cn")->reply();
 *               break;
 *       }
 */

namespace Org;

class Weibo {

	const MENU_CREATE_URL = "https://m.api.weibo.com/2/messages/menu/create.json";

	//自定义回复相关地址
	const RECEIVE_MSG_URL = "https://m.api.weibo.com/2/messages/receive.json";		//需要长连接，详见文档：http://open.weibo.com/wiki/2/messages/receive
	const REPLY_MSG_URL = "https://m.api.weibo.com/2/messages/reply.json";	//消息回复接口，详见文档：http://open.weibo.com/wiki/2/messages/reply
	const MSGTYPE_TEXT = "text";
	const MSGTYPE_POSITION = "position";
	const MSGTYPE_IMAGE = "image";
	const MSGTYPE_VOICE = "voice";
	const MSGTYPE_EVENT = "event";
	const MSGTYPE_MENTION = "mention";
	const EVENT_SUBSCRIBE = "subscribe";
	const EVENT_UNSUBSCRIBE = "unsubscribe";
	const EVENT_FOLLOW = "follow";
	const EVENT_UNFOLLOW = "unfollow";



	//订阅发送相关地址
	


	//私信提醒相关地址
	


	//好友邀请相关地址
	
	//获取用户信息
	const USER_INFO_URL = "https://api.weibo.com/2/eps/user/info.json";


	//私有变量
	private $appkey;	//指定用于开发模式的应用appkey，详见：http://t.cn/zRp0sr6
	private $appsecret;
	private $token;
	private $message; //要回复的消息内容
	private $messageType; //要回复的消息类型
	private $_receive;


	public function __construct($options) {
		$this->appkey = isset($options['appkey'])?$options['appkey']:'';
		$this->appsecret = isset($options['appsecret'])?$options['appsecret']:'';
		$this->token = isset($options['token'])?$options['token']:'';
	}
	/**
	 * 微博粉丝服务平台验证
	 */
	private function checkSignature() {
		$signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];	
        		
		$appsecret = $this->appsecret;  //开发者的appsecret
		$tmpArr = array($appsecret, $timestamp, $nonce);
		sort($tmpArr, SORT_STRING);
		$tmpStr = implode( $tmpArr );
		$tmpStr = sha1( $tmpStr );
		
		if( $tmpStr == $signature ){
			return true;
		}else{
			return false;
		}
	}

	/**
	 * 回复文字消息
	 */
	public function text($text = "") {
		$this->message = array("text" => urlencode($text));
		$this->messageType = "text";
		return $this;	
	}

	/**
	 * 回复图文消息
	 * @param  $article 图文消息内容
	 * array(
	 * 		array(
	 * 			"title" => "消息的标题",
	 * 		 	"content" => "消息的内容",
	 * 		  	"image" => "消息的图片",
	 * 		   	"url" => "消息链接"
	 *       )
	 * )
	 * 
	 *
	 * //多图文方式回复，多个图文时在“articles”中添加多个数组既可，最多支持8个
	 *  $type = "articles";
	 *  $replayText = json_encode(
	 *     
	 *              array (
	 *                  'display_name'=>'图文标题1',
	 *                  'summary'=>'图文摘要​1',
	 *                  'image'=>'http://storage.mcp.weibo.cn/0JlIv.jpg',
	 *                  'url'=>'http://open.weibo.com/wiki/Messages'
	 *              ),
	 *             array (
	 *                 'display_name'=>'图文标题2',
	 *                 'summary'=>'图文摘要​2',
	 *                 'image'=>'http://ww2.sinaimg.cn/small/71666d49tw1dxms4qp4q0j.jpg',
	 *                 'url'=>'http://open.weibo.com/wiki/Messages'
	 *             ),
	 *             array (
	 *                 'display_name'=>'图文标题3',
	 *                 'summary'=>'图文摘要​3',
	 *                 'image'=>'http://http://ww2.sinaimg.cn/small/71666d49tw1dxms5mm654j.jpg',
	 *                 'url'=>'http://open.weibo.com/wiki/Messages'
	 *             )
	 * );
	 */
	public function article($article = "") {
		$this->message = array("articles" => $articles);

		$this->messageType = "articles";
		return $this;	
	}

	public function reply() {
		$msg = array(
			"result" => true,
            "sender_id" => $this->_receive['receiver_id'],
            "receiver_id" => $this->_receive['sender_id'],
            "type" => $this->messageType,
            //data字段需要进行urlencode编码
            "data" => json_encode($this->message)
        );

        echo json_encode($msg);
	}

	/**
	 * 验证服务器端
	 */
	public function valid() {
		if (isset($_GET["echostr"])) {
		    die($_GET["echostr"]);
		}
		return true;
	}


	/**
	 * 获取用户发送的信息
	 */
	public function getRev() {
		if ($this->_receive) return $this;
		$this->_receive = json_decode($GLOBALS['HTTP_RAW_POST_DATA'], true);
		return $this;
	}

	/**
	 * 获取消息发送者的ID，即蓝v粉丝
	 */
	public function getRevFrom() {
		return $this->_receive['receiver_id'];
	}

	/**
	 * 获取消息接受者的ID，即蓝v自己
	 */
	public function getRevTo() {
		return $this->_receive['sender_id'];
	}

	/**
	 * 获取消息类型
	 */
	public function getRevType() {
		return $this->_receive['type'];
	}

	/**
	 * 获取消息内容
	 */
	public function getRevContent() {
		return $this->_receive['text'];
	}

	/**
	 * 获取消息data
	 */
	public function getRevData() {
		return $this->_receive['data'];
	}

	/**
	 * 获取产生事件的用户信息
	 *
	 * @param  [type] $uid [description]
	 * @return [type]      [description]
	 */
	public function getUserInfo($uid = "2541756412") {
		$result = $this->httpGet(self::USER_INFO_URL . "?access_token=" . $this->token . "&uid=" . $uid);
		return json_decode($result, true);
	}


	/**
	 * 获取用户uid
	 */
	public function getUserUid() {

	}


	/**
	 * 创建自定义菜单
	 *
	 * {
     *"button": [
     *   {
     *       "type": "click",
     *       "name": "获取优惠券",
     *       "key": "get_groupon"
     *   },
     *   {
     *       "type": "click",
     *       "name": "查询客服电话",
     *       "key": "the_big_brother_need_your_phone"
     *   },
     *   {
     *       "name": "菜单",
     *       "sub_button": [
     *           {
     *               "type": "view",
     *               "name": "网上4S店",
     *               "url": "http://apps.weibo.com/1838358847/8rYu1uHD"
     *           },
     *           {
     *               "type": "view",
     *               "name": "砍价团",
     *               "url": "http://apps.weibo.com/1838358847/8s1i6v74"
     *           },
     *           {
     *               "type": "click",
     *               "name": "么么哒",
     *               "key": "memeda"
     *           }
     *       ]
     *   }
     *]
	 *}
	 */
	public function createMenu($data) {
		$result = $this->httpPost(array(
			"access_token" => $this->token,
			"menus" => json_encode($data)
		), self::MENU_CREATE_URL);

		$parse = json_decode($result, true);
		if ($parse['result']) {
			return array(
				"code" => 200,
				"message" => "菜单创建成功"
			);
		} else {
			return array(
				"code" => 400,
				"message" => $parse
			);
		}
	}

	/**
	 * POST请求
	 */
	public function httpPost($param, $url, $timeout = 30) {
        
        $ch = curl_init();

        if(stripos($url,"https://")!==FALSE){
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
			curl_setopt($ch, CURLOPT_SSLVERSION, 1); //CURL_SSLVERSION_TLSv1
		}

		if (is_string($param)) {
			$strPOST = $param;
		} else {
			$aPOST = array();
			foreach($param as $key=>$val){
				$aPOST[] = $key."=".urlencode($val);
			}
			$strPOST =  join("&", $aPOST);
		}

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        #curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        #curl_setopt($ch, CURLOPT_USERPWD, $user . ':' . $passwd);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $strPOST);
        $data = curl_exec($ch);
        curl_close($ch);
        return $data;
    }

    /**
     * get请求
     */
    private function httpGet($url) {
		$oCurl = curl_init();
		if(stripos($url,"https://")!==FALSE){
			curl_setopt($oCurl, CURLOPT_SSL_VERIFYPEER, FALSE);
			curl_setopt($oCurl, CURLOPT_SSL_VERIFYHOST, FALSE);
			curl_setopt($oCurl, CURLOPT_SSLVERSION, 1); //CURL_SSLVERSION_TLSv1
		}
		curl_setopt($oCurl, CURLOPT_URL, $url);
		curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, 1 );
		$sContent = curl_exec($oCurl);
		curl_close($oCurl);

		return $sContent;
	}
}



