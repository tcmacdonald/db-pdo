<?php
/**
 * @author tcmacdonald at gmail dot com
 */
class u extends Utility {}
/**
 * @author tcmacdonald at gmail dot com
 */
class Utility {
	/**
	 * Print an array wrapped in pre tags
	 * @return void
	 * @access public
	 */
	public static function pr($arr) { 
		echo '<pre>'.print_r($arr,1).'</pre>';
	}
	/**
	 * Wrap pr in die()
	 * @return void
	 * @access public
	 */
	public static function d($arr) { 
		die(self::pr($arr)); 
	}
	/**
	 * Obfuscate string, email address
	 * @param string $str
	 * @param boolean $mailto
	 * @return string
	 * @access public
	 */
	public static function obfuscate($str,$mailto=true) {
		$rv = '';
		for($i = 0; $i < strlen($str); $i++) {
			$rv .= '&#' . ord($str[$i]) . ';';
		}
		return $mailto ? 'mailto:'.$rv : $rv;
	}
	/**
	 * Format string as phone number
	 * @param string $phone
	 * @param boolean $convert
	 * @param boolean $trim
	 * @return string
	 * @access public
	 */
	public static function phone($phone='', $convert=false, $trim=true) {
		if (empty($phone)) return '';
		$phone = preg_replace("/[^0-9A-Za-z]/", "", $phone);
		if ($convert == true) {
			$replace = array('2'=>array('a','b','c'),
				'3'=>array('d','e','f'),
				'4'=>array('g','h','i'),
				'5'=>array('j','k','l'),
				'6'=>array('m','n','o'),
				'7'=>array('p','q','r','s'),
				'8'=>array('t','u','v'),
				'9'=>array('w','x','y','z'));
			foreach($replace as $digit=>$letters) {
				$phone = str_ireplace($letters, $digit, $phone);
			}
		}
		if ($trim == true && strlen($phone)>11) $phone = substr($phone, 0, 11);
		if (strlen($phone) == 7) {
			return preg_replace("/([0-9a-zA-Z]{3})([0-9a-zA-Z]{4})/", "$1-$2", $phone);
		} elseif (strlen($phone) == 10) {
			return preg_replace("/([0-9a-zA-Z]{3})([0-9a-zA-Z]{3})([0-9a-zA-Z]{4})/", "($1) $2-$3", $phone);
		/*} elseif (strlen($phone) == 11) {
			return preg_replace("/([0-9a-zA-Z]{1})([0-9a-zA-Z]{3})([0-9a-zA-Z]{3})([0-9a-zA-Z]{4})/", "$1($2) $3-$4", $phone);*/
		} elseif (strlen($phone) > 10) {
			return preg_replace("/([0-9a-zA-Z]{3})([0-9a-zA-Z]{3})([0-9a-zA-Z]{4})([0-9]*)/", "($1) $2-$3", $phone);
		}
		return $phone;
	}
	/**
	 * Adds variable key/value pairs to query string without duplicating keys
	 * @param array $additions Array in form of key=>value for var=value additions 
	 * @return string
	 */
	public static function qs($additions=array(),$exclusions=array()) {
		parse_str($_SERVER['QUERY_STRING'],$existing);
		foreach($existing as $k=>$v) {
			if(in_array($k,$exclusions)||RM_DRUPAL_Q && $k=='q') unset($existing[$k]); 
		}
		$params = $additions+$existing; 
		return http_build_query($params); 
	}
	/**
	 * Iterates over an array and decodes all html entities 
	 * @param array $arr
	 */
	public static function decodeArray($arr) {
		foreach($arr as &$a) {
			$a = array_map('urldecode',$a); 
		}
		return $arr; 
	}
	/**
	 * Shuffle array while preserving keys
	 * @return boolean
	 * @param $array Object
	 */
	public static function shuffle_assoc(&$array) {
		if (count($array)>1) { 
		$keys = array_rand($array, count($array));
		foreach($keys as $key)
			$new[$key] = $array[$key];
			$array = $new;
		}
		return true; 
	}
	/**
	 * Alias to PHP Header (Location) method
	 * @param $url string
	 */
	public static function redirect($url) {
		header("Location: $url");
		exit; 
	}
	
	/**
	 * Generate random string
	 * @return 
	 * @param $length Integer
	 */
	public static function generateString($length=8) {
		$str = "";
		$possible = "0123456789bcdfghjkmnpqrstvwxyz"; 
		$i = 0; 
		while ($i < $length) { 
			$char = substr($possible, mt_rand(0, strlen($possible)-1), 1);
			if (!strstr($str, $char)) { 
				$str .= $char;
				$i++;
			}
		}
		return $str;
	}
   	/**
	 * Truncate a string to a certain length if necessary,
	 * optionally splitting in the middle of a word, and
 	 * appending the $etc string or inserting $etc into the middle.
	 * @param string
	 * @param integer
	 * @param string
	 * @param boolean
	 * @param boolean
	 * @return string
	 */
	public static function truncate($string, $length = 80, $etc = '...',$break_words = false, $middle = false) {
		if ($length == 0)
			return '';
		if (strlen($string) > $length) {
			$length -= min($length, strlen($etc));
			if (!$break_words && !$middle) {
				$string = preg_replace('/\s+?(\S+)?$/', '', substr($string, 0, $length+1));
			}
			if(!$middle) {
				return substr($string, 0, $length) . $etc;
			} else {
				return substr($string, 0, $length/2) . $etc . substr($string, -$length/2);
			}
		} else {
			return $string;
		}
	}

}
