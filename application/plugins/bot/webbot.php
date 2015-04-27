<?php
namespace WebBot;

class WebBot {

	/**
    * Error message (false when no errors)
    *
    * @var boolean|string
    */
	public $error;

	/**
    * HTTP\Response Document Storage
    *
    * @var object \WebBot\Document class
    */
	private $documents;

	/**
    * Fetch URLs
    *
    * @var array
    */
	private $urls;

	/**
    * Trace Log
    *
    * @var array
    */
	private $log = [];

	/**
    * Document Count Successful fetch
    *
    * @var int
    */
	public $total_documents_success;
	
	/**
    * Document Count fetch failure
    *
    * @var int
    */
	public $total_documents_failed;
	
	/**
    * Document Count
    *
    * @var int
    */
	public $total_documents;

	public static $conf_default_timeout;

	/**
    * Delay between fetches (seconds), 0 (zero) for no delay
    *
    * @var int|float
    */
	public static $conf_delay_between_fetches;


    /**
    * Force HTTPS protocol when fetching URL data
    *
    * Note: will not override URL protocol if set, ex: fetch URL 'http://url' will
    * not be forced to 'https://url', only 'url' gets forced to 'https://url'
    *
    * @var boolean
    */
	public static $conf_force_https;

	/**
    * Include document field raw values when matching field patterns
    * ex: '<h2>(.*)</h2>' => [(field value)'heading', (field raw value)'<h2>heading</h2>']
    *
    * @var boolean
    */
	public static $conf_include_document_field_raw_values;

	/**
    * Directory for storing data
    * 
    * @var string
    */
	public static $conf_store_dir;

	/**
	* Init
	* Log count of URL's in the array
	*
	* @param array $urls
	*/
	function __construct($urls = []) {
		if(empty($urls)) {
			$this->error = "Invalid number of URLs (zero)";
			$this->urls = [];
			$this->log($this->error, __METHOD__);
		} else {
			$this->urls = $urls;
			$this->log(count($this->urls) . ' URL(s) initialized', __METHOD__);
		}
	}

	/**
	* Get the documents initialized 
	*
	* @return object array \WebBot\Document
	*/
	public function getDocuments() {
		return $this->documents;
	}

	/**
	 * Add message to log file
	 *
	 * @param string $message
	 * @param string $method
	 * @return void
	*/
	private function log($message, $method) {
		$date = date('Y-m-d H:i:s');
		$this->log[] = $date.' => '. $message . ' ('. $method. ')';
	}

	/**
	* format the URL by checking protocol
	*
	* @param string $url
	* @return boolean|string
	*/	
	private function formatUrl($url) {
		$url = trim($url);
		
		if(empty($url)) {
			return false;
		}

        // do not force protocol if protocol is already set
        if (!preg_match('/^https?\:\/\/.*/i', $url)) { // match 'http(s?)://*'
            // set protocol
            $url = ( self::$conf_force_https ? 'https' : 'http' ) . '://' . $url;
        }
        return $url;
	}
	
	/**
	* Execute the bot request for the given URLs
	* Log the results for debugging purpose 
	*
	* @return void
	*/
	public function execute() {
		$response = NULL; $document = NULL;
		$this->documents = [];
		$this->urls = array_unique($this->urls);

		$success = 0; $fail = 0;

		$this->log(count($this->urls) . ' URL(s) to be executed', __METHOD__);

		foreach ($this->urls as $id => $location) {
			if($this->formatUrl($location)) {
				$response = \HTTP\Request::get($location, self::$conf_default_timeout);
				$document = new \WebBot\Document($response, $id);

				usleep((self::$conf_delay_between_fetches)*1000); // Being polite and sleeping

				if($document->success) {
					$success++;
					$document->url = $location;
					$this->documents[] = $document;
					$this->log("Fetched URL: {$location} ", __METHOD__);
				} else {
					$fail++;
					$document->url = $location;
					$this->documents[] = $document;
					$this->log("Unable to fetch URL: '{$location}', ". $document->error, __METHOD__);
				}
			} else {
				$this->error = 'Invalid URL detected (empty URL with key '. $id. ' )';
				$this->log($this->error, __METHOD__);
			}
		}

		$this->total_documents_success = $success;
		$this->total_documents_failed = $fail;

		$this->total_documents = $this->total_documents_failed + $this->total_documents_success;

		$this->log("Totat documents fetched = {$this->total_documents}", __METHOD__);
		$this->log("Documents fetched successfully = {$this->total_documents_success}", __METHOD__);
		$this->log("Documents failed to fetch = {$this->total_documents_failed}", __METHOD__);
	}

	/**
	 * Store data to storage directory file
	 *
	 * @param string $filename
	 * @param string $data
	 * @return boolean
	 */
	public function store($filename, $data) {
		// check if data directory exists
		if(!is_dir(self::$conf_store_dir)) {
			$this->error = 'Invalid data storage directory "'. self::$conf_store_dir . '"';
			return false;
		}

		// check if data directory is writable
		if(!is_writable(self::$conf_store_dir)) {
			$this->error = 'Data storage directory "'. self::$conf_store_dir .'" is not writable';
			return false;
		}

		// format data directory and filename
		$file_path = self::$conf_store_dir. trim($filename);

		// store data in data file
		if(file_put_contents($file_path, $data, FILE_APPEND) == false) {
			$this->error = 'Failed to save data to data file "'. $file_path .'"';
			return false;
		}

		return true;
	}
}
?>
