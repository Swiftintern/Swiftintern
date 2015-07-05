<?php
namespace Shared {
	class Company extends PlacementPaper {
		/**
		 * Stores the list of papers of a company
		 * @readwrite
		 * @var array
		 */
		protected $_papers;

		/**
		 * Check database for pre-existing papers of the company
		 * @readwrite
		 * @var boolean
		 */
		protected $_check;

		/**
		 * Flag to set if company is new (i.e. not registered in the database)
		 * @readwrite
		 * @var boolean
		 */
		protected $_new;

		/**
		 * Stores the details of fetch for company
		 * @readwrite
		 * @var \Shared\Model\Meta object
		 */
		protected $_fetch;

		/**
		 * Store the Organization's id in database
		 * @readwrite
		 * @var int
		 */
		protected $_org_id;

		/**
		 * Stores the latest paper id of company
		 * @readwrite
		 * @var int
		 */
		protected $_latest_paper_id;

		/**
		 * Fetch the given company page from the url
		 */
		public function __construct($url) {
			parent::__construct($url);
			
			$this->setLatestPaperId();
			$this->setPaperList();
			$this->new = !$this->checkExist();
		}

		/**
		 * Queries the db to check if the company exists, if it does then save the organization id
		 *
		 * @return boolean
		 */
		protected function checkExist() {
			$org = \Organization::first(array("name = ?" => $this->getCompanyName()));
			//var_dump($org);
			if ($org) {
				$this->org_id = $org->id;
				return true;
			}
			return false;
		}

		/**
		 * Parses an XPath query on the document to search for the company Name
		 *
		 * @return string
		 */
		protected function getCompanyName() {
			return $this->filterXPath('//*[@id="ib-main-bar"]/div[1]/table[1]/tr/td[2]/p[1]/b/span');
		}

		/**
		 * Parses an XPath query on the document to search for the company Website
		 *
		 * @param \WebBot\Document object
		 * @return string
		 */
		protected function getCompanyWebsite() {
			$link = $this->filterXPath('//*[@id="ib-main-bar"]/div[1]/table[1]/tr/td[2]/p[2]/a');
			return $this->filterLink($link);
		}

		/**
		 * Parses an XPath query on the document to search for the latest paper id of the given company
		 *
		 */
		protected function setLatestPaperId() {
			$id = $this->filterXPath('//*[@id="ib-main-bar"]/div[1]/table[2]/tr[2]/td');
			$id = array_shift($id);
			$array = explode('/', $id);
			$this->latest_paper_id = (int) array_pop($array);
		}

		/**
		 * Returns documents containing list placement papers of the company
		 *
		 * @return array urls ['id' => 'link']
		 */
		protected function setPaperList() {
			$this->papers = $this->filterXPath('//*[@id="ib-main-bar"]/div[1]/table[2]');
		}

		protected function getPaperList($limit = false) {
			$urls = [];

			if ($limit) {
				foreach ($this->papers as $key => $value) {
					if (($value == $limit)) {
						break;
					} else {
						$urls[$key] = $value;
					}
				}
			} else {
				$urls = $this->papers;
			}
			return $urls;
		}

		/**
		 * Setup the properties before saving the papers in the db
		 */
		protected function setup($new) {
			$fetch = false;
			
			// If organization is new i.e not in db
			if ($new) {
				$org = new \Organization(array(
				    "photo_id" => 0, "name" => $this->getCompanyName(), "country" => "", "website" => $this->getCompanyWebsite(), "sector" => "", "type" => "company", "about" => "", "fbpage" => "", "linkedin_id" => "", "validity" => 1, "updated" => date('Y-m-d H:i:s')
				));
				// Register the company in the db
				$org->save();
				$this->org_id = $org->id;
				$this->check = false;
			} else {
				// Comp exist so find the latest fetch
				$fetch = \Meta::first(array(
					"property = ?" => "organization",
                    "property_id = ?" => $this->org_id,
                    "meta_key = ?" => "last_paper_id"
                ));

                if ($fetch) {
                	$this->check = false;
                } else { // First time bot request
                	$experience = \Experience::first(array("organization_id = ?" => $this->org_id));
                	$this->check = ($experience) ? true : false;
                }
			}
			$this->fetch = $fetch;
		}

		/**
		 * Parses the document and stores the paper along with html tags in a variable
		 *
		 * @param \WebBot\Document object
		 * @return string containing paper with format preserved
		 */
		protected function parsePaper($doc) {
			$parent = $this->filterXPath('//*[@id="ib-main-bar"]/div/div[2]', false, $doc);
			$children = $parent->childNodes;	// Get all children of <div> i.e. <br> and #text nodes

			$output = "";		// create a string of all html tags
			$tagName = $parent->tagName;
			$attributes = $parent->attributes;	// Find the attributes of <div>
			$output .= "<".$tagName;

			foreach ($attributes as $attr) {
				// Append the attributes to the output string
				$output .= " ".$attr->nodeName."=\"{$attr->nodeValue}\"";
			}
			$output .= ">";

			// Find all children of the parent to maintain the text format
			foreach ($children as $child) {
				// Text Node then append the text
				if ($child->nodeName == '#text') {
					$output .= $child->nodeValue;
				} else if ($child->nodeName == 'br') {
					$output .= "<br>";	// br tag append
				}
			}
			$output .= "</".$tagName.">";
			return $output;
		}

		public function savePapers() {
			$this->setup($this->new);
			$papers = $this->getPaperList();

			if (!$this->fetch) {
				$this->fetch = new \Meta(array(
					"property" => "organization",
                    "property_id" => $this->org_id,
                    "meta_key" => "last_paper_id",
                    "meta_value" => $this->latest_paper_id
                ));
			} else {
				$diff = $this->latest_paper_id - $this->fetch->meta_value;
				if ($diff) {
					// Have to fetch only new papers
					$limit = $this->document->uri.'/'.$this->fetch->meta_value;
					$papers = $this->getPaperList($limit);
				} else {
					// We are already upto date
					$papers = [];
				}
				$this->fetch->meta_value = $this->latest_paper_id;
			}
			$this->fetch->save();

			foreach ($papers as $id => $location) {
                // See if we need to check database for paper existence
                $isSaved = ($this->check) ? \Experience::first(array("title = ?" => $id, "organization_id = ?" => $this->org_id)) : null;

                // If experience not found in the database then save it
                if (!$isSaved) {
                	$experience = $this->requestDocuments([$id => $location]);
                     $exp = new \Experience(array(
                        "organization_id" => $this->org_id,
                        "user_id" => 1,
                        "title" => $experience->id,
                        "details" => $this->parsePaper($experience),
                        "validity" => 1
                    ));
                    $exp->save();
                }
            }
		}
	}
}