<?php
/**
 * Imports
 */
App::uses('AppController', 'Controller');

/**
 * Questions Controller
 *
 * @package app.Controller
 * @property Question $Question
 * @author Bruno Sampaio
 */
class QuestionsController extends AppController {
	
	/**
	 * @var array Components used by this Controller
	 */
	public $components = array('LMSServices');
	
	
	/**
	 * @var array Pagination Component Properties
	 */
	public $paginate = array(
			'order' => array('Question.modified' => 'desc', 'Question.name' => 'asc')
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
				'model' => 'Activity',
				'controller' => 'activities',
				'fields' => array(),
				'action' => 'hints',
				'modal' => true
			), 
			'file' => array(
				'title' => __('manage-title', $entity, __('Resources')),
				'model' => 'Activity',
				'controller' => 'activities',
				'fields' => array(),
				'action' => 'resources',
				'modal' => true
			),
			'delete' => array('controller' => 'activities')
		));
	}
	
	
	/**
	 * Select Section.
	 *
	 * This action lists the types of questions the user can create.
	 * It is displayed before the add action.
	 *
	 * @param int $activity_type - the activity type id.
	 * @throws InvalidRequestException if the request type is not supported.
	 */
	public function select($activity_type=null) {
		if($this->RequestHandler->isAjax()) {
			if($activity_type) {
				
				$this->request->data['Activity']['type_id'] = $activity_type;
				$this->selector(__('Question'), __('question'), __('create'), $this->Question->Type);
			}
			else {
				$this->redirect(array('controller' => 'activities', 'action' => 'select'));
			}
		}
		else throw new InvalidRequestException();
	}
	
	
	/**
	 * Choose Section
	 *
	 * This action prepares the options to be sent to the chooser method.
	 * It is invoked on a situation where the user must select one or more questions to continue.
	 * @return array - list of values to be sent to an element view.
	 */
	public function choose() {
		$options = array();
		
		// Filters Default Values
		$options['filters'] = array('type_id' => null);
		
		// Unbind Options
		$options['unbind'] = array('hasMany' => array('Answer'));
		
		// List Options
		$fields = array('Question.id', 'Activity.name', 'Activity.description', 'Type.icon');
		$conditions['Activity.user_id'] = $this->Auth->user('id');
		$order = array('Activity.name');
		$options['list'] = array('fields' => $fields, 'conditions' => $conditions, 'order' => $order);
		
		// Selected Options
		$options['selected'] = array();
		
		return $this->chooser($this->modelClass, $this->name, $this->Question, $options);
	}
	
	
	/**
	 * Add Question
	 *
	 * @param int $activity_type
	 * @param int $question_type
	 * @throws InvalidRequestException if the request type is not supported.
	 */
	public function add($activity_type=null, $question_type=null) {
		if($this->RequestHandler->isAjax()) {
			if(!$activity_type) {
				$this->redirect(array('controller' => 'activities', 'action' => 'select'));
			}
			else if(!$question_type) {
				$this->redirect(array('action' => 'select', $activity_type));
			}
			
			$storeRequest = $this->request->is('post');

			$this->addOrEdit(null, $activity_type, $question_type, $storeRequest);
		}
		else throw new InvalidRequestException();
	}
	
	
	/**
	 * Edit Question
	 *
	 * @param int $activity_id
	 * @throws NotFoundException if the question doesn't exist.
	 * @throws InvalidRequestException if the request type is not supported.
	 */
	public function edit($activity_id=null) {
		$this->Question->Activity->id = $activity_id;
		if (!$this->Question->Activity->exists()) {
			throw new NotFoundException(__('Invalid Question'));
		}
		
		if($this->RequestHandler->isAjax()) {

			// If request is empty get question data
			if(!isset($this->request->data['Activity'])) {
				$this->request->data = $this->Question->find('first', array('conditions' => array('activity_id' => $activity_id)));

				// Get correct Answers
				if($this->request->data['Type']['max_answers'] > 1) {
					for($i = 0; $i < count($this->request->data['Answer']); $i++) {
						if($this->request->data['Answer'][$i]['is_correct']) {
							$this->request->data['Answers']['correct'] = $i;
						}
					}
				}
			}

			// Check if question belongs to current user
			if($this->itemBelongsToUser($this->request->data['Activity'])) {
				$storeRequest = $this->request->is('post') || $this->request->is('put');
				$this->addOrEdit($activity_id, null, null, $storeRequest);
			}
		}
		else throw new InvalidRequestException();
	}
	
	
	/**
	 * Processes a add or edit operation.
	 *
	 * @param int $activity_id - the activity to which the question belongs.
	 * @param int $activity_type - the activity type id.
	 * @param int $question_type - the question type id.
	 * @param bool $storeRequest - determines the type of request.
	 */
	private function addOrEdit($activity_id=null, $activity_type=null, $question_type=null, $storeRequest) {
		
		// Set Page Variables
		$this->layout = 'modal';
		$sections = array('create basic', 'create question', 'complete');
		$params['current'] = $sections[0];
		
		// Set True or False Answers Data
		if(isset($this->request->data['Type']) && $this->request->data['Type']['icon'] == 'truefalse') {
			$this->request->data['Answer'][0]['content'] = __('True');
			$this->request->data['Answer'][1]['content'] = __('False');
		}
		
		// Check the request type
		if($storeRequest) {
			
			// Determine next section
			$submitted = $this->request->data['Section']['current'];
			if($this->request->data['Section']['previous'] == $submitted) {
				if($submitted == -1) {
					$this->redirect(array('action' => 'select', $activity_type));
				}
				$params['current'] = $submitted;
			}
			else {
				if($submitted == $sections[0]) {
					$this->Question->Activity->set($this->request->data);

					if ($this->Question->Activity->validates()) {

						// Check Activity Name
						$activity = $this->checkItemNameForCurrentUser($this->Question->Activity, $this->request->data['Activity']);

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

					// Set Activity LMS
					if(!$activity_id) {
						$this->request->data['Activity']['lms_id'] = null;
						$this->request->data['Activity']['lms_url'] = null;
						$this->request->data['Activity']['external_id'] = null;
					}

					// Validate and Set Correct and Incorrect Answers
					$error = false;
					if($this->request->data['Type']['max_answers'] > 1) {
						$type = $this->request->data['Type']['icon'];
						
						// If True or False Question
						if($type == 'truefalse') {
							$this->request->data['Answer'][0]['is_correct'] = $this->request->data['Answers']['correct'] == 0;
							$this->request->data['Answer'][1]['is_correct'] = !$this->request->data['Answer'][0]['is_correct'];
						}
						else if($type == 'multichoice') {
							$found = false;
							foreach($this->request->data['Answer'] as $key => $value) {
								if(strlen($value['content']) > 0) {
									if($this->request->data['Answers']['correct'] == $key) {
										$this->request->data['Answer'][$key]['is_correct'] = true;
										$found = true;
									}
									else {
										$this->request->data['Answer'][$key]['is_correct'] = false;
									}
								}
								else {
									unset($this->request->data['Answer'][$key]);
								}
							}
							
							if(count($this->request->data['Answer']) < 2) {
								$error = __('error-minimum-two');
							}
							else if(!$found) {
								$error = __('error-no-answer');
							}
						}
					}
					else {
						if(!strlen($this->request->data['Answer'][0]['content'])) {
							$error = __('error-minimum-one');
						}
					}

					if(!$error) {
						$operation = $activity_id? Configure::read('Operations.edit') : Configure::read('Operations.add');
						if($this->createQuestion($activity_id, $operation)) {
							$params['current'] = $sections[2];
						}
					}
					else {
						$this->Session->setFlash($error);
					}
				}
			}
		}

		// Set Activity Data
		if(!$activity_id) {
			$this->request->data['Activity']['type_id'] = $activity_type;

			// Set Question Data
			if(!isset($this->request->data['Type'])) {
				$type = $this->Question->Type->read(null, $question_type);
				$this->request->data['Type'] = $type['Type'];
				$this->request->data['Question']['type_id'] = $question_type;
			}
			
		}
		$this->request->data['Activity']['user_id'] = $this->Auth->user('id');

		// Set section properties
		$entity = $this->request->data['Type']['name'].' '.__('Question');
		if(!$activity_id) {
			$params['title'] = __('create-add-title', $entity);
			$params['message'] = __('create-add-message');
		}
		else {
			$params['title'] = __('create-edit-title', $entity);
			$params['message'] = __('create-edit-message');
		}
		
		if($params['current'] == $sections[0]) {
			if(!$activity_id) {
				$params['previous'] = -1;
			}
			else {
				$params['previous'] = false;
			}
		}
		else if($params['current'] == $sections[1]) {
			$params['previous'] = $sections[0];
		}
		else if($params['current'] == $sections[2]) {
			$params['title'] = __('success-add-title', $entity);
			$params['message'] = __('success-message');
			$params['previous'] = false;
		}

		$this->set(compact('params', 'sections'));
		$this->render('/Questions/add_edit');
	}
	
	
	/**
	 * Import Question
	 *
	 * @throws InvalidRequestException if the request type is not supported.
	 */
	public function import() {
		if($this->RequestHandler->isAjax()) {
			$this->layout = 'modal';
			$sections = array('create connect', 'choose course', 'choose content', 'choose question', 'complete');
			$filter = 'quiz';
			$params['current'] = $sections[0];
			
			$actionRequest = $this->request->is('post') || $this->request->is('put');
			
			if($actionRequest) {
				
				// Determine next section
				$submitted = $this->request->data['Section']['current'];
				if($this->request->data['Section']['previous'] == $submitted) {
					if($submitted == -1) {
						$this->redirect(array('controller' => 'activities', 'action' => 'select'));
					}
					$params['current'] = $submitted;
				}
				else {
					$params['current'] = $submitted;
					
					$lms = $this->LMSServices->getLMS($this->request->data['LMS'], $this->request->data['User']);
					if($lms) {
						$this->LMSServices = $this->Components->load($lms['name'].'Services');
						
						if($submitted == $sections[0]) {
							if($this->LMSServices->connect($lms, $this->request->data['User'])) {
								$params['current'] = $sections[1];
							}
						}
						else if($submitted == $sections[1]) {

							if(isset($this->request->data['Course']['id']) && $this->request->data['Course']['id']) {
								$params['current'] = $sections[2];
							}
							else {
								$this->Session->setFlash(__('error-choose-one', __('course')));
							}
						}
						else if($submitted == $sections[2]) {
							if(isset($this->request->data['Content']['id']) && $this->request->data['Content']['id']) {
								$params['current'] = $sections[3];
							}
							else {
								$this->Session->setFlash(__('error-choose-one', __($filter)));
							}
						}
						else if($submitted == $sections[3]) {
							if(isset($this->request->data['Question']) && count($this->request->data['Question']) > 0) {
								$ids = array();
								foreach($this->request->data['Question'] as $question) {
									array_push($ids, $question['id']);
								}
								$list = $this->LMSServices->request($this->LMSServices->requests[3], array('ids' => $ids));

								if($list) {
									$this->loadModel('Activity');
									$atype = $this->Activity->Type->find('first', array('conditions' => array('name' => $this->modelClass)));

									$this->request->data['Activities'] = array();
									foreach($list as $key => $data) {
										$data['Activity']['type_id'] = $atype['Type']['id'];
										$data['Activity']['user_id'] = $this->Auth->user('id');

										// Check Activity Name
										$activity = $this->checkItemNameForCurrentUser($this->Activity, $data['Activity']);
										if(!$activity) {

											$qtype = $this->Question->Type->find('first', array('conditions' => array('icon' => $data['Question']['type_icon'])));
											if($qtype) {
												$data['Question']['type_id'] = $qtype['Type']['id'];
												unset($data['Question']['type_icon']);

												if($this->Question->saveAll($data)) {
													$id = $this->Activity->getInsertID();
													$data['Activity']['id'] = $id;
													$this->request->data['Activities'][$key]['id'] = $id;
													$this->request->data['Activities'][$key]['name'] = $data['Activity']['name'];

													// Log Data
													$this->logData('activities', $id, $data['Activity']['name'], Configure::read('Operations.import'));

													// Save Hints
													if(count($data['Hint']) > 0) {
														foreach($data['Hint'] as $key => $val) {
															$data['Hint'][$key]['activity_id'] = $id;
														}
														$this->Activity->Hint->saveMany($data['Hint']);
													}
												}
												else {
													$this->Session->setFlash(__('error-while-saving-import', __('questions')));
												}
											}
										}
										else {
											$this->Session->setFlash(__('error-already-exists-item', __('activity'), $data['Activity']['name']));
										}
									}

									// If at least one is saved
									if(count($this->request->data['Activities']) > 0) {
										$params['current'] = $sections[4];
									}
								}
							}
							else {
								$this->Session->setFlash(__('error-choose-minimum-one', __('question')));
							}
						}
					}
				}
			}
			
			if(!isset($this->request->data['LMS']['id'])) {
				$this->request->data['LMS']['id'] = $this->Auth->user('lms_id');
			}
			if(!isset($this->request->data['LMS']['url'])) {
				$this->request->data['LMS']['url'] = $this->Auth->user('lms_url');
			}
			
			$this->request->data['User']['password'] = '';
			
			$entity = __('Question');
			if($params['current'] == $sections[0]) {
				$params['title'] = __('import-title', $entity);
				$params['message'] = __('import-connect-message');
				$params['previous'] = -1;
				
				$this->loadModel('LMS');
				$this->set('lms', $this->LMS->find('list'));
			}
			else if($params['current'] == $sections[1] || $params['current'] == $sections[2] || $params['current'] == $sections[3]) {
				switch($params['current']) {
					case $sections[1]:
						$params['previous'] = $sections[0];
						$request = $this->LMSServices->requests[0];
						$multiple = false;
						break;
						
					case $sections[2]:
						$params['previous'] = $sections[1];
						$request = $this->LMSServices->requests[1];
						$multiple = false;
						break;
						
					case $sections[3]:
						$params['previous'] = $sections[2];
						$request = $this->LMSServices->requests[2];
						$multiple = true;
						$name = $request;
						break;
				}
				
				$singular = Inflector::singularize($request);
				$name = !isset($name)? $singular : $name;
				$model = ucfirst($singular);
				
				$params['title'] = __('import-title', $entity);
				$params['message'] = __('choose-message', __($name));
				
				$this->request->data['Chooser']['multiple'] = $multiple;
				
				$this->set(compact('model', 'filter', 'request'));
			}
			else if($params['current'] == $sections[4]) {
				$params['title'] = __('success-import-title', $entity);
				$params['message'] = __('success-message');
				$params['previous'] = false;
			}
			
			$this->set(compact('params', 'sections'));
		}
		else throw new InvalidRequestException();
	}
	
	
	/**
	 * Reload Question
	 *
	 * @param int $activity_id
	 * @throws NotFoundException if the question doesn't exist.
	 * @throws InvalidRequestException if the request type is not supported.
	 */
	public function reload($activity_id=null) {
		$this->Question->Activity->id = $activity_id;
		if (!$this->Question->Activity->exists()) {
			throw new NotFoundException(__('Invalid Question'));
		}
		
		if($this->RequestHandler->isAjax()) {
			
			// If request is empty get question data
			if(!isset($this->request->data['Activity'])) {
				$this->Question->Activity->unbindModel(array(
					'belongsTo' => array('User'),
					'hasMany' => array('Hint', 'QuestionsGroup'),
					'hasAndBelongsToMany' => array('Subject', 'Project', 'Resource')
				));
				$this->request->data = $this->Question->Activity->read(null, $activity_id);
				
				if($this->request->data['Activity']['imported']) {
					$this->request->data['LMS']['url'] = $this->request->data['Activity']['lms_url'];
				}
				else {
					$this->Session->setFlash(__('error-not-imported'));
					throw new MethodNotAllowedException();
				}
			}
			
			// Check if activity belongs to current user
			if($this->itemBelongsToUser($this->request->data['Activity'])) {
				$this->layout = 'modal';
				$sections = array('create connect', 'confirm activity', 'complete');
				$params['current'] = $sections[0];

				$actionRequest = $this->request->is('post') || $this->request->is('put');

				if($actionRequest) {

					// Determine next section
					$submitted = $this->request->data['Section']['current'];
					if($this->request->data['Section']['previous'] == $submitted) {
						$params['current'] = $submitted;
					}
					else {
						$params['current'] = $submitted;

						$lms = $this->LMSServices->getLMS($this->request->data['LMS'], $this->request->data['User']);
						if($lms) {
							$this->LMSServices = $this->Components->load($lms['name'].'Services');
							
							if($submitted == $sections[0]) {
								if($this->LMSServices->connect($lms, $this->request->data['User'])) {

									// Get Activity Data
									$ids = array($this->request->data['Activity']['external_id']);
									$list = $this->LMSServices->request($this->LMSServices->requests[3], array('ids' => $ids));
									if(count($list) > 0) {
										$this->request->data['Activity'] = array_merge($this->request->data['Activity'], $list[0]['Activity']);
										$this->request->data['Question'] = array_merge($this->request->data['Question'], $list[0]['Question']);
										$this->request->data['Answer'] = $list[0]['Answer'];
										$this->request->data['Hint'] = $list[0]['Hint'];
									}
									else {
										$this->Session->setFlash(__('error-lms-external-item-not-found', __('activity')));
									}
									$params['current'] = $sections[1];
								}
							}
							else if($submitted == $sections[1]) {
								$this->loadModel('Activity');

								// Check Activity Name
								$activity = $this->checkItemNameForCurrentUser($this->Activity, $this->request->data['Activity']);
								if(!$activity) {

									// Prepare Answers Data
									$this->Question->Answer->deleteAll(array('Answer.question_id' => $this->request->data['Question']['id']));
									foreach($this->request->data['Answer'] as $key => $val) {
										$this->request->data['Answer'][$key]['question_id'] = $this->request->data['Question']['id'];
									}

									// Prepare Hints Data
									$this->Activity->Hint->deleteAll(array('Hint.activity_id' => $this->request->data['Activity']['id']));
									if(isset($this->request->data['Hint']) && count($this->request->data['Hint']) > 0) {
										foreach($this->request->data['Hint'] as $key => $val) {
											$this->request->data['Hint'][$key]['activity_id'] = $this->request->data['Activity']['id'];
										}
									}

									// Save the Question
									if($this->createQuestion($activity_id, Configure::read('Operations.reload'))) {
										$params['current'] = $sections[2];
									}
								}
								else {
									$this->Session->setFlash(__('error-already-exists-item', __('resource'), $data['Resource']['name']));
								}
							}
						}
					}
				}
				
				$this->request->data['User']['password'] = '';

				$entity = __('Question');
				if($params['current'] == $sections[0]) {
					$params['title'] = __('reload-title', $entity);
					$params['message'] = __('reload-connect-message');
					$params['previous'] = -1;
				}
				else if($params['current'] == $sections[1]) {
					$params['title'] = __('reload-title', $entity);
					$params['message'] = __('confirm-message', __('question'));
					$params['previous'] = $sections[0];
				}
				else if($params['current'] == $sections[2]) {
					$params['title'] = __('success-reload-title', $entity);
					$params['message'] = __('success-message');
					$params['previous'] = false;
				}

				$this->set(compact('params', 'sections'));
			}
		}
		else throw new InvalidRequestException();
	}
	
	
	/**
	 * Creates a Question
	 *
	 * @param int $id - the question id.
	 * @param string $operation - the type of operation.
	 */
	private function createQuestion($id=null, $operation) {
		if($id) {
			$this->Question->Answer->deleteAll(array('Answer.question_id' => $this->request->data['Question']['id']));
		}
		
		if($this->Question->saveAll($this->request->data)) {
			if(!$id) {
				$this->request->data['Activity']['id'] = $this->Question->Activity->getInsertID();
				$this->request->data['Question']['id'] = $this->Question->getInsertID();
			}

			// Log Data
			$this->logData('activities', $this->request->data['Activity']['id'], $this->request->data['Activity']['name'], $operation);
			$this->logData($this->params['controller'], $this->request->data['Question']['id'], $this->request->data['Activity']['name'], $operation);
			
			// Save Hints
			if(isset($this->request->data['Hint']) && count($this->request->data['Hint']) > 0) {
				foreach($this->request->data['Hint'] as $key => $val) {
					$this->request->data['Hint'][$key]['activity_id'] = $this->request->data['Activity']['id'];
				}
				$this->Activity->Hint->saveMany($this->request->data['Hint']);
			}

			return true;
		}
		else {
			$this->Session->setFlash(__('error-while-saving', __('question')));
			return false;
		}
	}
	
	
	/**
	 * View Question
	 *
	 * @param int $activity_id
	 * @throws NotFoundException if the question doesn't exist.
	 * @throws InvalidRequestException if the request type is not supported.
	 */
	public function view($activity_id=null) {
		$this->Question->Activity->id = $activity_id;
		if (!$this->Question->Activity->exists()) {
			throw new NotFoundException(__('Invalid Question'));
		}
		
		if($this->RequestHandler->isAjax()) {
			$this->layout = 'modal';
			
			$this->request->data = $this->Question->getData($activity_id);

			if($this->itemBelongsToUser($this->request->data['Activity'])) {
				$params['title'] = $this->request->data['Activity']['name'];
				$params['message'] = __('view-message', strtolower($this->request->data['Type']['name']).' '.__('question'));
				$params['model'] = 'Activity';
				$params['icons'] = $this->getOptions();
				$params['current'] = 'view';
				
				$this->set('params', $params);
			}
		}
		else throw new InvalidRequestException();
	}
}
