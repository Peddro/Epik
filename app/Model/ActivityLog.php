<?php
/**
 * Imports
 */
App::uses('AppModel', 'Model');

/**
 * ActivityLog Model
 *
 * @package app.Model
 * @property GameActivity $Activity
 * @property Player $Player
 * @author Bruno Sampaio
 */
class ActivityLog extends AppModel {
	
	/**
	 * @var string Database configuration name
	 */
	public $useDbConfig = 'games';
	
	
	/**
	 * @var string Model name
	 */
	public $name = 'ActivityLog';
	
	
	/**
	 * @var string Table name
	 */
	public $useTable = 'activities_logs';


	/**
	 * @var string Display field
	 */
	public $displayField = 'activity_id';
	
	
	/**
	 * @var array Validation rules
	 */
	public $validate = array(
		'reward' => array(
			'decimal' => array(
				'rule' => array('decimal')
			),
		),
		'penalty' => array(
			'decimal' => array(
				'rule' => array('decimal')
			),
		),
		'attempts' => array(
			'decimal' => array(
				'rule' => array('decimal')
			),
		),
		'player_id' => array(
			'numeric' => array(
				'rule' => array('numeric')
			),
		),
		'activity_id' => array(
			'numeric' => array(
				'rule' => array('numeric')
			),
		)
	);
	

	/**
	 * @var array belongsTo associations
	 */
	public $belongsTo = array(
		'Activity' => array(
			'className' => 'GameActivity',
			'foreignKey' => 'activity_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'Player' => array(
			'className' => 'Player',
			'foreignKey' => 'player_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);
}
