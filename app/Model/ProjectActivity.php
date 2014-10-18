<?php
/**
 * Imports
 */
App::uses('AppModel', 'Model');

/**
 * ProjectActivity Model
 *
 * @package app.Model
 * @property Activity $Activity
 * @property Project $Project
 * @author Bruno Sampaio
 */
class ProjectActivity extends AppModel {
	
	/**
	 * @var string Model name
	 */
	public $name = 'ProjectActivity';
	
	
	/**
	 * @var string Table name
	 */
	public $useTable = 'projects_activities';
	
	
	/**
	 * @var string Display field
	 */
	public $displayField = 'project_id';
	

	/**
	 * @var array Validation rules
	 */
	public $validate = array();


	/**
	 * @var array belongsTo associations
	 */
	public $belongsTo = array(
		'Activity' => array(
			'className' => 'Activity',
			'foreignKey' => 'activity_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'Project' => array(
			'className' => 'Project',
			'foreignKey' => 'project_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);
	
}
