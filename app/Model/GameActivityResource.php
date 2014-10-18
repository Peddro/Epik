<?php
/**
 * Imports
 */
App::uses('AppModel', 'Model');

/**
 * GameActivityResource Model
 *
 * @package app.Model
 * @property GameActivity $Activity
 * @property GameResource $Resource
 * @author Bruno Sampaio
 */
class GameActivityResource extends AppModel {
	
	/**
	 * @var string Database configuration name
	 */
	public $useDbConfig = 'games';
	
	
	/**
	 * @var string Model name
	 */
	public $name = 'GameActivityResource';
	
	
	/**
	 * @var string Model name
	 */
	public $useTable = 'activities_resources';
	
	
	/**
	 * @var string Display field
	 */
	public $displayField = 'game_id';
	

	/**
	 * @var array Validation rules
	 */
	public $validate = array();


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
		'Resource' => array(
			'className' => 'GameResource',
			'foreignKey' => 'resource_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);
}
