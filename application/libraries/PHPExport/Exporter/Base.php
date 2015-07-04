<?php
namespace PHPExport\Exporter;

/**
 * Provides common functionality for several Exporter classes.
 * 
 * Accepts MySQL Improved, PDO database and stdClass objects.
 * Requires PHP Version 5.3 or later
 *
 * @version 1.0.0
 * @author Hemant Mann
 * @license http://opensource.org/licenses/MIT MIT
 */
class Base {
    /**
     * @var \mysqli_result | \PDOStatement Database | \stdClass objects
     */
    protected $object;
    
    /** 
     * @var string Name of output file
     */
    protected $filename;
    
    /**
     * @var string Identifies class of $object
     */
	protected $objectType;
	
	/**
	 * @var array Array of column names to be omitted from output
	 */
	protected $suppress = array();
	
	/**
	 * @var bool Flag that determines whether to save the output to a local file
	 */
	protected $local = false;
	
	/**
	 * @var string MIME type of output file
	 */
	protected $fileType;
	
	
	/**
	 * @param \mysqli_result | \PDOStatement | \stdClass $object (required)
	 * @param string $filename Name of output file (optional)
	 * @param array $options Array of options (optional)
	 */
	public function __construct(
	    $object, 
	    $filename = null, 
	    $options = array()
	) {
		$this->setObjectType($object);
		$this->filename = $filename;
		if (isset($options['suppress'])) {
			$this->buildSuppressedArray($options['suppress']);
		}
		if (isset($options['local'])) {
			$this->local = $options['local'];
		}
	}
	
	/**
	 * Sets the $objectType property.
	 * 
	 * Throws an exception if the value isn't a resource of
	 * an expected type. 
	 * 
	 * @param unknown $object First argument passed to constructor
	 * @throws \Exception
	 */
	protected function setObjectType($object) {
		if (is_array($object)) {
			$type = get_class($object[0]);
			if ($type == 'stdClass') {
				$this->objectType = 'stdClass';
			}
		} else {
			$type = get_class($object);
			if ($type == 'mysqli_result') {
				$this->objectType = 'mysqli';
			} elseif ($type == 'PDOStatement') {
				$this->objectType = 'pdo';
			} else {
				throw new \Exception ('Object must be of type mysqli_result or PDOStatement or stdClass');
			}
		}
		
		$this->object = $object;
	}
	
	/**
	 * Converts a comma-separated list of column names into an array
	 * that is used to omit designated fields from the output.
	 * 
	 * @param string $option Comma-separated list of column name
	 */
	protected function buildSuppressedArray($option) {
	    $colnames = explode(',', $option);
	    foreach ($colnames as $col) {
	        $this->suppress[] = trim($col);
	    }
	}
	
	/**
	 * Removes designated fields from the current row of the database result.
	 * 
	 * @param array $row Single row of a database result
	 * @return array Row from database result with the designated fields removed
	 */
	protected function removeSuppressedColumns($row) {
		foreach ($this->suppress as $col) {
			if (array_key_exists($col, $row)) {
				unset($row[$col]);
			}
		}
		return $row;
	}
	
	/**
	 * Returns the current row of the database result or current db object using the appropriate
	 * method according to the value of the $objectType property.
	 * 
	 * @return array|object Current row of database result or an object of stdClass
	 */
	protected function getRow() {
		if ($this->objectType == 'mysqli') {
			return $this->object->fetch_assoc();
		} elseif ($this->objectType == 'pdo') {
			return $this->object->fetch(\PDO::FETCH_ASSOC);
		} else {
			return array_shift($this->object);
		}
	}
	
	/**
	 * Generates the HTTP headers for the download file using the $fileType
	 * and $filename properties to insert the appropriate values in the 
	 * Content-Type and Content-Disposition headers.
	 */
	protected function outputHeaders() {
		header('Content-Type: ' . $this->fileType);
		header('Content-Disposition: attachment; filename=' . $this->filename);
		header('Cache-Control: no-cache, no-store, must-revalidate');
		header('Pragma: no-cache');
		header('Expires: 0');
	}
}