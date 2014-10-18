<?php
/**
 * Imports
 */
App::uses('AppController', 'Controller');

/**
 * Templates Controller
 *
 * @package app.Controller
 * @property Template $Template
 * @author Bruno Sampaio
 */
class TemplatesController extends AppController {
	
	/**
	 * @var array Pagination Component Properties
	 */
	public $paginate = array(
			'order' => array('Template.modified' => 'desc', 'Template.name' => 'asc')
	);
	
	
	/**
	 * Extends the parent method.
	 */
	public function beforeFilter() {
		parent::beforeFilter();
	}
	
	
	/**
	 * Extends the parent method.
	 */
	public function beforeRender() {
		parent::beforeRender();
	}
	

	/**
	 * Choose Section
	 *
	 * This action prepares the options to be sent to the chooser method.
	 * It is invoked on a situation where the user must select one or more templates to continue.
	 * @return array - list of values to be sent to an element view.
	 */
	public function choose() {
		$options = array();
		
		// Filters Default Values
		$options['filters'] = array('genre_id' => 1);
		
		// Unbind Options
		$options['unbind'] = array('belongsTo' => array('Mode', 'User'));
		
		// List Options
		$fields = array('Template.id', 'Template.name', 'Template.description', 'Template.image');
		$options['list'] = array(
			'fields' => $fields, 
			'conditions' => array(
				'OR' => array(
					'Template.visibility_id <>' => 2, 
					'AND' => array(
						'Template.visibility_id' => 2,
						'Template.user_id' => $this->Auth->user('id')
					)
				)
			)
		);
		
		// Selected Options
		$options['selected'] = array('name' => Configure::read('Default.template.name'));
		
		// Specific Options
		$options['specific'] = array('unbind' => true, 'find' => 'first', 'model' => 'Genre', 'fields' => array('Genre.instructions as description'));
		
		return $this->chooser($this->modelClass, $this->name, $this->Template, $options);
	}
	
	
	/**
	 * Add Template from Project
	 * 
	 * @param int $project_id - the identifier of the project from which the template must be generated.
	 */
	public function add($project_id) {}

}
