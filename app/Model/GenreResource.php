<?php
/**
 * Imports
 */
App::uses('AppModel', 'Model');

/**
 * GenreResource Model
 *
 * @package app.Model
 * @property GameGenre $Genre
 * @property ResourceType $Type
 * @author Bruno Sampaio
 */
class GenreResource extends AppModel {
	
	/**
	 * @var string Model name
	 */
	public $name = 'GenreResource';
	
	
	/**
	 * @var string Table name
	 */
	public $useTable = 'genres_resources';
	
	
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
			'className' => 'ResourceType',
			'foreignKey' => 'type_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);
	
	
	/**
	 * Get the type of resources permitted by a specific game genre.
	 *
	 * @param int $genreId - the genre id.
	 * @param array $icons - the types of resources permitted.
	 * @param array $tools - the types of resources sorted by id.
	 */
	public function getGenreResources($genreId, &$icons=array(), &$tools=array()) {
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
	 * Check if a game genre permits the specified resource type.
	 *
	 * @param int $genreId - the genre id.
	 * @param int $typeId - the resource type id.
	 */
	public function hasType($genreId, $typeId) {
		return count($this->find('first', array('fields' => array('id'), 'conditions' => array($this->name.'.genre_id' => $genreId, $this->name.'.type_id' => $typeId))));
	}
}
