<?php
/**
 * Imports
 */
App::uses('AppModel', 'Model');

/**
 * Login Model
 *
 * @package app.Model
 * @property User $User
 * @author Bruno Sampaio
 */
class Login extends AppModel {

	/**
	 * @var string Display field
	 */
	public $displayField = 'user_id';


	/**
	 * @var array Validation rules
	 */
	public $validate = array(
		'user_id' => array(
			'numeric' => array(
				'rule' => array('numeric')
			),
		),
		'location' => array(
			'notempty' => array(
				'rule' => array('notempty')
			),
		),
		'user_status' => array(
			'notempty' => array(
				'rule' => array('notempty')
			),
		),
	);


	/**
	 * @var array belongsTo associations
	 */
	public $belongsTo = array(
		'User' => array(
			'className' => 'User',
			'foreignKey' => 'user_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);
}
