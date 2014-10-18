<?php
/**
 * Imports
 */
App::uses('AppModel', 'Model');

/**
 * Game Model
 * 
 * @package app.Model
 * @property GameActivity $Activity
 * @property Session $Session
 * @author Bruno Sampaio
 */
class GameReference extends AppModel {
	
	/**
	 * @var string Database configuration name
	 */
	public $useDbConfig = 'games';
	
	
	/**
	 * @var string Model name
	 */
	public $name = 'GameReference';
	
	
	/**
	 * @var string Table name
	 */
	public $useTable = 'games';
	
	
	/**
	 * @var string Display field
	 */
	public $displayField = 'name';
	
	
	/**
	 * @var array belongsTo associations
	 */
	public $belongsTo = array(
		'Game' => array(
			'className' => 'Game',
			'foreignKey' => 'id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);


	/**
	 * @var array hasMany associations
	 */
	public $hasMany = array(
		'Activity' => array(
			'className' => 'GameActivity',
			'foreignKey' => 'game_id',
			'dependent' => false,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		),
		'Resource' => array(
			'className' => 'GameResource',
			'foreignKey' => 'game_id',
			'dependent' => false,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		),
		'Session' => array(
			'className' => 'GameSession',
			'foreignKey' => 'game_id',
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
