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
     * Error message (false when no errors)
     *
     * @var boolean|string
     */ 
	public $error;

	/**
	 * Init
	 *
	 * @param int $status_code 
	 * @param string $type
	 * @param string $body
	 * @param array $header
	 */ 
	function __construct($status_code, $type, $body, $header) {
		$this->status_code = $status_code;
		$this->type = $type;
		$this->body = $body;
		$this->header = $header;

		if($this->status_code == 200) {
			$this->success = true;
			$this->error = false;
		} else {
			$this->success = false;
			$this->error = "Error: ". $this->status_code;
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