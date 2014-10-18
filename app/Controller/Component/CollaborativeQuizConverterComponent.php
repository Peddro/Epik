<?php
/**
 * Imports
 */
App::uses('QuizConverterComponent', 'Controller/Component');

/**
 * Projects Component
 *
 * @package app.Controller.Component
 * @author Bruno Sampaio
 */
class CollaborativeQuizConverterComponent extends QuizConverterComponent {
	
	/**
	 * Constructor
	 * @param ComponentCollection $collection
	 * @param array $settings
	 */
	public function __construct(ComponentCollection $collection, $settings = array()) {
		parent::__construct($collection, $settings);
		
		$properties =& $this->defaults['properties'];
		$scenarios =& $this->defaults['types'][$this->defaults['collections'][0]];
		
		$specific = array(
			'maximum' => array(
				$properties[3] => 4,
			),
			'minimum' => array(
				$properties[3] => 2
			),
			'rules' => array(
				$scenarios[1] => array('bonus' => new stdClass())
			),
			'scores' => array(
				'timeout' => array('value' => 15)
			),
			'types' => array(
				$properties[1] => array('team', 'collaboration'),
				$properties[3] => array('normal', 'collaborating'),
				'bonus' => array(
					$scenarios[1] => array('collaboration', 'firstToFinish')
				)
			)
		);
		
		$this->defaults = array_merge_recursive($this->defaults, $specific);
	}
	
	
	/**
	 * Extends the parent method.
	 */
	public function getProjectDefaults() {
		return parent::getProjectDefaults();
	}
	
}