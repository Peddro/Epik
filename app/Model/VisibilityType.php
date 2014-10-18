<?php
/**
 * Imports
 */
App::uses('AppModel', 'Model');

/**
 * VisibilityType Model
 *
 * @package app.Model
 * @author Bruno Sampaio
 */
class VisibilityType extends AppModel {

	/**
	 * @var string Display field
	 */
	public $displayField = 'name';
	
	
	/**
	 * Get list of visibility types with ID greater then 1.
	 *
	 * @return array
	 */
	public function getTypesList() {
		return $this->find('list', array('conditions' => array('id > ' => 1)));
	}

}
