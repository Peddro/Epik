<?php
/**
 * Imports
 */
App::uses('AppModel', 'Model');

/**
 * ScoreType Model
 *
 * @package app.Model
 * @author Bruno Sampaio
 */
class ScoreType extends AppModel {
	
	/**
	 * @var string Database configuration name
	 */
	public $useDbConfig = 'games';
	
	
	/**
	 * @var string Model name
	 */
	public $name = 'ScoreType';
	
	
	/**
	 * @var string Table name
	 */
	public $useTable = 'scores_types';
	
	
	/**
	 * @var string Display field
	 */
	public $displayField = 'name';

}
