<?php
/**
 * Imports
 */
App::uses('AppModel', 'Model');

/**
 * GameActivityHint Model
 *
 * @package app.Model
 * @property GameActivity $Activity
 * @author Bruno Sampaio
 */
class GameActivityHint extends AppModel {
	
	/**
	 * @var string Database configuration name
	 */
	public $useDbConfig = 'games';
	
	
	/**
	 * @var string Model name
	 */
	public $name = 'GameActivityHint';
	
	
	/**
	 * @var string Table name
	 */
	public $useTable = 'activities_hints';
	
	
	/**
	 * @var string Display field
	 */
	public $displayField = 'content';


	/**
	 * @var array Validation rules
	 */
	public $validate = array(
		'content' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'Mandatory field.'
			),
			'maxlength' => array(
				'rule' => array('maxlength', 200),
				'message' => 'Maximum 200 characters long.'
			)
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
		)
	);
	
}
