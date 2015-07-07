<?php
namespace Shared {
	use WebBot\lib\WebBot\Bot as Bot;
	use Framework\Base as Base;

	class PlacementPaper extends Base {
		/**
		 * Stores the list of companies with their id's
		 * @readwrite
		 * @var array
		 */
		protected $_companies;

		/**
		 * Stores the xpath of the document
		 * @readwrite
		 * @var XPath object
		 */
		protected $_xPath;

		/**
		 * Stores the xpath of the document
		 * @readwrite
		 * @var \WebBot\Response object
		 */
		protected $_document;

		/**
		 * Store domain of crawling website
		 * @readwrite
		 * @var string
		 */
		protected $_domain = "http://www.indiabix.com";

		public function __construct($url = []) {
			parent::__construct();
			
			$this->init($url);
		}

		/**
		 * Stores document and its xPath after executing bot request
		 *
		 * @param array $url Array of URLs which are needed to fetched
		 */
		private function init($url) {
			if (empty($url)) {
				$url = ["companies" => $this->domain."/placement-papers/companies/"];
			}
			$this->document = $this->requestDocuments($url);
			$this->xPath = $this->document->returnXPathObject();
		}

		/**
		  * Execute the WebBot request for the given urls array
		  *
		  * @param array $urls Associative array containing urls for each key
		  * @return array \WebBot\Document objects
		  */
		protected function requestDocuments($urls = [], $single = true) {
			if (empty($urls)) { return []; }
			
			$webbot = new Bot($urls);
			$webbot->execute();
			$documents = $webbot->getDocuments();
			
			return ($single) ? array_shift($documents) : $documents;
		}

		/**
		 * setter
		 */
		public function getCompaniesList() {
			$this->companies = $this->filterXPath('//*[@id="ib-tbl-topics"]');
			return $this->companies;
		}

		/**
		 * find the node parses the nested elements and returns the required value
		 *
		 * @param string $query XPath query string to find an element in the document
		 * @param boolean $value True: if the value inside the element is to be returned without 
		 * 		parsing any nested tags, False: if the element is to be returned
		 * @param \XPathObject $document If the query string is to be run on any other document 
		 * 		than the current document stored in $_document
		 * @return string|array|null
		 */
		protected function filterXPath($query, $value = true, $document = false) {
			$doc = (!$document) ? $this->xPath : $document->returnXPathObject();
			if ($element = $doc->query($query)->item(0)) {
				switch ($element->nodeName) {
					case 'table':
						return $this->parseTable($element);
					
					case 'td':
						return $this->parseATag($element);

					default:
						return ($value) ? $element->nodeValue : $element;
				}
			} else {
				return null;
			}		
		}

		/**
		 * Parses the table to find the <a> tags within each cell
		 *
		 * @param XPathElement $table
		 * @return array
		 */
		private function parseTable($table) {
			$arr = [];

			$trs = $table->childNodes;	// Find all the rows of table
			foreach ($trs as $tr) {
				$tds = $tr->childNodes;		// Find all the children of tr
				if($tds != null) {
					foreach ($tds as $td) {
						if(($td->nodeType == 1) && ($td->tagName == 'td')) {	// Check if child is table-cell
							$linksArr = $this->parseATag($td);
							foreach ($linksArr as $key => $value) {
								$arr[$key] = $value;
							}
						}
					}
				}
			}
			return $arr;
		}
		
		/**
		 * If a table cell i.e. 'td' contains '<a>' tag then parse the node to get href and text of tag
		 *
		 * @param DOMNode|XPathNode $node
		 * @return array|null
		 */
		private function parseATag($node) {
			$childNodes = $node->childNodes;	// find childNodes of the given Node
			if ($childNodes->length > 0) {		// If exists then parse else return
				$arr = [];

				foreach ($childNodes as $child) {
					if ($child->nodeName == 'a') {		// check for the 'a' tag
						$attributes = $child->attributes;	// find all attributes
						$aText = htmlspecialchars($child->nodeValue);		// Text b/w 'a' tag i.e. <a>Text</a>

						for ($i = 0; $i < $attributes->length; $i++) {
							if ($attributes->item($i)->name == 'href') {	// check for 'href' attribute
								$href = $attributes->item($i)->nodeValue;	// assign href attribute
								break;
							}
						}
						$arr["{$aText}"] = $this->filterLink($href);
					}
				}
				return $arr;
			} else {
				return null;
			}
		}

		/**
		 * Links can be of two types - Relative and Absolute
		 * Relative - '/login', 'home', '/products/product/display'
		 * Absolute - 'http://something.com', 'https://something.com'
		 * Filters the link and return absolute path
		 *
		 * @param string $link
		 * @return string
		 */
		private function filterLink($link) {
			// Relative link
			if (preg_match("/^\/[^\/].*/", $link)) {
				return $this->domain.$link;
			} elseif (preg_match("/^https?\:\/\/.*/", $link)) {
				// Absolute link
				return $link;
			} elseif (preg_match("/^\w+.*/", $link)) {
				return 'http://'.$link;
			}
		}
	}
}