<?php
/**
 * Imports
 */
App::uses('AppModel', 'Model');

/**
 * GameQuestionAnswer Model
 *
 * @package app.Model
 * @property GameQuestion $Question
 * @author Bruno Sampaio
 */
class GameQuestionAnswer extends AppModel {
	
	/**
	 * @var string Database configuration name
	 */
	public $useDbConfig = 'games';
	
	
	/**
	 * @var string Model name
	 */
	public $name = 'GameQuestionAnswer';
	
	
	/**
	 * @var string Table name
	 */
	public $useTable = 'answers';
	
	
	/**
	 * @var string Display field
	 */
	public $displayField = 'content';


	/**
	 * @var array Validation rules
	 */
	public $validate = array(
		'content' => array(
			'maxlength' => array(
				'rule' => array('maxlength', 200),
				'message' => 'Maximum 200 characters long.'
			)
		),
		'is_correct' => array(
			'boolean' => array(
				'rule' => array('boolean')
			),
		)
	);
	

	/**
	 * @var array belongsTo associations
	 */
	public $belongsTo = array(
		'Question' => array(
			'className' => 'GameQuestion',
			'foreignKey' => 'question_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);
}
