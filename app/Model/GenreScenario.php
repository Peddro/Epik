<?php
/**
 * Imports
 */
App::uses('AppModel', 'Model');

/**
 * GenreScenario Model
 *
 * @package app.Model
 * @property Scenario $Scenario
 * @property Genre $Genre
 * @author Bruno Sampaio
 */
class GenreScenario extends AppModel {
	
	/**
	 * @var string Model name
	 */
	public $name = 'GenreScenario';
	
	
	/**
	 * @var string Table name
	 */
	public $useTable = 'genres_scenarios';
	
	
	/**
	 * @var string Display field
	 */
	public $displayField = 'scenario_id';
	

	/**
	 * @var array Validation rules
	 */
	public $validate = array();


	/**
	 * @var array belongsTo associations
	 */
	public $belongsTo = array(
		'ScenarioTemplate' => array(
			'className' => 'ScenarioTemplate',
			'foreignKey' => 'scenario_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'Genre' => array(
			'className' => 'GameGenre',
			'foreignKey' => 'genre_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);
	
}
