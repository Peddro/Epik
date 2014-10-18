<?php
/**
 * Imports
 */
App::uses('Component', 'Controller');

/**
 * ProjectConverter Component
 *
 * @package app.Controller.Component
 * @author Bruno Sampaio
 */
class ProjectConverterComponent extends Component {
	
	/**
	 * @var array Project Game Category.
	 */
	protected $name;
	
	
	/**
	 * @var array Components used by this Component.
	 */
	public $components = array('Files');
	
	
	/** 
	 * @var array Defines several validation properties needed to validate, read and write projects data.
	 */
	protected $defaults;
	
	/**
	 * Initializes ProjectConverterComponent for use in the controller
	 *
	 * This method creates an array containing all the defaults values necessary in a project.
	 * IMPORTANT: 
	 * 		- Those are also the values for many simple and complex types present in the projects XML SCHEMA file.
	 *		- New values must always be added to the arrays end because their positions may be referenced on other files.
	 *		- This is also most of the data present on the E.defaults variable on Javascript files.
	 *
	 * @param ComponentCollection $collection
	 * @param array $settings
	 */
	public function __construct(ComponentCollection $collection, $settings = array()) {
		parent::__construct($collection, $settings);
		
		if(isset($settings['mode']) && isset($settings['genre'])) {

			$general = array(
				'aligned' => array(
					'horizontal' => array(
						'left' => __('Align Left'),
						'center' => __('Align Center'),
						'right' => __('Align Right')
					),
					'vertical' => array(
						'top' => __('Align Top'),
						'middle' => __('Align Middle'),
						'bottom' => __('Align Bottom')
					)
				),
				'collections' => array(),
				'corners' => array(
					'top-left' => __('Top Left Corner'),
					'top-right' => __('Top Right Corner'),
					'bottom-left' => __('Bottom Left Corner'),
					'bottom-right' => __('Bottom Right Corner')
				),
				'fonts' => array(
					'sizes' => array(10 => 10, 12 => 12, 14 => 14, 16 => 16, 18 => 18, 20 => 20, 24 => 24, 28 => 28, 32 => 32, 36 => 36, 48 => 48, 64 => 64, 72 => 72),
					'styles' => array(
						'normal' => __('Normal'), 
						'bold' => __('Bold'),
						'italic' => __('Italic')
					)
				),
				'genre' => $settings['genre'],
				'icons' => array(),
				'ids' => array(),
				'maximum' => array(
					'name' => 50,
					'numbers' => 9999,
					'paragraph' => 2500,
					'percentage' => 100,
					'rotation' => 359,
					'text' => 150
				),
				'minimum' => array(
					'text' => 1
				),
				'mode' => $settings['mode'],
				'positions' => array(
					'absolute' => __('Absolute'),
					'aligned' => __('Aligned')
				),
				'properties' => array(),
				'saves' => array('all', 'name', 'text', 'source', 'locked', 'position', 'styles', 'removed'),
				'screens' => array('start' => 'start', 'instructions' => 'instructions', 'wait' => 'wait', 'game' => 'game', 'rankings' => 'rankings', 'gameover' => 'gameover'),
				'sides' => array(
					'top' => __('Top'),
					'left' => __('Left'),
					'right' => __('Right'),
					'bottom' => __('Bottom')
				),
				'types' => array()
			);
			
			$this->defaults = array_merge_recursive($this->defaults, $general);
			$collections =& $this->defaults['collections'];
			
			// Get Genre Models
			$this->GenreActivity = ClassRegistry::init('GenreActivity');
			$this->GenreResource = ClassRegistry::init('GenreResource');
			
			// Get Genre Resources and Activities
			$this->GenreResource->getGenreResources($settings['genre'], $this->defaults['icons'], $this->defaults['elementsByCollection'][$collections[3]]);
			$this->GenreActivity->getGenreActivities($settings['genre'], $this->defaults['icons'], $this->defaults['elementsByCollection'][$collections[4]]);
			$icons =& $this->defaults['icons'];
			
			// Get Genre Questions Types and Questions Helps
			if(isset($icons['question']) || isset($icons['group'])) {
				$this->GenreQuestion = ClassRegistry::init('GenreQuestion');
				
				$this->defaults['types'][$icons['question']] = array();
				$this->defaults['helps'][$icons['question']] = array();
				$this->defaults['helps'][$icons['group']] = array();
				
				$this->GenreQuestion->getGenreQuestions($settings['genre'], 
					$this->defaults['types'][$icons['question']], 
					$this->defaults['helps']['all'], 
					$this->defaults['helps'][$icons['question']], 
					$this->defaults['helps'][$icons['group']]
				);
				
			}
		}
	}
	
	
	/**
	 * Creates an array with all defaults settings plus the default elements properties to be used on Javascript when creating new Elements.
	 * This data must only be loaded if it will be necessary later.
	 */
	public function getProjectDefaults() {
		$defaults = $this->defaults;
		
		// Set Game Category
		$defaults['category'] = $this->name;

		// Set Default Elements Names
		$defaults['names'] = array();
		foreach($this->defaults['collections'] as $collection) {
			$defaults['names'][$collection] = __("new-$collection-element");
		}
		
		return $defaults;
	}
	
	
	/**
	 * Creates an array with the data need by Javascript to play a Game.
	 * This data must only be loaded if it will be necessary later.
	 */
	public function getGameDefaults() {
		return array(
			'category' => $this->name,
			'collections' => $this->defaults['collections'],
			'genre' => $this->defaults['genre'],
			'icons' => $this->defaults['icons'],
			'ids' => $this->defaults['ids'],
			'mode' => $this->defaults['mode'],
			'properties' => $this->defaults['properties'],
			'screens' => $this->defaults['screens'],
			'types' => $this->defaults['types']
		);
	}
	
	
	/**
	 * Parse Project XML to Project JSON
	 *
	 * This method converts a project XML data into an array format.
	 * IMPORTANT: This method uses the names of several tags in the XML document, 
	 * so if the XML document structure is changed this method must be verified.
	 *
	 * @param $file - the path to the XML file.
	 */
	public function xml2project($file) {
		$project = array();
		
		if(file_exists($file)) {
			$collections =& $this->defaults['collections'];
			
			// Build the XML Object
			$xml = Xml::build($file);
			
			// Parse Game Start
			$this->parseStart($xml, $project);
			
			// Parse Status
			$this->parseStatus($xml, $project);
			
			// Parse Properties
			$this->parseProperties($xml, $project);
			
			// Parse Scenarios Collection
			$this->parseScenarios($xml, $project);
			
			// Parse Elements Collections
			$collectionsElements =& $this->defaults['elementsByCollection'];
			for($i = 1; $i < count($collections); $i++) {
				$this->parseCollection($collections[$i], $collectionsElements[$collections[$i]], $xml, $project);
			}
		}
		else {
			throw new NotFoundException(__('error-corrupted-project'));
		}
		
		return $project;
	}
	
	
	/**
	 * Parse Project XML to Template XML
	 *
	 * This method converts a project XML data into template XML.
	 * IMPORTANT: This method uses the names of several tags in the XML document, 
	 * so if the XML document structure is changed this method must be verified.
	 *
	 * @param $file - the path to the XML file.
	 * @param $newFile - the path to the new XML template file.
	 */
	public function xml2template($file, $newFile) {
		
		if(file_exists($file)) {
			$collections =& $this->defaults['collections'];
			
			// Build the XML Object
			$xml = new DOMDocument();
			$xml->preserveWhiteSpace = false;
			$xml->load($file);

			// Build the XPath Object
			$xpath = new DOMXpath($xml);
			$xpath->registerNamespace('e', 'http://epik.com/project');
			
			// Remove Sounds Sources
			// Remove Resources Sources
			// Remove Activities Sources
			// Remove Activities Helps
			
			// Validate and Save the XML Document
			//$xml->schemaValidate(FILES.'project_schema.xsd');
			$xml->save($newFile);
			
			return true;
		}
		else {
			throw new NotFoundException(__('error-corrupted-project'));
		}
		
		return false;
	}
	
	
	/**
	 * Parse Project XML to Game JSON
	 *
	 * This method converts a project xml data into an array format.
	 * IMPORTANT: This method uses the names of several tags in the XML document, 
	 * so if the XML document structure is changed this method must be verified.
	 *
	 * @param $file - the path to the XML file.
	 */
	public function xml2game($file) {
		$collections =& $this->defaults['collections'];
		$server = array();
		$client = array();
		$errors = array();
		$used = array($collections[3] => array(), $collections[4] => array(), 'helps' => array());
		
		if(isset($this->defaults['helps'])) {
			if(isset($this->defaults['helps']['all']['hints'])) {
				$used['helps']['hints'] = array(); // Used to store the activities hints ids (or in case of groups the group activity id)
			}
			if(isset($this->defaults['helps']['all']['resource'])) {
				$used['helps']['resource'] = array(); // Used to store the groups activities ids that use the resource help
			}
		}
		
		if(file_exists($file)) {
			try {
				
				$collections =& $this->defaults['collections'];

				// Build the XML Object
				$xml = Xml::build($file);

				// Parse Game Start
				$this->parseStart($xml, $server, $errors);

				// Parse Properties
				$this->parseProperties($xml, $client, $server, $used, $errors);

				// Parse Scenarios Collection
				$this->parseScenarios($xml, $client, $server, $errors);

				// Parse Elements Collections
				$collectionsElements =& $this->defaults['elementsByCollection'];
				for($i = 1; $i < count($collections); $i++) {
					$this->parseCollection($collections[$i], $collectionsElements[$collections[$i]], $xml, $client, $server, $used, $errors);
				}

				if(count($used[$collections[4]]) == 0) {
					array_push($errors, __('error-no-activities'));
				}

			} catch (XmlException $e) {
				array_push($errors, __('error-corrupted-project'));
			}
		}
		else {
			array_push($errors, __('error-corrupted-project'));
		}
		
		return array('server' => $server, 'client' => $client, 'used' => $used, 'errors' => $errors);
	}
	
	
	/**
	 * Parse Game Start
	 *
	 * This method loads the project start into an array.
	 * @param SimpleXML $xml - the XML element with the scenarios data.
	 * @param array $data - the array where to store the data.
	 * @param array $errors - the errors list.
	 */
	protected function parseStart($xml, &$data, &$errors=null) {}
	
	
	/**
	 * Parse Project Status
	 *
	 * This method loads the project status into an array.
	 * @param SimpleXML $xml - the XML element with the status data.
	 * @param array $data - the array where to store the data.
	 */
	protected function parseStatus($xml, &$data) {}
	
	
	/**
	 * Parse Properties
	 *
	 * This method loads all project properties into an array.
	 * @param SimpleXML $xml - the XML element with the scenarios data.
	 * @param array $data - the array where to store the data.
	 * @param array $server - the array where to store the server data.
	 * @param array $used - the array where to store the used resources and activities ids.
	 * @param array $errors - the errors list.
	 */
	protected function parseProperties($xml, &$data, &$server=null, &$used=null, &$errors=null) {}
	
	
	/**
	 * Parse Scenarios Data
	 *
	 * This method loads all project scenarios into an array.
	 * @param SimpleXML $xml - the XML element with the scenarios data.
	 * @param array $data - the array where to store the data.
	 * @param array $server - the array where to store the server data.
	 * @param array $errors - the errors list.
	 */
	protected function parseScenarios($xml, &$data, &$server= null, &$errors=null) {}
	
	
	/**
	 * Parse Collection Data
	 *
	 * A collection contains a group of different types of elements. This method loads all those types of elements into an array.
	 * @param string $collection - the collection name.
	 * @param array $elements - an array with keys for each type of elements contained on this collection and each key must have an empty array associated.
	 * @param SimpleXML $xml - the XML element with the collection data.
	 * @param array $data - the project data in array format.
	 * @param array $server - the array where to store the server data.
	 * @param array $used - the array where to store the used resources and activities ids.
	 * @param array $errors - the errors list.
	 */
	protected function parseCollection($collection, $elements, $xml, &$data, &$server=null, &$used=null, &$errors=null) {}
	
	
	/**
	 * Parse Element Styles
	 *
	 * This method loads one element styles.
	 * @param SimpleXML $styles - the styles xml element.
	 * @param array $list - the array where the styles data must be stored.
	 */
	protected function parseStyles($styles, &$list) {
		foreach($styles->children() as $item) {
			$tag = $item->getName();
			switch($tag) {
				
				case 'background': case 'border': case 'font':
					if(count($item->attributes()) > 0) {
						if(!isset($list[$tag])) $list[$tag] = array();
						$element = (string) $item->attributes()->element;
						$this->parseStyles($item, $list[$tag][$element]);
					}
					else $this->parseStyles($item, $list[$tag]);
					break;
				
				case 'height': case 'length': case 'radius': case 'size': case 'thickness': case 'width':
					$list[$tag] = (int) $item;
					break;
					
				case 'rotation':
					$list[$tag] = -((int) $item);
					break;
					
				default:
					$list[$tag] = (string) $item;
					break;
			}
		}
	}
	
	
	/**
	 * Validates the JSON data and stores it into the XML file
	 *
	 * This method iterates over the data received and if valid adds it to the XML document. The order in which the data is read is important.
	 * - First it reads the properties data (if any provided) and updates the XML contents with the new values if they are valid.
	 * - Then it reads the data for each elements collection and adds, updates, or removes data from the document.
	 * - Only after that it will read the scenarios collection data, because now its possible to validate if the scenario contents exist or if they are incorrect.
	 * - Finally, it reads the status and game start data, because its already possible to validate if the scenarios exist.
	 *
	 * IMPORTANT: To be able to validate the scenarios contents, the project status, and the game start, the DOMXpath object must be refreshed and the only way to do this
	 * is by converting the DOMDocument into a string and loading it again from that string. This is a bug found on PHP 5 which must be corrected on following versions.
	 * The areas where it is done are highlighted with coments around them.
	 *
	 * @param string $file - the XML file path.
	 * @param object $data - the new project data.
	 * @return array
	 */
	public function json2xml($file, $data) {
		$collections =& $this->defaults['collections'];
		$properties =& $this->defaults['properties'];
		$screens =& $this->defaults['screens'];
		$used = array($collections[3] => false, $collections[4] => false);
		$errors = array();
		
		if(file_exists($file)) {
			try {
				
				// Build the XML Object
				$xml = new DOMDocument();
				$xml->preserveWhiteSpace = false;
				$xml->load($file);

				// Build the XPath Object
				$xpath = new DOMXpath($xml);
				$xpath->registerNamespace('e', 'http://epik.com/project');

				// Save Properties
				if(isset($data->properties)) {
					$this->validateProperties($data->properties, $collections, $properties, $used, $errors, $xml);
				}


				// Save Collections
				for($i = 1; $i < count($collections); $i++) {
					if(isset($data->{$collections[$i]})) {
						$this->validateCollection($collections[$i], $data->{$collections[$i]}, $used, $errors, $xpath, $xml, 'validateElementsCollection');
					}
				}

				/************************************ SOLVE THIS *****************************************/
				$xml = Xml::build($xml->saveXML(), array('return' => 'domdocument'));
				$xpath = new DOMXpath($xml);
				$xpath->registerNamespace('e', 'http://epik.com/project');
				/****************************************************************************************/

				// Save Scenarios
				if(isset($data->{$collections[0]})) {
					$this->validateCollection($collections[0], $data->{$collections[0]}, $used, $errors, $xpath, $xml, 'validateScenariosCollection');
				}

				/************************************ SOLVE THIS *****************************************/
				$xml = Xml::build($xml->saveXML(), array('return' => 'domdocument'));
				$xpath = new DOMXpath($xml);
				$xpath->registerNamespace('e', 'http://epik.com/project');
				/****************************************************************************************/

				// Save Status
				$this->validateStatus($data->status, $collections, $properties, $screens, $errors, $xpath, $xml);

				// Save Start
				$start = $xml->createElement('start');
				$exists = $this->getCollectionElementById($xpath, $collections[0], $data->start)->length > 0;
				if($exists) {
					$startScenario = $xml->createElement('scenario_reference');
					$startScenario->setAttribute('id', $data->start);
					$start->appendChild($startScenario);
				}
				$xml->getElementsByTagName('project')->item(0)->replaceChild($start, $xml->getElementsByTagName('start')->item(0));


				// Get Used Elements
				if($used[$collections[3]]) {
					$extraQuery = '/e:project/e:properties/e:'.$properties[4].'/e:*[@source_id > 0]/@source_id | /e:project/e:'.$collections[4].'//e:resource[@source_id > 0]/@source_id';
					$this->getUsedElements($xpath, $collections[3], $used, $extraQuery);
				}
				if($used[$collections[4]]) $this->getUsedElements($xpath, $collections[4], $used);

				// Validate and Save the XML Document
				//$xml->schemaValidate(FILES.'project_schema.xsd');
				$xml->save($file);
				
			} 
			catch (XmlException $e) {
				array_push($errors, __('error-corrupted-project'));
			}
		}
		else {
			array_push($errors, __('error-corrupted-project'));
		}
		
		return array('used' => $used, 'errors' => $errors);
	}
	
	
	/**
	 * Validates and Stores the Project Status
	 * 
	 * @param object $data - the status data.
	 * @param array $collections - the collections names list.
	 * @param array $properties - the properties names list.
	 * @param array $screens - the screens names list.
	 * @param array $errors - the errors list.
	 * @param DOMXpath $xpath - the xpath object used for queries.
	 * @param DOMDocument $xml - the XML object with document data.
	 */
	protected function validateStatus(&$data, &$collections, &$properties, &$screens, &$errors, &$xpath, &$xml) {}
	
	
	/**
	 * Validates and Stores the Project Properties
	 * 
	 * @param object $data - the status data.
	 * @param array $collections - the collections names list.
	 * @param array $properties - the properties names list.
	 * @param array $used - the list of elements used with source id.
	 * @param array $errors - the errors list.
	 * @param DOMDocument $xml - the XML object with document data.
	 */
	protected function validateProperties(&$data, &$collections, &$properties, &$used, &$errors, &$xml) {}
	
	
	/**
	 * Validates and Stores a Collection Data
	 * 
	 * @param string $collection - the collection name.
	 * @param object $data - the status data.
	 * @param array $used - the list of elements used with source id.
	 * @param array $errors - the errors list.
	 * @param DOMXpath $xpath - the xpath object used for queries.
	 * @param DOMDocument $xml - the XML object with document data.
	 * @param string $callback - callback function name.
	 */
	protected function validateCollection($collection, &$data, &$used, &$errors, &$xpath, &$xml, $callback) {}
	
	
	/**
	 * Validates and Stores the Scenarios
	 * 
	 * @param string $collection - the collection name.
	 * @param object $data - the status data.
	 * @param DOMElement $currentXML - the collection xml element.
	 * @param array $used - the list of elements used with source id.
	 * @param array $errors - the errors list.
	 * @param DOMXpath $xpath - the xpath object used for queries.
	 * @param DOMDocument $xml - the XML object with document data.
	 */
	protected function validateScenariosCollection($collection, &$data, &$currentXML, &$used, &$errors, &$xpath, &$xml) {}
	
	
	/**
	 * Validates and Stores a Collection Elements
	 * 
	 * @param string $collection - the collection name.
	 * @param object $data - the status data.
	 * @param DOMElement $currentXML - the collection xml element.
	 * @param array $used - the list of elements used with source id.
	 * @param array $errors - the errors list.
	 * @param DOMXpath $xpath - the xpath object used for queries.
	 * @param DOMDocument $xml - the XML object with document data.
	 */
	protected function validateElementsCollection($collection, &$data, &$currentXML, &$used, &$errors, &$xpath, &$xml) {}
	
	
	/**
	 * Validates a Element ID
	 * 
	 * Checks if the id starts with the string 'sample'.
	 * @param string $value - the id to be validated.
	 * @param string $sample - the first two characters for the id.
	 * @return bool
	 */
	protected function validateId($value, $sample) {
		return substr($value, 0, 2) == $sample;
	}
	
	
	/**
	 * Validates Range
	 *
	 * Checks if a certain value is in a certain range.
	 * @param int/string $value - the integer or string to check. If string checks if its lenght is on provided range.
	 * @param int $min - the minimum value allowed.
	 * @param int $max - the maximum value allowed.
	 * @return bool
	 */
	protected function validateRange($value, $min, $max) {
		if(is_numeric($value)) {
			return $value >= $min && $value <= $max;
		}
		else return strlen($value) >= $min && strlen($value) <= $max;
	}
	
	
	/**
	 * Validates Boolean
	 * 
	 * @param int $value - integer that must be 0 or 1.
	 * @return bool
	 */
	protected function validateBoolean($value) {
		return $value == 0 || $value == 1;
	}
	
	
	/**
	 * Validates Element Name
	 * 
	 * @param bool $valid - determines if previous validated data is valid.
	 * @param object $data - the element data which contains its name.
	 * @param DOMElement $old - the old XML element.
	 * @param DOMElement $new - the new XML element.
	 * @param array $errors - the errors list.
	 * @param DOMDocument $xml - the XML object with document data.
	 * @return bool
	 */
	protected function validateName($valid, &$data, &$old, &$new, &$errors, &$xml) {
		if($valid && isset($data->name)) {
			$min = $this->defaults['minimum']['text'];
			$max = $this->defaults['maximum']['name'];
			
			if($this->validateRange($data->name, $min, $max)) {
				$newName = $xml->createElement('name', $data->name);
				if($old) {
					$new->replaceChild($newName, $new->getElementsByTagName('name')->item(0));
				}
				else $new->appendChild($newName);
			}
			else {
				array_push($errors, __('error-incorrect-text-size', __('name'), $data->name, $min, $max));
				$valid = false;
			}
		}
		else if(!$old) $valid = false;
		
		return $valid;
	}
	
	
	/**
	 * Validates Scenarios Jump
	 * 
	 * @param string $name - the jump type.
	 * @param object $data - the jump data.
	 * @param string $scenarioName - the jump scenario name.
	 * @param array $buttons - contains the ids of the existing buttons in the scenario.
	 * @param array $errors - the errors list.
	 * @param DOMElement $item - the scenario XML element.
	 * @param DOMDocument $xml - the XML object with document data.
	 * @return bool
	 */
	protected function validateJump($name, &$data, $scenarioName, $buttons, &$errors, &$item, &$xml) {
		$valid = true;
		
		if(isset($data->on)) {
			
			// Timeout
			if(is_numeric($data->on) && isset($this->defaults['minimum'][$name]) && isset($this->defaults['maximum'][$name])) {
				$min = $this->defaults['minimum'][$name];
				$max = $this->defaults['maximum'][$name];
				
				if($this->validateRange($data->on, $min, $max)) {
					$item->setAttribute('time', $data->on);
				}
				else {
					array_push($errors, __('error-incorrect-scenario-range', __("jump $name"), $scenarioName, $min, $max));
					$valid = false;
				}
			}
			
			// Continue or Skip
			else if(isset($buttons[$data->on])) {
				$reference = $xml->createElement('text_reference');
				$reference->setAttribute('id', $data->on);
				$item->appendChild($reference);
			}
			
			else {
				array_push($errors, __('error-corrupted-jumps', $scenarioName));
				$valid = false;
			}
		}
		
		return $valid;
	}
	
	
	/**
	 * Validate Game Flow
	 *
	 * Check if game has cycles.
	 *
	 * @param Graph $graph - the game flow graph.
	 * @return bool - true if is acyclic and false if is cyclic.
	 */
	protected function validateFlow($graph) {
		App::uses('Queue', 'Lib');
		
		$counter = array();
		$ready = new Queue();
		$vertices = $graph->getVertices();
		
		foreach($vertices as $v) {
			$counter[$v] = $graph->inDegree($v);
			if($counter[$v] == 0) {
				$ready->enqueue($v);
			}
		}
		
		$numSortedVertices = 0;
		while(!$ready->isEmpty()) {
			$v = $ready->dequeue();
			$numSortedVertices++;
			
			foreach($graph->outAdjacentVertices($v) as $w) {
				$counter[$w]--;
				if($counter[$w] == 0) {
					$ready->enqueue($w);
				}
			}
		}
		
		return $numSortedVertices == count($vertices);
	}
	
	
	/**
	 * Validates Styles
	 * 
	 * @param bool $valid - determines if previous validated data is valid.
	 * @param object $data - the element data which contains its styles.
	 * @param DOMElement $item - the XML element.
	 * @param DOMDocument $xml - the XML object with document data.
	 * @param bool $replace - determines if the new styles must replace old ones or if they must be appended to the item.
	 * @return bool
	 */
	protected function validateStyles($valid, &$data, &$item, &$xml, $replace=false) {
		if($valid && isset($data->styles)) {
			$styles = $xml->createElement('styles');
			$valid = $this->validateRecursiveStyles($data->styles, $styles, $xml);
			if($valid) {
				if($replace) $item->replaceChild($styles, $item->getElementsByTagName('styles')->item(0));
				else $item->appendChild($styles);
			}
		}
		else if(!$replace) $valid = false;
		
		return $valid;
	}
	
	
	/**
	 * Validates Styles Recursively
	 * 
	 * @param object $data - the element data which contains its styles.
	 * @param DOMElement $item - the XML element.
	 * @param DOMDocument $xml - the XML object with document data.
	 * @return bool
	 */
	private function validateRecursiveStyles(&$data, &$item, &$xml) {
		$valid = true;
		
		foreach($data as $key => $style) {
			
			switch($key) {
				
				// Validate Font
				case 'font':
					$font = $xml->createElement($key);
					$valid = $this->validateRecursiveStyles($style, $font, $xml);
					if($valid) $item->appendChild($font);
					break;
					
				// Validate Font Size
				case 'size':
					if(isset($this->defaults['fonts']['sizes'][$style])) {
						$item->appendChild($xml->createElement($key, $style));
					}
					else $valid = false;
					break;
					
				// Validate Font Style
				case 'style':
					if(isset($this->defaults['fonts']['styles'][$style])) {
						$item->appendChild($xml->createElement($key, $style));
					}
					else $valid = false;
					break;
					
				// Validate Border
				case 'border':
					$border = $xml->createElement($key);
					$valid = $this->validateRecursiveStyles($style, $border, $xml);
					if($valid) $item->appendChild($border);
					break;
				
				// Validate Background
				case 'background':
					foreach($style as $name => $element) {
						$background = $xml->createElement($key);
						$field = $style;
						if(!isset($style->color) && in_array($name, $this->defaults['types']['players'])) {
							$background->setAttribute('element', $name);
							$field = $element;
						}
						$valid = $this->validateRecursiveStyles($field, $background, $xml);
						
						if($valid) $item->appendChild($background);
						else break;
					}
					break;
		
				// Validate Sizes
				case 'width': case 'height': case 'length': case 'radius': case 'thickness':
					if($this->validateRange($style, $this->defaults['minimum']['numbers'], $this->defaults['maximum']['numbers'])) {
						$item->appendChild($xml->createElement($key, $style));
					}
					else $valid = false;
					break;
					
				// Validate Angles
				case 'rotation':
					$style = abs($style);
					if($this->validateRange($style, $this->defaults['minimum']['numbers'], $this->defaults['maximum'][$key])) {
						$item->appendChild($xml->createElement($key, $style));
					}
					else $valid = false;
					break;
					
				// Validate Corners
				case 'corner': case 'tail':
					if(isset($this->defaults['corners'][$style])) {
						$item->appendChild($xml->createElement($key, $style));
					}
					else $valid = false;
					break;
					
				// Validate Side
				case 'side':
					if(isset($this->defaults['sides'][$style])) {
						$item->appendChild($xml->createElement($key, $style));
					}
					else $valid = false;
					break;
					
				// Validate Color
				case 'color':
					if(preg_match('/^(#)?([0-9a-fA-F]{3})([0-9a-fA-F]{3})?$/', $style) || $style == 'transparent') {
						$item->appendChild($xml->createElement($key, $style));
					}
					else $valid = false;
					break;
			}
			
			if(!$valid) break;
		}
		
		return $valid;
	}
	
	
	/**
	 * Set Terminal Nodes of a Node
	 * 
	 * @param object $data - the data with terminal nodes.
	 * @param DOMElement $item - the XML element.
	 * @param DOMDocument $xml - the XML object with document data.
	 */
	protected function setTerminalNodes($data, &$item, &$xml) {
		foreach($data as $tag => $content) {
			if($tag == 'log') $content = $content? 1 : 0;
			$item->appendChild($xml->createElement($tag, $content));
		}
	}
	
	
	/**
	 * Get a Collection Element by its ID
	 * 
	 * @param DOMXpath $xpath - the xpath object used for queries.
	 * @param string $collection - collection name.
	 * @param string $id - element id.
	 * @return DOMNodeList
	 */
	protected function getCollectionElementById(&$xpath, $collection, $id) {
		return $xpath->query('/e:project/e:'.$collection.'/e:*[@id="'.$id.'"]');
	}
	
	
	/**
	 * Get Used Elements
	 * 
	 * @param DOMXpath $xpath - the xpath object used for queries.
	 * @param string $collection - collection name.
	 * @param array $used - this list where to store ids.
	 * @param string $extra - extra query.
	 * @return array
	 */
	protected function getUsedElements(&$xpath, $collection, &$used, $extra=false) {
		$query = '/e:project/e:'.$collection.'/e:*[@source_id > 0]/@source_id';
		if($extra) $query.= ' | ' . $extra;
		$list = $xpath->evaluate($query);
		
		$used[$collection] = array();
		for($i = 0; $i < $list->length; $i++) {
			$used[$collection][] = (int) $list->item($i)->value;
		}
		$used[$collection] = array_unique($used[$collection]);
	}
	
	
	/**
	 * Get Project/Game Files
	 *
	 * @param string $urlBase - the base url for the files.
	 * @param bool $isGame - determines if is loading files for a project or game.
	 * @return array - images lists for the client and server.
	 */
	public function getFiles($urlBase, $isGame=true) {
		$client = array();
		$server = array();
		
		// Get general images list
		$folder = IMAGES.Configure::read('Default.game.img');
		$url = $urlBase.'/'.IMAGES_URL.Configure::read('Default.game.img').'/';
		$this->Files->getFolderFiles($folder.DS.'general', $url.'general/', 'image', $server);

		// Get avatars list
		$server['avatars'] = array();
		$this->Files->getFolderFiles($folder.DS.'avatars', $url.'avatars/', 'image', $server['avatars']);
		
		// Get resources images list
		$folder = $folder.DS.'resources';
		$url = $url.'resources/';
		$this->Files->getFolderFiles($folder, $url, 'image', $client, $this->defaults['elementsByCollection']['resources']);
		
		if(!$isGame) {
			
			// Get no source images list
			$this->Files->getFolderFiles($folder.DS.'nosource', $url.'nosource/', 'image', $client);
		}
		
		// Get sounds list
		$folder = FILES.Configure::read('Default.game.files').DS.'sounds';
		$url = $urlBase.'/'.FILES_URL.Configure::read('Default.game.files').'/sounds/';
		$this->Files->getFolderFiles($folder, $url, 'audio', $client);
		
		return array('client' => $client, 'server' => $server);
	}
	
}