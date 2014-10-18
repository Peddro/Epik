<?php
/**
 * Imports
 */
App::uses('AppModel', 'Model');

/**
 * Game Model
 *
 * @package app.Model
 * @property VisibilityType $Visibility
 * @property GameGenre $Genre
 * @property User $User
 * @property LearningSubject $Subject
 * @author Bruno Sampaio
 */
class Game extends AppModel {

	/**
	 * @var string Display field
	 */
	public $displayField = 'name';


	/**
	 * @var array Validation rules
	 */
	public $validate = array(
		'name' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'Mandatory field.'
			),
			'minlength' => array(
				'rule' => array('minlength', 2),
				'message' => 'Minimum 2 characters long.'
			),
			'maxlength' => array(
				'rule' => array('maxlength', 50),
				'message' => 'Maximum 50 characters long.'
			)
		),
		'visibility_id' => array(
			'numeric' => array(
				'rule' => array('numeric')
			)
		),
		'genre_id' => array(
			'numeric' => array(
				'rule' => array('numeric')
			)
		),
		'user_id' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				'allowEmpty' => true
			)
		)
	);

	
	/**
	 * @var array belongsTo associations
	 */
	public $belongsTo = array(
		'Visibility' => array(
			'className' => 'VisibilityType',
			'foreignKey' => 'visibility_id',
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
		),
		'User' => array(
			'className' => 'User',
			'foreignKey' => 'user_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);
	
	
	/**
	 * @var array hasOne associations
	 */
	public $hasOne = array(
		'Reference' => array(
			'className' => 'GameReference',
			'foreignKey' => 'id',
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'dependent' => true
		)
	);


	/**
	 * @var array hasAndBelongsToMany associations
	 */
	public $hasAndBelongsToMany = array(
		'Subject' => array(
			'className' => 'LearningSubject',
			'joinTable' => 'games_subjects',
			'foreignKey' => 'game_id',
			'associationForeignKey' => 'subject_id',
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
	 * Set the 'icon_url' attribute for each game.
	 * 
	 * @param array $results
	 * @param bool $primary
	 * @return array
	 */
	public function afterFind($results, $primary = false) {
		foreach($results as $key => $val) {
			if(isset($val[$this->name]['icon'])) {
				$this->data = $val;
				$results[$key][$this->name]['icon_url'] = $this->getIcon();
			}
		}
		return $results;
	}
	
	
	/**
	 * Get path to the game icon.
	 *
	 * @return string
	 */
	public function getIcon() {	
		$data = $this->data[$this->name];
		$field = 'icon';
		$path = $this->getImagePath($data, $this->name, FILES, Configure::read('Folders.files.games').DS.$data['id'].DS.$field, $field);
		if(!$data[$field]) $path.= DS.'general'.DS.$field.'.png';
		
		return $path;		
	}

}
