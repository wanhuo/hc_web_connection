<?php
use \GatewayWorker\Lib\Gateway;
use \GatewayWorker\Lib\Db;
use \GatewayWorker\Lib\Store;

/**
 * 主逻辑
 * 主要是处理 onConnect onMessage onClose 三个方法
**/
class Event
{    

    // 存储连接REDIS实例
    private static $redisConnection = null;
    /**
     * 接收信息字节长度
     * @var int
     */
    const MESSAGE_LENGTH = 16;
    /**
     * 客户端注册
     * @var int
     */
    const CONNECTION_STATE = 1;
    /**
     * 客户端连接成功
     * @var sting
     */
    const SUCCESS_CONNECTION = '{"MsgType": "Register", "Content": "Success"}';
    /**
     * 修改蓝牙标志成功
     * @var sting
     */
    const SUCCESS_SET_SIGN = '{"MsgType": "BlueSign", "Content": "SignSuccess"}';
    /**
     * 初始化按键成功
     * @var string
     */
    const SUCCESS_SET_PARAM = '{"MsgType": "iniButton", "Content": "ParamSuccess"}';
    /**
     * 发送给websocket消息格式
     * @var string
     */
    private static $sendWebTpl = '{MsgType:Trans,ButtonId:%d,Content:%s}';
    /**
     * 发送给APP消息格式
     * @var string
     */
    private static $sendAppTpl = '{"MsgType": "Trans", "ClientId": "%s", "Content": %d}';
	  /**
     * redis数据库链接
     * @return object
     */
    private static function connectRedis(){

    	$redis = new Redis();
        $redis->connect('127.0.0.1', '6379');
        return $redis;
    }
    /**
     * 过滤macid
     * @param  string  $mecid
     * @return boolean
     */
    private static function isMacId($mecid){
    	$macpreg = "/^[a-zA-Z0-9]{16}$/";
    	return preg_match($macpreg, $mecid);
    }
	/**
	 * 客户端连接
	 * 根据macid注册
	 * @param  int
	 * @param  string
	 * @return void
	 */
	private static function connectionMacId($client_id,$mac_id)
	{

		$connectHC = Db::instance('ConnectHC');
		$result = $connectHC->single("SELECT clientid FROM `WEBHC` WHERE macid='$mac_id'");
		// clientid不存在
		if($result === false){
			// 新增客户端
			$connectHC->query("INSERT INTO `WEBHC` (`macid`,`clientid`) VALUES ('$mac_id', '$client_id')");
			// 连接标记
			self::$redisConnection->set($client_id, self::CONNECTION_STATE);
			// 连接成功
			Gateway::sendToCurrentClient(self::SUCCESS_CONNECTION);
			return;
		// clientid等于0
		}else if($result === '0'){
			// 更新客户端
			$row = $connectHC->query("UPDATE `WEBHC` SET `clientid` = '$client_id', `sign` = 1 WHERE macid='$mac_id'");
			if($row === 1){
				// 连接标记
				self::$redisConnection->set($client_id, self::CONNECTION_STATE);
				// 连接成功
				Gateway::sendToCurrentClient(self::SUCCESS_CONNECTION);
			}else{
				// 连接失败
				Gateway::sendToCurrentClient("fail");
			}
			return;
		}
		// clientid存在且不等于0
		if($result == $client_id){
			Gateway::sendToCurrentClient(self::SUCCESS_CONNECTION);
		}else{
			$row = $connectHC->query("UPDATE `WEBHC` SET `clientid` = '$client_id' WHERE macid='$mac_id'");
			if($row === 1){
				Gateway::closeClient($result);
				Gateway::sendToCurrentClient(self::SUCCESS_CONNECTION);
			}else{
				Gateway::sendToCurrentClient("fail");
			}
		}
	}

  public static function transToMessage($message)
  {
    $clientid = $message['ClientId'];
    if(!Gateway::isOnline($clientid))
    {
      return false;
    }
    switch ($message['Content']) {
      // 发送成功
      case '001':
        $sendMsg = sprintf(self::$sendWebTpl, $message['ButtonId'], '001');
        Gateway::sendToClient($clientid, $sendMsg);
        break;

      // APP与蓝牙设备断开
      case '102':
        $sendMsg = sprintf(self::$sendWebTpl, $message['ButtonId'], '102');
        Gateway::sendToClient($clientid, $sendMsg);
        break;
      
      default:
        # code...
        break;
    }
  }

	/**
	 * 当客户端连接时触发
	 * @param  int
	 * @return void
	 */
    public static function onConnect($client_id)
    {
    	if(!isset(self::$redisConnection))
    	{
    		self::$redisConnection = self::connectRedis();
    	}
    }

	/**
	 * 当客户端发来消息时触发
	 * @param  int
	 * @param  string
	 * @return void
	 */
   public static function onMessage($client_id, $message)
   {
   		// 数据库实例
      $connectHC = Db::instance('ConnectHC');

   		// websocket数据
   		if($_SERVER['GATEWAY_PORT'] === 4404)
   		{
   			$webdata = json_decode($message, true);
   			$macid = $webdata['MacId'];
   			$content = $webdata['Content'];
   			$appclientid = $connectHC->single("SELECT clientid FROM `WEBHC` WHERE macid='$macid'");
        if(!$appclientid || !Gateway::isOnline($appclientid))
        {
          $sendMsg = sprintf(self::$sendWebTpl, $content, '101');
          Gateway::sendToClient($client_id, $sendMsg);
          return;
        }
        $sendMsg = sprintf(self::$sendAppTpl, $client_id, $content);
        Gateway::sendToClient($appclientid, $sendMsg);
        return;
   		}
      // APP数据
      $appdata = $message;
      var_dump($appdata);
      switch ($appdata['MsgType']) {
        // 心跳包
        case 'HeartPacket':
          Gateway::sendToCurrentClient(1);
          break;

        // 注册
        case 'Register':
          if(!self::$redisConnection->get($client_id))
          {
            // 过滤MACID
            $macid = $appdata['Content'];
            if(self::isMacId($macid)){
              self::connectionMacId($client_id, $macid);
              return;
            }
            Gateway::sendToCurrentClient('fail');
          }
          break;

        // 蓝牙连接标志
        case 'BlueSign':
          $sign = $appdata['Content'];
          $row = $connectHC->query("UPDATE `WEBHC` SET `sign` = $sign WHERE clientid='$client_id'");
          if($row !== false)
          {
            Gateway::sendToCurrentClient(self::SUCCESS_SET_SIGN);
            return;
          }
          Gateway::sendToCurrentClient('fail');
          break;

        case 'iniButton':
          var_dump($appdata['Content']);
          $param = $appdata['Content'];
          $row = $connectHC->query("UPDATE `WEBHC` SET `param` = '$param' WHERE clientid='$client_id'");
          if($row !== false)
          {
            Gateway::sendToCurrentClient(self::SUCCESS_SET_PARAM);
            return;
          }
          Gateway::sendToCurrentClient('fail');
          break;
        // 修改按键值
        case 'altButton':
          $altkey = substr($appdata['Content'], 0, 1);
          $altparam = substr($appdata['Content'], 2);
          $dbparam = $connectHC->single("SELECT `param` FROM `WEBHC` WHERE clientid='$client_id'");
          $parambefore = explode('],[', substr(substr($dbparam, 1), 0, -1));
          $parambefore[$altkey-1] = $altparam;
          $paramafter = '['.implode('],[', $parambefore).']';
          $connectHC->query("UPDATE `WEBHC` SET `param` = '$paramafter' WHERE clientid='$client_id'");
          break;

        // 传输
        case 'Trans':
          self::transToMessage($message);
          break;

        default:
          # code...
          break;
      }
      return;
    }
   
   
    /**
     * 当用户断开连接时触发
     * @param  int
     * @return void
     */
    public static function onClose($client_id)
    {
       
    	// websocket客户端断开连接
      if($_SERVER['GATEWAY_PORT'] === 4404)
   		{
   			return;
   		}
       	$connectHC = Db::instance('ConnectHC');
       	// 清除记录
       	$connectHC->query("DELETE FROM `WEBHC` WHERE clientid='$client_id'");
       	self::$redisConnection->del($client_id);
    }
}