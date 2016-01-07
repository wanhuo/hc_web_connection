<?php
namespace GatewayWorker\lib;

class Store
{
	/**
	 * REDIS实例数组
	 * @var array
	 */
	protected static $instance = array();
	/**
	 * 获取REDIS实例
	 * @param  string $config_name
	 * @throws \Exception
	 */
	public static function instance($config_name){

		if(!isset(\Config\Db::$$config_name))
        {
            echo "\\Config\\Db::$config_name not set\n";
            throw new \Exception("\\Config\\Db::$config_name not set\n");
        }
        
        if(empty(self::$instance[$config_name]))
        {
            self::$instance[$config_name] = \Config\Db::$$config_name;
        }
        return self::$instance[$config_name];
	}
}
?>