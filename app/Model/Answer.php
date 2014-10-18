<?php
/**
 * Imports
 */
App::uses('AppModel', 'Model');

/**
 * Answer Model
 *
 * @package app.Model
 * @property Question $Question
 * @author Bruno Sampaio
 */
class Answer extends AppModel {

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
			'className' => 'Question',
			'foreignKey' => 'question_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);
}
