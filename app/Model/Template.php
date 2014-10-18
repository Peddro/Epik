<?php
/**
 * Imports
 */
App::uses('AppModel', 'Model');

/**
 * Project Template Model
 *
 * @package app.Model
 * @property VisibilityType $Visibility
 * @property GameMode $Genre
 * @property User $User
 * @author Bruno Sampaio
 */
class Template extends AppModel {

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
			),
		),
		'image' => array(
			'boolean' => array(
				'rule' => array('boolean')
			),
		),
		'genre_id' => array(
			'numeric' => array(
				'rule' => array('numeric')
			),
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
	 * Set the 'image' attribute for each project template.
	 * 
	 * @param array $results
	 * @param bool $primary
	 * @return array
	 */
	public function afterFind($results, $primary = false) {
		foreach($results as $key => $val) {
			if(isset($val[$this->name]['image'])) {
				$this->data = $val;
				$results[$key][$this->name]['image'] = $this->getImage();
			}
		}
		return $results;
	}
	
	
	/**
	 * Get Project Template Image Path
	 *
	 * @return string
	 */
	public function getImage() {
		$data = $this->data[$this->name];
		return $this->getImagePath($data, $this->name, IMAGES, Configure::read('Folders.img.templates').DS.$data['id'], 'image');
	}
}
