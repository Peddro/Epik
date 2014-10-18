<?php
/**
 * Imports
 */
App::uses('AppModel', 'Model');

/**
 * Session Model
 *
 * @package app.Model
 * @property Game $Game
 * @property Player $Player
 * @author Bruno Sampaio
 */
class GameSession extends AppModel {
	
	/**
	 * @var string Database configuration name
	 */
	public $useDbConfig = 'games';
	
	
	/**
	 * @var string Model name
	 */
	public $name = 'GameSession';
	
	
	/**
	 * @var string Table name
	 */
	public $useTable = 'sessions';
	
	
	/**
	 * @var string Display field
	 */
	public $displayField = 'team_score';
	

	/**
	 * @var array Validation rules
	 */
	public $validate = array(
		'lms_url' => array(
			'url' => array(
				'rule' => array('url'),
				'message' => 'Must be a URL.',
				'allowEmpty' => true
			)
		),
		'score' => array(
			'numeric' => array(
				'rule' => array('numeric')
			)
		),
		'game_id' => array(
			'numeric' => array(
				'rule' => array('numeric')
			)
		),
	);


	/**
	 * @var array belongsTo associations
	 */
	public $belongsTo = array(
		'Game' => array(
			'className' => 'Game',
			'foreignKey' => 'game_id',
			'conditions' => '',
			'fields' => 'visibility_id, user_id',
			'order' => ''
		),
		'LMS' => array(
			'className' => 'SessionLMS',
			'foreignKey' => 'lms_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);


	/**
	 * @var array hasMany associations
	 */
	public $hasMany = array(
		'Player' => array(
			'className' => 'Player',
			'foreignKey' => 'session_id',
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
