<?php
namespace WebBot\lib\HTTP;
use WebBot\lib\HTTP\Response as Response;
/**
 * HTTP Request class - execute HTTP GET and HEAD requests
 *
 * @package HTTP
 */
class Request {
	/**
	 * User Agent
	 *
	 * @var string
	*/
	public static $user_agent = 'Mozilla/5.0 (Windows NT 6.3; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2272.101 Safari/537.36';

	/**
	 * Format timeout in seconds, if no timeout use default timeout
	 *
	 * @param int|float $timeout (seconds)
	 * @return int|float
	*/
	private static function __formatTimeout($timeout = 0) {
		$timeout = (float) $timeout; // format timeout value

		if ($timeout < 0.1) {
			$timeout = 60; // default timeout
		}

		return $timeout;
	}

	/**
	 * Parse HTTP response
	 *
	 * @param string $body
	 * @param array $header
	 * @return Response object
	*/
	private static function __parseResponse($body, $header) {
		$status_code = 0;
		$content_type = '';

		if(is_array($header) && count($header) > 0) {
			foreach($header as $v) {
				// ex: HTTP/1.x XYZ Message
				if (substr($v, 0, 4) == 'HTTP' && strpos($v, ' ') !== false) {
					$status_code = (int) substr($v, strpos($v, ' '), 4); // parse status code
				}
				// ex: Content-Type: *; charset=*
				else if(strncasecmp($v, 'Content-Type:', 13) === 0) {
					$content_type = $v;
				}
			}
		}

		return new Response($status_code, $content_type, $body, $header);
	}

	/**
	 * Execute HTTP GET request
	 *
	 * @param string $url
	 * @param int|float $timeout(seconds)
	 * @return Response object
	*/
	public static function get($url, $timeout = 0) {
		$context = stream_context_create();
		stream_context_set_option($context, [
			'http' => [
				'timeout' => self::__formatTimeout($timeout),
				'header' => "User-Agent: ". self::$user_agent. "\r\n"
			], 
			'ssl' => [
				'verify_peer' => false,
				'verify_peer_name' => false 
			]
		]);

		$http_response_header = NULL; // allow updating

		$res_body = @file_get_contents($url, false, $context);

		return self::__parseResponse($res_body, $http_response_header);
	}

	/**
	 * Execute HTTP HEAD request
	 *
	 * @param string $url
	 * @param int|float $timeout
	 * @return Response object
	*/
	public static function head($url, $timeout = 0) {
		$context = stream_context_create();
		$array = [
			'http' => [
				'method' => 'HEAD',
				'timeout' => self::__formatTimeout($timeout),
				'header' => "User-Agent: ". self::$user_agent
			], 
			'ssl' => [
				'verify_peer' => false,
				'verify_peer_name' => false 
			]
		];

		stream_context_set_option($context, $array);

		$http_response_header = NULL; // allow updating
		
		$res_body = file_get_contents($url, false, $context);
		
		return self::__parseResponse($res_body, $http_response_header);
	}
}