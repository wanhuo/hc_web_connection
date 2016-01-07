<?php
require_once dirname(dirname(dirname(dirname(__DIR__)))).'/Workerman/Autoloader.php';
require_once dirname(dirname(__DIR__)).'/GatewayClient/Gateway.php';
\Workerman\Autoloader::setRootPath(__DIR__.'/../../');
use \GatewayWorker\Lib\Db;
use \Workerman\Protocols\Http;
if(!isset($_POST['macid']) && !isset($_POST['buttonid'])){

	echo "对不起，您访问的页面不存在！";
	HTTP::end();
}
$macid = $_POST['macid'];
$buttonid = $_POST['buttonid'];
$connectHC = Db::instance('ConnectHC');
$clientdata = $connectHC->row("SELECT clientid, sign FROM `WEBHC` WHERE macid='$macid'");
if(!$clientdata || !Gateway::isOnline($clientdata['clientid'])){
	echo "102";
	HTTP::end();
}
if(!$clientdata['sign']){
	echo "103";
	HTTP::end();
}
Gateway::sendToClient($clientdata['clientid'], "{".$buttonid."}");
echo "101";
HTTP::end();
?>

