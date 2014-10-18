<?php
/**
 * Imports
 */
App::uses('AppController', 'Controller');

/**
 * Resources Controller
 *
 * @package app.Controller
 * @property Resource $Resource
 * @author Bruno Sampaio
 */
class ResourcesController extends AppController {
	
	/**
	 * @var array Components used by this Controller
	 */
	public $components = array('Files', 'LMSServices');
	
	
	/**
	 * @var array Pagination Component Properties
	 */
	public $paginate = array(
			'order' => array('Resource.modified' => 'desc', 'Resource.name' => 'asc')
	);
	
	
	/**
	 * Extends the parent method.
	 */
	public function beforeFilter() {
		parent::beforeFilter();
		
		if($this->request->action == 'add') {
			$this->Security->validatePost = false;
			$this->Security->csrfCheck = false;
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
		return parent::options(array(
			'reload' => array(
				'title' => __('reload-title', __('Resource')),
				'model' => 'Resource',
				'fields' => array('lms_id', 'lms_url', 'external_id'),
				'action' => 'reload',
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
	 * to create a list of resources items.
	 * @param string $keyword - use to filter the search by the name attribute.
	 * @return array - list of values to be sent to an element view.
	 */
	public function listing($keyword='') {
		$options = array(
			'conditions' => array(),
			'recursive' => 0,
			'unbind' => array(
				'belongsTo' => array('LMS', 'User'),
				'hasAndBelongsToMany' => array('Subject', 'Project')
			),
			'use' => array('modal' => true, 'icon' => true),
			'options' => $this->getOptions()
		);
		
		return $this->paginator($this->modelClass, $this->name, $this->Resource, $keyword, $options);
	}
	
	
	/**
	 * Choose Section
	 *
	 * This action prepares the options to be sent to the chooser method.
	 * It is invoked on a situation where the user must select one or more resources to continue.
	 * @return array - list of values to be sent to an element view.
	 */
	public function choose() {
		$options = array();
		
		// Filters Default Values
		$options['filters'] = array('type_id' => null);
		
		// Unbind Options
		$options['unbind'] = array('belongsTo' => array('LMS', 'User'), 'hasAndBelongsToMany' => array('Subject', 'Project'));
		
		// List Options
		$fields = array('Resource.id', 'Resource.name', 'Resource.description', 'Resource.source', 'Resource.lms_id', 'Resource.lms_url', 'Resource.external_id', 'Type.mime', 'Type.icon');
		$conditions['Resource.user_id'] = $this->Auth->user('id');
		$order = array('Resource.name');
		$options['list'] = array('fields' => $fields, 'conditions' => $conditions, 'order' => $order);
		
		// Selected Options
		$options['selected'] = array();
		
		return $this->chooser($this->modelClass, $this->name, $this->Resource, $options);
	}
	
	
	/**
	 * Select Section.
	 *
	 * This action lists the types of resources the user can create.
	 * It is displayed before the add action.
	 *
	 * @throws InvalidRequestException if the request type is not supported.
	 */
	public function select() {
		if($this->RequestHandler->isAjax()) {
			$this->selector(__('Resource'), __('resource'), __('upload'), $this->Resource->Type);
		}
		else throw new InvalidRequestException();
	}
	
	
	/**
	 * Add Resource
	 *
	 * @param int $type_id
	 * @throws InvalidRequestException if the request type is not supported.
	 */
	public function add($type_id=null) {
		if($this->RequestHandler->isAjax()) {
			$this->addOrEdit(null, $type_id);
		}
		else throw new InvalidRequestException();
	}
	
	
	/**
	 * Edit Resource
	 *
	 * @param int $id
	 * @throws NotFoundException if the resource doesn't exist.
	 * @throws InvalidRequestException if the request type is not supported.
	 */
	public function edit($id=null) {
		$this->Resource->id = $id;
		if (!$this->Resource->exists()) {
			throw new NotFoundException(__('Invalid Resource'));
		}
		
		if($this->RequestHandler->isAjax()) {

			// If request is empty get question data
			if(!isset($this->request->data['Resource'])) {
				$this->Resource->unbindModel(array(
					'belongsTo' => array('LMS', 'User'),
					'hasAndBelongsToMany' => array('Subject', 'Project')
				));
				$this->request->data = $this->Resource->read(null, $id);
			}
			
			// Check if resource belongs to current user
			if($this->itemBelongsToUser($this->request->data['Resource'])) {
				$this->addOrEdit($id, $this->request->data['Resource']['type_id']);
			}
		}
		else throw new InvalidRequestException();
	}
	
	
	/**
	 * Processes a add or edit operation.
	 *
	 * @param int $id - the resource id.
	 * @param int $type_id - the resource type id.
	 */
	private function addOrEdit($id=null, $type_id=null) {
		if($type_id) {
			$this->layout = 'modal';
			$sections = array('create basic', 'create file', 'complete');
			$params['current'] = $sections[0];
			
			if(!isset($this->request->data['Type'])) {
				$this->request->data['Type'] = $this->Resource->Type->read(null, $type_id)['Type'];
			}
			
			$storeRequest = $this->request->is('post') || $this->request->is('put');

			if($storeRequest) {
				
				// Determine next section
				$submitted = $this->request->data['Section']['current'];
				if($this->request->data['Section']['previous'] == $submitted) {
					if($submitted == -1) {
						$this->redirect(array('action' => 'select'));
					}
					$params['current'] = $submitted;
				}
				else {
					$this->Resource->set($this->request->data);
					
					if($submitted == $sections[0]) {
							
						if($this->Resource->validates()) {
							
							// Check Resource Name
							$resource = $this->checkItemNameForCurrentUser($this->Resource, $this->request->data['Resource']);
							
							if(!$resource) {
								$params['current'] = $sections[1];
							}
							else {
								$this->Session->setFlash(__('error-already-exists-item', __('resource'), $this->request->data['Resource']['name']));
							}
						}
						else {
							$this->Session->setFlash(__('error-empty-fields'));
						}
					}
					else if($submitted == $sections[1]) {
						$params['current'] = $sections[1];
						$operation = $id? Configure::read('Operations.edit') : Configure::read('Operations.add');
						
						if($this->request->data['File']['source'] == 0) {
							$this->request->data['Resource']['changed'] = false;
							
							// Check File
							if(!empty($this->request->data['Resource']['file']['name'])) {
								
								if($id) {
									$filename = $id;
								}
								else {
									$filename = 'tmp'.date('Y-m-d-His');
								}

								//Upload file to the correspondent folder
								$folder = FILES.Configure::read('Folders.files.resources');
								$file = $this->Files->upload($folder, $filename, $this->request->data['Resource']['file']);

								//Check if there was any error
								if(empty($file['error'])) {
									$oldsource = $this->request->data['Resource']['source'];
									$this->request->data['Resource']['source'] = null;
									$this->request->data['Resource']['file_url'] = $file['name'];

									if($this->createResource($id, $folder, $operation)) {
										$params['current'] = $sections[2];
									}
									else {
										$this->request->data['Resource']['source'] = $oldsource;
									}
								}
								else {
									$this->Session->setFlash($file['error']);
								}
							}
							else if($id) {
								
								if($this->createResource($id, FILES, $operation)) {
									$params['current'] = $sections[2];
								}
							}
							else {
								$this->Session->setFlash(__('error-no-file'));
							}
						}
						else if($this->request->data['File']['source'] == 1) {
							
							if(strlen($this->request->data['Resource']['source']) > 0) {
								$valid = $this->Resource->validateExternalFile();
								
								if(!isset($valid['error'])) {
									$this->request->data['Resource'] = $this->Resource->data['Resource'];
									
									if($id && !$this->request->data['Resource']['external']) {
										$this->request->data['Resource']['changed'] = true;
									}
									
									if($this->createResource($id, FILES, $operation)) {
										$params['current'] = $sections[2];
									}
								}
								else {
									$this->Session->setFlash($valid['error']);
								}
							}
							else {
								$this->Session->setFlash(__('error-empty-fields'));
							}
						}
					}
				}
			}

			// Set Resource Data
			$this->request->data['Resource']['type_id'] = $type_id;
			$this->request->data['Resource']['user_id'] = $this->Auth->user('id');
			
			$methods = array(0 => 'Upload File', 1 => 'Provide file url');
			if(!isset($this->request->data['File']['source'])) {
				if($id) {
					if($this->request->data['Resource']['external']) {
						$this->request->data['File']['source'] = 1;
					}
					else {
						$this->request->data['File']['source'] = 0;
					}
				}
				else {
					$this->request->data['File']['source'] = 0;
				}
			}

			// Set section properties
			$entity = __('Resource');
			if($params['current'] == $sections[0]) {
				$name = $this->request->data['Type']['name'].' '.$entity;
				if($id) {
					$params['title'] = __('create-edit-title', $name);
				}
				else {
					$params['title'] = __('create-add-title', $name);
				}
				$params['message'] = __('create-add-message');
				$params['previous'] = -1;
			}
			else if($params['current'] == $sections[1]) {
				$params['title'] = __('upload-title', $this->request->data['Type']['name']);
				$params['message'] = __('upload-message');
				$params['previous'] = $sections[0];
			}
			else if($params['current'] == $sections[2]) {
				if($id) {
					$params['title'] = __('success-edit-title', $entity);
				}
				else {
					$params['title'] = __('success-add-title', $entity);
				}
				
				$params['message'] = __('success-message');
				$params['previous'] = false;
			}

			$this->set(compact('params', 'sections', 'methods'));
			$this->render('/Resources/add_edit');
		}
		else {
			$this->redirect(array('action' => 'select'));
		}
	}
	
	
	/**
	 * Import Resource
	 *
	 * @throws InvalidRequestException if the request type is not supported.
	 */
	public function import() {
		if($this->RequestHandler->isAjax()) {
			$this->layout = 'modal';
			$sections = array('create connect', 'choose course', 'choose content', 'complete');
			$filter = 'resource';
			$params['current'] = $sections[0];
			
			$actionRequest = $this->request->is('post') || $this->request->is('put');
			
			if($actionRequest) {
				
				// Determine next section
				$submitted = $this->request->data['Section']['current'];
				if($this->request->data['Section']['previous'] == $submitted) {
					if($submitted == -1) {
						$this->redirect(array('action' => 'select'));
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
							if(isset($this->request->data['Content']) && count($this->request->data['Content']) > 0) {
								$ids = array();
								foreach($this->request->data['Content'] as $content) {
									array_push($ids, $content['id']);
								}
								$list = $this->LMSServices->request($this->LMSServices->requests[4], array('ids' => $ids), array(true));

								$this->request->data['Resources'] = array();
								foreach($list as $key => $data) {
									$type = $this->Resource->Type->find('first', array('conditions' => array('mime' => $data['File']['type'])));
									if($type) {
										$data['Resource']['file_url'] = $data['File']['name'];
										$data['Resource']['type_id'] = $type['Type']['id'];
										$data['Resource']['user_id'] = $this->Auth->user('id');

										// Check Resource Name
										$resource = $this->checkItemNameForCurrentUser($this->Resource, $data['Resource']);
										if(!$resource) {
											$this->request->data['Resource'] = $data['Resource'];

											if($this->createResource(null, FILES.Configure::read('Folders.files.resources'), Configure::read('Operations.import'))) {
												$this->request->data['Resources'][$key]['id'] = $this->request->data['Resource']['id'];
												$this->request->data['Resources'][$key]['name'] = $data['Resource']['name'];
											}
										}
										else {
											$this->Session->setFlash(__('error-already-exists-item', __('resource'), $data['Resource']['name']));
										}
									}
								}

								// If at least one is saved
								if(count($this->request->data['Resources']) > 0) {
									$params['current'] = $sections[3];
								}
							}
							else {
								$this->Session->setFlash(__('error-choose-minimum-one', __($filter)));
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
			
			$entity = __('Resource');
			if($params['current'] == $sections[0]) {
				$params['title'] = __('import-title', $entity);
				$params['message'] = __('import-connect-message');
				$params['previous'] = -1;
				
				$this->loadModel('LMS');
				$this->set('lms', $this->LMS->find('list'));
			}
			else if($params['current'] == $sections[1] || $params['current'] == $sections[2]) {
				switch($params['current']) {
					case $sections[1]:
						$params['previous'] = $sections[0];
						$request = $this->LMSServices->requests[0];
						$multiple = false;
						break;
						
					case $sections[2]:
						$params['previous'] = $sections[1];
						$request = $this->LMSServices->requests[1];
						$multiple = true;
						$name = Inflector::pluralize($filter);
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
			else if($params['current'] == $sections[3]) {
				$params['title'] = __('success-import-title', $entity);
				$params['message'] = __('success-message');
				$params['previous'] = false;
			}
			
			$this->set(compact('params', 'sections'));
		}
		else throw new InvalidRequestException();
	}
	
	
	/**
	 * Reload Resource
	 *
	 * @param int $id
	 * @throws NotFoundException if the resource doesn't exist.
	 * @throws InvalidRequestException if the request type is not supported.
	 */
	public function reload($id=null) {
		$this->Resource->id = $id;
		if (!$this->Resource->exists()) {
			throw new NotFoundException(__('Invalid Resource'));
		}
		
		if($this->RequestHandler->isAjax()) {
			
			// If request is empty get question data
			if(!isset($this->request->data['Resource'])) {
				$this->Resource->unbindModel(array(
					'belongsTo' => array('User'),
					'hasAndBelongsToMany' => array('Subject', 'Project')
				));
				$this->request->data = $this->Resource->read(null, $id);
				
				if($this->request->data['Resource']['imported']) {
					$this->request->data['LMS']['url'] = $this->request->data['Resource']['lms_url'];
				}
				else {
					$this->Session->setFlash(__('error-not-imported'));
					throw new MethodNotAllowedException();
				}
			}
			
			// Check if resource belongs to current user
			$hasPermission = $this->itemBelongsToUser($this->request->data['Resource']);

			if($hasPermission) {
				$this->layout = 'modal';
				$sections = array('create connect', 'confirm resource', 'complete');
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

									// Get Resource Info
									$ids = array($this->request->data['Resource']['external_id']);
									$list = $this->LMSServices->request($this->LMSServices->requests[4], array('ids' => $ids), array(false));
									if(count($list) > 0) {
										$this->request->data['Resource'] = array_merge($this->request->data['Resource'], $list[0]['Resource']);
										$this->request->data['File'] = $list[0]['File'];
										$this->request->data['Resource']['source'] = null;
									}
									else {
										$this->Session->setFlash(__('error-lms-external-item-not-found', __('resource')));
									}
									$params['current'] = $sections[1];
								}
							}
							else if($submitted == $sections[1]) {

								// Check Resource Name
								$resource = $this->checkItemNameForCurrentUser($this->Resource, $this->request->data['Resource']);
								if(!$resource) {

									// Get Resource Data
									$folder = FILES.Configure::read('Folders.files.resources');
									$file = $this->Files->download($folder, $id, $this->request->data['File']);

									// If no error with the file
									if(!isset($file['error'])) {
										if($this->createResource($id, $folder, Configure::read('Operations.reload'))) {
											$params['current'] = $sections[2];
										}
									}
									else {
										$this->Session->setFlash($file['error']);
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

				$entity = __('Resource');
				if($params['current'] == $sections[0]) {
					$params['title'] = __('reload-title', $entity);
					$params['message'] = __('reload-connect-message');
					$params['previous'] = -1;
				}
				else if($params['current'] == $sections[1]) {
					$params['title'] = __('reload-title', $entity);
					$params['message'] = __('confirm-message', __('resource'));
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
	 * Creates a Resource
	 *
	 * @param int $id - the resource id.
	 * @param string $folder - the folder to store the resource if it isn't external.
	 * @param string $operation - the type of operation.
	 */
	private function createResource($id=null, $folder=null, $operation) {
		if($this->Resource->saveAll($this->request->data['Resource'])) {
			if(!$id) {
				$this->request->data['Resource']['id'] = $this->Resource->getInsertID();
			}

			if(!$id && isset($this->request->data['Resource']['file_url'])) {
				
				// Rename File
				$this->Files->rename($folder, $this->request->data['Resource']['file_url'], $this->request->data['Resource']['id']);
			}
			else if(isset($this->request->data['Resource']['changed']) && $this->request->data['Resource']['changed']) {
				$this->Files->delete($folder, $this->request->data['Resource']['file_url']);
			}

			// Log Data
			$this->logData($this->params['controller'], $this->request->data['Resource']['id'], $this->request->data['Resource']['name'], $operation);

			return true;
		}
		else {
			if(isset($this->request->data['Resource']['file_url'])) {
				$this->Files->delete($folder, $this->request->data['Resource']['file_url']);
			}
			$this->Session->setFlash(__('error-while-saving', __('resource')));
			return false;
		}
	}
	
	
	/**
	 * View Resource
	 *
	 * @param int $id
	 * @throws NotFoundException if the resource doesn't exist.
	 * @throws InvalidRequestException if the request type is not supported.
	 */
	public function view($id=null) {
		$this->Resource->id = $id;
		if (!$this->Resource->exists()) {
			throw new NotFoundException(__('Invalid Resource'));
		}
		
		if($this->RequestHandler->isAjax()) {
			$this->layout = 'modal';
			
			$this->Resource->unbindModel(array('belongsTo' => array('User'), 'hasAndBelongsToMany' => array('Project')));
			$this->request->data = $this->Resource->read(null, $id);

			if($this->itemBelongsToUser($this->request->data['Resource'])) {
				$params['title'] = $this->request->data['Resource']['name'];
				$params['message'] = __('view-message', __('resource'));
				$params['model'] = $this->modelClass;
				$params['icons'] = $this->getOptions();
				$params['current'] = 'view';
				
				$this->set('params', $params);
			}
		}
		else throw new InvalidRequestException();
	}
	
	
	/**
	 * Choose Resource Type to be added to Project
	 *
	 * @param int $genre_id - project genre id.
	 * @param int $type_id - resource type id.
	 * @throws InvalidRequestException if the request type is not supported.
	 */
	public function choose_type($genre_id, $type_id) {
		$this->loadModel('GenreResource');
		
		if($this->RequestHandler->isAjax() && $this->GenreResource->hasType($genre_id, $type_id)) {			
			$this->layout = 'modal';
			$sections = array('choose');
			$params['current'] = $sections[0];
			$params['previous'] = false;
			
			// Set section properties
			if($params['current'] == $sections[0]) {
				$params['title'] = __('add-title', __('Resource'));
				$params['message'] = __('choose-message', __('resource'));

				$params['filters'] = array();
				$params['filters']['model'] = 'Chooser';
				$params['filters']['url'] = array('controller' => 'resources', 'action' => 'choose');
				$params['filters']['fields'] = array();

				$this->request->data['Chooser']['filters'] = array('type_id' => $type_id);
				$this->request->data['Chooser']['multiple'] = false;
			}
			
			$this->set(compact('params', 'sections'));
		}
		else throw new InvalidRequestException();
	}


	/**
	 * Delete Resource
	 *
	 * @param int $id
	 * @throws InvalidRequestException if the request type is not supported.
	 * @throws NotFoundException if the resource doesn't exist.
	 */
	public function delete($id = null) {
		if (!$this->request->is('post') || $this->RequestHandler->isAjax()) {
			throw new InvalidRequestException();
		}
		
		$this->Resource->id = $id;
		if (!$this->Resource->exists()) {
			throw new NotFoundException(__('Invalid Resource'));
		}
		
		$this->autoRender = false;
		
		$this->Resource->unbindModel(array(
			'belongsTo' => array('LMS', 'User'),
			'hasAndBelongsToMany' => array('Subject', 'Project')
		));
		$this->Resource->read(null, $id);
		
		if($this->itemBelongsToUser($this->Resource->data['Resource'])) {
			if ($this->Resource->delete()) {

				//Deletes File
				if(!$this->Resource->data['Resource']['external']) {
					$this->Files->delete(FILES.Configure::read('Folders.files.resources'), $this->Resource->data['Resource']['id'], false);
				}

				//Log Operation
				$this->logData($this->params['controller'], $id, $this->Resource->data['Resource']['name'], Configure::read('Operations.delete'));
			}
		}
		
		$this->redirect(array('action' => 'index'));
	}
}
