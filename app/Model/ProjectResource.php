<?php
/**
 * Imports
 */
App::uses('AppModel', 'Model');

/**
 * ProjectResource Model
 *
 * @package app.Model
 * @property Resource $Resource
 * @property Project $Project
 * @author Bruno Sampaio
 */
class ProjectResource extends AppModel {
	
	/**
	 * @var string Model name
	 */
	public $name = 'ProjectResource';
	
	
	/**
	 * @var string Table name
	 */
	public $useTable = 'projects_resources';

	
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
		'Resource' => array(
			'className' => 'Resource',
			'foreignKey' => 'resource_id',
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
