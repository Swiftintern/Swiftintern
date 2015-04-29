<?php
namespace HTTP;

/**
 * HTTP Response class
 *
 * @package HTTP
 */
class Response {
    /**
     * HTTP response codes
     *
     * @link http://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html
     * @var array
     */
    private static $__status_messages = [
        // internal
        0 => 'Initialization Error',
        // info
        100 => 'Continue',
        101 => 'Switching Protocols',
        // success
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',
        // redirection
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Found',
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        307 => 'Temporary Redirect',
        // client error
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Timeout',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Request Entity Too Large',
        414 => 'Request-URI Too Long',
        415 => 'Unsupported Media Type',
        416 => 'Requested Range Not Satisfiable',
        417 => 'Expectation Failed',
        // server error
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Timeout',
        505 => 'HTTP Version Not Supported',
    ];

    /**
     * Response body
     *
     * @var string
     */
    private $__body;

    /**
     * Response encoding type (HTTP charset parameter)
     *
     * @var string
     */
    private $__encoding = 'UTF-8';

    /**
     * Response header
     *
     * @var array
     */
    private $__header = [];

    /**
     * Content type (MIME type)
     *
     * @var string
     */
    private $__mime = 'text/plain';

    /**
     * HTTP response status code
     *
     * @var int
     */
    private $__status = 0;

    /**
     * HTTP response status code message
     *
     * @var string
     */
    private $__status_message;

    /**
     * Request URL
     *
     * @var string
     */
    public $__url;

    /**
     * Successful response flag
     *
     * @var boolean
     */
    public $success = false;

    /**
     * Init response
     *
     * @param int $status_code (ex: 200)
     * @param string $type (ex: Content-Type: *; charset=*)
     * @param string $body (raw HTML/content)
     * @param array $header (raw HTTP header)
     * @param string $url
     */
    public function __construct($status_code, $type, $body, $header, $url) {
        $this->__status = (int) $status_code;
        $this->__status_message = self::getDefaultStatusMessage($this->__status);
        $this->success = $this->__status === 200;
        $this->__body = $body;
        $this->__header = $header;
        $this->__url = $url;

        if (!empty($type)) { // parse content (MIME) type/encoding
            $type = explode(';', $type);

            if (isset($type[0])) {
                // strip 'Content-Type:'
                $this->__mime = trim(str_ireplace('content-type:', '', $type[0]));
            }

            if (isset($type[1])) {
                $type = explode('=', $type[1]);

                if (isset($type[1])) {
                    $this->__encoding = trim($type[1]);
                }
            }
        }
    }

    /**
     * Response body getter
     *
     * @return string
     */
    public function getBody() {
        return $this->__body;
    }

    /**
     * Status message by status code getter
     *
     * @param int $status_code
     * @return string
     */
    public static function getDefaultStatusMessage($status_code) {
        $status_code = isset(self::$__status_messages[(int) $status_code]) ? (int) $status_code : 1;

        return self::$__status_messages[$status_code];
    }

    /**
     * Response header getter (raw params)
     *
     * @return array
     */
    public function getHeaderRaw() {
        return $this->__header;
    }

    /**
     * HTTP response status code getter
     *
     * @return int
     */
    public function getStatusCode() {
        return $this->__status;
    }

    /**
     * HTTP response status message getter
     *
     * @return string
     */
    public function getStatusMessage() {
        return $this->__status_message;
    }

    /**
     * Response encoding type getter (HTTP charset parameter)
     *
     * @return string
     */
    public function getTypeEncoding() {
        return $this->__encoding;
    }

    /**
     * Content type getter (MIME type)
     *
     * @return string
     */
    public function getTypeMime() {
        return $this->__mime;
    }

    /**
     * Request URL getter
     *
     * @return string
     */
    public function getUrl() {
        return $this->__url;
    }

}
