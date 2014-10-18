<?php
/**
 * Imports
 */
App::uses('AppController', 'Controller');
App::uses('CakeEmail', 'Network/Email');

/**
 * Users Controller
 *
 * @package app.Controller
 * @property User $User
 * @author Bruno Sampaio
 */
class UsersController extends AppController {
	
	/**
	 * @var array Components used by this Controller
	 */
	public $components = array('Files');
	
	
	/**
	 * @var array Pagination Component Properties
	 */
	public $paginate = array(
			'order' => array('User.created' => 'desc')
	);
	
	
	/**
	 * Extends the parent method.
	 */
	public function beforeFilter() {
		parent::beforeFilter();
		
		$this->Auth->authenticate = array(
			'Form', 
			'Basic' => array('fields' => array('password' => 'secret'))
		);
		$this->Auth->allow('signup', 'success', 'signin', 'reset', 'oauthentication');
		
		$this->Security->requireGet('view');
		$this->Security->requirePost('oauthentication', 'update');
		
		if($this->request->action == 'oauthentication') {
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
		$list = parent::options(array(), true, false);
		$list['edit']['modal'] = false;
		return $list;
	}
	
	
	/**
	 * User Sign Up
	 */
	public function signup() {
		if(!$this->Auth->user()) {
			if ($this->request->is('post')) {
				$this->User->create();
				$error = false;
			
				$conditions = array('User.username' => $this->request->data['User']['username']);
				$usernameExists = $this->User->find('count', array('conditions' => $conditions));
			
				$conditions = array('User.email' => $this->request->data['User']['email']);
				$emailExists = $this->User->find('count', array('conditions' => $conditions));
			
				// Verify Password
				if(strcmp($this->request->data['User']['password'], $this->request->data['User']['confirm_password'])) {
					$error = __('error-incorrect-passwords');
				}
			
				// Verify Username
				else if($usernameExists) {
					$error = __('error-already-exists-username');
				}
			
				// Verify E-mail
				else if($emailExists) {
					$error = __('error-already-exists-email');
				}
				
				// Verify Terms
				else if(!$this->request->data['User']['agrees']) {
					$error = __('error-notagree-terms');
				}
			
				// Check Picture
				$folder = IMAGES.Configure::read('Folders.img.users');
				if(!$error) {
					if(!empty($this->request->data['User']['picture']['name'])) {

						//Upload file to the correspondent folder
						$file = $this->Files->upload($folder, 'tmp'.date('Y-m-d-His'), $this->request->data['User']['picture']);

						//Check if there was any error
						if(empty($file['error'])) {
							$this->request->data['User']['picture'] = 1;
							$this->request->data['User']['picture_url'] = $file['name'];
						}
						else {
							$error = $file['error'];
						}
					}
					else {
						$this->request->data['User']['picture'] = 0;
					}
				}
			
				// Begin Save
				if(!$error) {
				
					// Prepare Data
					if(strlen($this->request->data['User']['lms_url']) == 0) {
						$this->request->data['User']['lms_url'] = null;
					}
					
					$secret = $this->request->data['User']['username'];
					$this->request->data['User']['secret'] = Security::hash($secret, 'sha1', true);
					$this->request->data['User']['role'] = 'normal';
				
					// Save Data
					if ($this->User->save($this->request->data)) {
						$id = $this->User->getInsertID();
						$this->User->read(null, $id);
					
						// Change picture name
						if(isset($this->request->data['User']['picture_url'])) {
							$this->Files->rename($folder, $this->request->data['User']['picture_url'], $id);
						}
					
						// Log Data
						$this->logData($this->params['controller'], $id, $this->User->getName(), Configure::read('Operations.add'), $id);
						
						return $this->redirect(array('action' => 'success'));
					}
					else {
						if(isset($this->request->data['User']['picture_url'])) {
							$this->Files->delete($folder, $this->request->data['User']['picture_url']);
						}
						$error = __('error-empty-fields');
					}
				}
			
				// Set Error Messages
				if($error) {
					$this->Session->setFlash($error);
				}
			}
		
			// Set data to send to the view
			$lms = $this->User->LMS->find('list');
			$this->set('lms', $lms);
		
			// Reset Password fields
			$this->request->data['User']['password'] = '';
			$this->request->data['User']['confirm_password'] = '';

			// Set Page Title
			$this->set('title_for_layout', __('users-signup-title'));
		}
		else {
			return $this->redirect('/');
		}
	}
	
	
	/**
	 * Successful User Registration
	 */
	public function success() {
		if(!$this->Auth->user()) {
			
			$this->Session->setFlash(__('success-account-creation'), 'default', array('class' => 'message success'));
			
			// Set Page Title
			$this->set('title_for_layout', __('users-signup-title'));
		}
		else {
			return $this->redirect('/');
		}
	}
	
	
	/**
	 * User Sign In
	 */
	public function signin() {
		$isAjax = $this->RequestHandler->isAjax();
		if(!$this->Auth->user()) {
			if($isAjax) {
				$this->layout = 'modal';
				$sections = array('signin', 'complete');
				$params['current'] = $sections[0];
				$params['previous'] = -1;
			}
			
			if ($this->request->is('post')) {
				$conditions = array(
					'OR' => array(
						'User.username' => $this->request->data['User']['username'], 
						'User.email' => $this->request->data['User']['username']
					),
					'User.password' => AuthComponent::password($this->request->data['User']['password'])
				);

				// Check if user exists
				$user = $this->User->find('first', array('conditions' => $conditions));
				if(isset($user['User']['id'])) {
					if ($this->Auth->login($user['User'])) {
						$this->handleAuth($user);
						
						if($isAjax) {
							$params['current'] = $sections[1];
						}
						else {
							return $this->redirect($this->Auth->redirect());
						}
					}
					else {
						$this->Session->setFlash(__('error-while-logging-in'));
					}
				}
				else {
					$this->Session->setFlash(__('error-incorrect-credentials'));
				}
			}

			// Reset Fields Validation and Password Field
			$this->User->validate = array();
			$this->request->data['User']['password'] = '';
			
			if($isAjax) {
				if($params['current'] == $sections[0]) {
					$params['title'] = __('signin-title');
					$params['message'] = __('signin-message');
				}
				else if($params['current'] == $sections[1]) {
					$params['title'] = __('success-signin-title');
					$params['message'] = __('success-signin-message');
				}
				
				$this->set(compact('params', 'sections'));
			}
			else {
				
				//Set Page Title
				$this->set('title_for_layout', __('users-signin-title'));
			}
		}
		else {
			return $this->redirect('/');
		}
	}
	

	/**
	 * Authentication with OAuth and IMS LTI
	 */
	public function oauthentication() {
		if ($this->request->is('post')) {
			$requestParams = $this->data;
			
			// Validate LTI Request
			$this->LMSServices = $this->Components->load('LMSServices');
			$requestData = $this->LMSServices->validateLTIRequest($this->User, 'username', 'secret', $requestParams);
			$error =& $requestData['error'];
			
			if($requestData['entity'] && !$error) {
				$user =& $requestData['entity'];
				
				// Set LMS Data
				$lms = $this->storeLMS($user['User']['id'], $requestParams);
				$lms['User']['lms_id'] = $lms['id'];
				$lms['User']['lms_url'] = $lms['url'];
				
				// Login User
				if ($this->Auth->login($user['User'])) {
					$this->handleAuth($user);
					
					// Check user data from LMS
					$different = array();
					if(isset($requestParams['lis_person_name_given']) && $requestParams['lis_person_name_given'] != $user['User']['firstname']) {
						$different['firstname'] = $requestParams['lis_person_name_given'];
					}
					if(isset($requestParams['lis_person_name_family']) && $requestParams['lis_person_name_family'] != $user['User']['lastname']) {
						$different['lastname'] = $requestParams['lis_person_name_family'];
					}
					if(isset($requestParams['lis_person_contact_email_primary']) && $requestParams['lis_person_contact_email_primary'] != $user['User']['email']) {
						$different['email'] = $requestParams['lis_person_contact_email_primary'];
					}
					
					if(count($different) > 0) {
						$this->set('update', $different);
					}
					else {
						return $this->redirect($this->Auth->redirect());
					}
				}
				else {
					$error = array('string' => __('error-while-logging-in'), 'code' => 5);
				}
			}
			else {
				$this->Session->setFlash($error['string']);
				$this->set('error', $error['code']);
			}
			
			//Set Page Title
			$this->set('title_for_layout', __('users-oauthentication-title'));
		}
	}
	
	
	/**
	 * Updates the current user information with the one provided by the LMS.
	 */
	public function update() {
		if ($this->request->is('post') && $this->Auth->user()) {
			
			if($this->request->data['submit'] == 'update') {
				
				$this->request->data['User']['id'] = $this->Auth->user('id');

				$allowedFields = array('firstname', 'lastname', 'email');
				if($this->User->save($this->request->data, true, $allowedFields)) {
					
					$user = $this->User->read(null, $this->request->data['User']['id']);
					
					$this->Session->write('Auth.User', array_merge($this->Auth->user(), $user['User']));

					// Log Data
					$this->logData($this->params['controller'], $this->request->data['User']['id'], $user['User']['name'], Configure::read('Operations.edit'));
				}
			}
			
			return $this->redirect($this->Auth->redirect());
		}
		else {
			return $this->redirect('/');
		}
	}
	
	
	/**
	 * User Profile
	 *
	 * @param int $id
	 * @throws NotFoundException if the user doesn't exist.
	 */
	public function view($id = null) {
		if($this->request->is('get') && $this->Auth->user('id') == $id) {
			
			$this->User->id = $id;
			if (!$this->User->exists()) {
				throw new NotFoundException(__('Invalid user'));
			}

			//Set data to send to the view
			$this->request->data = $this->User->read(null, $id);
			$this->set('options', $this->getOptions());

			//Set Page Title
			$this->set('title_for_layout', __('users-view-title'));
		}
		else {
			return $this->redirect('/');
		}
	}
	
	
	/**
	 * Edit User
	 *
	 * @param int $id
	 * @throws NotFoundException if the user doesn't exist.
	 */
	public function edit($id=null) {
		if($id == $this->Auth->user('id')) {
			$this->User->id = $id;
			if (!$this->User->exists()) {
				throw new NotFoundException(__('Invalid user'));
			}
			$error = false;

			if ($this->request->is('post') || $this->request->is('put')) {
				
				// Verify Password
				if(strcmp($this->request->data['User']['password'], $this->request->data['User']['confirm_password'])) {
					$error = __('error-incorrect-passwords');
				}

				// Verify E-mail
				else if($this->request->data['User']['email'] != $this->Auth->user('email')) {
					$conditions = array('User.email' => $this->request->data['User']['email']);
					$emailExists = $this->User->find('count', array('conditions' => $conditions));
					
					if($emailExists) $error = __('error-already-exists-email');
				}
				
				// Check Picture
				$folder = IMAGES.Configure::read('Folders.img.users');
				if(!$error && !empty($this->request->data['User']['picture']['name'])) {

					//Upload file to the correspondent folder
					$file = $this->Files->upload($folder, $id, $this->request->data['User']['picture']);

					//Check if there was any error
					if(empty($file['error'])) {
						$this->request->data['User']['picture'] = 1;
						$this->request->data['User']['picture_url'] = $file['name'];
					}
					else {
						$error =  $file['error'];
						
						if($this->request->data['User']['picture_url']) {
							$this->request->data['User']['picture'] = 1;
						}
						else {
							$this->request->data['User']['picture'] = 0;
						}
					}
				}
				else if($this->request->data['User']['picture_url']) {
					$this->request->data['User']['picture'] = 1;
				}
				else {
					$this->request->data['User']['picture'] = 0;
				}
				
				
				// Begin Save
				if(!$error) {
					
					// Prepare Data
					if(strlen($this->request->data['User']['lms_url']) == 0) {
						$this->request->data['User']['lms_url'] = null;
					}
					
					$allowedFields = array('firstname', 'lastname', 'picture', 'email', 'password', 'lms_id', 'lms_url');

					if ($this->User->save($this->request->data, true, $allowedFields)) {
						$user = $this->User->read(null, $id);
						
						if(!$user['User']['picture']) {
							$this->Files->delete($folder, $id, false);
						}
						
						if($user['User']['lms_id']) {
							$user['User']['lms_name'] = $user['LMS']['name'];
						}
						
						$this->Session->write('Auth.User', array_merge($this->Auth->user(), $user['User']));

						// Log Data
						$this->logData($this->params['controller'], $id, $user['User']['name'], Configure::read('Operations.edit'));

						return $this->redirect(array('action' => 'view', $id));
					} 
					else {
						$error = __('error-empty-fields');
					}
				}
				
				// Set Error Messages
				if($error) {
					$this->Session->setFlash($error);
				}
			}
			else {
				$this->request->data = $this->User->read(null, $id);
			}

			// Set data to send to the view
			$this->request->data['User']['username'] = $this->Auth->user('username');
			$this->request->data['User']['secret'] = $this->Auth->user('secret');
			$lms = $this->User->LMS->find('list');
			$this->set('lms', $lms);

			if($this->request->data['User']['picture']) {
				if(!isset($this->request->data['User']['picture_url'])) {
					$this->request->data['User']['picture_url'] = $this->Auth->user('picture_url');
				}
			}
			else {
				$this->request->data['User']['picture_url'] = null;
			}
			
			// Reset Password fields
			$this->request->data['User']['password'] = '';
			$this->request->data['User']['confirm_password'] = '';

			//Set Page Title
			$this->set('title_for_layout', __('users-edit-title'));
		}
		else {
			return $this->redirect('/');
		}
	}
	
	
	/**
	 * User Sign Out
	 */
	public function signout() {
		$this->logUserStatus('logout');
		
		// Logout
		$this->Auth->logout();
		$this->Session->delete('LMS');
		$this->Session->delete('Dashboard');

		return $this->redirect('/');
	}
	
	
	/**
	 * Reset User Password
	 */
	public function reset() {
		if ($this->request->is('post') || $this->request->is('put')) {
			
			$user = $this->User->find('first', array('conditions' => array('User.email' => $this->request->data['User']['email'])));

			if($user) {

				$user['User']['password'] = $this->User->createTempPassword(8);
			
				if ($this->User->save($user, false)) {
				
					$email = new CakeEmail();
					$email->helpers(array('Html'));
					$email->template('password_recovery');
					$email->viewVars(array('password' => $user['User']['password']));
					$email->from(Configure::read('System.email'));
					$email->to($user['User']['email']);
					$email->subject('['.Configure::read('System.name').'] Password Recovery');
					$email->emailFormat('both');

					if($email->send()) {
						$this->Session->setFlash(__('success-email-sent'), 'default', array('class' => 'message success'));
						return $this->redirect(array('action' => 'signin'));
					}
					else {
						$this->Session->setFlash(__('error-while-sending-email'));
					}
				}
				else {
					$this->Session->setFlash(__('error-reset-password'));
				}
			}
			else {
				$this->Session->setFlash(__('error-incorrect-email'));
			}
		}
		
		// Reset Fields Validation
		$this->User->validate = array();
		
		//Set Page Title
		$this->set('title_for_layout', __('users-reset-title'));
	}
	
	
	/**
	 * Project Tools Options
	 */
	public function settings() {}
	
	
	/**
	 * Handle Authentication
	 *
	 * Sets the user lms and picture information in the Auth Session object.
	 * @param array $user - the user that just logged in.
	 */
	private function handleAuth($user) {
		$this->Session->write('Dashboard.current_section', 'projects');

		// Log user status
		$this->logUserStatus('login');
	}
	
	
	/**
	 * Store User LMS
	 *
	 * @param int $id - user identifier.
	 * @param array $params - request parameters.
	 * @return array lms - the LMS information.
	 */
	private function storeLMS($id, $params) {
		$this->User->id = $id;
		$lms = array('id' => null, 'name' => null, 'url' => null);
		
		// Save LMS Name
		if(isset($params['tool_consumer_info_product_family_code'])) {
			$consumerLMSName = $params['tool_consumer_info_product_family_code'];
			$conditions = array("LMS.name LIKE '".$consumerLMSName."'");
			$lms_data = $this->User->LMS->find('first', array('conditions' => $conditions));
			
			$lms['id'] = $lms_data['LMS']['id'];
			$lms['name'] = $lms_data['LMS']['name'];
			$this->User->saveField('lms_id', $lms_data['LMS']['id']);
		}
		
		// Save LMS URL
		if(isset($params['tool_consumer_instance_url'])) {
			$lms['url'] = $params['tool_consumer_instance_url'];
			$this->User->saveField('lms_url', $lms['url']);
		}
		else if(isset($params['tool_consumer_instance_guid'])) {
			$lms['url'] = 'http://'.$params['tool_consumer_instance_guid'];
			$this->User->saveField('lms_url', $lms['url']);
		}
		
		return $lms;
	}
	
	
	/**
	 * Log User Status
	 *
	 * Adds a row to the logins table with current user status information.
	 * @param string $status - user status (login or logout).
	 */
	private function logUserStatus($status) {
		$this->loadModel('Login');
		$this->Login->data['Login']['user_id'] = $this->Auth->user('id');
		$this->Login->data['Login']['user_status'] = $status;
		$this->Login->save($this->Login->data);
	}
}
