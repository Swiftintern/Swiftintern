<?php
namespace HTTP;
/**
 * HTTP Response class - grab HTTP GET and HEAD responses
 *
 * @package HTTP
 */
class Response {

	/**
	* HTTP response status code
	* @var int
	*/
	private $status_code;
	
	/**
	* HTTP response type
	* @var string
	*/
	private $type;
	
	/**
	* HTTP response body
	* @var string
	*/
	private $body;
	
	/**
	* HTTP response header
	* @var string
	*/
	private $header;

	/**
	* Successful fetch flag
	*
	* @var boolean
	*/
	public $success;

	/**
	* Init
	*
	* @param HTTP\Response StatusCode, Type, Body, Header
	* @return HTTP\Response StatusCode
	*/
	function __construct($status_code, $type, $body, $header) {
		$this->status_code = $status_code;
		$this->type = $type;
		$this->body = $body;
		$this->header = $header;

		if($this->status_code == 200) {
			$this->success = true;
		} else {
			$this->success = false;
		}
	}

	/**
	* getter
	* @return HTTP\Response StatusCode
	*/
	public function getStatusCode() {
		return $this->status_code;
	}

	/**
	* getter
	* @return HTTP\Response Body
	*/
	public function getBody() {
		return $this->body;
	}	
}
?>