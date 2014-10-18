<?php
/**
 * Imports
 */
App::uses('AppModel', 'Model');

/**
 * GameResource Model
 *
 * @package app.Model
 * @author Bruno Sampaio
 */
class GameResource extends AppModel {
	
	/**
	 * @var string Database configuration name
	 */
	public $useDbConfig = 'games';
	
	
	/**
	 * @var string Model name
	 */
	public $name = 'GameResource';
	
	
	/**
	 * @var string Table name
	 */
	public $useTable = 'resources';
	
	
	/**
	 * @var string Display field
	 */
	public $displayField = 'id';
	
	
	/**
	 * @var array hasAndBelongsToMany associations
	 */
	public $hasAndBelongsToMany = array(
		'Activity' => array(
			'className' => 'GameActivity',
			'joinTable' => 'activities_resources',
			'foreignKey' => 'resource_id',
			'associationForeignKey' => 'activity_id',
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
