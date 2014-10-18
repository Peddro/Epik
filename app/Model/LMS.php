<?php
/**
 * Imports
 */
App::uses('AppModel', 'Model');

/**
 * LMS Model
 *
 * @package app.Model
 * @author Bruno Sampaio
 */
class LMS extends AppModel {
	
	/**
	 * @var string Model name
	 */
	public $name = 'LMS';
	
	
	/**
	 * @var string Table name
	 */
	public $useTable = 'lms';
	
	
	/**
	 * @var string Display field
	 */
	public $displayField = 'name';

}
