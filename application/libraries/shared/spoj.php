<?php
namespace Shared {
	use Framework\Base as Base;
	use WebBot\WebBot as WebBot;

	class Spoj extends Base {
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
		protected $_url = "http://www.spoj.com/users/";

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
		* Checks for a valid spoj user then finds the given XPath
		* @return string|null
		*/
		private function query($query) {
			if($this->isValid) {
				$element = $this->xPath->query($query);
				return ($element->length > 0) ? $element->item(0)->nodeValue : null;
			} else {
				return null;
			}
		}

		/**
		* @return string|null
		* format 'Country City'
		*/
		public function getAddress() {
			return $this->query('//div[@id="user-profile-left"]/p[1]');
		}

		/**
		* @return string|null
		* format 'Joined Month Year'
		*/
		public function getJoined() {
			return $this->query('//div[@id="user-profile-left"]/p[2]');
		}

		/**
		* @return string|null
		* format 'World Rank: #rank (points)'
		*/
		public function getRank() {
			return $this->query('//div[@id="user-profile-left"]/p[3]');
		}

		/**
		* @return string|null
		* format 'Institution: Name'
		*/
		public function getSchool() {
			return $this->query('//div[@id="user-profile-left"]/p[4]');
		}
		
		/**
		* @return string|null
		*/
		public function getProbSolved() {
			return $this->query('//*[@id="content"]/div[2]/div/div[2]/div[1]/div/div[2]/div[1]/dl/dd[1]');
		}

		/**
		* @return string|null
		*/
		public function getSolSubmitted() {
			return $this->query('//*[@id="content"]/div[2]/div/div[2]/div[1]/div/div[2]/div[1]/dl/dd[2]');
		}
	}
}

?>