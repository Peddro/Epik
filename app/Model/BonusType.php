<?php
/**
 * Imports
 */
App::uses('AppModel', 'Model');

/**
 * BonusType Model
 *
 * @package app.Model
 * @author Bruno Sampaio
 */
class BonusType extends AppModel {
	
	/**
	 * @var string Database configuration name
	 */
	public $useDbConfig = 'games';
	
	
	/**
	 * @var string Model name
	 */
	public $name = 'BonusType';
	
	
	/**
	 * @var string Table name
	 */
	public $useTable = 'bonus_types';
	
	
	/**
	 * @var string Display field
	 */
	public $displayField = 'name';

}
