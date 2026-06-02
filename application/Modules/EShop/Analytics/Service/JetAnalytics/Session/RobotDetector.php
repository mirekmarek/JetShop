<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */


namespace JetApplicationModule\EShop\Analytics\Service\JetAnalytics;

use Jet\Http_Request;

class Session_RobotDetector
{
	protected static array $robots_user_agent_kw = [
		'http://',
		'https://',
		'google',
		'bing',
		'microsoft',
		'whatsapp',
		'python',
		'curl',
		'http_get',
		'crawler',
		'wget',
		'google',
		'facebook',
		'bing',
		'zabbix',
		'lynx',
		'links',
		'crawler',
		'guzzlehttp',
		'gatherer',
		'fasthttp',
		'webhook',
		'apachebench',
		'wappalyzer',
		'go-http-client',
		'seznam',
		'passwords',
		'mergadobot',
		'apache',
		'spider',
		'php',
		'telegrambot',
		'blackbox'
	];
	
	protected static array $robots_IPs = [
		'2001:4860:7:', //Google
		'66.249.90.', //Google
		'34.1.16.', //Google
		'34.1.17.', //Google
		'34.1.18.', //Google
		'34.1.19.', //Google
		'34.1.20.', //Google
		'34.1.21.', //Google
		'34.1.22.', //Google
		'34.1.23.', //Google
		'34.1.24.', //Google
		'34.1.25.', //Google
		'34.1.26.', //Google
		'34.1.27.', //Google
		'34.1.28.', //Google
		'34.1.29.', //Google
		'34.1.30.', //Google
		'34.1.31.', //Google
		'4.153.66.27', //MS
		'18.117.180.', //Amazon
		'2a03:2880:', //Facebook
	];
	
	public static function isRobot() : bool
	{
		$ua = mb_strtolower($_SERVER['HTTP_USER_AGENT']??'');
		
		foreach( static::$robots_user_agent_kw as $robot) {
			if(str_contains($ua, $robot)) {
				return true;
			}
		}
		
		$IP = Http_Request::clientIP();
		
		foreach(static::$robots_IPs as $robot_IP) {
			if(str_starts_with($robot_IP, $IP)) {
				return true;
			}
		}
		
		return false;
	}
	
}