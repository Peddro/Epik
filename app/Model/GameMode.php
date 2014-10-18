<?php
/**
 * Imports
 */
App::uses('AppModel', 'Model');

/**
 * GameMode Model
 *
 * @package app.Model
 * @author Bruno Sampaio
 */
class GameMode extends AppModel {
	
	/**
	 * @var string Model name
	 */
	public $name = 'GameMode';
	
	
	/**
	 * @var string Table name
	 */
	public $useTable = 'games_modes';
	
	
	/**
	 * @var string Display field
	 */
	public $displayField = 'name';
	
	
	/**
	 * @var array hasMany associations
	 */
	public $hasMany = array(
		'Genre' => array(
			'className' => 'GameGenre',
			'foreignKey' => 'mode_id',
			'dependent' => false,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		)
	);

}
