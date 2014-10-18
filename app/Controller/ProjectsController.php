<?php
/**
 * Imports
 */
App::uses('AppController', 'Controller');
App::uses('Folder', 'Utility');

/**
 * Projects Controller
 *
 * @package app.Controller
 * @property Project $Project
 * @author Bruno Sampaio
 */
class ProjectsController extends AppController {
	
	/**
	 * @var array Components used by this Controller
	 */
	public $components = array('Files');
	
	
	/**
	 * @var array Pagination Component Properties
	 */
	public $paginate = array(
			'order' => array('Project.modified' => 'desc', 'Project.name' => 'asc')
	);
	
	
	/**
	 * Extends the parent method.
	 */
	public function beforeFilter() {
		parent::beforeFilter();
		
		$this->Security->requireGet('view');
		$this->Security->requirePost('save');
		
		if($this->request->action == 'save') {
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
			'template' => array(
				'title' => __('Export as Template'),
				'model' => 'Project',
				'fields' => array(),
				'action' => 'template',
				'modal' => true
			),
			'game' => array(
				'title' => __('Export as Game'),
				'model' => 'Project',
				'fields' => array(),
				'action' => 'game',
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
	 * to create a list of projects items.
	 * @param string $keyword - use to filter the search by the name attribute.
	 * @return array - list of values to be sent to an element view.
	 */
	public function listing($keyword='') {
		$options = array(
			'conditions' => array(),
			'recursive' => -1,
			'unbind' => array(),
			'use' => array('modal' => false, 'icon' => false),
			'options' => $this->getOptions()
		);
		
		return $this->paginator($this->modelClass, $this->name, $this->Project, $keyword, $options);
	}
	
	
	/**
	 * Choose Section
	 *
	 * This action prepares the options to be sent to the chooser method.
	 * It is invoked on a situation where the user must select one or more projects to continue.
	 * @return array - list of values to be sent to an element view.
	 */
	public function choose() {
		$options = array();
		
		// Filters Default Values
		$options['filters'] = array();
		
		// Unbind Options
		$options['unbind'] = array('belongsTo' => array('Genre', 'User'), 'hasAndBelongsToMany' => array('Activity', 'Resource'));
		
		// List Options
		$fields = array('Project.id', 'Project.name', 'Project.description');
		$conditions['Project.user_id'] = $this->Auth->user('id');
		$order = array('Project.name', 'Project.modified');
		$options['list'] = array('fields' => $fields, 'conditions' => $conditions, 'order' => $order);
		
		// Selected Options
		$options['selected'] = array();
		
		return $this->chooser($this->modelClass, $this->name, $this->Project, $options);
	}
	
	
	/**
	 * Add Project
	 *
	 * @throws InvalidRequestException if the request type is not supported.
	 */
	public function add() {
		if($this->RequestHandler->isAjax()) {
			$this->layout = 'modal';
			$sections = array('create basic', 'choose', 'complete');
			$params['current'] = $sections[0];

			if($this->request->is('post')) {
				$submitted = $this->request->data['Section']['current'];

				if($this->request->data['Section']['previous'] == $submitted) {
					$params['current'] = $submitted;
				}
				else {
					$params['current'] = $submitted;
					$this->Project->set($this->request->data['Project']);

					if($submitted == $sections[0]) {

						if ($this->Project->validates()) {

							// Check Project Name
							$project = $this->checkItemNameForCurrentUser($this->Project, $this->request->data['Project']);

							if(!$project) {
								$params['current'] = $sections[1];
							}
							else {
								$this->Session->setFlash(__('error-already-exists-item', __('project'), $this->request->data['Project']['name']));
							}
						}
						else {
						    $this->Session->setFlash(__('error-empty-fields'));
						}
					}
					else if($submitted == $sections[1]) {
						if(isset($this->request->data['Template']['id']) && $this->request->data['Template']['id']) {
							
							// Set project genre id
							$this->Project->data['Project']['genre_id'] = $this->request->data['Chooser']['filters']['genre_id'];
							
							if($this->Project->save()) {
								$this->request->data['Project']['id'] = $this->Project->getInsertID();

								$ext = '.xml';
								$template = FILES.Configure::read('Folders.files.templates').DS.$this->request->data['Template']['id'].$ext;

								if(file_exists($template)) {
									$project = FILES.Configure::read('Folders.files.projects').DS.$this->request->data['Project']['id'].$ext;

									if(copy($template, $project)) {

										// Log Data
										$operation = Configure::read('Operations.add');
										$this->logData($this->params['controller'], $this->request->data['Project']['id'], $this->request->data['Project']['name'], $operation);

										$params['current'] = $sections[2];
									}
									else {
										$this->Project->delete($this->request->data['Project']['id']);
										$this->Session->setFlash(__('error-no-template'));
									}
								}
								else {
									$this->Project->delete($this->request->data['Project']['id']);
									$this->Session->setFlash(__('error-no-template'));
								}
							}
							else {
								$this->Session->setFlash(__('error-while-saving', __('project')));
							}
						}
						else {
							$this->Session->setFlash(__('error-choose-one', __('template')));
						}
					}
				}
			}

			// Set user id
			$this->request->data['Project']['user_id'] = $this->Auth->user('id');
			
			// Set section properties
			$entity = __('Project');
			if($params['current'] == $sections[0]) {
				$params['title'] = __('create-add-title', $entity);
				$params['message'] = __('create-add-message');
				$params['previous'] = false;
			}
			else if($params['current'] == $sections[1]) {
				$params['title'] = __('create-add-title', $entity);
				$params['message'] = __('choose-message', __('template'));
				$params['previous'] = $sections[0];

				$this->loadModel('GameMode');
				$this->loadModel('GameGenre');
				$modes = $this->GameMode->find('list');
				$options = $this->GameGenre->find('list', array('fields' => array('GameGenre.id', 'GameGenre.name', 'GameGenre.mode_id'), 'order' => array('GameGenre.mode_id')));
				
				foreach($modes as $key => $val) {
					$options[$val] = $options[$key];
					unset($options[$key]);
				}

				$params['filters'] = array();
				$params['filters']['model'] = 'Chooser';
				$params['filters']['url'] = array('controller' => 'templates', 'action' => 'choose');
				$params['filters']['fields'] = array(
					0 => array('name' => 'Chooser.filters.genre_id', 'label' => __('Genre'), 'type' => 'select', 'options' => $options, 'empty' => false),
					1 => array('name' => 'Chooser.multiple', 'type' => 'hidden')
				);

				if(isset($this->request->data['Chooser']['filters']['genre_id'])) {
					$params['filters']['fields'][0]['selected'] = $this->request->data['Chooser']['filters']['genre_id'];
				}
				$this->request->data['Chooser']['multiple'] = false;
			}
			else if($params['current'] == $sections[2]) {
				$params['title'] = __('success-add-title', $entity);
				$params['message'] = __('success-message');
				$params['previous'] = false;
			}

			$this->set(compact('params', 'sections'));
		}
		else throw new InvalidRequestException();
	}
	
	
	/**
	 * Edit Project
	 *
	 * @param int $id
	 * @throws NotFoundException if the project doesn't exist.
	 * @throws InvalidRequestException if the request type is not supported.
	 */
	public function edit($id=null) {
		if($this->RequestHandler->isAjax()) {
			$this->Project->id = $id;
			if (!$this->Project->exists()) {
				throw new NotFoundException(__('Invalid Project'));
			}

			if(!isset($this->request->data['Project'])) {
				$this->request->data = $this->Project->read(null, $id);
			}
			
			if($this->itemBelongsToUser($this->request->data['Project'])) {
				$this->layout = 'modal';
				$sections = array('create basic', 'complete');
				$params['current'] = $sections[0];

				if ($this->request->is('post') || $this->request->is('put')) {
					$submitted = $this->request->data['Section']['current'];

					if($submitted == $sections[0]) {
						$this->Project->set($this->request->data['Project']);

						// Check Project Name
						$project = $this->checkItemNameForCurrentUser($this->Project, $this->request->data['Project']);

						if(!$project) {
							if($this->Project->save($this->Project->data, true, array('name', 'description'))) {

								// Log Data
								$operation = Configure::read('Operations.edit');
								$this->logData($this->params['controller'], $this->request->data['Project']['id'], $this->request->data['Project']['name'], $operation);
								
								$params['current'] = $sections[1];
							}
							else {
								$this->Session->setFlash(__('error-empty-fields'));
							}
						}
						else {
							$this->Session->setFlash(__('error-already-exists-item', __('project'), $this->request->data['Project']['name']));
						}
					}
				}

				// Set user id
				$this->request->data['Project']['user_id'] = $this->Auth->user('id');

				// Set section properties
				$entity = __('Project');
				if($params['current'] == $sections[0]) {
					$params['title'] = __('create-edit-title', $entity);
					$params['message'] = __('create-edit-message');
				}
				else if($params['current'] == $sections[1]) {
					$params['title'] = __('success-edit-title', $entity);
					$params['message'] = __('success-message');
				}

				$this->set(compact('params', 'sections'));
			}
		}
		else throw new InvalidRequestException();
	}
	

	/**
	 * View Project
	 *
	 * @param int $id
	 * @throws NotFoundException if the project doesn't exist.
	 */
	public function view($id=null) {
		if(!$this->RequestHandler->isAjax()) {
			$this->Project->id = $id;
			if (!$this->Project->exists()) {
				throw new NotFoundException(__('Invalid project'));
			}
			
			// Get Project
			$info = $this->Project->get($id, array('Project.name', 'Genre.instructions', 'Genre.gameover'));
			$project = &$info['Project'];

			if($this->itemBelongsToUser($project)) {
				
				try {
					
					// Load Converter Component
					$this->Converter = $this->Components->load($info['Genre']['code'].'Converter', array('mode' => $info['Genre']['mode_id'], 'genre' => $project['genre_id']));
					
					// Load Project Data
					$data = $this->Converter->xml2project($project['file']);
					
					// Get Project Defaults
					$defaults = $this->Converter->getProjectDefaults();
					
					// Get Project Files
					$files = $this->Converter->getFiles($this->base, false);
					$defaults['resources'] = $files['client'];
					
					// Set View Variables
					$this->set(compact('project', 'data', 'defaults'));
					$this->set('genre', $info['Genre']);
					$this->set('files', $files['server']);
					$this->set('title_for_layout', $project['name']);

				} catch (Exception $e) {
					$this->Session->setFlash(__('error-corrupted-project'));
					$this->redirect(array('action' => 'index'));
				}
			}
		}
	}
	
	
	/**
	 * Choose Project to Open
	 *
	 * @throws InvalidRequestException if the request type is not supported.
	 */
	public function open() {
		if($this->RequestHandler->isAjax()) {
			$this->layout = 'modal';
			$sections = array('choose');
			$params['current'] = $sections[0];
			$params['previous'] = false;
			
			// Set section properties
			if($params['current'] == $sections[0]) {
				$params['title'] = __('open-title', __('Project'));
				$params['message'] = __('choose-message', __('project'));

				$params['filters'] = array();
				$params['filters']['model'] = 'Chooser';
				$params['filters']['url'] = array('controller' => 'projects', 'action' => 'choose');
				$params['filters']['fields'] = array();
				
				$this->request->data['Chooser']['multiple'] = false;
			}

			$this->set(compact('params', 'sections'));
		}
		else throw new InvalidRequestException();
	}
	
	
	/**
	 * Load Project Contents
	 *
	 * @throws InvalidRequestException if the request type is not supported.
	 */
	public function load_contents() {
		if($this->RequestHandler->isAjax()) {
			$this->autoRender = false;
			$this->layout = 'ajax';
			
			if(isset($this->request->query['load'])) {
				
				$data = array();
				$toLoad = json_decode($this->request->query['load']);
				
				// Load Activities Data
				if(isset($toLoad->activities)) {
					
					// Get Activities Ids
					$ids = array(0 => array(), 1 => array());
					foreach($toLoad->activities as $key => $value) {
						array_push($ids[$value->type], $key);
					}
					
					// Get Questions Data
					if(count($ids[0]) > 0) {
						$list = $this->Project->Activity->Question->getProjectData($ids[0], $this->Auth->user('id'));
						
						$data['activities'] = $list;
					}
					
					// Get Groups Data
					if(count($ids[1]) > 0) {
						$list = $this->Project->Activity->QuestionsGroup->getProjectData($ids[1], $this->Auth->user('id'));
						
						if(isset($data['activities'])) {
							$data['activities']+= $list;
						}
						else $data['activities'] = $list;
					}
				}
				
				// Load Resources
				if(isset($toLoad->resources)) {
					
					// Get Resources Ids
					$ids = array();
					foreach($toLoad->resources as $key => $value) {
						array_push($ids, $key);
					}
					
					// Get Resources Data
					if(count($ids) > 0) {
						$list = $this->Project->Resource->getProjectData($ids, $this->Auth->user('id'), $this->base.'/'.FILES_URL);
						
						$data['resources'] = $list;
					}
				}
				
				return json_encode($data);
			}
		}
		else throw new InvalidRequestException();
	}
	
	
	/**
	 * Save Project
	 *
	 * @param int $id - project id.
	 * @throws NotFoundException if the project doesn't exist.
	 * @throws InvalidRequestException if the request type is not supported.
	 */
	public function save($id=null) {
		if($this->RequestHandler->isAjax()) {
			$this->autoRender = false;
			
			$this->Project->id = $id;
			if (!$this->Project->exists()) {
				throw new NotFoundException(__('Invalid project'));
			}
			
			// Get Project
			$info = $this->Project->get($id);
			$project = &$info['Project'];

			if($this->itemBelongsToUser($project)) {
				$data = json_decode($this->request->data['save']);

				// Update XML Data
				$this->Converter = $this->Components->load($info['Genre']['code'].'Converter', array('mode' => $info['Genre']['mode_id'], 'genre' => $project['genre_id']));
				$results = $this->Converter->json2xml($project['file'], $data);
				
				// Get Results
				$used = &$results['used'];
				$errors = &$results['errors'];
				
				// Set Data to Modify
				$save = array('Project' => $project);
				if($used['resources']) $save['Resource'] = $used['resources'];
				if($used['activities']) $save['Activity'] = $used['activities'];
				
				// Store used resources and activities and updated project modified date
				try {
					$this->Project->save($save, true, array('Project.modified'));
				}
				catch(Exception $e) {
					array_push($errors, __('error-inexistent-elements'));
				}
				
				// Display Errors
				if(count($errors) > 0) {
					$this->layout = 'modal';
					
					$sections = array('error');
					$params['current'] = $sections[0];
					$params['previous'] = false;
					
					$params['title'] = __('error-title', __('Save'));
					$params['message'] = __('error-project-message', __('saving'));
					
					$this->set(compact('params', 'sections'));
					$this->set('errors', $results['errors']);
					
					$this->render('/Projects/save');
				}
			}
		}
		else throw new InvalidRequestException();
	}
	
	
	/**
	 * Export Project as Game
	 *
	 * @param int $id
	 */
	public function template($id=null) {
		$this->redirect(array('controller' => 'templates', 'action' => 'add', $id));
	}

	
	/**
	 * Export Project as Game
	 *
	 * @param int $id
	 */
	public function game($id=null) {
		$this->redirect(array('controller' => 'games', 'action' => 'add', $id));
	}
	

	/**
	 * Delete Project
	 *
	 * @param int $id
	 * @throws InvalidRequestException if the request type is not supported.
	 * @throws NotFoundException if the project doesn't exist.
	 */
	public function delete($id = null) {
		if (!$this->request->is('post') || $this->RequestHandler->isAjax()) {
			throw new InvalidRequestException();
		}
		
		$this->Project->id = $id;
		if (!$this->Project->exists()) {
			throw new NotFoundException(__('Invalid Project'));
		}
		
		$this->autoRender = false;
		
		$this->Project->recursive = -1;
		$this->Project->read(null, $id);
		
		if($this->itemBelongsToUser($this->Project->data['Project'])) {
			if ($this->Project->delete()) {

				//Deletes XML File
				$this->Files->delete(FILES.Configure::read('Folders.files.projects'), $this->Project->data['Project']['id'].'.xml');

				//Log Operation
				$this->logData($this->params['controller'], $id, $this->Project->data['Project']['name'], Configure::read('Operations.delete'));
			}
		}
		
		$this->redirect(array('action' => 'index'));
	}
	
	
}
