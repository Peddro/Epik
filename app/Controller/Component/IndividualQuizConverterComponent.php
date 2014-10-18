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
class IndividualQuizConverterComponent extends QuizConverterComponent {
	
	/**
	 * Constructor
	 * @param ComponentCollection $collection
	 * @param array $settings
	 */
	public function __construct(ComponentCollection $collection, $settings = array()) {
		parent::__construct($collection, $settings);
		
		$properties =& $this->defaults['properties'];
		
		$specific = array(
			'maximum' => array(
				$properties[3] => 1,
			),
			'minimum' => array(
				$properties[3] => 1
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