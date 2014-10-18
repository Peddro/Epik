<?php
/**
 * Imports
 */
App::uses('AppController', 'Controller');

/**
 * Activities Controller
 * 
 * @package app.Controller
 * @property Activity $Activity
 * @author Bruno Sampaio
 */
class ActivitiesController extends AppController {

	/**
	 * @var array Pagination Component Properties
	 */
	public $paginate = array(
			'order' => array('Activity.modified' => 'desc', 'Activity.name' => 'asc')
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
	 * Extends the parent method.
	 */
	private function getOptions() {
		$entity = __('Activity');
		return parent::options(array(
			'reload' => array(
				'title' => __('reload-title', $entity),
				'model' => 'Activity',
				'fields' => array('lms_id', 'lms_url', 'external_id'),
				'action' => 'reload',
				'modal' => true
			),
			'hint' => array(
				'title' => __('manage-title', $entity, __('Hints')),
				'model' => 'Type',
				'fields' => array('allows_hints'),
				'action' => 'hints',
				'modal' => true
			), 
			'file' => array(
				'title' => __('manage-title', $entity, __('Resources')),
				'model' => 'Type',
				'fields' => array('allows_resources'),
				'action' => 'resources',
				'modal' => true
			)
		));
	}
	
	
	/**
	 * Dashboard Section
	 *
	 * This action uses the listing action to list user items in a dashboard page.
	 * This kind of page allows the user to manage items that belong to him.
	 */
	public function index() {
		$this->dashboard();
	}
	

	/**
	 * List of Items
	 *
	 * This action prepares the options to be sent to the paginator method
	 * to create a list of activities items.
	 * @param string $keyword - use to filter the search by the name attribute.
	 * @return array - list of values to be sent to an element view.
	 */
	public function listing($keyword='') {
		$options = array(
			'conditions' => array(),
			'recursive' => 0,
			'unbind' => array(
				'belongsTo' => array('LMS', 'User'),
				'hasOne' => array('Question'),
				'hasMany' => array('QuestionsGroup', 'Hint'),
				'hasAndBelongsToMany' => array('Subject', 'Project', 'Resource')
			),
			'use' => array('modal' => true, 'icon' => true),
			'options' => $this->getOptions()
		);
		
		return $this->paginator($this->modelClass, $this->name, $this->Activity, $keyword, $options);
	}
	
	
	/**
	 * Choose Section
	 *
	 * This action prepares the options to be sent to the chooser method.
	 * It is invoked on a situation where the user must select one or more activities to continue.
	 * @return array - list of values to be sent to an element view.
	 */
	public function choose() {
		$options = array();
		
		// Filters Default Values
		$options['filters'] = array('type_id' => null);
		
		// Unbind Options
		$options['unbind'] = array(
			'belongsTo' => array('LMS', 'User'), 
			'hasOne' => array('Question'), 
			'hasMany' => array('QuestionsGroup', 'Hint'),
			'hasAndBelongsToMany' => array('Subject', 'Project', 'Resource')
		);
		
		// List Options
		$fields = array('Activity.id', 'Activity.name', 'Activity.description', 'Activity.lms_id', 'Activity.lms_url', 'Activity.external_id', 'Type.icon');
		$conditions['Activity.user_id'] = $this->Auth->user('id');
		$order = array('Activity.name');
		$options['list'] = array('fields' => $fields, 'conditions' => $conditions, 'order' => $order);
		
		// Selected Options
		$options['selected'] = array();
		
		return $this->chooser($this->modelClass, $this->name, $this->Activity, $options);
	}
	
	
	/**
	 * Select Section.
	 *
	 * This action lists the types of activities the user can create.
	 * It is displayed before the add action.
	 * @throws InvalidRequestException if the request type is not supported.
	 */
	public function select() {
		if($this->RequestHandler->isAjax()) {
			$this->selector(__('Activity'), __('activity'), __('create'), $this->Activity->Type);
		}
		else throw new InvalidRequestException();
	}
	
	
	/**
	 * Manage Activity Hints
	 *
	 * @param int $id
	 * @throws NotFoundException if the activity doesn't exist.
	 * @throws InvalidRequestException if the request type is not supported.
	 */
	public function hints($id=null) {
		$this->Activity->id = $id;
		if (!$this->Activity->exists()) {
			throw new NotFoundException(__('Invalid activity'));
		}
		
		if($this->RequestHandler->isAjax()) {
			
			// Get Activity
			if(!isset($this->request->data['Activity'])) {
				$this->Activity->unbindModel(array(
					'belongsTo' => array('LMS', 'User'),
					'hasOne' => array('Question'),
					'hasMany' => array('QuestionsGroup'),
					'hasAndBelongsToMany' => array('Subject', 'Project', 'Resource')
				));
				$this->request->data = $this->Activity->read(null, $id);
			}
			
			// If user has permission
			if($this->itemBelongsToUser($this->request->data['Activity']) && $this->request->data['Type']['allows_hints']) {
				
				// Get Request
				$storeRequest = $this->request->is('post') || $this->request->is('put');
				
				// Set Page Variables
				$this->layout = 'modal';
				$sections = array('create', 'complete');
				$params['current'] = $sections[0];

				// Check the request type
				if($storeRequest) {
					
					$hints = $this->request->data['Hint'];
					foreach($hints as $key => $value) {
						if(strlen($value['content']) == 0) {
							unset($hints[$key]);
						}
					}
					
					if(count($hints) > 0) {
						$this->Activity->Hint->set($hints);
						
						if($this->Activity->Hint->validates()) {
							$this->Activity->Hint->deleteAll(array('Hint.activity_id' => $id));
							
							if($this->Activity->Hint->saveAll($hints)) {
								$params['current'] = $sections[1];
							}
							else {
								$this->Session->setFlash(__('error-while-saving', __('hints')));
							}
						}
					}
					else {
						if($this->Activity->Hint->deleteAll(array('Hint.activity_id' => $id))) {
							$params['current'] = $sections[1];
						}
 						else {
							$this->Session->setFlash(__('error-while-deleting', __('hints')));
						}
					}
				}
				
				// Set section properties
				$entity = __('Activity');
				if($params['current'] == $sections[0]) {
					$params['title'] = __('manage-title', $entity, __('Hints'));
					$params['message'] = __('create-add-message');
				}
				else if($params['current'] == $sections[1]) {
					$params['title'] = __('success-edit-title', $entity);
					$params['message'] = __('success-message');
				}
				$params['previous'] = false;

				$this->set(compact('params', 'sections'));
			}
		}
		else throw new InvalidRequestException();
	}
	
	
	/**
	 * Manage Activity Resources
	 *
	 * @param int $id
	 * @throws NotFoundException if the activity doesn't exist.
	 * @throws InvalidRequestException if the request type is not supported.
	 */
	public function resources($id=null) {
		$this->Activity->id = $id;
		if (!$this->Activity->exists()) {
			throw new NotFoundException(__('Invalid activity'));
		}
		
		if($this->RequestHandler->isAjax()) {
			
			// Get Activity
			if(!isset($this->request->data['Activity'])) {
				$this->Activity->unbindModel(array(
					'belongsTo' => array('LMS', 'User'),
					'hasOne' => array('Question'),
					'hasMany' => array('QuestionsGroup', 'Hint'),
					'hasAndBelongsToMany' => array('Subject', 'Project')
				));
				$this->request->data = $this->Activity->read(null, $id);
			}
			
			// If user has permission
			if($this->itemBelongsToUser($this->request->data['Activity']) && $this->request->data['Type']['allows_resources']) {
				
				// Get Request
				$storeRequest = $this->request->is('post') || $this->request->is('put');
				
				// Set Page Variables
				$this->layout = 'modal';
				$sections = array('choose', 'complete');
				$params['current'] = $sections[0];

				// Check the request type
				if($storeRequest) {
					
					$this->loadModel('ActivityResource');
					$delete = $this->ActivityResource->deleteAll(array('ActivityResource.activity_id' => $id));
					
					if(isset($this->request->data['Resource']) && count($this->request->data['Resource']) > 0) {
						$activity_resources = array();
						foreach($this->request->data['Resource'] as $resource) {
							array_push($activity_resources, array('ActivityResource' => array('activity_id' => $id, 'resource_id' => $resource['id'])));
						}
						
						
						if($this->ActivityResource->saveAll($activity_resources)) {
							$params['current'] = $sections[1];
						}
						else {
							$this->Session->setFlash(__('error-while-associating', __('resources'), __('activity')));
						}
					}
					else {
						if($delete) {
							$params['current'] = $sections[1];
						}
 						else {
							$this->Session->setFlash(__('error-while-saving', __('activity')));
						}
					}
				}
				
				// Set section properties
				if($params['current'] == $sections[0]) {
					$params['title'] = __('manage-title', __('activity'), __('Resources'));
					$params['message'] = __('choose-message', __('resources'));
					
					$this->loadModel('ResourceType');

					$params['filters'] = array();
					$params['filters']['model'] = 'Chooser';
					$params['filters']['url'] = array('controller' => 'resources', 'action' => 'choose');
					$params['filters']['fields'] = array(
						0 => array('name' => 'Chooser.filters.type_id', 'label' => __('Resource type'), 'type' => 'select', 'options' => $this->ResourceType->find('list'), 'empty' => __('All')),
						1 => array('name' => 'Chooser.multiple', 'type' => 'hidden')
					);

					if(isset($this->request->data['Chooser']['filters']['type_id'])) {
						$params['filters']['fields'][0]['selected'] = $this->request->data['Chooser']['filters']['type_id'];
					}
					$this->request->data['Chooser']['multiple'] = true;
					
					if(isset($this->request->data['Resource'])) {
						foreach($this->request->data['Resource'] as $resource) {
							array_push($params['filters']['fields'], array(
								'name' => 'Resource.'.$resource['id'].'.id',
								'type' => 'hidden'
							));
						}
					}
				}
				else if($params['current'] == $sections[1]) {
					$params['title'] = __('success-edit-title', __('Activity'));
					$params['message'] = __('success-message');
				}
				$params['previous'] = false;

				$this->set(compact('params', 'sections'));
			}
		}
		else throw new InvalidRequestException();
	}
	
	
	/**
	 * Edit Activity
	 *
	 * @param int $id
	 */
	public function edit($id=null) {
		$this->viewOrEdit($id, 'edit');
	}
	
	
	/**
	 * View Activity
	 *
	 * @param int $id
	 */
	public function view($id=null) {
		$this->viewOrEdit($id, 'view');
	}
	
	
	/**
	 * Processes a view or edit operation.
	 *
	 * @param int $id
	 * @param string $action
	 * @throws NotFoundException if the activity doesn't exist.
	 * @throws InvalidRequestException if the request type is not supported.
	 */
	private function viewOrEdit($id, $action) {
		$this->Activity->id = $id;
		if (!$this->Activity->exists()) {
			throw new NotFoundException(__('Invalid activity'));
		}
		
		if($this->RequestHandler->isAjax()) {

			$this->Activity->unbindModel(array(
				'belongsTo' => array('LMS', 'User'),
				'hasOne' => array('Question'),
				'hasMany' => array('QuestionsGroup', 'Hint'),
				'hasAndBelongsToMany' => array('Subject', 'Project', 'Resource')
			));
			$type = $this->Activity->find('first', array('fields' => array('Type.controller'), 'conditions' => array('Activity.id' => $id)));
			
			$this->redirect(array('controller' => $type['Type']['controller'], 'action' => $action, $id));
		}
		else throw new InvalidRequestException();
	}
	
	
	/**
	 * Import Activity
	 * @throws InvalidRequestException if the request type is not supported.
	 */
	public function import() {
		if($this->RequestHandler->isAjax()) {
			$this->redirect(array('controller' => 'questions', 'action' => 'import'));
		}
		else throw new InvalidRequestException();
	}
	
	
	/**
	 * Reload Activity
	 *
	 * @param int $id
	 * @throws InvalidRequestException if the request type is not supported.
	 */
	public function reload($id=null) {
		if($this->RequestHandler->isAjax()) {
			$this->redirect(array('controller' => 'questions', 'action' => 'reload', $id));
		}
		else throw new InvalidRequestException();
	}
	
	
	/**
	 * Choose Activity Type to be added to Project
	 *
	 * @param int $genre_id - project genre id.
	 * @param int $type_id - activity type id.
	 * @throws InvalidRequestException if the request type is not supported.
	 */
	public function choose_type($genre_id, $type_id) {
		$this->loadModel('GenreActivity');
		
		if($this->RequestHandler->isAjax() && $this->GenreActivity->hasType($genre_id, $type_id)) {
			$this->layout = 'modal';
			$sections = array('choose');
			$params['current'] = $sections[0];
			$params['previous'] = false;

			// Set section properties
			if($params['current'] == $sections[0]) {
				$params['title'] = __('add-title', __('Activity'));
				$params['message'] = __('choose-message', __('activity'));

				$params['filters'] = array();
				$params['filters']['model'] = 'Chooser';
				$params['filters']['url'] = array('controller' => 'activities', 'action' => 'choose');
				$params['filters']['fields'] = array();

				$this->request->data['Chooser']['filters'] = array('type_id' => $type_id);
				$this->request->data['Chooser']['multiple'] = false;
			}

			$this->set(compact('params', 'sections'));
		}
		else throw new InvalidRequestException();
	}


	/**
	 * Delete Activity
	 *
	 * @param int $id
	 * @throws InvalidRequestException if the request type is not supported.
	 * @throws NotFoundException if the activity doesn't exist.
	 */
	public function delete($id = null) {
		if (!$this->request->is('post') || $this->RequestHandler->isAjax()) {
			throw new InvalidRequestException();
		}
		
		$this->Activity->id = $id;
		if (!$this->Activity->exists()) {
			throw new NotFoundException(__('Invalid Activity'));
		}
		
		$this->autoRender = false;
		
		$this->Activity->unbindModel(array(
			'belongsTo' => array('LMS', 'Type', 'User'),
			'hasMany' => array('QuestionsGroup', 'Hint'),
			'hasAndBelongsToMany' => array('Subject', 'Project', 'Resource')
		));
		$this->Activity->read(null, $id);
		
		// Check Permission
		if($this->itemBelongsToUser($this->Activity->data['Activity'])) {
			
			// Get Activity Groups if it is a Questios
			$groups = array();
			if(isset($this->Activity->data['Question']['id'])) {
				
				// Get Groups Activities Ids
				$this->Activity->QuestionsGroup->recursive = -1;
				$fields = array('QuestionsGroup.activity_id', 'QuestionsGroup.id');
				$conditions = array('QuestionsGroup.question_id' => $this->Activity->data['Question']['id']);
				$groups = $this->Activity->QuestionsGroup->find('list', array('fields' => $fields, 'conditions' => $conditions));
				
				// Get Groups Questions Count
				$this->Activity->QuestionsGroup->recursive = -1;
				$fields = array('QuestionsGroup.activity_id', 'COUNT(QuestionsGroup.activity_id) as count');
				$conditions = array('QuestionsGroup.activity_id' => array_keys($groups));
				$groupby = array('QuestionsGroup.activity_id');
				$counts = $this->Activity->QuestionsGroup->find('all', array('fields' => $fields, 'conditions' => $conditions, 'group' => $groupby));
				
				// Map each group id to the number of questions it contains
				foreach($counts as $item) {
					$groups[$item['QuestionsGroup']['activity_id']] = $item[0]['count'];
				}
			}
			
			// Delete Activity
			if ($this->Activity->delete()) {
				
				// If there are groups delete the ones with two or less questions
				if(count($groups) > 0) {
					$ids = array();
					foreach($groups as $key => $value) {
						if($value <= 2) {
							$ids[] = $key;
						}
					}
					$this->Activity->deleteAll(array('Activity.id' => $ids));
				}

				//Log Operation
				$this->logData($this->params['controller'], $id, $this->Activity->data['Activity']['name'], Configure::read('Operations.delete'));
			}
		}
		
		$this->redirect(array('action' => 'index'));
	}
}
