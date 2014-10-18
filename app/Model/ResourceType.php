<?php
/**
 * Imports
 */
App::uses('AppModel', 'Model');

/**
 * ResourceType Model
 *
 * @package app.Model
 * @author Bruno Sampaio
 */
class ResourceType extends AppModel {
	
	/**
	 * @var string Model name
	 */
	public $name = 'ResourceType';
	
	
	/**
	 * @var string Table name
	 */
	public $useTable = 'resources_types';
	
	
	/**
	 * @var string Display field
	 */
	public $displayField = 'name';
	
	
	/**
	 * Get list of resources types.
	 *
	 * @return array
	 */
	public function getIcons() {
		return $this->find('list', array('fields' => array('icon'), 'order' => array('id')));
	}
	
}
