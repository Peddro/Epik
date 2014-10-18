<?php
/**
 * Imports
 */
App::uses('AppModel', 'Model');

/**
 * GameGenre Model
 *
 * @package app.Model
 * @author Bruno Sampaio
 */
class GameGenre extends AppModel {
	
	/**
	 * @var string Model Name
	 */
	public $name = 'GameGenre';
	
	
	/**
	 * @var string Table name
	 */
	public $useTable = 'games_genres';
	
	
	/**
	 * @var string Display field
	 */
	public $displayField = 'name';

	
	/**
	 * @var array belongsTo associations
	 */
	public $belongsTo = array(
		'Mode' => array(
			'className' => 'GameMode',
			'foreignKey' => 'mode_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);
	
	/**
	 * @var array hasAndBelongsToMany associations
	 */
	public $hasAndBelongsToMany = array(
		'ActivityType' => array(
			'className' => 'ActivityType',
			'joinTable' => 'genres_activities',
			'foreignKey' => 'genre_id',
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
		),
		'ResourceType' => array(
			'className' => 'ResourceType',
			'joinTable' => 'genres_resources',
			'foreignKey' => 'genre_id',
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
