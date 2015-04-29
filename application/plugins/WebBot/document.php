<?php
namespace WebBot;

use WebBot\WebBot;

/**
 * WebBot Document class
 *
 * @package WebBot
 */
class Document {

    /**
     * HTTP Response object
     *
     * @var \HTTP\Response
     */
    private $__response;

    /**
     * Error (if error occurs, false if no error)
     *
     * @var boolean|string
     */
    public $error = false;

    /**
     * Document ID
     *
     * @var mixed
     */
    public $id;

    /**
     * HTTP response success flag
     *
     * @var boolean
     */
    public $success = false;

    /**
     * HTTP request URL
     *
     * @var string
     */
    public $url;

    /**
     * Init
     *
     * @param \HTTP\Response $response
     * @param array $fields
     * @param mixed $id
     */
    public function __construct(\HTTP\Response $response, $id) {
        $this->__response = $response;
        $this->id = $id;
        $this->url = $this->getHttpResponse()->getUrl();

        $this->success = $this->getHttpResponse()->success;

        if (!$this->success) { // HTTP Response failed, set error
            $this->error = $this->getHttpResponse()->getStatusCode() . ' '
                    . $this->getHttpResponse()->getStatusMessage();
        }
    }

    /**
     * Test if value/pattern exists in response data
     * 
     * @param mixed $value (value or regex pattern, if regex pattern do not use 
     * pattern modifiers and use regex delims '/', ex: '/pattern/')
     * @param boolean $case_insensitive
     * @return boolean
     */
    public function test($value, $case_insensitive = true) {
        if (preg_match('#^\/.*\/$#', $value)) { // regex // pattern 
            return preg_match($value . 'Usm' . ( $case_insensitive ? 'i' : '' ), $this->getHttpResponse()->getBody());
        } else { // no regex, use string position
            return call_user_func(( $case_insensitive ? 'stripos' : 'strpos'), $this->getHttpResponse()->getBody(), $value) !== false;
        }
        return false; // value/pattern not found
    }

    public function find($value, $read_length_or_str = 0, $case_insensitive = true) {
        if ($this->test($value, $case_insensitive)) {
            if (preg_match('#^\/.*\/$#', $value)) { // regex pattern
                preg_match_all($value . 'Usm' . ( $case_insensitive ? 'i' : '' ), $this->getHttpResponse()->getBody(), $m);
                return $m;
            } else { // no regex, use string position
                $pos = call_user_func(( $case_insensitive ? 'stripos' : 'strpos'), $this->getHttpResponse()->getBody(), $value);
                if (is_string($read_length_or_str)) { // read to string position
                    $pos += strlen($value); // move position // length of value
                    $pos_end = call_user_func(( $case_insensitive ? 'stripos' : 'strpos'), $this->getHttpResponse()->getBody(), $read_length_or_str);
                    echo "start: $pos, end: $pos_end<br />";
                    if ($pos_end !== false && $pos_end > $pos) {
                        $diff = $pos_end - $pos;
                        return substr($this->getHttpResponse()->getBody(), $pos, $diff);
                    }
                } else { // int read length
                    $read_length = (int) $read_length_or_str;
                    return $read_length < 1 ? substr($this->getHttpResponse()->getBody(), $pos)// use read length
                            : substr($this->getHttpResponse()->getBody(), $pos, $read_length);
                }
            }
        }
        return false; // value/pattern not found
    }

    /**
     * HTTP Response object getter
     *
     * @return \HTTP\Response
     */
    public function &getHttpResponse() {
        return $this->__response;
    }

}
