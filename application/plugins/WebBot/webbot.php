<?php
namespace WebBot;

use HTTP\Request;
use WebBot\Document;

/**
 * WebBot class - fetch document data from Website URLs
 *
 * @package WebBot
 */
class WebBot {
    /**
     * Documents
     *
     * @var array (of \WebBot\Document)
     */
    private $__documents = [];

    /**
     * Fetch URLs
     *
     * @var array
     */
    private $__urls = [];

    /**
     * Trace log
     * 
     * @var array
     */
    private $__log = [];

    /**
     * Default timeout configuration setting (seconds)
     *
     * @var int|float
     */
    public static $conf_default_timeout = 30;

    /**
     * Delay between fetches (seconds), 0 (zero) for no delay
     *
     * @var int|float
     */
    public static $conf_delay_between_fetches = 0;

    /**
     * Force HTTPS protocol when fetching URL data
     *
     * Note: will not override URL protocol if set, ex: fetch URL 'http://url' will
     * not be forced to 'https://url', only 'url' gets forced to 'https://url'
     *
     * @var boolean
     */
    public static $conf_force_https = false;

    /**
     * Include document field raw values when matching field patterns
     * ex: '<h2>(.*)</h2>' => [(field value)'heading', (field raw value)'<h2>heading</h2>']
     *
     * @var boolean
     */
    public static $conf_include_document_field_raw_values = false;

    /**
     * Error message (false when no errors)
     *
     * @var boolean|string
     */
    public $error = false;

    /**
     * Successful fetch flag
     *
     * @var boolean
     */
    public $success = false;

    /**
     * Document count (distinct documents)
     *
     * @var int
     */
    public $total_documents = 0;

    /**
     * Document count of failed fetched documents
     *
     * @var int
     */
    public $total_documents_failed = 0;

    /**
     * Document count of successfully fetched documents
     *
     * @var int
     */
    public $total_documents_success = 0;

    /**
     * Directory for storing data
     * 
     * @var string
     */
    public static $conf_store_dir;

    /**
     * Init
     *
     * @param array $urls
     * @param array $document_fields
     * 		(fields with patterns, ex: ['title' => '<title.*?>(.*)</title>', [...]])
     */
    public function __construct(array $urls) {
        $this->__urls = $urls;

        if (count($this->__urls) < 1) { // ensure URLs are set
            $this->error = 'Invalid number of URLs (zero URLs)';
            $this->__log($this->error, __METHOD__);
        } else {
            $this->__log(count($this->__urls) . ' URL(s) initialized', __METHOD__);
        }
    }

    /**
     * Format URL for fetch, ex: 'www.[dom].com/page' => 'http://www.[dom].com/page'
     *
     * @param string $url
     * @return string
     */
    private function __formatUrl($url) {
        $url = trim($url);

        // do not force protocol if protocol is already set
        if (!preg_match('/^https?\:\/\/.*/i', $url)) { // match 'http(s?)://*'
            // set protocol
            $url = ( self::$conf_force_https ? 'https' : 'http' ) . '://' . $url;
        }

        return $url;
    }

    /**
     * Fetch documents from fetch URLs
     *
     * @return void
     */
    public function execute() {
        $i = 0;

        $this->__log('Executing bot URL fetches', __METHOD__);

        foreach ($this->__urls as $id => $url) {
            if ($i > 0 && (float) self::$conf_delay_between_fetches > 0) { // fetch delay
                sleep((float) self::$conf_delay_between_fetches);
            }

            if (!empty($url)) {
                $md5 = md5($url);

                if (!isset($this->__documents[$md5])) { // distinct documents only
                    $this->total_documents++; // add to document distinct count

                    $this->__documents[$md5] = new Document(
                            Request::get($this->__formatUrl($url), self::$conf_default_timeout), $id
                    );

                    // set fetched counts
                    if ($this->__documents[$md5]->success) {
                        $this->total_documents_success++;
                    } else {
                        $this->total_documents_failed++;
                    }
                }
            } else {
                $this->error = 'Invalid URL detected (empty URL with ID "' . $id . '")';
                $this->__log($this->error, __METHOD__);
            }

            $i++;
        }

        $this->__log($this->total_documents . ' total documents', __METHOD__);
        $this->__log($this->total_documents_success . ' documents fetched successfully', __METHOD__);
        $this->__log($this->total_documents_failed . ' documents failed to fetch', __METHOD__);

        // set success if no errors
        $this->success = !$this->error;
    }

    /**
     * Documents getter
     *
     * @return array (of \WebBot\Document)
     */
    public function getDocuments() {
        return $this->__documents;
    }

    /**
     * Store data to storage directory file
     * @param string $filename
     * @param string $data
     * @return boolean
     */
    public function store($filename, $data) {
        // check if data directory exists
        if (!is_dir(self::$conf_store_dir)) {
            $this->error = 'Invalid data storage directory"' . self::$conf_store_dir . '"';
            return false;
        }
        // check if data directory is writable
        if (!is_writable(self::$conf_store_dir)) {
            $this->error = 'Data storage directory "' . self::$conf_store_dir . '" is not writable';
            return false;
        }
        // format data directory and filename
        $file_path = self::$conf_store_dir . rtrim($filename, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
        // flush existing data file
        if (is_file($file_path)) {
            unlink($file_path);
        }
        // store data in data file
        if (file_put_contents($file_path, $data) === false) {
            $this->error = 'Failed to save data to data file "' . $file_path . '"';
            return false;
        }
        return true;
    }

    /**
     * Add message to log trace
     * 
     * @param string $message
     * @param string $method
     * @return void
     */
    private function __log($message, $method) {
        $this->__log[] = $message . ' (' . $method . ')';
    }

    /**
     * Trace log getter
     * 
     * @return array
     */
    public function getLog() {
        return $this->__log;
    }

}
