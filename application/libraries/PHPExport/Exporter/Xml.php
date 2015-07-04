<?php
namespace PHPExport\Exporter;

/**
 * Generates XML from an object, and outputs it as a file or string.
 *
 * Accepts object of stdClass | \mysqli_result | \PDOStatement
 * Requires PHP Version 5.3 or later
 *
 * @author Hemant Mann
 * @license http://opensource.org/licenses/MIT MIT */

class Xml extends Base {
	/**#@+
	 * @var bool Indicates if the XML is to be downloaded
	 */
	protected $download = false;
	
	/**
	 * @var string Name of XML root element (default: root)
	 */
	protected $rootname = 'root';
	
	/**
	 * @var string Name of XML child elements (default: row)
	 */
	protected $rowname = 'row';

	/**
	 * @var array Stores a list of result fields that are to be created with child elements (paragraphs)
	 */
	protected $hasChildren = array();
	
	/**
	 * @var string The XML generated from the database result
	 */
	protected $xml = '';
	
	/**
     * This is the class's only public method.
     * 
     * It takes the object and generates the output file.
     * Only the first argument, the object, is required.
     * The default document root is <root>, and each row is in a
     * <row> element. The database column names are used as the 
     * child elements of each <row>. 
	 * 
	 * By default, the XML is output as a string. To save the file to
	 * the web server's local file system or as a download file, provide
	 * a filename, and set the local or download option to true.
	 * When the local option is set, the filename can be a relative or
	 * absolute path. Otherwise, the file is saved in the same directory
	 * as the script that calls the class. If both local and download are
	 * set to true, only the local file is created.
	 * 
	 * To exclude specific fields from the output, set the suppress
	 * option to a comma-separated list of column names to be skipped.
	 * 
	 * To change the names of the document root and row elements, set the
	 * rootname and rowname options.
	 * 
	 * The stripNsplit option takes a comma-separated list of column names
	 * where the data contains HTML tags and/or newline characters. The
	 * tags are stripped, and the data is split on newline characters and
	 * stored in child <p> elements. If there are no new lines, a single
	 * child <p> element is created.
	 * 
	 * @param array $objects Array of objects of 'stdClass'
	 * @param string $filename Filename/path of output file, if required
	 * @param array $options Array of optional settings
	 */
	public function __construct(
	    $object,
	    $filename = null,
	    $options = array(
		    'local'       => false,
	        'download'    => false,
	        'suppress'    => null,
	        'rootname'    => 'root',
	        'rowname'     => 'row'
	    )
	) {
		$this->filetype = 'text/xml';
		parent::__construct($object, $filename, $options);

		if (isset($options['download'])) {
		    $this->download = $options['download'];
		}
		if (isset($options['rootname'])) {
		    $this->isValidName($options['rootname']);
		    $this->rootname = $options['rootname'];
		}
		if (isset($options['rowname'])) {
			$this->isValidName($options['rowname']);
			$this->rowname = $options['rowname'];
		}
		if (isset($options['stripNsplit'])) {
		    $fields = explode(',', $options['stripNsplit']);
		    foreach ($fields as $field) {
		    	$this->hasChildren[] = trim($field);
		    }
		}
		
		$this->generate();
	}
		
	/**
	 * Generates the XML output and saves it to a file or returns it as a string
	 *
	 * @return null|int Returns the number of bytes written to a local file or false on failure
	 */
	protected function generate() {
		$w = new \XmlWriter();
		$w->openMemory();
		$w->setIndent(true);
		$w->setIndentString("    ");
		$w->startDocument('1.0', 'utf-8');
		$w->startElement($this->rootname);
		
		while($object = $this->getRow()) {
			// Start a new row for each object
			$w->startElement($this->rowname);

			foreach ($object as $key => $value) {
				if ($this->suppress && in_array($key, $this->suppress)) {
			        continue;
			    }

				$this->isValidName($key);
				
				// Check if the key contains another object
			    if(is_object($value)) {
			    	// Start parent element containing rows of each object
			        $w->startElement($key."s");
			        // $value is an array of objects
			        foreach ($value as $obj) {
			            $w->startElement($key);
			            foreach ($obj as $field => $val) {
			            	$this->isValidName($key);
			                $w->writeElement($field, $val);
			            }
			            $w->endElement();
			        }
			        $w->endElement();
			    } else {
			    	// Write each object's property->value as <key>value</key>
			    	if ($this->hasChildren && in_array($key, $this->hasChildren)) {
						$stripped = $this->stripHtml($value);
						$w->startElement($key);
						foreach ($stripped as $para) {
							$w->writeElement('p', $para);
						}
						$w->endElement();
					} else {
					    $w->writeElement($key, $value);
					}
			    }
			}
			$w->endElement();
		}

		$w->endElement();
		$w->endDocument();
		$this->xml = $w->outputMemory();
		
		// write to file
		if (isset($this->filename) && $this->local) {
		    $success = file_put_contents($this->filename, $this->xml);
		    return $success;
		} elseif (isset($this->filename) && $this->download) {
    		$this->outputHeaders();
    		file_put_contents('php://output', $this->xml);
    		exit;
        }
	}
	
	/**
	 * 
	 * @return string
	 */
	public function __toString() {
	    return $this->xml;
	}
	
	/**
	 * Removes HTML tags and splits the remaining content on newline characters
	 *
	 * Consecutive newline characters are treated as a single character. If no newline
	 * characters are detected, the cleaned up text is returned as a single array element.
	 *
	 * @param string $value Text to be processed
	 * @return array One or more text elements with HTML tags and newline characters removed
	 */
	protected function stripHtml($value) {
		$value = strip_tags($value);
		$paras = preg_split('/[\r\n]+/', $value);
		return $paras;
	}
	
	/**
	 * Tests whether the supplied value is a valid XML identifier
	 * 
	 * @param string $name Value to be checked
	 * @throws \Exception
	 */
	protected function isValidName($name) {
		if (!preg_match('/^(?!xml|\d|-|\.)[-\w.]+$/', $name)) {
		    throw new \Exception("$name is not a valid XML identifier.");
		}
	}
}