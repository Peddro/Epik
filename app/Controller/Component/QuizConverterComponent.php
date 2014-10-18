<?php
/**
 * Imports
 */
App::uses('ProjectConverterComponent', 'Controller/Component');

/**
 * QuizConverter Component
 *
 * @package app.Controller.Component
 * @author Bruno Sampaio
 */
class QuizConverterComponent extends ProjectConverterComponent {
	
	/**
	 * @var array Project Game Category.
	 */
	protected $name = 'Quiz';

	
	/**
	 * Constructor
	 * @param ComponentCollection $collection
	 * @param array $settings
	 */
	public function __construct(ComponentCollection $collection, $settings = array()) {
		
		// Set Properties and Collections
		$properties = array('logo', 'scores', 'helps', 'players', 'sounds', 'sync');
		$collections = array('scenarios', 'texts', 'shapes', 'resources', 'activities');
		
		$scenarios = array('lecture', 'activities');
		
		// Set Icons
		$icons = array(
			'scenario' => 'scenario',
			'heading' => 'heading',
			'paragraph' => 'paragraph',
			'button' => 'button',
			'line' => 'line',
			'square' => 'square',
			'circle' => 'circle',
			'balloon' => 'balloon'
		);

		$this->defaults = array(
			'collections' => $collections,
			'elementsByCollection' => array(
				$collections[1] => array($icons['heading'], $icons['paragraph'], $icons['button']),
				$collections[2] => array($icons['line'], $icons['square'], $icons['circle'], $icons['balloon']),
				$collections[3] => array(),
				$collections[4] => array(),
			),
			'helps' => array('all' => array()),
			'icons' => $icons,
			'ids' => array(
				$collections[0] => 'S_',
				$collections[1] => 'T_',
				$collections[2] => 'F_',
				$collections[3] => 'R_',
				$collections[4] => 'A_',
				$properties[3] => 'P_',
			),
			'maximum' => array(
				$properties[2] => 20,
				'thickness' => 30,
				'timeout' => 600
			),
			'minimum' => array(
				'bonus' => 1,
				$properties[2] => 1,
				'numbers' => 0,
				'timeout' => 5
			),
			'properties' => $properties,
			'rules' => array(
				$scenarios[0] => array(),
				$scenarios[1] => array()
			),
			'scores' => array(
				'reward' => array('value' => 5, 'log' => 1),
				'penalty' => array('value' => 5, 'log' => 1)
			),
			'types' => array(
				$properties[1] => array('reward', 'penalty', 'total'),
				$properties[3] => array('bar', 'current'),
				$properties[4] => array('background', 'correct', 'incorrect', 'help'),
				$collections[0] => $scenarios,
				'bonus' => array(
					$scenarios[0] => array(),
					$scenarios[1] => array()
				),
				'jumps' => array(
					$scenarios[0] => array('timeout', 'continue'),
					$scenarios[1] => array('timeout', 'skip', 'allFinished')
				)
			)
		);
		
		parent::__construct($collection, $settings);
	}
	
	
	/**
	 * Creates an array with all defaults settings plus the default elements properties to be used on Javascript when creating new Elements.
	 * This data must only be loaded if it will be necessary later.
	 */
	public function getProjectDefaults() {
		$defaults = parent::getProjectDefaults();
		$icons =& $this->defaults['icons'];
		$scenariosTypes =& $this->defaults['types'][$this->defaults['collections'][0]];
		$jumpsTypes =& $this->defaults['types']['jumps'];

		// Set Default Elements Contents
		$defaults['contents'] = array(
			$icons['heading'] => __('Heading'),
			$icons['paragraph'] => __('A paragraph of text.'),
			$icons['button'] => array($jumpsTypes[$scenariosTypes[0]][1] => __('Continue'), $jumpsTypes[$scenariosTypes[1]][1] => __('Skip'))
		);

		// Set Default Elements Styles
		$defaults['styles'] = array(
			$icons['scenario'] => array('background' => array('color' => '#FFFFFF')),
			$icons['heading'] => array(
				'font' => array('size' => 28, 'style' => 'bold', 'color' => '#000000'),
				'width' => 200,
				'height' => 70,
				'rotation' => 0,
				'border' => array('thickness' => 0, 'color' => 'transparent'),
				'background' => array('color' => 'transparent')
			),
			$icons['paragraph'] => array(
				'font' => array('size' => 14, 'style' => 'normal', 'color' => '#000000'),
				'width' => 400,
				'height' => 200,
				'rotation' => 0,
				'border' => array('thickness' => 1, 'color' => '#000000'),
				'background' => array('color' => 'transparent')
			),
			$icons['button'] => array(
				'font' => array('size' => 24, 'style' => 'bold', 'color' => '#FFFFFF'),
				'width' => 158,
				'height' => 52,
				'rotation' => 0,
				'border' => array('thickness' => 2, 'color' => '#000000'),
				'background' => array('color' => '#113E87')
			),
			$icons['line'] => array(
				'length' => 100,
				'rotation' => 0,
				'border' => array('thickness' => 5, 'color' => '#000000')
			),
			$icons['square'] => array(
				'font' => array('size' => 14, 'style' => 'normal', 'color' => '#000000'),
				'width' => 100,
				'height' => 100,
				'rotation' => 0,
				'border' => array('thickness' => 2, 'color' => '#000000'),
				'background' => array('color' => '#FFFFFF')
			),
			$icons['circle'] => array(
				'font' => array('size' => 14, 'style' => 'normal', 'color' => '#000000'),
				'radius' => 100,
				'border' => array('thickness' => 2, 'color' => '#000000'),
				'background' => array('color' => '#FFFFFF')
			),
			$icons['balloon'] => array(
				'font' => array('size' => 14, 'style' => 'normal', 'color' => '#000000'),
				'width' => 200,
				'height' => 100,
				'rotation' => 0,
				'border' => array('thickness' => 2, 'color' => '#000000'),
				'background' => array('color' => '#FFFFFF'),
				'tail' => 'bottom-left'
			),
			$icons['audio'] => array(
				'width' => 100,
				'height' => 100,
				'rotation' => 0,
				'border' => array('thickness' => 0, 'color' => 'transparent')
			),
			$icons['image'] => array(
				'width' => null,
				'height' => null,
				'rotation' => 0,
				'border' => array('thickness' => 0, 'color' => 'transparent')
			),
			$icons['video'] => array(
				'width' => 100,
				'height' => 100,
				'rotation' => 0,
				'border' => array('thickness' => 0, 'color' => 'transparent')
			),
			$icons['pdf'] => array(
				'width' => 100,
				'height' => 100,
				'rotation' => 0,
				'border' => array('thickness' => 0, 'color' => 'transparent')
			),
			$icons['question'] => array(
				'width' => 300,
				'rotation' => 0,
				'border' => array('thickness' => 2, 'color' => '#000000'),
				'background' => array('color' => '#FFFFFF'),
				'tail' => 'bottom-left'
			),
			$icons['group'] => array(
				'width' => 300,
				'rotation' => 0,
				'border' => array('thickness' => 2, 'color' => '#000000'),
				'background' => array('color' => '#FFFFFF'),
				'tail' => 'bottom-right'
			)
		);
		
		return $defaults;
	}
	
	
	/**
	 * Creates an array with the data need by Javascript to play a Game.
	 * This data must only be loaded if it will be necessary later.
	 */
	public function getGameDefaults() {
		$defaults = parent::getGameDefaults();
		$defaults['helps'] = $this->defaults['helps'];
		return $defaults;
	}
	
	
	/**
	 * Parse Game Start
	 *
	 * This method loads the project start into an array.
	 * @param SimpleXML $xml - the XML element with the scenarios data.
	 * @param array $data - the array where to store the data.
	 * @param array $errors - the errors list.
	 */
	protected function parseStart($xml, &$data, &$errors=null) {
		$start = $xml->start->children();
		$data['start'] = (count($start) > 0)? (string) $start->scenario_reference->attributes() : 0;
		
		if(!is_null($errors) && !$data['start']) {
			array_push($errors, __('error-no-start-scenario'));
		}
	}
	
	
	/**
	 * Parse Project Status
	 *
	 * This method loads the project status into an array.
	 * @param SimpleXML $xml - the XML element with the status data.
	 * @param array $data - the array where to store the data.
	 */
	protected function parseStatus($xml, &$data) {
		$selected = $xml->status->selected;
		
		// Parse Selected Scenario
		if(isset($selected->scenario_reference)) {
			$data['status'] = array('scenario' => (string) $selected->scenario_reference->attributes());
			
			// Parse Selected Element
			if(isset($selected->element)) {
				$data['status']['element'] = (string) current($selected->element->children())->attributes();
			}
		}
		
		// Parse Selected Property
		else if(isset($selected->property)) {
			$data['status'] = array('property' => (string) $selected->property);
		}
		
		// Parse Selected Screen
		else if(isset($selected->screen)) {
			$data['status'] = array('screen' => (string) $selected->screen);
		}
	}
	
	
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
	protected function parseProperties($xml, &$data, &$server=null, &$used=null, &$errors=null) {
		$data['properties'] = array();
		$list =& $data['properties'];
		$properties = $xml->properties;
		$validating = !is_null($server) && !is_null($errors);
		
		if($validating) {
			$server['properties'] = array();
			$serverList =& $server['properties'];
		}
		
		// Parse Logo
		$logo = $properties->logo;
		$list['logo'] = array(
			'styles' => array('corner' => (string) $logo->styles->corner)
		);
		
		// Parse Scores
		$list['scores'] = array();
		if($validating) $serverList['scores'] = array();
		$scores = $properties->scores->score;
		foreach($scores as $score) {
			$scoreType = (string) $score->attributes()->type;
			$list['scores'][$scoreType] = array(
				'name' => (string) $score->name,
				'log' => (int) $score->log
			);
			
			if($validating) {
				$serverList['scores'][$scoreType] = $list['scores'][$scoreType]['log'];
			}
		}
		
		// Parse Helps
		$helps = $properties->helps;
		$list['helps'] = array('name' => (string) $helps->name, 'value' => (int) $helps->value, 'log' => (int) $helps->log);
		if($validating) {
			$serverList['helps'] = array('value' => (int) $helps->value, 'log' => (int) $helps->log);
		}

		//Parse Sync 
		$sync = $properties->sync;
		$list['sync'] = array('value' => (int) $sync);
		if ($validating) {
			$serverList['sync'] = array('value' => (int) $sync);
		}

		// Parse Players
		$players = $properties->players;
		$list['players'] = array('max' => (int) $players->max, 'styles' => array());
		if($validating) {
			$serverList['players'] = array('min' => $this->defaults['minimum']['players'], 'max' => $list['players']['max']);
		}
		$this->parseStyles($players->styles, $list['players']['styles']);
		
		// Parse Sounds
		$list['sounds'] = array();
		foreach($this->defaults['types']['sounds'] as $sound) { 
			$list['sounds'][$sound] = 0; 
		}
		
		$sounds = $properties->sounds->sound;
		foreach($sounds as $sound) {
			$soundType = (string) $sound->attributes()->type;
			$sourceId = (int) $sound->attributes()->source_id;
			
			$list['sounds'][$soundType] = $sourceId;
			if(!is_null($used)) {
				$used[$this->defaults['collections'][3]][$sourceId] = true;
			}
		}
		
		// Check Logo and Players Position
		if($validating) {
			$corner = $list['logo']['styles']['corner'];
			$side = $list['players']['styles']['side'];
			if(($side == 'left' && ($corner == 'top-left' || $corner == 'bottom-left')) || ($side == 'right' && ($corner == 'top-right' || $corner == 'bottom-right')) ||
				($side == 'top' && ($corner == 'top-left' || $corner == 'top-right')) || ($side == 'bottom' && ($corner == 'bottom-left' || $corner == 'bottom-right'))) {
				
				array_push($errors, __('error-logo-players-overlaid'));
			}
		}
	}
	
	
	/**
	 * Parse Scenarios Data
	 *
	 * This method loads all project scenarios into an array.
	 * @param SimpleXML $xml - the XML element with the scenarios data.
	 * @param array $data - the array where to store the data.
	 * @param array $server - the array where to store the server data.
	 * @param array $errors - the errors list.
	 */
	protected function parseScenarios($xml, &$data, &$server= null, &$errors=null) {
		$scenarios = $xml->scenarios;
		$collections =& $this->defaults['collections'];
		$collection = $collections[0];
		$scenariosTypes =& $this->defaults['types'][$collection];
		$validating = !is_null($server) && !is_null($errors);
		
		if(!$validating) {
			$data[$collection] = array('count' => (int) $scenarios->attributes()->count, 'list' => array());
			$list =& $data[$collection]['list'];
		}
		else {
			$data[$collection] = array();
			$server[$collection] = array();
			$list =& $data[$collection];
			$serverList =& $server[$collection];
			
			App::uses('Graph', 'Lib');
			$graph = new Graph();
		}
		
		$i = 0;
		$referenced = array();
		$scenarios = $scenarios->scenario;
		foreach($scenarios as $scenario) {
			$attributes = $scenario->attributes();
			$id = (string) $attributes->id;
			$type = (string) $attributes->type;
			
			
			if(!$validating) {
				$list[$i] = array(
					'id' => $id,
					'name' => (string) $scenario->name,
					'type' => $type,
					'contents' => array(),
					'rules' => new stdClass(),
					'jumps' => new stdClass(),
					'styles' => array()
				);
				
				$scenarioData =& $list[$i];
			}
			else {
				$list[$id] = array(
					'type' => $type,
					'contents' => array(),
					'styles' => array()
				);
				
				$serverList[$id] = array(
					'name' => (string) $scenario->name,
					'type' => $type,
					'contents' => array(),
					'rules' => new stdClass(),
					'jumps' => new stdClass(),
					$collections[4] => 0
				);
				
				$scenarioData =& $list[$id];
			}
			
			// Parse Scenario Contents
			$contents = $scenario->contents->element;
			if($contents) {
				foreach($contents as $element) {
					$children = $element->children();
					$elementId = (string) $children[0]->attributes()->id;
					$position = $children[1];

					// Parse Element Position
					$pos = array();
					if(isset($position->absolute)) {
						$point = $position->absolute->point;
						$pos['absolute'] = array('point' => array('x' => (int) $point->x, 'y' => (int) $point->y));
					}
					else if(isset($position->aligned)) {
						$pos['aligned'] = array(
							'horizontal' => (string) $position->aligned->horizontal,
							'vertical' => (string) $position->aligned->vertical
						);
					}
					
					// Set Reference Data
					$elementCollection = (string) Inflector::pluralize(explode('_', $children[0]->getName())[0]);
					array_push($scenarioData['contents'], array(
						'id' => $elementId,
						'collection' => $elementCollection,
						'position' => $pos
					));
					
					if($validating) {
						$serverList[$id]['contents'][$elementId] = 1;
						
						// Count Scenario Activities
						if($elementCollection == $collections[4]) {
							$serverList[$id][$elementCollection]++; 
						}
					}
				}
				
				if($validating) {
					if($type == $scenariosTypes[0] && $serverList[$id][$collections[4]] > 0) {
						
						// Lecture Scenarios Must Not Have Activities
						array_push($errors, __('error-lecture-scenario', $scenario->name));
					}
					else if($type == $scenariosTypes[1] && $serverList[$id][$collections[4]] == 0) {
						
						// Activities Scenarios Must Have Activities
						array_push($errors, __('error-activities-scenario', $scenario->name));
					}
				}
			}
			else if($validating) {
				
				// Empty scenario
				array_push($errors, __('error-empty-scenario', $scenario->name));
			}
			
			// Parse Scenario Rules
			if(isset($scenario->rules) && $scenario->rules) {
				$rules = $scenario->rules;
				
				if(!$validating) $rulesData =& $scenarioData['rules'];
				else $rulesData =& $serverList[$id]['rules'];
				
				// Parse Helps
				if(isset($rules->helps)) {
					$rulesData->helps = (int) $rules->helps;
				}
				
				// Parse Bonus
				if(isset($rules->bonus)) {
					$rulesData->bonus = new stdClass();
					$bonusTypes = $this->defaults['types']['bonus'][$type];
					
					foreach($rules->bonus as $bonus) {
						$itemType = (string) $bonus->attributes()->type;
						
						if(!$validating || in_array($itemType, $bonusTypes)) {
							$rulesData->bonus->{$itemType} = array('value' => (int) $bonus->value, 'log' => (int) $bonus->log);
						}
						else {
							
							// Incorrect Scenario Bonus
							array_push($errors, __('error-incorrect-scenario-bonus', $scenario->name, $itemType));
						}
					}
				}
				else if(!$validating && count($this->defaults['rules'][$type]) > 0) {
					$rulesData->bonus = new stdClass();
				}
			}
			
			// Parse Scenario Jumps
			$jumps = $scenario->jumps->jump;
			if($jumps) {
				$jumpTypes =& $this->defaults['types']['jumps'][$type];
				
				if(!$validating) $jumpsData =& $scenarioData['jumps'];
				else {
					$jumpsData =& $serverList[$id]['jumps'];
				}
				
				foreach($jumps as $jump) {
					$attr = $jump->attributes();
					$itemType = (string) $attr->type;
					
					if(!$validating || in_array($itemType, $jumpTypes)) {
						$jumpsData->{$itemType} = new stdClass();
						
						// Set Jump Scenario
						if(isset($jump->scenario_reference)) {
							$jumpToId = (string) $jump->scenario_reference->attributes()->id;
							$jumpsData->{$itemType}->to = $jumpToId;
							$referenced[$jumpToId] = true;
							
							if($validating) {
								$graph->insertEdge($id, $jumpToId);
							}
						}
						
						// Set Jump Timeout or Button
						if(isset($attr->time)) {
							$jumpsData->{$itemType}->on = (int) $attr->time;
						}
						else if(isset($jump->text_reference)) {
							$jumpsData->{$itemType}->on = (string) $jump->text_reference->attributes()->id;
						}
					}
					else {
						
						// Incorrect Scenario Jump
						array_push($errors, __('error-incorrect-scenario-jump', $scenario->name, $itemType));
					}
				}
			}
			else if($validating) {
				
				// Scenario has no Jumps
				array_push($errors, __('error-no-scenario-jumps', $scenario->name));
			}
			
			// Parse Scenario Styles
			$this->parseStyles($scenario->styles, $scenarioData['styles']);
			
			$i++;
		}
		
		if($validating) {
			
			// There are no more than one scenario
			if($i <= 1) {
				array_push($errors, __('error-not-enough-scenarios'));
			}
			
			// There are isolated scenarios
			if(count($referenced) != ($i-1)) {
				array_push($errors, __('error-isolated-scenarios'));
			}
			
			// There are cycles
			if(!$this->validateFlow($graph)) {
				array_push($errors, __('error-cyclic-game'));
			}
		}
	}
	
	
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
	protected function parseCollection($collection, $elements, $xml, &$data, &$server=null, &$used=null, &$errors=null) {
		$xml = $xml->{$collection};
		$data[$collection] = array();
		$isText = $collection == $this->defaults['collections'][1];
		$isResource = $collection == $this->defaults['collections'][3];
		$isActivity = $collection == $this->defaults['collections'][4];
		$validating = !is_null($server) && !is_null($errors);
		
		if($validating) {
			$server[$collection] = array();
			$serverList =& $server[$collection];
		}
		
		foreach($elements as $type) {
			$i = 0;
			$data[$collection][$type] = array();
			
			$list =& $data[$collection][$type];
			$currentXML = $xml->{$type};
			
			foreach($currentXML as $item) {
				$attr = $item->attributes();
				$id = (string) $attr->id;
				$name = (string) $item->name;
				
				$list[$i] = array(
					'id' => $id,
					'styles' => array()
				);
				
				if(!$validating) {
					$list[$i]['locked'] = (int) $attr->locked;
					$list[$i]['name'] = $name;
				}
				else {
					$serverList[$id] = array();
				}
				
				// Parse Type
				if(isset($attr->type)) {
					$list[$i]['type'] = (string) $attr->type;
				}
				
				// Parse Source
				if(isset($attr->source_id)) {
					$list[$i]['source'] = (int) $attr->source_id;
					
					if($validating && $isActivity) {
						$serverList[$id]['source'] = $list[$i]['source'];
					}
					
					// Store Source
					if($isResource && !is_null($used)) {
						$used[$collection][$list[$i]['source']] = true;
					}
					
					// There are template elements
					if($validating && !$list[$i]['source']) {
						array_push($errors, __('error-no-element-source', $name, Inflector::singularize($collection)));
					}
				}
				
				// Parse Content
				if(isset($item->content)) {
					$list[$i]['content'] = (string) $item->content;
					
					if($validating && $isText && strlen($list[$i]['content']) == 0) {
						array_push($errors, __('error-empty-element-content', $name));
					}
				}
				
				if((isset($this->defaults['icons']['question']) || isset($this->defaults['icons']['group'])) && $type == 'question') {
					$isGroup = (int) $attr->group;
					
					if($validating) {
						$questionData =& $serverList[$id];
						$list[$i]['group'] = $isGroup;
						$usedHelps =& $used['helps'];
					}
					else $questionData =& $list[$i];
					
					$questionData['group'] = $isGroup;
					
					// Parse Question Scores
					$scores = $item->scores;
					$questionData['scores'] = array(
						'reward' => array('value' => (int) $scores->reward->value, 'log' => (int) $scores->reward->log),
						'penalty' => array('value' => (int) $scores->penalty->value, 'log' => (int) $scores->penalty->log)
					);
					if(isset($scores->timeout)) {
						$questionData['scores']['timeout'] = array('value' => (int) $scores->timeout);
					}
					
					// Parse Question Helps
					$questionData['helps'] = array();
					foreach($item->helps->children() as $help) {
						$helpName = $help->getName();
						$helpValue = (int) $help->use;
						$helpSelected = 0;
						
						if(!$validating || $helpValue) {
							$isHintsHelp = $helpName == 'hints';
							$isResourceHelp = $helpName == 'resource';
							$questionData['helps'][$helpName] = array('use' => $helpValue, 'selected' => 0);

							// If help is used
							if($helpValue) {
								
								// Set Used Hints and Resources Ids List
								if(isset($usedHelps) && ($isHintsHelp || $isResourceHelp)) {
									if(!isset($usedHelps[$helpName][$type])) {
										$usedHelps[$helpName][$type] = array();
									}
									if(!isset($usedHelps[$helpName][$type][$isGroup])) {
										$usedHelps[$helpName][$type][$isGroup] = array();
									}

									// Store groups used resources and hints
									if($isGroup) {
										$usedHelps[$helpName][$type][$isGroup][$questionData['source']] = true;
									}
								}
								
								// If there are selected items (not applicable for groups)
								if(isset($help->selected) && $help->selected) {
									$questionData['helps'][$helpName]['selected'] = new stdClass();

									foreach($help->selected as $helpSources) {
										$sourceId = (int) $helpSources->attributes()->source_id;
										$questionData['helps'][$helpName]['selected']->{$sourceId} = true;

										if(isset($usedHelps) && ($isHintsHelp || $isResourceHelp)) {

											// Store questions used resources and hints
											$usedHelps[$helpName][$type][$isGroup][$sourceId] = true;
											
											if($isResourceHelp) $used[$this->defaults['collections'][3]][$sourceId] = true;
										}

										$helpSelected++;
									}
								}
							}

							if($validating) {
								if(!$questionData['group']) {
									if(($isResourceHelp || $isHintsHelp) && $helpValue && !$questionData['helps'][$helpName]['selected']) {
										array_push($errors, __('error-corrupted-help', $name, $helpName));
									}
									else if($isHintsHelp && $helpValue && $helpSelected < 2) {
										array_push($errors, __('error-not-enough-hints', $name));
									}
								}
							}
						}
					}
					
					if($validating) {
						$list[$i]['scores'] = $questionData['scores'];
						$list[$i]['helps'] = $questionData['helps'];
					}
					
					// Store Question Source
					if(!isset($used[$collection][$type])) {
						$used[$collection][$type] = array(0 => array(), 1 => array());
					}
					$used[$collection][$type][$isGroup][(string) $questionData['source']] = true;
				}
				
				// Parse Styles
				$this->parseStyles($item->styles, $list[$i]['styles']);
				
				// If element data empty remove it from server data
				if($validating && count($serverList[$id]) == 0) {
					unset($serverList[$id]);
				}
				
				$i++;
			}
			
			// If list is empty remove it from data
			if($validating && count($list) == 0) {
				unset($data[$collection][$type]);
			}
		}
		
		if($validating) {
			// Remove Collection from Data if it's empty
			if(count($data[$collection]) == 0) unset($data[$collection]);
			if(count($serverList) == 0) unset($server[$collection]);
		}
		else {
			// Store Collection Count
			$data[$collection]['count'] = (int) $xml->attributes()->count;
		}
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
	protected function validateStatus(&$data, &$collections, &$properties, &$screens, &$errors, &$xpath, &$xml) {
		$currentXML = $xml->getElementsByTagName('status')->item(0);
		$newSelected = $xml->createElement('selected');
		$valid = true;
		
		// If a scenario is selected
		if(isset($data->scenario)) {
			$exists = $this->getCollectionElementById($xpath, $collections[0], $data->scenario)->length > 0;
			
			if($exists) {
				$reference = $xml->createElement('scenario_reference');
				$reference->setAttribute('id', $data->scenario);
				$newSelected->appendChild($reference);
				
				// If a element is selected
				if(isset($data->element) && isset($data->element->id) && isset($data->element->collection)) {
					$element = $data->element;
					$exists = $this->getCollectionElementById($xpath, $element->collection, $element->id)->length > 0;
					
					if($exists) {
						$reference = $xml->createElement(Inflector::singularize($element->collection).'_reference');
						$reference->setAttribute('id', $element->id);
						
						$element = $xml->createElement('element');
						$element->appendChild($reference);
						
						$newSelected->appendChild($element);
					}
					else $valid = false;
				}
			}
			else $valid = false;
		}
		
		// If a property is selected
		else if(isset($data->property) && in_array($data->property, $properties)) {
			$newSelected->appendChild($xml->createElement('property', $data->property));
		}
		
		// If a screen is selected
		else if(isset($data->screen) && isset($screens[$data->screen])) {
			$newSelected->appendChild($xml->createElement('screen', $data->screen));
		}
		
		else $valid = false;
		
		
		if($valid) {
			$currentXML->replaceChild($newSelected, $currentXML->getElementsByTagName('selected')->item(0));
		}
		else array_push($errors, __('error-corrupted-status-element'));
	}
	
	
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
	protected function validateProperties(&$data, &$collections, &$properties, &$used, &$errors, &$xml) {
		$currentXML = $xml->getElementsByTagName('properties')->item(0);

		foreach($data as $key => $property) {
			$item = $xml->createElement($key);
			$valid = true;

			switch($key) {

				// Logo
				case $properties[0]:
				
					// Validate Styles
					$valid = $this->validateStyles($valid, $property, $item, $xml);
					break;

				// Scores and Helps
				case $properties[1]:
					$scores = $currentXML->getElementsByTagName('score');
					
					// Validate Scores
					for($i = 0; $i < $scores->length; $i++) {
						$score = $scores->item($i);
						$scoreType = $score->getAttribute('type');
						
						if(isset($property->{$scoreType})) {
							$scoreData = $property->{$scoreType};
							$scoreXML = $xml->createElement('score');
							$scoreXML->setAttribute('type', $scoreType);
							
							// Validate Score Name and Log
							if(isset($scoreData->name) && isset($scoreData->log) && strlen($scoreData->name) > 0 && $this->validateBoolean($scoreData->log)) {
								$this->setTerminalNodes($scoreData, $scoreXML, $xml);
							}
							else {
								$valid = false;
								break;
							}
							
							$item->appendChild($scoreXML);
						}
						else $item->appendChild($score);
					}
					break;
					
				// Helps
				case $properties[2]:
					$helps = $currentXML->getElementsByTagName('helps');
					
					// Validate Helps Name
					if(isset($property->name) && strlen($property->name) > 0 &&
						isset($property->value) && $this->validateRange($property->value, $this->defaults['minimum'][$key], $this->defaults['maximum'][$key]) &&
						isset($property->log) && $this->validateBoolean($property->log)) {
						
						$this->setTerminalNodes($property, $item, $xml);
					}
					else $valid = false;
					break;

				// Players
				case $properties[3]:
				
					// Validate Maximum Players
					if(isset($property->max)) {
						if($this->validateRange($property->max, $this->defaults['minimum'][$key], $this->defaults['maximum'][$key])) {
							$item->appendChild($xml->createElement('max', $property->max));
						}
						else {
							$valid = false;
							array_push($errors, __('The number of players must be between %s and %s.', $min, $max));
						}
					}

					// Validate Styles
					$valid = $this->validateStyles($valid, $property, $item, $xml);
					break;

				// Sounds
				case $properties[4]:
					$sounds =& $this->defaults['types'][$key];
					foreach($sounds as $sound) {
						
						// Validate Sound Element
						if(isset($property->{$sound}) && $property->{$sound} > 0) {
							$id = $property->{$sound};
							$soundItem = $item->appendChild($xml->createElement('sound'));
							$soundItem->setAttribute('type', $sound);
							$soundItem->setAttribute('source_id', $id);
							
							// Set Resources as Updated
							$used[$collections[3]] = true; 
						}
					}
					break;

				//Sync DANGER
				case $properties[5]:
					if (isset($property->{$sync}) && $this->validateBoolean($property->{$sync})) {
						//$item->appendChild($xml->createElement('sync', $property->{$sync}));
					}
					else {
						$valid = false;
					}
					break;
			}

			if($valid) {
				// Replace old data with new data
				$currentXML->replaceChild($item, $currentXML->getElementsByTagName($key)->item(0));
			}
			else array_push($errors, __('error-corrupted-property', __($key)));
		}
	}
	
	
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
	protected function validateCollection($collection, &$data, &$used, &$errors, &$xpath, &$xml, $callback) {
		$currentXML = $xml->getElementsByTagName($collection)->item(0);
		
		// Validate count
		$count = (isset($data->count) && $data->count > 0)? $data->count : 0;
		$currentXML->setAttribute('count', $count);
		unset($data->count);
		
		if($count > 0) call_user_func_array(array(__CLASS__, $callback), array($collection, &$data, &$currentXML, &$used, &$errors, &$xpath, &$xml));
	}
	
	
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
	protected function validateScenariosCollection($collection, &$data, &$currentXML, &$used, &$errors, &$xpath, &$xml) {
		$icons =& $this->defaults['icons'];
		$defaultId = $this->defaults['ids'][$collection];
		$maxHelps = (int) $xml->getElementsByTagName('properties')->item(0)->getElementsByTagName('helps')->item(0)->getElementsByTagName('value')->item(0)->textContent;
		
		// Remove Scenarios
		$action = $this->defaults['saves'][7];
		if(isset($data->{$action})) {
			foreach($data->{$action} as $id) {
				$list = $this->getCollectionElementById($xpath, $collection, $id);
				if($list->length > 0) $currentXML->removeChild($list->item(0));
			}
			
			unset($data->{$action});
		}
		
		// Add or Modify Scenarios
		foreach($data as $index => $info) {
			$id = $info->id;
			
			if($this->validateId($id, $defaultId)) {
				$list = $this->getCollectionElementById($xpath, $collection, $id);
				$valid = true;

				// If there is more information besides the scenario id
				if(isset($info->type)) {
					$type = $info->type;
					
					if($list->length == 0) {
						$old = false;
						$new = $xml->createElement($icons['scenario']);
						$new->setAttribute('id', $id);
						$new->setAttribute('type', $type);
					}
					else {
						$old = $list->item(0);
						$new = $old->cloneNode(true);
					}

					// Validate Name
					$valid = $this->validateName($valid, $info, $old, $new, $errors, $xml);
					if($valid) $name = isset($info->name)? $info->name : $new->getElementsByTagName('name')->item(0)->textContent;

					// Validate Contents
					$buttons = array();
					if($valid && isset($info->contents)) {
						$newContents = $xml->createElement('contents');

						foreach($info->contents as $content) {
							$element = $this->getCollectionElementById($xpath, $content->collection, $content->id);
							$validReference = true;

							if($element->length > 0) {
								$elementName = $element->item(0)->getElementsByTagName('name')->item(0)->textContent;
								
								if(isset($content->position)) {
									$newContent = $xml->createElement('element');

									// Set Element Reference
									$elementReference = $xml->createElement(Inflector::singularize($content->collection).'_reference');
									$elementReference->setAttribute('id', $content->id);
									$newContent->appendChild($elementReference);

									// Set Element Position
									$position =& $content->position;
									$elementPosition = $xml->createElement('position');
									$newContent->appendChild($elementPosition);

									// Set Position Type
									if(isset($position->absolute) && isset($position->absolute->point)) {
										$point = $position->absolute->point;
										$max = $this->defaults['maximum']['numbers'];
										if($this->validateRange($point->x, 0, $max) && $this->validateRange($point->y, 0, $max)) {
											$absolutePosition = $xml->createElement('absolute');
											$absolutePoint = $xml->createElement('point');
											$absolutePoint->appendChild($xml->createElement('x', round($point->x)));
											$absolutePoint->appendChild($xml->createElement('y', round($point->y)));

											$absolutePosition->appendChild($absolutePoint);
											$elementPosition->appendChild($absolutePosition);
										}
										else {
											$validReference = false;
											array_push($errors, __('error-incorrect-position-range', $elementName, $name, $min, $max));
										}
									}
									else if(isset($position->aligned) && 
										isset($position->aligned->horizontal) && isset($this->defaults['aligned']['horizontal'][$position->aligned->horizontal]) && 
										isset($position->aligned->vertical) && isset($this->defaults['aligned']['vertical'][$position->aligned->vertical])) {

										$alignedPosition = $xml->createElement('aligned');
										$alignedPosition->appendChild($xml->createElement('horizontal', $position->aligned->horizontal));
										$alignedPosition->appendChild($xml->createElement('vertical', $position->aligned->vertical));
										$elementPosition->appendChild($alignedPosition);
									}
									else {
										$validReference = false;
										array_push($errors, __('error-corrupted-position', $elementName, $name));
									}
								}
								else {
									$contentsList = $xpath->query('/e:project/e:'.$collection.'/e:*[@id="'.$id.'"]/e:contents/e:element[e:*[@id="'.$content->id.'"]]');
									if($contentsList->length > 0) {
										$newContent = $contentsList->item(0);
									}
									else {
										$validReference = false;
										array_push($errors, __('error-no-scenario-element', $elementName, $name));
									}
								}

								if($validReference) {

									// If is button add to buttons array
									if($element->item(0)->tagName == $icons['button']) {
										$buttons[$content->id] = true;
									}

									$newContents->appendChild($newContent);
								}
								else $xml->getElementsByTagName($content->collection)->item(0)->removeChild($element->item(0));
							}
						}

						if($old) {
							$new->replaceChild($newContents, $new->getElementsByTagName('contents')->item(0));
						}
						else $new->appendChild($newContents);
					}

					// Validate Rules
					$currentRules = $new->getElementsByTagName('rules');
					if($valid && isset($info->rules)) {
						$newRules = $xml->createElement('rules');

						// Validate Scenario Helps
						if(isset($info->rules->helps) && $info->rules->helps != $maxHelps) {
							$min = 0;
							$max = $maxHelps;
							if($this->validateRange($info->rules->helps, $min, $max-1)) {
								$newRules->appendChild($xml->createElement('helps', $info->rules->helps));
							}
							else {
								array_push($errors, __('error-incorrect-scenario-range', __('helps'), $name, $min, $max));
							}
						}

						// Validate Scenario Bonus
						if(isset($info->rules->bonus)) {
							$min = $this->defaults['minimum']['bonus'];
							$max = $this->defaults['maximum']['numbers'];
							
							foreach($this->defaults['types']['bonus'][$type] as $bonus) {
								if(isset($info->rules->bonus->{$bonus})) {

									$bonusData = $info->rules->bonus->{$bonus};
									if(isset($bonusData->value) && $this->validateRange($bonusData->value, $min, $max) &&
										isset($bonusData->log) && $this->validateBoolean($bonusData->log)) {

										$newBonus = $xml->createElement('bonus');
										$newBonus->setAttribute('type', $bonus);
										$this->setTerminalNodes($bonusData, $newBonus, $xml);
										$newRules->appendChild($newBonus);
									}
									else if($bonusData->value) {
										array_push($errors, __('error-incorrect-scenario-range', __('bonus'), $name, $min, $max));
									}
								}
							}
						}

						if($old) {
							if($currentRules->length > 0) {
								$new->replaceChild($newRules, $currentRules->item(0));
							}
							else $new->insertBefore($newRules, $new->getElementsByTagName('jumps')->item(0));
						}
						else $new->appendChild($newRules);
					}

					// Validate Jumps
					if($valid && isset($info->jumps)) {
						$jumpsTypes = $this->defaults['types']['jumps'][$type];
						$newJumps = $xml->createElement('jumps');

						foreach($info->jumps as $jumpName => $jumpValue) {
							$jump = $xml->createElement('jump');
							$jump->setAttribute('type', $jumpName);
							$validJump = false;
							
							if(in_array($jumpName, $jumpsTypes)) {
								
								// Set Jump To
								if(isset($jumpValue->to)) {
									$reference = $xml->createElement('scenario_reference');
									$reference->setAttribute('id', $jumpValue->to);
									$jump->appendChild($reference);
								}
								
								// Set Jump On
								$validJump = $this->validateJump($jumpName, $jumpValue, $name, $buttons, $errors, $jump, $xml);
							}
							
							// If jump data is valid add it to the list
							if($validJump) {
								$newJumps->appendChild($jump);
							}
						}

						if($old) {
							$new->replaceChild($newJumps, $new->getElementsByTagName('jumps')->item(0));
						}
						else $new->appendChild($newJumps);
					}
					else if(!$old) {
						$valid = false;
					}

					// Validate Styles
					$valid = $this->validateStyles($valid, $info, $new, $xml, $old);

					// Remove old scenario data
					if($old) $currentXML->removeChild($old);
				}

				// If no information provided and the scenario already exists
				else if($list->length == 1) {
					$old = true;
					$new = $list->item(0);
					$name = $new->getElementsByTagName('name')->item(0)->textContent;
				}

				// If no information provided and the scenario doesn't exist yet
				else {
					$old = false;
					$valid = false;
					$name = isset($info->name)? $info->name : '';
				}

				if($valid || $old) {
					$currentXML->appendChild($new);
				}
				else array_push($errors, __('error-corrupted-scenario', $name));
			}
			else array_push($errors, __('error-corrupted-identifier', $id));
		}
	}
	
	
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
	protected function validateElementsCollection($collection, &$data, &$currentXML, &$used, &$errors, &$xpath, &$xml) {
		$collections =& $this->defaults['collections'];
		$icons =& $this->defaults['icons'];
		$defaultId = $this->defaults['ids'][$collection];
		
		$isText = $collection == $collections[1];
		$isShape = $collection == $collections[2];
		$hasSource = $collection == $collections[3] || $collection == $collections[4];
		
		foreach($data as $id => $info) {
			if($this->validateId($id, $defaultId)) {
				$list = $this->getCollectionElementById($xpath, $collection, $id);
				$valid = true;

				// Store
				if($info) {

					$isQuestion = (isset($icons['question']) && $info->icon == $icons['question']) || (isset($icons['group']) && $info->icon == $icons['group']);

					if($list->length == 0) {
						$old = false;
						$new = $xml->createElement($isQuestion? 'question' : $info->icon);
						$new->setAttribute('id', $id);
					}
					else {
						$old = $list->item(0);
						$new = $old->cloneNode(true);
					}

					// Validate Source
					if($hasSource) {
						if($valid && isset($info->source)) {
							if($info->source >= 0) {
								$new->setAttribute('source_id', $info->source);
								$used[$collection] = true;
							}
							else $valid = false;
						}
						else if(!$old) $valid = false;
					}

					// Validate Locked
					if($valid && isset($info->locked)) {
						if($this->validateBoolean($info->locked)) {
							$new->setAttribute('locked', $info->locked? 1 : 0);
						}
						else $valid = false;
					}
					else if(!$old) $valid = false;

					// Validate Button Type
					if($isText && $info->icon == $icons['button']) {
						if($valid && isset($info->type)) {
							$new->setAttribute('type', $info->type);
						}
						else if(!$old) $valid = false;
					}

					// Validate Name
					$valid = $this->validateName($valid, $info, $old, $new, $errors, $xml);
					if($valid) $name = isset($info->name)? $info->name : $new->getElementsByTagName('name')->item(0)->textContent;

					// Validate Text Content
					if($isText || ($isShape && $info->icon != $icons['line'])) {
						if($valid && isset($info->text)) {
							$min = 0;
							if($info->icon == 'heading') {
								$max = $this->defaults['maximum']['text'];
								$valid = $this->validateRange($info->text, $min, $max);
							}
							else {
								$max = $this->defaults['maximum']['paragraph'];
								$valid = $this->validateRange($info->text, $min, $max);
							}

							if($valid) {
								$newContent = $xml->createElement('content', $info->text);

								if($old) {
									$new->replaceChild($newContent, $new->getElementsByTagName('content')->item(0));
								}
								else $new->appendChild($newContent);
							}
							else array_push($errors, __('error-incorrect-text-size', __('text'), $name, $min, $max));
						}
						else if(!$old) $valid = false;
					}

					// Validate Question Fields
					if($isQuestion) {
						$isGroup = false;
						
						// Validate Question Category
						if(isset($info->group) && $this->validateBoolean($info->group)) {
							$isGroup = $info->group? 1 : 0;
							$new->setAttribute('group', $isGroup);
						}
						else if(!$old) $valid = false;

						// Validate Question Scores
						if($valid && isset($info->scores)) {
							$newScores = $xml->createElement('scores');

							// Set Reward Score
							$valid = $this->validateQuestionScore($name, 'reward', $this->defaults['maximum']['numbers'], $info->scores, $errors, $newScores, $xml);

							// Set Penalty Score
							if($valid) $valid = $this->validateQuestionScore($name, 'penalty', $this->defaults['maximum']['percentage'], $info->scores, $errors, $newScores, $xml);

							// Set Timeout
							if($this->defaults['maximum']['players'] > 1) {
								$min = $this->defaults['minimum']['timeout'];
								$max = $this->defaults['maximum']['timeout'];
								if($valid && isset($info->scores->timeout) && isset($info->scores->timeout->value) && $this->validateRange($info->scores->timeout->value, $min, $max)) {
									$newScores->appendChild($xml->createElement('timeout', $info->scores->timeout->value));
								}
								else {
									array_push($errors, __('error-incorrect-element-range', __('timeout'), $name, $min, $max));
									$valid = false;
								}
							}

							if($old) {
								$new->replaceChild($newScores, $new->getElementsByTagName('scores')->item(0));
							}
							else $new->appendChild($newScores);

						}
						else if(!$old) $valid = false;

						// Validate Question Helps
						if($valid && isset($info->helps)) {
							
							// Set New Helps
							$newHelps = $xml->createElement('helps');
							if(isset($info->source) && $info->source) {
								
								// Get List of Allowed Helps
								$helpsList = $this->defaults['helps'][$info->icon];
								if(!$isGroup) {
									$helpsList = (isset($info->type) && isset($helpsList[$info->type]))? $helpsList[$info->type] : false;
								}
								
								// Validate each Help
								if($helpsList) {
									foreach($helpsList as $helpName => $helpData) {
										if(isset($info->helps->{$helpName})) {
											$newHelp = $xml->createElement($helpName);
											
											if($this->validateBoolean($info->helps->{$helpName}->use)) {
												$helpUse = $info->helps->{$helpName}->use? 1 : 0;
												$newHelp->appendChild($xml->createElement('use', $helpUse));
												
												if(!$isGroup && $helpUse && isset($info->helps->{$helpName}->selected) && $info->helps->{$helpName}->selected) {
													$isResourceHelp = $helpName == 'resource';
													
													foreach($info->helps->{$helpName}->selected as $selectedKey => $selectedValue) {
														$newSelected = $xml->createElement('selected');
														$newSelected->setAttribute('source_id', $selectedKey);
														$newHelp->appendChild($newSelected);
														
														if($isResourceHelp) {
															$used[$collections[3]] = true;
														}
													}
												}
												
												$newHelps->appendChild($newHelp);
											}
										}
									}
								}
							}

							if($old) {
								$new->replaceChild($newHelps, $new->getElementsByTagName('helps')->item(0));
							}
							else $new->appendChild($newHelps);
						}
						else if(!$old) $valid = false;

					}

					// Validate Styles
					$valid = $this->validateStyles($valid, $info, $new, $xml, $old);

					// If all fields are valid add the new data to the XML
					if($valid) {
						if($old)  $currentXML->replaceChild($new, $old);
						else $currentXML->appendChild($new);
					}
					else array_push($errors, __('error-corrupted-element', $name));
				}

				// Remove
				else {
					if($list->length > 0) $currentXML->removeChild($list->item(0));
					if($hasSource) $used[$collection] = true;
				}
			}
			else array_push($errors, __('error-corrupted-identifier', $id));
		}
	}
	
	
	/**
	 * Validates Question Score
	 * 
	 * @param string $elementName - the element name.
	 * @param string $name - the score type name.
	 * @param int $max - the maximum value allowed.
	 * @param object $data - the score data.
	 * @param array $errors - the errors list.
	 * @param DOMElement $item - the question scores XML element.
	 * @param DOMDocument $xml - the XML object with document data.
	 * @return bool
	 */
	private function validateQuestionScore($elementName, $name, $max, &$data, &$errors, &$item, &$xml) {
		$min = 1;
		if(isset($data->{$name}) && 
			isset($data->{$name}->value) && $this->validateRange($data->{$name}->value, $min, $max) &&
			isset($data->{$name}->log) && $this->validateBoolean($data->{$name}->log)) {

			$newScore = $xml->createElement($name);
			$this->setTerminalNodes($data->{$name}, $newScore, $xml);
			$item->appendChild($newScore);
			
			return true;
		}
		else {
			array_push($errors, __('error-incorrect-element-range', __($name), $elementName, $min, $max));
			return false;
		}
	}
	
	
	/**
	 * Get Project/Game Files
	 *
	 * @param string $urlBase - the base url for the files.
	 * @param bool $isGame - determines if is loading files for a project or game.
	 * @return array - images lists for the client and server.
	 */
	public function getFiles($urlBase, $isGame=true) {
		$list = parent::getFiles($urlBase, $isGame);
		$client =& $list['client'];
		$server =& $list['server'];
		
		// Get general images list
		$folder = IMAGES.Configure::read('Default.game.img');
		$url = $urlBase.'/'.IMAGES_URL.Configure::read('Default.game.img').'/';
		
		// Get helps images list
		$this->Files->getFolderFiles($folder.DS.'helps', $url.'helps/', 'image', $client, $this->defaults['helps']['all']);
		
		if(!$isGame) {
			
			// Set Players Avatars
			$used = array();
			$avatars = array_keys($server['avatars']);
			$len = count($avatars)-1;

			if($len > 0) {
				for($i = 0; $i < 4; $i++) {
					do {
						$r = mt_rand(1, $len);
					} while(isset($used[$r]));
					
					$used[$r] = true;
					$client["avatar$i"] = $server['avatars'][$avatars[$r]];
				}
			}
		}
		
		return $list;
	}
	
}