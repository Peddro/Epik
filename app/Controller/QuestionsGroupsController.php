<?php
/**
 * Imports
 */
App::uses('AppController', 'Controller');

/**
 * QuestionsGroups Controller
 *
 * @package app.Controller
 * @property QuestionsGroup $QuestionsGroup
 * @author Bruno Sampaio
 */
class QuestionsGroupsController extends AppController {
	
	/**
	 * @var array Pagination Component Properties
	 */
	public $paginate = array(
			'order' => array('QuestionsGroup.modified' => 'desc', 'QuestionsGroup.name' => 'asc')
	);
	
	
	/**
	 * Extends the parent method.
	 */
	public function beforeFilter() {
		parent::beforeFilter();
		
		if($this->request->action == 'add' || $this->request->action == 'edit') {
			$this->Security->validatePost = false;
		}
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
		return parent::options(array('delete' => array('controller' => 'activities')));
	}
	
	
	/**
	 * Add Questions Group
	 *
	 * @param int $activity_type - the activity type id.
	 * @throws InvalidRequestException if the request type is not supported.
	 */
	public function add($activity_type=null) {
		if($this->RequestHandler->isAjax()) {
			if(!$activity_type) {
				$this->redirect(array('controller' => 'activities', 'action' => 'select'));
			}
			
			$storeRequest = $this->request->is('post');
			$this->addOrEdit(null, $activity_type, $storeRequest);
		}
		else throw new InvalidRequestException();
	}
	
	
	/**
	 * Edit Questions Group
	 *
	 * @param int $activity_id
	 * @throws NotFoundException if the questions group doesn't exist.
	 * @throws InvalidRequestException if the request type is not supported.
	 */
	public function edit($activity_id=null) {
		$this->QuestionsGroup->Activity->id = $activity_id;
		if (!$this->QuestionsGroup->Activity->exists()) {
			throw new NotFoundException(__('Invalid QuestionsGroup'));
		}
		
		if($this->RequestHandler->isAjax()) {

			// If request is empty get questions group data
			if(!isset($this->request->data['Activity'])) {
				$this->QuestionsGroup->recursive = -1;
				$groups = $this->QuestionsGroup->find('all', array('conditions' => array('QuestionsGroup.activity_id' => $activity_id)));
				
				$this->request->data['Activity'] = $this->QuestionsGroup->Activity->read(null, $activity_id)['Activity'];
				
				foreach($groups as $group) {
					$id = $group['QuestionsGroup']['question_id'];
					$this->request->data['Question'][$id]['id'] = $id;
				}
			}
			
			// Check if questions group belongs to current user
			if($this->itemBelongsToUser($this->request->data['Activity'])) {
				$storeRequest = $this->request->is('post') || $this->request->is('put');
				$this->addOrEdit($activity_id, null, $storeRequest);
			}
		}
		else throw new InvalidRequestException();
	}
	
	
	/**
	 * View Questions Group
	 *
	 * @param int $activity_id
	 * @throws NotFoundException if the questions group doesn't exist.
	 * @throws InvalidRequestException if the request type is not supported.
	 */
	public function view($activity_id=null) {
		$this->QuestionsGroup->Activity->id = $activity_id;
		if (!$this->QuestionsGroup->Activity->exists()) {
			throw new NotFoundException(__('Invalid QuestionsGroup'));
		}
		
		if($this->RequestHandler->isAjax()) {
			$this->layout = 'modal';
			
			$this->request->data = $this->QuestionsGroup->getData($activity_id);
			
			if($this->itemBelongsToUser($this->request->data['Activity'])) {
				$params['title'] = $this->request->data['Activity']['name'];
				$params['message'] = __('view-message', __('questions group'));
				$params['model'] = 'Activity';
				$params['icons'] = $this->getOptions();
				$params['current'] = 'select';
				
				$this->set(compact('params', 'types'));
			}
		}
		else throw new InvalidRequestException();
	}
	
	
	/**
	 * Processes a add or edit operation.
	 *
	 * @param int $activity_id - the activity to which the question belongs.
	 * @param int $activity_type - the activity_type id.
	 * @param bool $storeRequest - determines the type of request.
	 */
	private function addOrEdit($activity_id=null, $activity_type=null, $storeRequest) {
		// Set Page Variables
		$this->layout = 'modal';
		$sections = array('create basic', 'choose', 'complete');
		$params['current'] = $sections[0];
		
		// Check the request type
		if($storeRequest) {
			
			// Determine next section
			$submitted = $this->request->data['Section']['current'];
			if($this->request->data['Section']['previous'] == $submitted) {
				if($submitted == -1) {
					$this->redirect(array('controller' => 'activities', 'action' => 'select'));
				}
				$params['current'] = $submitted;
			}
			else {
				if($submitted == $sections[0]) {
					$this->QuestionsGroup->Activity->set($this->request->data);

					if ($this->QuestionsGroup->Activity->validates()) {

						// Check Activity Name
						$activity = $this->checkItemNameForCurrentUser($this->QuestionsGroup->Activity, $this->request->data['Activity']);

						if(!$activity) {
							$params['current'] = $sections[1];
						}
						else {
							$this->Session->setFlash(__('error-already-exists-item', __('activity'), $this->request->data['Activity']['name']));
						}
					}
					else {
					    $this->Session->setFlash(__('error-empty-fields'));
					}
				}
				else if($submitted == $sections[1]) {
					$params['current'] = $sections[1];
					
					// Set Activity Source
					$this->request->data['Activity']['lms_id'] = null;
					$this->request->data['Activity']['lms_url'] = null;
					$this->request->data['Activity']['external_id'] = null;
					
					if(isset($this->request->data['Question']) && count($this->request->data['Question']) > 1) {
						
						// Save Activity
						if($this->QuestionsGroup->Activity->save($this->request->data)) {
							if(!$activity_id) {
								$this->request->data['Activity']['id'] = $this->QuestionsGroup->Activity->getInsertID();
								$operation = Configure::read('Operations.add');
							}
							else {
								$this->QuestionsGroup->deleteAll(array('QuestionsGroup.activity_id' => $activity_id));
								$operation = Configure::read('Operations.edit');
							}
							
							$group = array();
							foreach($this->request->data['Question'] as $question) {
								array_push($group, array(
									'QuestionsGroup' => array(
										'activity_id' => $this->request->data['Activity']['id'],
										'question_id' => $question['id']
									)
								));
							}
							
							if($this->QuestionsGroup->saveAll($group)) {
								
								// Log Data
								$this->logData('activities', $this->request->data['Activity']['id'], $this->request->data['Activity']['name'], $operation);

								$params['current'] = $sections[2];
							}
							else {
								$this->QuestionsGroup->Activity->delete($this->request->data);
								$this->Session->setFlash(__('error-while-saving', __('questions group')));
							}
						}
						else {
							$this->Session->setFlash(__('error-empty-fields'));
						}
					}
					else {
						$this->Session->setFlash(__('error-choose-minimum-two', __('questions')));
					}
				}
			}
		}

		// Set Activity Data
		if(!$activity_id) {
			$this->request->data['Activity']['type_id'] = $activity_type;
		}
		$this->request->data['Activity']['user_id'] = $this->Auth->user('id');

		// Set section properties
		$entity = __('Questions Group');
		if($params['current'] == $sections[0]) {
			if(!$activity_id) {
				$params['title'] = __('create-add-title', $entity);
				$params['message'] = __('create-add-message');
			}
			else {
				$params['title'] = __('create-edit-title', $entity);
				$params['message'] = __('create-edit-message');
			}
			$params['previous'] = -1;
		}
		if($params['current'] == $sections[1]) {
			if(!$activity_id) {
				$params['title'] = __('create-add-title', $entity);
			}
			else {
				$params['title'] = __('create-edit-title', $entity);
			}
			$params['message'] = __('choose-message', __('questions'));
			
			$this->loadModel('QuestionType');

			$params['filters'] = array();
			$params['filters']['model'] = 'Chooser';
			$params['filters']['url'] = array('controller' => 'questions', 'action' => 'choose');
			$params['filters']['fields'] = array(
				0 => array('name' => 'Chooser.filters.type_id', 'label' => __('Question type'), 'type' => 'select', 'options' => $this->QuestionType->find('list'), 'empty' => __('All')),
				1 => array('name' => 'Chooser.multiple', 'type' => 'hidden')
			);

			if(isset($this->request->data['Chooser']['filters']['type_id'])) {
				$params['filters']['fields'][0]['selected'] = $this->request->data['Chooser']['filters']['type_id'];
			}
			$this->request->data['Chooser']['multiple'] = true;
			
			if(isset($this->request->data['Question'])) {
				foreach($this->request->data['Question'] as $question) {
					array_push($params['filters']['fields'], array(
						'name' => 'Question.'.$question['id'].'.id',
						'type' => 'hidden'
					));
				}
			}
			
			$params['previous'] = $sections[0];
		}
		else if($params['current'] == $sections[2]) {
			if(!$activity_id) {
				$params['title'] = __('success-add-title', $entity);
			}
			else {
				$params['title'] = __('success-edit-title', $entity);
			}
			$params['message'] = __('success-message');
			$params['previous'] = false;
		}

		$this->set(compact('params', 'sections'));
		$this->render('/QuestionsGroups/add_edit');
	}
}
