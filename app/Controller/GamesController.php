<?php
/**
 * Imports
 */
App::uses('AppController', 'Controller');
App::uses('Folder', 'Utility');
App::uses('File', 'Utility');

/**
 * Games Controller
 *
 * @package app.Controller
 * @property Game $Game
 * @author Bruno Sampaio
 */
class GamesController extends AppController {
	
	/**
	 * @var array Components used by this Controller
	 */
	public $components = array('Files');
	
	
	/**
	 * @var array Pagination Component Properties
	 */
	public $paginate = array(
			'order' => array('Game.modified' => 'desc', 'Game.name' => 'asc')
	);
	
	
	/**
	 * Extends the parent method.
	 */
	public function beforeFilter() {
		parent::beforeFilter();
		
		$this->Auth->allow(array('view', 'play', 'logs'));
		
		$this->Security->requireGet('view');
		$this->Security->requireGet('logs');
		
		if($this->request->action == 'play') {
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
			'play' => array(
				'title' => __('Play Game'),
				'model' => 'Game',
				'fields' => array(),
				'action' => 'play',
				'modal' => false
			),
			'logs' => array(
				'title' => __('Sessions Logs'),
				'model' => 'Game',
				'fields' => array(),
				'action' => 'logs',
				'modal' => false
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
	 * to create a list of games items.
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
		
		return $this->paginator($this->modelClass, $this->name, $this->Game, $keyword, $options);
	}
	
	
	/**
	 * Add Game from Project
	 * 
	 * @param int $project_id - the identifier for the project from which this game must be generated.
	 * @throws NotFoundException if the project doesn't exist.
	 * @throws InvalidRequestException if the request type is not supported.
	 */
	public function add($project_id) {
		$this->loadModel('Project');
		$this->Project->id = $project_id;
		if (!$this->Project->exists()) {
			throw new NotFoundException(__('Invalid project'));
		}
		
		if($this->RequestHandler->isAjax()) {
			$this->autoRender = false;
			
			// Get Project
			$project = $this->Project->get($project_id);
			
			if(!isset($this->request->data['Game'])) {
				$this->request->data['Game'] = array('icon_url' => 0);
			}
			
			// Set Project and Genre Id
			$this->request->data['Project']['id'] = $project_id;
			
			// Check if game belongs to current 
			if($this->itemBelongsToUser($project['Project'])) {
				$this->addOrEdit(null, $project);
			}
		}
		else throw new InvalidRequestException();
	}
	
	
	/**
	 * Edit Game
	 *
	 * @param int $id
	 * @throws NotFoundException if the game doesn't exist.
	 * @throws InvalidRequestException if the request type is not supported.
	 */
	public function edit($id=null) {
		if($this->RequestHandler->isAjax()) {
			$this->Game->id = $id;
			if (!$this->Game->exists()) {
				throw new NotFoundException(__('Invalid Game'));
			}

			if(!isset($this->request->data['Game'])) {
				$this->request->data = $this->Game->read(null, $id);
				
				if(!$this->request->data['Game']['icon'])
					$this->request->data['Game']['icon_url'] = null;
			}
			
			// Check if game belongs to current user
			if($this->itemBelongsToUser($this->request->data['Game'])) {
				$this->addOrEdit($id, null);
			}
		}
		else throw new InvalidRequestException();
	}
	
	
	/**
	 * Processes a add or edit operations.
	 * 
	 * @param int $id - the game id.
	 * @param array $project - the project data.
	 */
	private function addOrEdit($id, $project) {
		$this->layout = 'modal';
		$sections = array('info', 'create basic', 'create file', 'complete', 'error');
		$params['current'] = $id? $sections[1] : $sections[0];
		$action = $id? 'edit' : 'add';

		// Set icon
		if($this->request->data['Game']['icon_url']) {
			$this->request->data['Game']['icon'] = 1;
		}
		else {
			$this->request->data['Game']['icon'] = 0;
		}

		// Set user id
		$this->request->data['Game']['user_id'] = $this->Auth->user('id');

		if($this->request->is('post') || $this->request->is('put')) {

			// Determine next section
			$submitted = $this->request->data['Section']['current'];
			if($this->request->data['Section']['previous'] == $submitted) {
				$params['current'] = $submitted;
			}
			else {
				if($submitted == $sections[0]) {
					$params['current'] = $sections[1];
				}
				else if($submitted == $sections[1]) {
					$this->Game->set($this->request->data);
					$params['current'] = $sections[1];

					if($this->Game->validates(array('fieldList' => array('name', 'description')))) {

						// Check Game Name
						$game = $this->checkItemNameForCurrentUser($this->Game, $this->request->data['Game']);

						if(!$game) {
							$params['current'] = $sections[2];
						}
						else {
							$this->Session->setFlash(__('error-already-exists-item', __('game'), $this->request->data['Game']['name']));
						}
					}
					else {
						$this->Session->setFlash(__('error-empty-fields'));
					}
				}
				else if($submitted == $sections[2]) {
					$params['current'] = $sections[2];
					$gameData = null;
					$errors = array();

					if(!$id && $project) {

						// Load & Validate XML Data
						$this->Converter = $this->Components->load($project['Genre']['code'].'Converter', array(
							'mode' => $project['Genre']['mode_id'],
							'genre' => $project['Project']['genre_id']
						));
						$gameData = $this->Converter->xml2game($project['Project']['file']);

						// Check if there are validation errors
						if(count($gameData['errors']) == 0) {

							// Set Game Fields
							$this->request->data['Game']['genre_id'] = $project['Project']['genre_id'];
							$this->request->data['Game']['resource_key'] = '';
							$this->request->data['Game']['secret'] = '';
						}
						else {
							$errors = $gameData['errors'];
						}
					}

					// If no errors
					if(count($errors) == 0) {
						$folder = false; // Determines if game folder was created
						$invalid = false; // Determines if 
						$saved = true;

						// Set Path and File name
						$iconFolder = FILES.Configure::read('Folders.files.games');
						$filename = 'icon';
						if($id) $iconFolder.= DS.$id;
						else $filename.= time();

						// Check File
						if(!empty($this->request->data['Game']['file']['name'])) {

							//Upload file to the correspondent folder
							$file = $this->Files->upload($iconFolder, $filename, $this->request->data['Game']['file']);

							//Check if there was any error
							if(empty($file['error'])) {
								$this->request->data['Game']['icon'] = 1;
								$this->request->data['Game']['icon_url'] = $file['name'];
							}
							else {
								$invalid =  $file['error'];

								if($this->request->data['Game']['icon_url']) {
									$this->request->data['Game']['icon'] = 1;
								}
								else {
									$this->request->data['Game']['icon'] = 0;
								}
							}
						}

						// Save Game
						if(!$invalid) {
							try {

								$fieldList = $id? array('name', 'description', 'visibility_id', 'icon') : array();
								if($this->Game->save($this->request->data['Game'], true, $fieldList)) {

									if(!$id) {
										$this->request->data['Game']['id'] = $this->Game->getInsertID();
										$newId = $this->request->data['Game']['id'];

										// Create Key and Secret
										$key = str_replace(' ', '_', strtolower($this->request->data['Game']['name'])).'_'.$newId;
										$this->request->data['Game']['resource_key'] = $key;
										$this->request->data['Game']['secret'] = Security::hash($key, 'sha1', true);
										$this->request->data['Reference'] = array('id' => $newId);

										// Save Reference, Key and Secret
										if($this->Game->save($this->request->data['Game'], true, array('resource_key', 'secret')) && 
											$this->Game->Reference->save($this->request->data['Reference'])) {

											// Create Folder
											$folder = $iconFolder.DS.$newId;
											if((new Folder())->create($folder, 0755)) {
												$gameData['server']['load'] = array();
												$usedData = &$gameData['used'];

												$activitiesIds =& $usedData['activities'];
												$resourcesIds =& $usedData['resources'];
												$ARData = array();
												$helpsData =& $usedData['helps'];
												$userId = $this->Auth->user('id');

												/*
												 * REPLICATE ACTIVITIES
												 */
												$activitiesData = array();

												// If Project has Questions
												if($saved && isset($activitiesIds['question'])) {
													$questionsModel = $this->Project->Activity->Question;
													$groupsModel = $this->Project->Activity->QuestionsGroup;

													$questionsIds =& $activitiesIds['question'][0];
													$groupsIds =& $activitiesIds['question'][1];
													$haveHints = isset($helpsData['hints']) && isset($helpsData['hints']['question']);
													$haveResources = isset($helpsData['resource']) && isset($helpsData['resource']['question']);

													// Set Questions Hints List
													$hintsList = null;
													if($haveHints && isset($helpsData['hints']['question'][0]) && count($helpsData['hints']['question'][0]) > 0) {
														$hintsList =& $helpsData['hints']['question'][0];
													}

													// Set Questions Resources List
													$resourcesList = null;
													if($haveResources && isset($helpsData['resource']['question'][0]) && count($helpsData['resource']['question'][0]) > 0) {
														$resourcesList =& $helpsData['resource']['question'][0];
													}

													if(count($groupsIds) > 0) {

														// Get All Groups Hints and Resources Ids List
														$groupsHints = false;
														$groupsResources = false;
														if($haveHints && isset($helpsData['hints']['question'][1]) && count($helpsData['hints']['question'][1]) > 0) {
															$groupsHints = array_keys($helpsData['hints']['question'][1]);
														}
														if($haveResources && isset($helpsData['resource']['question'][1]) && count($helpsData['resource']['question'][1]) > 0) {
															$groupsResources = array_keys($helpsData['resource']['question'][1]);
														}

														// Get All Questions from each Group
														$groupsQuestions = $groupsModel->getGameData(array_keys($groupsIds), $groupsHints, $groupsResources, $userId);

														// Set Questions to Load
														foreach($groupsQuestions['questions'] as &$q) {

															// Add Questions to Load
															$questionsIds[$q['Question']['activity_id']] = true;

															// Set Group Question
															$groupId = $q['QuestionsGroup']['activity_id'];
															if(!is_array($groupsIds[$groupId])) {
																$groupsIds[$groupId] = array();
															}
															$groupsIds[$groupId][] = (int) $q['Question']['activity_id'];
														}

														// Add Groups Hints Ids to Questions Hints Ids List
														if(count($groupsQuestions['hints']) > 0) {
															if(is_null($hintsList)) {
																$helpsData['hints']['question'][0] = array();
																$hintsList =& $helpsData['hints']['question'][0];
															}

															foreach($groupsQuestions['hints'] as &$h) {
																$hintsList[$h['Hint']['id']] = true;
															}
														}

														// Add Groups Resources Ids to Questions Resources Ids List
														if(count($groupsQuestions['resources']) > 0) {
															if(is_null($resourcesList)) {
																$helpsData['resource']['question'][0] = array();
																$resourcesList =& $helpsData['resource']['question'][0];
															}

															foreach($groupsQuestions['resources'] as &$r) {
																$resourcesList[$r['Resource']['id']] = true;
																$resourcesIds[$r['Resource']['id']] = true;
															}
														}
													}
													else {
														unset($activitiesIds['question'][1]);
													}

													// Get Questions, Hints and Resources Distinct Ids
													$activitiesIds['question'][0] = array_keys($questionsIds);
													if(!is_null($hintsList)) $hintsList = array_keys($hintsList);
													if(!is_null($resourcesList)) $resourcesList = array_keys($resourcesList);

													// Get Questions Game Data
													$questionsModel->getGameData($questionsIds, $hintsList, $resourcesList, $userId, $newId, $activitiesData, $ARData);
												}

												// Replicate Actvities Data
												$newActivitiesIds = array();
												$activitiesModel = $this->Game->Reference->Activity;
												foreach($activitiesData as $item) {
													if($activitiesModel->saveAll($item, array('deep' => true))) {
														$newActivitiesIds[$item['Activity']['original_id']] = $activitiesModel->getLastInsertId();
													}
													else {
														$saved = false;
														break;
													}
												}

												// Set Activities Server Data
												$gameData['server']['load']['activities'] =& $activitiesIds;


												/*
												 * REPLICATE RESOURCES
												 */

												// If Project has Resources
												$newResourcesIds = array();
												if($saved && count($resourcesIds) > 0) {

													// Get Resources Distinct Ids
													$usedData['resources'] = array_keys($resourcesIds);

													// Get Resources Game Data
													$resourcesData = $this->Project->Resource->getGameData($resourcesIds, $userId, $newId);

													// Replicate Resources
													$resourcesModel = $this->Game->Reference->Resource;
													foreach($resourcesData as $item) {
														if($resourcesModel->saveAll($item)) {
															$newResourcesIds[$item['Resource']['original_id']] = $resourcesModel->getLastInsertId();
														}
														else {
															$saved = false;
															break;
														}
													}

													if($saved) {

														// Create Resources Folder
														if((new Folder())->create($folder.DS.'resources', 0755)) {

															// Copy Resources Files
															foreach($resourcesData as &$item) {
																if(!$item['Resource']['external']) {
																	if(!copy(FILES.$item['Resource']['url'], $folder.DS.$item['Resource']['url'])) {
																		$saved = false; break;
																	}
																}
															}

															// Set Resources Server Data
															$gameData['server']['load']['resources'] =& $resourcesIds;
														}
														else $saved = false;
													}
												}


												/*
												 * REPLICATE ACTIVITIES RESOURCES
												 */

												// If activities have resources associated
												if($saved && count($ARData) > 0) {
													$this->loadModel('GameActivityResource');
													foreach($ARData as &$item) {
														$item['GameActivityResource']['activity_id'] = $newActivitiesIds[$item['GameActivityResource']['activity_id']];
														$item['GameActivityResource']['resource_id'] = $newResourcesIds[$item['GameActivityResource']['resource_id']];
														if(!$this->GameActivityResource->saveAll($item)) {
															$saved = false;
															break;
														}
													}
												}


												/*
												 * CREATE CLIENT AND SERVER JSON FILES
												 */

												if($saved) {

													// Create Data Files
													$this->Files->create($folder.DS.'client.json', json_encode($gameData['client']));
													$this->Files->create($folder.DS.'server.json', json_encode($gameData['server']));

													// Move Icon
													if($this->request->data['Game']['icon']) {
														$oldIconFileName = $this->request->data['Game']['icon_url'];
														if($this->Files->move($iconFolder.DS.$oldIconFileName, $folder.DS.$oldIconFileName)) {
															$this->Files->rename($folder, $oldIconFileName, 'icon');
														}
														else $saved = false;
													}
												}
											}
											else $saved = false;
										}
										else $saved = false;
									}

									if($saved) {

										// Remove Icon
										if($id && !$this->request->data['Game']['icon']) {
											$this->Files->delete($iconFolder, $filename, false);
										}

										// Log Data
										$operation = Configure::read("Operations.$action");
										$this->logData($this->params['controller'], $this->request->data['Game']['id'], $this->request->data['Game']['name'], $operation);

										$params['current'] = $sections[3];
									}
								}
								else {
									$saved = false;
									$invalid = __('error-empty-fields');
								}
							}
							catch(Exception $e) {
								$saved = false;
							}
						}

						// If a error ocurred while saving
						if(!$saved) {
							if(!$id) {

								// Delete game from Database
								if($this->request->data['Game']['id']) {
									$this->Game->delete($this->request->data['Game']['id']);
								}

								// Delete game icon if any
								if($this->request->data['Game']['icon']) {
									$this->Files->delete($iconFolder, $filename, false);
								}

								// Delete game folder
								if($folder) {
									(new Folder())->delete($folder);
								}
							}

							if($invalid) $this->Session->setFlash($invalid);
							else array_push($errors, __('error-while-saving-export'));
						}
					}

					// If there are errors
					if(count($errors) > 0) {
						$params['current'] = $sections[4];
						$this->set('errors', $errors);
					}
				}
			}
		}

		// Set section properties
		$entity = __('Game');
		if($params['current'] == $sections[0]) {
			$params['title'] = __("create-$action-title", $entity);
			$params['message'] = __("export-message");
			$params['previous'] = -1;
		}
		if($params['current'] == $sections[1] || $params['current'] == $sections[2]) {
			$params['title'] = __("create-$action-title", $entity);
			$params['message'] = __("create-$action-message");

			if($params['current'] == $sections[2]) {
				$params['previous'] = $sections[1];
				$visibilities = $this->Game->Visibility->getTypesList();
				$this->set('visibilities', $visibilities);
			}
			else $params['previous'] = $sections[0];
		}
		else if($params['current'] == $sections[3]) {
			$params['title'] = __("success-$action-title", $entity);
			$params['message'] = __('success-message');
			$params['previous'] = false;
		}
		else if($params['current'] == $sections[4]) {
			$params['title'] = __('error-title', __('Export'));
			$params['message'] = __('error-project-message', __('exporting'));
			$params['previous'] = false;
		}

		$this->set(compact('params', 'sections'));
		$this->render('/Games/add_edit');
	}


	/**
	 * View Game
	 *
	 * @param int $id
	 * @throws NotFoundException if the game doesn't exist.
	 */
	public function view($id=null) {
		$this->Game->id = $id;
		if (!$this->Game->exists()) {
			throw new NotFoundException(__('Invalid game'));
		}
		
		$fields = array(
			'Game.id', 'Game.name', 'Game.description', 'Game.icon', 'Game.resource_key', 'Game.secret', 'Game.visibility_id', 'Game.user_id', 'Genre.name', 'Visibility.name'
		);
		
		// Get Game Data
		$this->Game->unbindModel(array('hasOne' => array('Reference')));
		$this->request->data = $this->Game->read($fields, $id);
		
		// Check if game is visible to the current user
		if($this->itemIsVisibleToUser($this->Game->data['Game'])) {
			$this->request->data['User'] = $this->Game->User->read(array('name'), $this->Game->data['Game']['user_id'])['User'];
			
			$this->set('title_for_layout', __('Game Data'));
			$this->set('options', $this->getOptions());
		}
	}
	
	
	/**
	 * Play Game
	 *
	 * @param int $id
	 * @throws NotFoundException if the game doesn't exist.
	 */
	public function play($id=null) {
		$data = array();
		$player = array('id' => 0, 'name' => '', 'avatar' => '', 'instance' => 0, 'lms' => 0, 'contextId' => 0, 'contextName' => 0, 'outcome' => 0, 'gameId' => 0, 'userId' => 0, 'genreCode' => '');
		$error = false;
		
		$fields = array('Game.id', 'Game.name', 'Game.icon', 'Game.genre_id', 'Genre.instructions', 'Genre.gameover', 'Genre.code', 'Genre.mode_id');
		$unbind = array('belongsTo' => array('Visibility', 'User'), 'hasOne' => array('Reference'), 'hasAndBelongsToMany' => array('Subject'));
		
		if ($this->request->is('post')) {
			$requestParams = $this->data;
			$this->Game->unbindModel($unbind);
			
			// Validate LTI Request
			$this->LMSServices = $this->Components->load('LMSServices');
			$requestData = $this->LMSServices->validateLTIRequest($this->Game, 'resource_key', 'secret', $requestParams, $fields);
			$error =& $requestData['error'];
			
			if($requestData['entity'] && !$error) {
				$game =& $requestData['entity'];
				
				// Set Request Id
				if(isset($requestParams['lis_result_sourcedid'])) {
					
					// Set Instance Id
					$player['instance'] = $requestParams['lis_result_sourcedid'];
					
					// Set LMS URL
					$lms = array('SessionLMS' => array('url' => 0, 'outcome' => 0));
					if(isset($requestParams['tool_consumer_instance_url'])) {
						$lms['SessionLMS']['url'] = $requestParams['tool_consumer_instance_url'];
					}
					else if(isset($requestParams['tool_consumer_instance_guid'])) {
						$lms['SessionLMS']['url'] = 'http://'.$requestParams['tool_consumer_instance_guid'];
					}
					else $error = __('error-lti-no-lms-url');
					
					// Set LMS Outcome URL
					if(isset($requestParams['lis_outcome_service_url'])) {
						$lms['SessionLMS']['outcome'] = $requestParams['lis_outcome_service_url'];
					}
					else $error = __('error-lti-no-outcome-url');
					
					
					// Set LMS Context Id
					if(isset($requestParams['context_id'])) {
						$player['contextId'] = $requestParams['context_id'];
					}
					else $error = __('error-lti-no-context-id');
					
					// Set LMS Context Name
					if(isset($requestParams['context_label'])) {
						$player['contextName'] = $requestParams['context_label'];
					}
					else if(isset($requestParams['context_title'])) {
						$player['contextName'] = $requestParams['context_title'];
					}
					
					
					// Set Player Id
					if(isset($requestParams['user_id'])) {
						$player['userId'] = $requestParams['user_id'];
					}
					else $error = __('error-lti-no-user-id');
					
					
					// Set Player Name
					if(isset($requestParams['lis_person_name_full'])) {
						$player['name'] = $requestParams['lis_person_name_full'];
					}
					else if(isset($requestParams['lis_person_name_given']) && isset($requestParams['lis_person_name_family'])) {
						$player['name'] = $requestParams['lis_person_name_given'] . ' ' . $requestParams['lis_person_name_family'];
					}
					
					
					// Get LMS Id
					if(!$error) {
						$this->loadModel('SessionLMS');
						$lmsId = $this->SessionLMS->find('first', array('conditions' => array('SessionLMS.url LIKE' => $lms['SessionLMS']['url'])));
						
						// Set Existing LMS Id
						if(isset($lmsId['SessionLMS'])) {
							$player['lms'] = $lmsId['SessionLMS']['id'];
							if($lms['SessionLMS']['outcome'] != $lmsId['SessionLMS']['outcome']) {
								$this->SessionLMS->id = $player['lms'];
								$this->SessionLMS->saveField('outcome', $lms['SessionLMS']['outcome']);
							}
						}
						
						// Save LMS and Get its Id
						else if($this->SessionLMS->save($lms)) {
							$player['lms'] = $this->SessionLMS->getLastInsertID();
						}
						
						else {
							$error = __('error-lti-invalid-lms');
						}
					}
					
				}
				else $error = __('error-lti-no-id');
			}
			else $error = $error['string'];
		}
		else {
			$this->Game->id = $id;
			if (!$this->Game->exists()) {
				throw new NotFoundException(__('Invalid game'));
			}
			
			// Set Player Name
			if($this->Auth->user('id')) {
				$player['name'] = $this->Auth->user('name');
			}

			// Get Game Data
			$this->Game->unbindModel($unbind);
			$game = $this->Game->read($fields, $id);
		}
		
		if(!$error) {
			$this->layout = 'game';
			
			$player['gameId'] = $game['Game']['id'];

			// -------------- DANGER for LMS ------------------------
			$player['genreCode'] = $game['Genre']['code'];
			// ------------------------------------------------------

			// Load Converter Component
			$this->Converter = $this->Components->load($game['Genre']['code'].'Converter', array('mode' => $game['Genre']['mode_id'], 'genre' => $game['Game']['genre_id']));
			
			// Get Game Defaults
			$defaults = $this->Converter->getGameDefaults();
			
			// Get Game Files
			$files = $this->Converter->getFiles($this->base);
			$defaults['resources'] = $files['client'];
			
			// Get Game Client Data
			$clientFile = new File(FILES.Configure::read('Folders.files.games').DS.$game['Game']['id'].DS.'client.json');
			if($clientFile->open()) {
				$data = json_decode($clientFile->read());
				$clientFile->close();
			}
			else {
				$this->Session->setFlash(__('error-corrupted-game'));
				$this->redirect('/');
			}
			
			// Remove Default Images not to Load
			if($game['Game']['icon']) {
				$files['server']['icon']['url'] = $this->base . '/' . FILES_URL . $game['Game']['icon_url'];
			}
			
			// Remove Default Sounds not to Load
			foreach($data->properties->sounds as $soundName => $soundValue) {
				if($soundValue) {
					unset($defaults['resources'][$soundName]);
				}
			}
			
			// Set View Variables
			$this->set('game', $game['Game']);
			$this->set('genre', $game['Genre']);
			$this->set('files', $files['server']);
			$this->set(compact('defaults', 'data', 'player'));
			
			$this->set('title_for_layout', $game['Game']['name']);
		}
		else {
			$this->Session->setFlash($error);
		}
	}
	
	
	/**
	 * Sessions Logs
	 *
	 * @param int $id
	 */
	public function logs($id=null) {
		$this->redirect(array('controller' => 'sessions', 'action' => 'index', $id));
	}
	
	
	/**
	 * Delete Game
	 *
	 * @param int $id
	 * @throws InvalidRequestException if the request type is not supported.
	 * @throws NotFoundException if the game doesn't exist.
	 */
	public function delete($id=null) {
		if (!$this->request->is('post') || $this->RequestHandler->isAjax()) {
			throw new InvalidRequestException();
		}
		
		$this->Game->id = $id;
		if (!$this->Game->exists()) {
			throw new NotFoundException(__('Invalid Game'));
		}
		
		$this->autoRender = false;
		
		$this->Game->recursive = -1;
		$this->Game->read(null, $id);
		
		if($this->itemBelongsToUser($this->Game->data['Game'])) {
			if ($this->Game->delete()) {

				//Deletes Game Folder
				$folder = new Folder(FILES.Configure::read('Folders.files.games').DS.$this->Game->data['Game']['id']);
				$folder->delete();

				//Log Operation
				$this->logData($this->params['controller'], $id, $this->Game->data['Game']['name'], Configure::read('Operations.delete'));
			}
		}
		
		$this->redirect(array('action' => 'index'));
	}
}
