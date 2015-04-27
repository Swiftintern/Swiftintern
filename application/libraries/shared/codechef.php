<?php
namespace Shared {
	use Framework\Base as Base;
	use WebBot\WebBot as WebBot;

	class CodeChef extends Base {
		/**
		* @readwrite
		*/
		protected $_username;

		/**
		* @var boolean 
		* Flag set to true for valid user
		* @readwrite
		*/
		protected $_isValid;

		/**
		* @read
		*/
		protected $_url = "http://www.codechef.com/users/";

		/**
		* Store the returned document
		*/
		private $document;

		/**
		* Store the resulting XPath object
		*/
		private $xPath;

		public function __construct($options = array()) {
			parent::__construct($options);

			$this->initialize();
		}

		/**
		* Execute the WebBot request for the given username
		* Sets boolean to true if user exists else false
		*/
		private function initialize() {
			$webbot = new WebBot(array(
				"user" => $this->url.$this->username
			));
			$webbot->execute();
			$result = $webbot->getDocuments();
			$this->document = array_shift($result);

			if($this->document->success) {
				$this->isValid = true;
				$this->xPath = $this->document->returnXPathObject();
			} else {
				$this->isValid = false;
			}
		}

		/**
		* @param $query XPath
		* @param $noValue Function will not return nodeValue rather it calls parseTable by passing in XPath obj
		* Checks for a valid codechef user then finds the given XPath
		* @return string|null
		*/
		private function query($query, $noValue = false) {
			if($this->isValid) {
				$element = $this->xPath->query($query);
				
				if($noValue) {
					return $this->parseTable($element->item(0));
				}

				return ($element->length > 0) ? $element->item(0)->nodeValue : null;
			} else {
				return null;
			}
		}

		/**
		* @param XPath Object
		* @return string Consisting of table rows and columns
		* 
		* Parses the table and creates a string consisting of html table tag <tr> and <td>
		* With hierachy maintained
		*/
		private function parseTable($table) {
			$trs = $table->childNodes;	// Find all the rows of table
			$output = "";
			foreach ($trs as $tr) {
				$output .= "<tr>";
				$tds = $tr->childNodes;		// Find all the children of tr
				
				if($tds != null) {
					foreach ($tds as $td) {
						if($td->nodeType == 1) {	// Check if child is of type element 
							if($td->nodeValue == 'Teams List:') {
								break 2;
							}
							$output .= "<td>".$td->nodeValue."</td>";
						}
					}
				}
				$output .= "</tr>";
			}
			return $output;
		}

		/**
		* @return string|null Returns a table of user data paresed from CodeChef Page
		* 
		*/
		public function getDetails() {
			return $this->query('//*[@id="primary-content"]/div/div[2]/table[2]', $noValue = true);
		}

		/**
		* @return string|null
		* format 'Table'
		*/
		public function getRank() {
			return $this->query('//*[@id="hp-sidebar-blurbRating"]/div/table', $noValue = true);
		}

		/**
		* @return string|null
		* format 'Name'
		*/
		public function getName() {
			return $this->query('//div[@class="user-name-box"]');
		}
	}
}

?>