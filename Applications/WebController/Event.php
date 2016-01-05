<?php
use \GatewayWorker\Lib\Gateway;
use \GatewayWorker\Lib\Db;
use \GatewayWorker\Lib\Store;
// require_once 'Web/transToWxServer.php';
require_once 'Web/redisData.php';

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
    const SUCCESS_CONNECTION = "success";
    /**
     * 修改蓝牙标志成功
     * @var sting
     */
    const SUCCESS_SET_SIGN = "sign success";

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
			$connectHC->query("INSERT INTO `WEBHC` ( `macid`,`clientid`, `param`) VALUES ( '$mac_id', $client_id, '1/2/3/4/5/6/7/8/9/10/11/12/13/14/15/16/17/18/19/20/21/22/23/24/25/26/27/28/29/30')");
			// 连接标记
			self::$redisConnection->set($client_id, self::CONNECTION_STATE);
			// 连接成功
			Gateway::sendToCurrentClient(self::SUCCESS_CONNECTION);
			return;
		// clientid等于0
		}else if($result === '0'){
			// 更新客户端
			$row = $connectHC->query("UPDATE `WEBHC` SET `clientid` = $client_id, `sign` = 1 WHERE macid='$mac_id'");
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
			$row = $connectHC->query("UPDATE `WEBHC` SET `clientid` = $client_id WHERE macid='$mac_id'");
			if($row === 1){
				Gateway::closeClient($result);
				Gateway::sendToCurrentClient(self::SUCCESS_CONNECTION);
			}else{
				Gateway::sendToCurrentClient("fail");
			}
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
   			/**
      		 * 得到客户端的IP和端口号
      		 * var_dump($_SERVER['REMOTE_ADDR'].':'.$_SERVER['REMOTE_PORT']);
      		 */
 		$connectHC = Db::instance('ConnectHC');
      	// 没有登录
      	if(null === self::$redisConnection->get($client_id)){
      		// 接收到的是JSON数据包
      		if(is_array($message)){
      			self::connectionMacId($client_id, $message['macid']);
      			return;
      		}
      		// 回复心跳包
      		Gateway::sendToCurrentClient(1);
		}else{
			Gateway::sendToCurrentClient(1);
			if(is_array($message)){
				// 更新蓝牙连接标志
				if(isset($message['sign'])){
					$sign = $message['sign'];
					$data = $connectHC->query("UPDATE `WEBHC` SET `sign` = $sign WHERE clientid=$client_id");
					Gateway::sendToCurrentClient(self::SUCCESS_SET_SIGN);
					return;
				}
				// 更新CLIENTID
				if(isset($message['macid'])){
					self::connectionMacId($client_id, $message['macid']);
					return;
				}
				// 更新APP键名
				$param = $connectHC->row("SELECT `param` FROM `WEBHC` WHERE clientid=$client_id");
				$parambefore = explode('/', $param['param']);
				foreach ($message as $key => $value) {
					$parambefore[$key-1] = $value;
				}
				$paramafter = implode('/', $parambefore);
				$connectHC->query("UPDATE `WEBHC` SET `param` = '$paramafter' WHERE clientid=$client_id");
			}else{
				// 回复心跳包
				Gateway::sendToCurrentClient(1);
			}

		}
    }
   
   
    /**
     * 当用户断开连接时触发
     * @param  int
     * @return void
     */
   public static function onClose($client_id)
   {
       
       $connectHC = Db::instance('ConnectHC');
       // clientid清0
       $connectHC->query("UPDATE `WEBHC` SET `clientid` = 0, `sign`= 0 WHERE clientid='$client_id'");
       self::$redisConnection->del($client_id);
   }

}
