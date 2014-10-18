<?php
/**
 * Imports
 */
App::uses('AppModel', 'Model');

/**
 * PlayerScore Model
 *
 * @package app.Model
 * @property ScoreType $Type
 * @property Player $Player
 * @author Bruno Sampaio
 */
class PlayerScore extends AppModel {
	
	/**
	 * @var string Database configuration name
	 */
	public $useDbConfig = 'games';
	
	
	/**
	 * @var string Model name
	 */
	public $name = 'PlayerScore';
	
	
	/**
	 * @var string Table name
	 */
	public $useTable = 'players_scores';
	
	
	/**
	 * @var string Display field
	 */
	public $displayField = 'value';


	/**
	 * @var array Validation rules
	 */
	public $validate = array(
		'name' => array(
			'notempty' => array(
				'rule' => array('notempty')
			),
		),
		'value' => array(
			'numeric' => array(
				'rule' => array('numeric')
			),
		),
		'type_id' => array(
			'numeric' => array(
				'rule' => array('numeric')
			),
		),
		'player_id' => array(
			'numeric' => array(
				'rule' => array('numeric')
			),
		)
	);
	

	/**
	 * @var array belongsTo associations
	 */
	public $belongsTo = array(
		'Type' => array(
			'className' => 'ScoreType',
			'foreignKey' => 'type_id',
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
