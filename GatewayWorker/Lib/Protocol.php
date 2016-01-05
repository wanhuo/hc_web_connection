<?php
namespace GatewayWorker\Lib;

class Protocol{

	// 协议版本V1
	public static $V1 = array(
		// 协议字段JSON
		'VERSION' => '{"HeadSign":4, "Protocol":2, "MessageLength":2, "TargetAddr":12, "StartAddr":12, "Model":1, "Class":1, "Left":1, "AT":1}',
		// 默认的模块MACID
		'DEFALUT_MMAC' => "\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF",
		// 默认的服务器MACID
		'DEFALUT_SMAC' => "\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x01",
		// 服务器消息类型
		'SERVER_MODEL' => "\x0F",
		// 手机APP消息类型
		'PHONE_MODEL' => "\x0E",
		// 连接服务器指令代号
		'REGISTER_MAC' => "\x22",
		// 目的客户端离线回复代号
		'STATUS_DISONLINE' => "\xE0",
		// 客户端连接成功回复代号
		'STATUS_CONNECTED' => "\xE1"
	);
}

?>