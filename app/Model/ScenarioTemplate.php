<?php
/**
 * Imports
 */
App::uses('AppModel', 'Model');

/**
 * Scenario Template Model
 *
 * @package app.Model
 * @author Bruno Sampaio
 */
class ScenarioTemplate extends AppModel {
	
	/**
	 * @var string Model name
	 */
	public $name = 'ScenarioTemplate';
	
	
	/**
	 * @var string Table name
	 */
	public $useTable = 'scenarios_templates';
	
	
	/**
	 * @var string Display field
	 */
	public $displayField = 'name';
	
	
	/**
	 * @var array hasAndBelongsToMany associations
	 */
	public $hasAndBelongsToMany = array(
		'Genre' => array(
			'className' => 'GameGenre',
			'joinTable' => 'genres_scenarios',
			'foreignKey' => 'scenario_id',
			'associationForeignKey' => 'genre_id',
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
	
	
	/**
	 * Set the 'image' attribute for each scenario template.
	 * 
	 * @param array $results
	 * @param bool $primary
	 * @return array
	 */
	public function afterFind($results, $primary = false) {
		foreach($results as $key => $val) {
			$this->data = $val;
			$results[$key][$this->name]['image'] = $this->getImage();
		}
		return $results;
	}
	
	
	/**
	 * Get Scenario Template Image Path
	 *
	 * @return string
	 */
	public function getImage() {	
		$data = $this->data[$this->name];
		return $this->getImagePath($data, $this->name, IMAGES, Configure::read('Folders.img.scenarios').DS.$data['id'], 'id');
	}

}
