<?php
/**
 * Imports
 */
App::uses('AppModel', 'Model');

/**
 * GenreActivity Model
 *
 * @package app.Model
 * @property GameGenre $Genre
 * @property ActivityType $Type
 * @author Bruno Sampaio
 */
class GenreActivity extends AppModel {
	
	/**
	 * @var string Model name
	 */
	public $name = 'GenreActivity';
	
	
	/**
	 * @var string Table name
	 */
	public $useTable = 'genres_activities';
	
	
	/**
	 * @var string Display field
	 */
	public $displayField = 'genre_id';
	

	/**
	 * @var array Validation rules
	 */
	public $validate = array();


	/**
	 * @var array belongsTo associations
	 */
	public $belongsTo = array(
		'Genre' => array(
			'className' => 'GameGenre',
			'foreignKey' => 'genre_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'Type' => array(
			'className' => 'ActivityType',
			'foreignKey' => 'type_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);
	
	
	/**
	 * Get the type of activities permitted by a specific game genre.
	 *
	 * @param int $genreId - the genre id.
	 * @param array $icons - the types of activities permitted.
	 * @param array $tools - the types of activities sorted by id.
	 */
	public function getGenreActivities($genreId, &$icons=array(), &$tools=array()) {
		$data = $this->find('all', array(
			'fields' => array('Type.id', 'Type.icon'), 
			'conditions' => array($this->name.'.genre_id' => $genreId), 
			'order' => array('Type.id')
		));
		
		foreach($data as $item) {
			$name = $item['Type']['icon'];
			$icons[$name] = $name;
			$tools[$item['Type']['id']] = $name;
		}
	}
	
	
	/**
	 * Check if a game genre permits the specified activity type.
	 *
	 * @param int $genreId - the genre id.
	 * @param int $typeId - the activity type id.
	 */
	public function hasType($genreId, $typeId) {
		return count($this->find('first', array('fields' => array('id'), 'conditions' => array($this->name.'.genre_id' => $genreId, $this->name.'.type_id' => $typeId))));
	}
}
