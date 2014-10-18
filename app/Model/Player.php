<?php
/**
 * Imports
 */
App::uses('AppModel', 'Model');

/**
 * Player Model
 *
 * @package app.Model
 * @property Session $Session
 * @property ActivityLog $ActivityLog
 * @author Bruno Sampaio
 */
class Player extends AppModel {
	
	/**
	 * @var string Database configuration name
	 */
	public $useDbConfig = 'games';

	
	/**
	 * @var string Display field
	 */
	public $displayField = 'name';


	/**
	 * @var array Validation rules
	 */
	public $validate = array(
		'name' => array(
			'notempty' => array(
				'rule' => array('notempty')
			),
		),
		'session_id' => array(
			'numeric' => array(
				'rule' => array('numeric')
			),
		),
	);


	/**
	 * @var array belongsTo associations
	 */
	public $belongsTo = array(
		'Session' => array(
			'className' => 'GameSession',
			'foreignKey' => 'session_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);


	/**
	 * @var array hasMany associations
	 */
	public $hasMany = array(
		'ActivityLog' => array(
			'className' => 'ActivityLog',
			'foreignKey' => 'player_id',
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
		'Bonus' => array(
			'className' => 'PlayerBonus',
			'foreignKey' => 'player_id',
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
		'Score' => array(
			'className' => 'PlayerScore',
			'foreignKey' => 'player_id',
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
