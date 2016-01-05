<?php 
namespace Protocols;

class Json{
	/**
	 * 判断数据包长度
	 * @param  string $buffer
	 * @return int
	 */
	public static function input($buffer){

		$num = strpos($buffer, '{');
		if($num === false){

			return strlen($buffer);
		}
		if($num === 0){

			$endnum = strpos($buffer, '}');
			if($endnum === false){

				return 0;
			}
			return $endnum+1;
		}
		return $num;
	}

	/**
	 * 数据json封包
	 * @param  array $buffer
	 * @return string
	 */
	public static function encode($buffer){
		
		return $buffer;

	}

	/**
	 * json数据解包
	 * @param  string $buffer
	 * @return array
	 */
	public static function decode($buffer){

		$num = strpos($buffer, '{');
		if($num === false){

			return $buffer;
		}
		return json_decode($buffer, true);

	}
}
// JSON_UNESCAPED_UNICODE

?>