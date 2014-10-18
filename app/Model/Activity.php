<?php
/**
 * Imports
 */
App::uses('AppModel', 'Model');

/**
 * Activity Model
 *
 * @package app.Model
 * @property ActivityType $Type
 * @property User $User
 * @property Question $Question
 * @property QuestionsGroup $QuestionsGroup
 * @property LearningSubject $Subject
 * @property Project $Project
 * @author Bruno Sampaio
 */
class Activity extends AppModel {
	
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
		'type_id' => array(
			'numeric' => array(
				'rule' => array('numeric')
			),
		),
		'user_id' => array(
			'numeric' => array(
				'rule' => array('numeric')
			),
		)
	);
	
	
	/**
	 * @var array belongsTo associations
	 */
	public $belongsTo = array(
		'LMS' => array(
			'className' => 'LMS',
			'foreignKey' => 'lms_id',
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
		'Question' => array(
			'className' => 'Question',
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
		'QuestionsGroup' => array(
			'className' => 'QuestionsGroup',
			'foreignKey' => 'activity_id',
			'dependent' => true,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		),
		'Hint' => array(
			'className' => 'ActivityHint',
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
		'Subject' => array(
			'className' => 'LearningSubject',
			'joinTable' => 'activities_subjects',
			'foreignKey' => 'activity_id',
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
		),
		'Project' => array(
			'className' => 'Project',
			'joinTable' => 'projects_activities',
			'foreignKey' => 'activity_id',
			'associationForeignKey' => 'project_id',
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
	
	
	/**
	 * Set the 'imported' attributed for each activity.
	 *
	 * @param array $results
	 * @param bool $primary
	 * @return array
	 */
	public function afterFind($results, $primary = false) {
		foreach($results as $key => $val) {
			if(isset($val['Activity']['id'])) {
				$results[$key]['Activity']['imported'] = $this->wasImported($val['Activity']);
			}
		}
		return $results;
	}
}
