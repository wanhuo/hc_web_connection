<?php 
/**
 * This file is part of workerman.
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the MIT-LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @author walkor<walkor@workerman.net>
 * @copyright walkor<walkor@workerman.net>
 * @link http://www.workerman.net/
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 */
use \Workerman\Worker;
use \Workerman\WebServer;
use \GatewayWorker\Gateway;
use \GatewayWorker\BusinessWorker;
use \Workerman\Autoloader;

// 自动加载类
require_once __DIR__ . '/../../Workerman/Autoloader.php';
Autoloader::setRootPath(__DIR__);

// gateway 进程
$gateway = new Gateway("Json://0.0.0.0:3303");
// gateway名称，status方便查看
$gateway->name = 'HCGateway';
// gateway进程数
$gateway->count = 1;
// 本机ip，分布式部署时使用内网ip
$gateway->lanIp = '127.0.0.1';
// 内部通讯起始端口，假如$gateway->count=4，起始端口为4000
// 则一般会使用4001 4002 4003 4004 4个端口作为内部通讯端口 
$gateway->startPort = 2300;
// 心跳间隔
$gateway->pingInterval = 60;
//允许没有心跳回复次数
$gateway->pingNotResponseLimit = 2;
// 心跳数据
$gateway->pingData = '';
$gateway->registerAddress = '127.0.0.1:1236';

$hcwebsocket = new Gateway("Websocket://0.0.0.0:4404");
$hcwebsocket->name = 'HCWebsocket';
$hcwebsocket->count = 1;
$hcwebsocket->IanIp = '127.0.0.1';
$hcwebsocket->startPort = 2500;
$hcwebsocket->registerAddress = "127.0.0.1:1236";

/* 
// 当客户端连接上来时，设置连接的onWebSocketConnect，即在websocket握手时的回调
$gateway->onConnect = function($connection)
{
    $connection->onWebSocketConnect = function($connection , $http_header)
    {
        // 可以在这里判断连接来源是否合法，不合法就关掉连接
        // $_SERVER['HTTP_ORIGIN']标识来自哪个站点的页面发起的websocket链接
        if($_SERVER['HTTP_ORIGIN'] != 'http://kedou.workerman.net')
        {
            $connection->close();
        }
        // onWebSocketConnect 里面$_GET $_SERVER是可用的
        // var_dump($_GET, $_SERVER);
    };
}; 
*/

$webserver = new WebServer('http://0.0.0.0:80');
$webserver->addRoot('120.25.148.172','/hc_web_connection/Applications/WebController/Web');
$webserver->count = 1;
// 如果不是在根目录启动，则运行runAll方法
if(!defined('GLOBAL_START'))
{
    Worker::runAll();
}

