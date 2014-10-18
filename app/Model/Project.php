<?php
/**
 * Imports
 */
App::uses('AppModel', 'Model');

/**
 * Project Model
 *
 * @package app.Model
 * @property User $User
 * @property Activity $Activity
 * @property Resource $Resource
 * @author Bruno Sampaio
 */
class Project extends AppModel {

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
			),
		),
		'genre_id' => array(
			'numeric' => array(
				'rule' => array('numeric')
			)
		),
		'user_id' => array(
			'numeric' => array(
				'rule' => array('numeric')
			),
		),
	);
	

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
		'User' => array(
			'className' => 'User',
			'foreignKey' => 'user_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);
	

	/**
	 * @var array hasAndBelongsToMany associations
	 */
	public $hasAndBelongsToMany = array(
		'Activity' => array(
			'className' => 'Activity',
			'joinTable' => 'projects_activities',
			'foreignKey' => 'project_id',
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
		'Resource' => array(
			'className' => 'Resource',
			'joinTable' => 'projects_resources',
			'foreignKey' => 'project_id',
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
	
	
	/**
	 * Get project data
	 *
	 * @param int $id - the project id.
	 * @param array $fields - list of additional fields to request.
	 * @return array
	 */
	public function get($id, $fields=array()) {
		$this->unbindModel(array('belongsTo' => array('User'), 'hasAndBelongsToMany' => array('Activity', 'Resource')));
		
		$data = $this->find('first', array('fields' => array_merge(array('Project.id', 'Project.genre_id', 'Project.user_id', 'Genre.code', 'Genre.mode_id'), $fields), 'conditions' => array('Project.id' => $id)));
		$data['Project']['file'] = FILES.Configure::read('Folders.files.projects').DS.$data['Project']['id'].'.xml';
		
		return $data;
	}

}
