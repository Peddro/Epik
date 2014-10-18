<?php
/**
 * Imports
 */
App::uses('AppModel', 'Model');

/**
 * GameQuestion Model
 *
 * @package app.Model
 * @property GameActivity $Activity
 * @property GameQuestionType $Type
 * @property GameQuestionAnswer $Answer
 * @author Bruno Sampaio
 */
class GameQuestion extends AppModel {
	
	/**
	 * @var string Database configuration name
	 */
	public $useDbConfig = 'games';
	
	
	/**
	 * @var string Model name
	 */
	public $name = 'GameQuestion';
	
	
	/**
	 * @var string Table name
	 */
	public $useTable = 'questions';
	
	
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
			'minlength' => array(
				'rule' => array('minlength', 5),
				'message' => 'Minimum 5 characters long.'
			),
			'maxlength' => array(
				'rule' => array('maxlength', 200),
				'message' => 'Maximum 200 characters long.'
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
		)
	);
	

	/**
	 * @var array hasMany associations
	 */
	public $hasMany = array(
		'Answer' => array(
			'className' => 'GameQuestionAnswer',
			'foreignKey' => 'question_id',
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
