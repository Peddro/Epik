<?php
/**
 * Imports
 */
App::uses('AppModel', 'Model');

/**
 * PlayerBonus Model
 *
 * @package app.Model
 * @property BonusType $Type
 * @property Player $Player
 * @author Bruno Sampaio
 */
class PlayerBonus extends AppModel {
	
	/**
	 * @var string Database configuration name
	 */
	public $useDbConfig = 'games';
	
	
	/**
	 * @var string Model name
	 */
	public $name = 'PlayerBonus';
	
	
	/**
	 * @var string Table name
	 */
	public $useTable = 'players_bonus';
	
	
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
			'className' => 'BonusType',
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
