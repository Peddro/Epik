<?php
/**
 * Imports
 */
App::uses('AppModel', 'Model');

/**
 * GameActivity Model
 *
 * @package app.Model
 * @property GameQuestion $Question
 * @author Bruno Sampaio
 */
class GameActivity extends AppModel {
	
	/**
	 * @var string Database configuration name
	 */
	public $useDbConfig = 'games';
	
	
	/**
	 * @var string Model name
	 */
	public $name = 'GameActivity';
	
	
	/**
	 * @var string Table name
	 */
	public $useTable = 'activities';
	
	
	/**
	 * @var string Display field
	 */
	public $displayField = 'id';
	
	
	/**
	 * @var array hasOne associations
	 */
	public $hasOne = array(
		'Question' => array(
			'className' => 'GameQuestion',
			'foreignKey' => 'activity_id',
			'dependent' => true,
			'conditions' => '',
			'fields' => '',
			'order' => '',
		)
	);


	/**
	 * @var array hasMany associations
	 */
	public $hasMany = array(
		'Hint' => array(
			'className' => 'GameActivityHint',
			'foreignKey' => 'activity_id',
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
	
	
	/**
	 * @var array hasAndBelongsToMany associations
	 */
	public $hasAndBelongsToMany = array(
		'Resource' => array(
			'className' => 'GameResource',
			'joinTable' => 'activities_resources',
			'foreignKey' => 'activity_id',
			'associationForeignKey' => 'resource_id',
			'unique' => 'keepExisting',
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'finderQuery' => '',
			'deleteQuery' => '',
			'insertQuery' => ''
		)
	);
	
}
