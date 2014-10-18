<?php
/**
 * Imports
 */
App::uses('Controller', 'Controller');

/**
 * Application Controller
 *
 * @package app.Controller
 * @link http://book.cakephp.org/2.0/en/controllers.html#the-app-controller
 * @author Bruno Sampaio
 */
class AppController extends Controller {
	
	/**
	 * @var array Components used by this Controller
	 */
	public $components = array(
			'Session',
			'Security' => array('blackHoleCallback' => '_blackHole'),
			'Auth' => array(
					'authError' => 'Please, sign in first.',
					'loginAction' => array('controller' => 'users', 'action' => 'signin'),
					'loginRedirect' => array('controller' => 'pages', 'action' => 'index')
			),
			'Paginator',
			'RequestHandler'
	);
	
	
	/**
	 * @var array Helpers used by this Controller Actions
	 */
	public $helpers = array('Html', 'Layouts', 'Elements', 'Modal', 'Form', 'Session', 'Js' => array('Jquery'), 'Paginator');
	
	
	/**
	 * Default before filter function for all controllers.
	 * Sets the Auth, Session and Security components properties.
	 * Also sets default view variables.
	 */
	public function beforeFilter() {
		parent::beforeFilter();
		
		$isAjax = $this->RequestHandler->isAjax();
		
		// Language Settings
		$this->Session->write('Config.language', 'eng');

		// Auth Settings
		$this->Auth->autoRedirect = false;
		
		// Security Settings
		if($isAjax) {
			$this->Security->validatePost = false;
		}
		
		if($this->request->action == 'listing' || $this->request->action == 'choose') {
			$this->Security->csrfCheck = false;
		}
		$this->Security->requirePost('delete');
		
		// Views Properties
		$this->set('ajax', $isAjax);
	}
	
	
	/**
	 * Security blackhole method.
	 * Executed when a security error occurs, cancelling the last request.
	 *
	 * @param $type - the type of error.
	 * @throws SecurityBreachException if a security breach occurs.
	 */
	public function _blackHole($type) {
		throw new SecurityBreachException(array('type' => strtolower($type)));
	}
	
	
	/**
	 * Checks if an item belongs to the currently logged in user.
	 * 
	 * @param array $item - the item array (must contain the user_id field).
	 * @throws PermissionDeniedException if the user hasn't permission to access this item.
	 * @return bool - if true the item belongs to the user.
	 */
	protected function itemBelongsToUser($item) {
		if($item['user_id'] != $this->Auth->user('id')) {
			throw new PermissionDeniedException();
		}
		return true;
	}
	
	
	/**
	 * Checks if an item is visible to a certain user.
	 * 
	 * @param array $item - the item array (must contain the visibility_id and user_id fields).
	 * @throws PermissionDeniedException if the user hasn't permission to access this item.
	 * @return bool - if true the item is visible to the user.
	 */
	protected function itemIsVisibleToUser($item) {
		if(($item['user_id'] != $this->Auth->user('id')) && ($item['visibility_id'] == 2)) {
			throw new PermissionDeniedException();
		}
		return true;
	}
	
	
	/**
	 * Checks if the current user already has an item of type $model with $item['name'].
	 *
	 * @param object $model - the model object.
	 * @param array $item - the item array (must contain the fields: id, name, user_id).
	 * @return int - number of ocurrences of an item with same name and same user. 
	 */
	protected function checkItemNameForCurrentUser($model, $item) {
		$conditions = array(
			'name' => $item['name'],
			'user_id' => $item['user_id']
		);

		if(isset($item['id']) && $item['id']) {
			$conditions['id !='] = $item['id'];
		}

		$model->recursive = -1;
		return $model->find('count', array('conditions' => $conditions));
	}
	
	
	/**
	 * Adds a row to the Logs table with information about an operation performed on other table row.
	 *
	 * @param string $table - table where operation was made.
	 * @param int $id - the table row id.
	 * @param string $name - the table row name.
	 * @param string $operation - the operation type: Add, Edit, Delete.
	 * @param int $userId - the identifier for the user that performed the operation.
	 */
	protected function logData($table, $id, $name, $operation, $userId=null) {
		$this->loadModel('Log');
		$this->Log->data['Log']['table_name'] = $table;
		$this->Log->data['Log']['row_id'] = $id;
		$this->Log->data['Log']['row_name'] = $name;
		$this->Log->data['Log']['operation'] = $operation;

		//If the user is logged in
		if($this->Auth->User('id')) {
			$this->Log->data['Log']['user_id'] = $this->Auth->user('id');
			$this->Log->data['Log']['user_name'] = $this->Auth->user('name');
		}
		else {
			$this->Log->data['Log']['user_id'] = $userId;
			$this->Log->data['Log']['user_name'] = 'Anonymous';
		}
		
		$this->Log->save($this->Log->data);
	}
	
	
	/**
	 * Creates the Options List
	 * The options list is used to create the icons for display on views.
	 * Each option can contain the following params:
	 * 		- title: the value for the title attribute;
	 * 		- model: the option related model;
	 * 		- fields: the fields to be tested;
	 * 		- action: the controller action;
	 * 		- modal: determines if the option uses the modal window;
	 * 		- post: determines if the option requires a post request.
	 * @param array $list - the extra options list.
	 * @param bool $edit - use edit option.
	 * @param bool $delete - use delete option.
	 * @return array $list
	 */
	protected function options($list=array(), $edit=true, $delete=true) {
		if($edit) $list = array_merge_recursive(array('edit' => array('modal' => true)), $list);
		if($delete) $list = array_merge_recursive($list, array('delete' => array('post' => true)));
		return $list;
	}
	
	
	/* GLOBAL ACTIONS
	 -----------------------------------------------------------------------------------------------------------------------------*/
	
	
	/**
	 * Creates the logic for a dashboard section.
	 * A dashboard section provides the user with options to manage his data.
	 */
	protected function dashboard() {
		$section = $this->params['controller'];
		$this->Session->write('Dashboard.current_section', $section);
	
		$this->set('dashboard_section', $section);
		$this->set('title_for_layout', __('Dashboard'));
	}
	
	
	/**
	 * Creates the logic for a selector window.
	 * The selector is used to choose the kind of action the user wants to perform.
	 *
	 * @param string $title - the word for the selector title.
	 * @param string $entity - the word for the selector message.
	 * @param string $operation - the kind of operation.
	 * @param object $model - the model object.
	 */
	protected function selector($title, $entity, $operation, $model) {
		$this->layout = 'modal';

		$params['title'] = __('select-title', $title);
		$params['message'] = __('select-message', $entity, $operation);
		$params['current'] = 'select';

		$types = $model->find('all', array('order' => 'Type.name'));

		$this->set(compact('params', 'types'));
	}
	
	
	/**
	 * Creates the logic for a listing section.
	 * The paginator is used to list a page of items of the current user.
	 *
	 * @param string $name - the model name.
	 * @param string $controller - the controller name.
	 * @param object $model - the model object.
	 * @param string $keyword - the search parameter.
	 * @param array $options - the options to be used. Available options are: 
	 *			- conditions: conditions for the pagination operation;
	 *			- recursive: the recursive property value;
	 *			- unbind: array with relations and respective models to unbind from current model.
	 *			- use: the items properties to be used (modal - sets all items with modal links; icon - sets an icon before items name);
	 *			- options: the options to be displayed for each item.
	 * @return array with the list of items, the keyword parameter, the paging options, the properties to use and the options to display.
	 */
	protected function paginator($name, $controller, $model, $keyword, $options) {
		if (isset($this->params['requested']) || $this->RequestHandler->isAjax()) {
			$this->autoRender = false;
			
			if($this->request->is('post') && isset($this->request->data[$name])) {
				$keyword = $this->request->data[$name]['keyword'];
			}

			$options['conditions'][$name.'.user_id'] = $this->Auth->user('id');
			if(strlen($keyword) > 0) {
				array_push($options['conditions'], $name.'.name LIKE \'%'.$keyword.'%\'');
			}

			// Get User Items
			$model->unbindModel($options['unbind']);
			$model->recursive = $options['recursive'];
			$model->order = array($name.'.name');
			$list = $this->paginate($name, $options['conditions']);
			
			// Use Modal Window and Icon
			$use = $options['use'];

			if (isset($this->params['requested'])) {
				return array('list' => $list, 'keyword' => $keyword, 'paging' => $this->params['paging'], 'use' => $use, 'options' => $options['options']);
			}
			else {
				$this->set('model', $name);
				$this->set('controller', lcfirst($this->name));
				$this->set('options', $options['options']);
				$this->set(compact('list', 'keyword', 'use'));
				$this->render('/Elements/list');
			}
		}
		else {
			$this->redirect(array('action' => 'index'));
		}
	}
	
	
	/**
	 * Creates the logic for a chooser window.
	 * The chooser is used to select one or more items from a list.
	 *
	 * @param string $name - the model name.
	 * @param string $controller - the controller name.
	 * @param object $model - the model object.
	 * @param array $options - the options to be used. Available options are: 
	 *			- filters: array with default values for all filters (filter_name => default_value);
	 *			- unbind: array with relations and respective models to unbind from current model.
	 *			- list: array to be used on find operation which contains the fields, the conditions, the sort, etc.
	 *			- selected: array with options to merge with the $selected array.
	 * @return array with the list of items, the fields to store, the selected items and a bool to determine the type of selection allowed.
	 */
	protected function chooser($name, $controller, $model, $options=array()) {
		if($this->request->is('post') || $this->params['requested']) {
			$this->autoRender = false;
			
			// Set Args
			if(isset($this->request->params['named']) && count($this->request->params['named']) > 0) {
				$args = $this->request->params['named'];
				$iterate = $options['filters'];
			}
			else {
				$args = array();
				$args['filters'] = array();
				$iterate = $this->request->data['Chooser']['filters'];
				$args['multiple'] = $this->request->data['Chooser']['multiple'];
			}
			
			// Set Store and Filters Arrays
			$store = array();
			$filters = array();
			foreach($iterate as $key => $val) {
				if(!isset($args['filters'][$key])) {
					$args['filters'][$key] = $val;
				}
				$val = $args['filters'][$key];
				$store['Chooser.filters.'.$key] = $val;
				if($val) $filters[$name.'.'.$key] = $val;
			}
			
			// List of elements to choose from
			$options['list']['conditions'] = array_merge($options['list']['conditions'], $filters);
			$model->unbindModel($options['unbind']);
			$list = $model->find('all', $options['list']);
			
			if(count($list) == 0) {
				$this->Session->setFlash(__('warning-not-found', __($controller)), 'default', array('class' => 'message notice'));
			}
			
			// Determines if can choose only one or multiple element
			$multiple = $args['multiple'];
			
			// Determines the value to be selected
			$selected = array('ids' => null);
			if($multiple) {
				if(isset($args['ids']) && $args['ids']) {
					$selected['ids'] = $args['ids'];
				}
				else if(isset($this->request->data[$name])) {
					foreach($this->request->data[$name] as $question) {
						$id = $question['id'];
						$selected[$id] = true;
					}
				}
			}
			else {
				if(isset($args['id']) && $args['id']) {
					$selected['ids'] = $args['id'];
				}
			}
			$selected = array_merge($selected, $options['selected']);
			
			// Get Specific Data
			$specific = false;
			if(isset($options['specific'])) {
				$specificOptions =& $options['specific'];
				if(isset($specificOptions['unbind']) && $specificOptions['unbind']) {
					$model->unbindModel($options['unbind']);
				}
				
				$specific = $model->find('first', array('fields' => $specificOptions['fields'], 'conditions' => $options['list']['conditions']));
				if(count($specific) > 0) {
					$specific = $specific[$specificOptions['model']];
				}
				else $specific = false;
			}
			
			// Send data to Element view
			if (isset($this->params['requested'])) {
				return array('list' => $list, 'store' => $store, 'selected' => $selected, 'multiple' => $multiple, 'specific' => $specific);
			}
			else {
				$this->layout = 'ajax';
				
				$controller = lcfirst($controller);
				$this->set('model', $name);
				$this->set(compact('controller', 'list', 'store', 'selected', 'multiple', 'specific'));
				
				$this->render('/Elements/chooser');
			}
		}
	}
	
}
