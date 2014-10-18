<?php
/**
 * Imports
 */
App::uses('AppModel', 'Model');

/**
 * QuestionType Model
 *
 * @package app.Model
 * @author Bruno Sampaio
 */
class QuestionType extends AppModel {
	
	/**
	 * @var string Model name
	 */
	public $name = 'QuestionType';
	
	
	/**
	 * @var string Table name
	 */
	public $useTable = 'questions_types';

	
	/**
	 * @var string Display field
	 */
	public $displayField = 'name';
	
	
	/**
	 * Get list of questions types.
	 *
	 * @return array
	 */
	public function getIcons() {
		return $this->find('list', array('fields' => array('icon'), 'order' => array('id')));
	}
}
