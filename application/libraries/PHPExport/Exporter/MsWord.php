<?php
namespace PHPExport\Exporter;

/**
 * Extends OpenDoc to merge XML into a Microsoft Word .docx file.
 *
 * Like the parent class, this requires a basic template in .docx format, and an XSL
 * stylesheet created from word/document.xml. The class merges with XML content with
 * the XSL stylesheet to create a new version of word/document.xml, which is then added
 * to the download file.
 *
 * If the parent class's setImageSource() method is called, this class copies the images
 * from the image source to the word/media folder in the download file, and inserts the 
 * images' identifier numbers (rId) into word/document.xml. The class also handles Word
 * headers and footers.
 * 
 * Requires PHP Version 5.3 or later.
 *
 * @version 1.0.0
 * @author Hemant Mann
 * @license http://opensource.org/licenses/MIT MIT
 */
class MsWord extends OpenDoc {
	/**
	 * @var string Name of download file (default: download.docx - overrides parent)
	 */
	protected $downloadFile = 'download.docx';
	
	/**
	 * @var \SimpleXMLElement Stores existing Relationship elements from document.xml.rels up to first image
	 */
	protected $docRels;
	
	/**
	 * @var \SimpleXMLElement Stores existing Relationship elements to be added after images
	 */
	protected $relsToAdd;
	
	/**
	 * @var integer Stores rId of first image
	 */
	protected $imageStart;
	
	/**
	 * @var bool Whether document has a header
	 */
	protected $header;
	
	/**
	 * @var bool Whether document has a footer
	 */
	protected $footer;
	
	/**
	 * @var \DOMDocument Stores contents of [Content_Types].xml, and adds new image types if necessary
	 */
	protected $types;
	
	/**#@+
	 * @var bool Indicates if an extension has been added to [Content_Types].xml
	 */
	protected $jpeg;
	protected $png;
	protected $gif;
	/**#@-*/

    /**#@+
	 * Constants for OpenXML and Microsoft namespaces used in Word
	 */	
	const A_NS = 'http://schemas.openxmlformats.org/drawingml/2006/main';
	const A14_NS = 'http://schemas.microsoft.com/office/drawing/2010/main';
	const FOOTER_NS = 'http://schemas.openxmlformats.org/officeDocument/2006/relationships/footer';
	const HEADER_NS = 'http://schemas.openxmlformats.org/officeDocument/2006/relationships/header';
	const IMAGE_NS = 'http://schemas.openxmlformats.org/officeDocument/2006/relationships/image';
	const PIC_NS = 'http://schemas.openxmlformats.org/drawingml/2006/picture';
	const R_NS = 'http://schemas.openxmlformats.org/officeDocument/2006/relationships';
	const RELATIONSHIPS_NS = 'http://schemas.openxmlformats.org/package/2006/relationships';
	const W_NS = 'http://schemas.openxmlformats.org/wordprocessingml/2006/main';
	const WP_NS = 'http://schemas.openxmlformats.org/drawingml/2006/wordprocessingDrawing';
	/**#@-*/
	
	/**
	 * Throws an exception if the document template doesn't have a .docx filename extension
	 *
	 * @param string $path Filepath to document template
	 * @return void
	 */
	protected function checkFileType($path) {
		$ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
		if ($ext != 'docx') {
			throw new \Exception ('The document template must have a .docx extension.');
		}
	}
	
	/**
	 * Generates the Word document and sets the headers to force the browser to download it
	 *
	 * @return void
	 */
	protected function generateDownload() {
		// Copy the document template to a temporary file, unzip it, and add the new content
		if (copy($this->template, $this->outputPath)) {
			$this->zip = new \ZipArchive();
			$this->zip->open($this->outputPath);
			if (isset($this->imageSource)) {
			    $this->addImages();
		    }
		    $this->zip->addFromString('word/document.xml', $this->mergedContent);
			
			// Close the updated document, and generate the headers for downloading
			$this->zip->close();
			
			header('Content-Description: File Transfer');
		    header('Content-Type: application/octet-stream');
			header('Content-Disposition: attachment; filename=' . $this->downloadFile);
			header('Content-Transfer-Encoding: binary');
			header('Expires: 0');
			header('Cache-Control: must-revalidate');
			header('Pragma: public');
			header('Content-Length: ' . filesize($this->outputPath));
			
			// Output the contents of the updated file to the browser
			readfile($this->outputPath);
			// Delete the temporary file after it has been downloaded
			unlink($this->outputPath);	
		} else {
			$this->fail = 'Sorry, unable to generate the requested file.';
		}
	}
	
	/**
	 * Adds images, content types, and reference IDs (rId) to the output file
	 *
	 * @return void
	 */
	protected function addImages() {
		// Check which image type(s) are registered in [Content_Types].xml
		$this->checkDefaultContentTypes();
		// Prepare word/_rels/document.xml.rels to add details of the images
		$this->initRelationships();
		// Get the merged text content ready for the image details
		$doc = new \DOMDocument();
		$doc->loadXML($this->mergedContent);
		$docPr = $doc->getElementsByTagNameNS(self::WP_NS, 'docPr');
		$cNvPr = $doc->getElementsByTagNameNS(self::PIC_NS, 'cNvPr');
		$blip = $doc->getElementsByTagNameNS(self::A_NS, 'blip');
		// Get the image filenames from the XML source
		$images = $this->getImageFilenames();
		$i = 0;
		$imgNum = 1;
		// Add the details of each image to the merged content
		foreach ($images as $image) {
			$pr = $docPr->item($i);
			$pr->setAttribute('id', $imgNum);
			$pr->setAttribute('name', 'Picture ' . $imgNum);
			$cNvPr->item($i)->setAttribute('id', 0);
			$cNvPr->item($i)->setAttribute('name', $image);
			$blip->item($i)->setAttributeNS(self::R_NS, 'r:embed', 'rId' . $this->imageStart);
			$ext = $blip->item($i)->getElementsByTagNameNS(self::A_NS, 'ext');
			$ext->item(0)->setAttribute('uri', '{28A0092B-C50C-407E-A947-70E740481C1C}');
			$extension = $this->checkType($image);
			$this->zip->addFile($this->imageSource . $image, 'word/media/image' . $i . ".$extension");
			$this->generateNewRelationship(self::IMAGE_NS, 'media/image' . $i . ".$extension");
			$i++;
			$imgNum++;
			$this->imageStart++;
		}
		// Add the Relationship elements that need to follow the images
		foreach ($this->relsToAdd as $rel) {
			$this->generateNewRelationship($rel['Type'], $rel['Target']);
			$this->imageStart++;
		}
		// Save the updated version of document.xml.rels, and add to the download file
		$relations = $this->docRels->saveXML();
		$this->zip->addFromString('word/_rels/document.xml.rels', $relations);
		
		// Save the updated version of [Content_Types].xml and add it to the download file
		$types = $this->types->saveXML();
		$this->zip->addFromString('[Content_Types].xml', $types);
		
		// Save the updated merged content
		$this->mergedContent = $doc->saveXML();
		
		// Add the header and footer Relationship IDs to the merged content if necessary
		if ($this->header) {
		    $this->fixHeadersFooters($relations, 'header');
		}
		if ($this->footer) {
		    $this->fixHeadersFooters($relations, 'footer');
		}
	}
	
	/**
	 * Checks image types registered in [Content_Types].xml in the document template
	 *
	 * @return void
	 */
	protected function checkDefaultContentTypes() {
		$contentTypes = $this->zip->getFromName('[Content_Types].xml');
		$this->types = new \DOMDocument();
		$this->types->loadXML($contentTypes);
		$defaults = $this->types->getElementsByTagName('Default');
		foreach ($defaults as $item) {
			$extension = $item->getAttribute('Extension');
			if ($extension == 'jpeg') {
				$this->jpeg = true;
			} elseif ($extension == 'png') {
				$this->png = true;
			} elseif ($extension == 'gif') {
				$this->gif = true;
			}
		}
	}
	
	/**
	 * Checks image's filename extension to see if it's registered in [Content_Types].xml
	 *
	 * @param string $image Image filename
	 * @return void
	 */
	protected function checkType($image) {
		$extension = pathinfo($image, PATHINFO_EXTENSION);
		$extension = strtolower($extension) == 'jpg' ? 'jpeg' : $extension;
		if ($extension == 'jpeg' && !$this->jpeg) {
			$this->addContentType('jpeg');
		} elseif ($extension == 'png' && !$this->png) {
			$this->addContentType('png');
		} elseif ($extension == 'gif' && !$this->gif) {
			$this->addContentType('gif');
		}
		return $extension;
	}
	
	/**
	 * Adds an image MIME type and extension to [Content_Types].xml
	 *
	 * @param string $ext Filename extension 
	 * @return void
	 */
	protected function addContentType($ext) {
		$newNode = $this->types->createElement('Default');
		$newNode->setAttribute('Extension', $ext);
		$newNode->setAttribute('ContentType', 'image/' . $ext);
		$this->types->documentElement->appendChild($newNode);
	}
	
	/**
	 * Gets the contents of word/_rels/document.xml.rels, and splits them into two lists.
	 *
	 * One list contains relations with rId numbers lower than the first image.
	 * The second list contains relations with higher rId numbers. Both lists are
	 * merged after the insertImages() method has interpolated rId numbers for
	 * the images.
	 *
	 * @return void
	 */
	protected function initRelationships() {
		// Get the contents of document.xml.rels
		$originalRels = $this->zip->getFromName('word/_rels/document.xml.rels');
		$originalRels = simplexml_load_string($originalRels);
		// Create a SimpleXMLElement to store the Relationship elements
		$this->docRels = new \SimpleXMLElement('<Relationships></Relationships>');
		$this->docRels->addAttribute('xmlns' , self::RELATIONSHIPS_NS);
		// Initialize an array for Relationship elements to be added after images
		$this->relsToAdd = array();
		// Get the rId number of the first image, and check for header and footer
		foreach ($originalRels->Relationship as $rel) {
			if ($rel['Type'] == self::IMAGE_NS) {
				$this->imageStart = isset($this->imageStart) ? $this->imageStart : substr($rel['Id'], 3);
			} elseif ($rel['Type'] == self::HEADER_NS) {
				$this->header = true;
			} elseif ($rel['Type'] == self::FOOTER_NS) {
				$this->footer = true;
			}
		}
		// Add the Relationship elements to different lists depending on whether
		// their rId number is lower or higher than the first image
		foreach ($originalRels->Relationship as $rel) {
			// Omit images from either list
			if ($rel['Type'] == self::IMAGE_NS) {
				continue;
			}
			$id = substr($rel['Id'], 3);
			if ($id < $this->imageStart) {
				$newRel = $this->docRels->addChild('Relationship');
				$newRel->addAttribute('Id', $rel['Id']);
				$newRel->addAttribute('Type', $rel['Type']);
				$newRel->addAttribute('Target', $rel['Target']);
			} elseif ($id > $this->imageStart) {
				$this->relsToAdd[$id] = array('Type' => $rel['Type'], 'Target' => $rel['Target']);
			}
			
		}
		// Arrange the Relationships after the images in ascending order
		ksort($this->relsToAdd);
	}
	
	
	/**
	 * Creates a new Relationship element to be added to word/_rels/document.xml.rels
	 *
	 * @param string $type The Type attribute of the Relationship element (its namespace)
	 * @param string $target The Target attribute of the Relationship element
	 * @return void
	 */
	protected function generateNewRelationship($type, $target) {
		$newRel = $this->docRels->addChild('Relationship');
		$newRel->addAttribute('Id', 'rId' . $this->imageStart);
		$newRel->addAttribute('Type', $type);
		$newRel->addAttribute('Target', $target);
	}
	
	/**
	 * Adds headers and footers to word/_rels/document.xml.rels,
	 * and updates IDs in merged content 
	 * 
	 * @param array $relations Updated content of word/_rels/document.xml.rels
	 * @param string $head_or_foot 'header' or 'footer'
	 */
	protected function fixHeadersFooters($relations, $head_or_foot) {
	    // Get the headerReference and footerReference elements from the merged content
	    $doc = new \DOMDocument();
	    $doc->loadXML($this->mergedContent);
	    $refs = $doc->getElementsByTagNameNS(self::W_NS, $head_or_foot . 'Reference');
	    // Get the Relationship elements from the updated content of word/_rels/document.xml.rels
	    $docrels = new \DOMDocument();
	    $docrels->loadXML($relations);
	    $relationships = $docrels->getElementsByTagName('Relationship');
	    // Get the Target and Id of each header and footer
	    $i = 0;
	    foreach ($relationships as $rel) {
	        $target = $rel->getAttribute('Target');
	        if (strpos($target, $head_or_foot) !== false) {
	            $headfoot[$i]['target'] = $target;
	            $headfoot[$i]['id'] = $rel->getAttribute('Id');
	            $i++;
	        }
	    }
	    // Word documents can have up to three headers and footers
	    // If there's only one, update its ID in the merged content
	    // If there's more than one, get the number from the Target
	    // header1.xml/footer1.xml is even
	    // header2.xml/footer2.xml is default
	    // header3.xml/footer3.xml is first
	    if (count($headfoot) == 1) {
	    	$refs->item(0)->setAttributeNS(self::R_NS, 'r:id', $headfoot[0]['id']);
	    } else {
	        foreach ($headfoot as $hf) {
	            $num = substr($hf['target'], 6, 1);
	            if ($num == 1) {
	                $headfoot['even'] = $hf;
	            } elseif ($num == 2) {
	                $headfoot['default'] = $hf;
	            } else {
	                $headfoot['first'] = $hf;
	            }
	        }
	    	foreach ($refs as $ref) {
	    	    $type = $ref->getAttributeNS(self::W_NS, 'type');
	    		$ref->setAttributeNS(self::R_NS, 'r:id', $headfoot[$type]['id']);
	    	}
	    }
	    // Save the changes to the merged content
	    $this->mergedContent = $doc->saveXML();
	}
}