<?php

namespace HTTP;

/**
 * HTTP Request class – execute HTTP get and head requests *  
 * * @package HTTP 
 */
class Request {
    /**
     * User agent
     *
     * @var string
     */
    public static $user_agent = 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.9.0.8) Gecko/2009032609 Firefox/3.0.8';

    /**
     * Proxy, ex: '[IP address]:8080'
     *
     * @var string
     */
    public static $proxy;

    /**
     * Use proxy HTTP tunnel
     *
     * @var bool
     */
    public $proxy_http_tunnel = false;

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
     * @return \HTTP\Response     
     */
    private static function __parseResponse($body, $header, $url) {
        $status_code = 0;
        $content_type = '';

        if (is_array($header) && count($header) > 0) {
            foreach ($header as $v) {
                // ex: HTTP/1.x XYZ Message
                if (substr($v, 0, 4) == 'HTTP' && strpos($v, ' ') !== false) {
                    $status_code = (int) substr($v, strpos($v, ' '), 4); // parse status code
                }
                // ex: Content-Type: *; charset=*
                else if (strncasecmp($v, 'Content-Type:', 13) === 0) {
                    $content_type = $v;
                }
            }
        }
        return new \HTTP\Response($status_code, $content_type, $body, $header, $url);
    }

    /**
     * Execute HTTP GET request
     * @param string $url
     * @param int|float $timeout (seconds)     
     * @return \HTTP\Response     
     */
    public static function get($url, $timeout = 0) {
        $context = stream_context_create();
        stream_context_set_option($context, [
            'http' => [
                'timeout' => self::__formatTimeout($timeout),
                'header' => "User-Agent: " . self::$user_agent . "\r\n"
            ]
        ]);
        $http_response_header = NULL; // allow updating
        $res_body = file_get_contents($url, false, $context);
        return self::__parseResponse($res_body, $http_response_header, $url);
    }

    /**
     * Execute HTTP HEAD request
     * 
     * @param string $url     
     * @param int|float $timeout     
     * @return \HTTP\Response     
     */
    public static function head($url, $timeout = 0) {
        $context = stream_context_create();
        stream_context_set_option($context, [
            'http' => [
                'method' => 'HEAD',
                'timeout' => self::__formatTimeout($timeout),
                'header' => "User-Agent: " . self::$user_agent . "\r\n",
                'proxy' => self::$proxy, // proxy IP
                'request_fulluri' => true
            ]
        ]);
        $http_response_header = NULL; // allow updating
        $res_body = file_get_contents($url, false, $context);
        return self::__parseResponse($res_body, $http_response_header, $url);
    }

}
