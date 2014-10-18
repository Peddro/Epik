<?php
/**
 * Imports
 */
App::uses('AppModel', 'Model');

/**
 * SessionLMS Model
 *
 * @package app.Model
 * @author Bruno Sampaio
 */
class SessionLMS extends AppModel {
	
	/**
	 * @var string Database configuration name
	 */
	public $useDbConfig = 'games';
	
	
	/**
	 * @var string Model name
	 */
	public $name = 'SessionLMS';
	
	
	/**
	 * @var string Table name
	 */
	public $useTable = 'lms';
	
	
	/**
	 * @var string Display field
	 */
	public $displayField = 'url';

}
