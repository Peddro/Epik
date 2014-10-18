<?php
/**
 * Imports
 */
App::uses('AppModel', 'Model');

/**
 * ActivityType Model
 *
 * @package app.Model
 * @author Bruno Sampaio
 */
class ActivityType extends AppModel {
	
	/**
	 * @var string Model name
	 */
	public $name = 'ActivityType';
	
	
	/**
	 * @var string Table name
	 */
	public $useTable = 'activities_types';
	
	
	/**
	 * @var string Display field
	 */
	public $displayField = 'name';

	
	/**
	 * Get list of activities types.
	 *
	 * @return array
	 */
	public function getIcons() {
		return $this->find('list', array('fields' => array('icon'), 'order' => array('id')));
	}
}
