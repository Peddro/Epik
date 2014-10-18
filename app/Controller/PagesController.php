<?php
/**
 * Imports
 */
App::uses('AppController', 'Controller');

/**
 * Pages Controller
 *
 * @package app.Controller
 * @link http://book.cakephp.org/2.0/en/controllers/pages-controller.html
 * @author Bruno Sampaio
 */
class PagesController extends AppController {

	/**
	 * @var string Controller name
	 */
	public $name = 'Pages';
	
	
	/**
	 * @var array This controller does not use a model
	 */
	public $uses = array();
	
	
	/**
	 * Extends the parent method.
	 */
	public function beforeFilter() {
		parent::beforeFilter();
		
		$this->Auth->allow('index', 'display');
	}
	
	
	/**
	 * Extends the parent method.
	 */
	public function beforeRender() {
		parent::beforeRender();
	}
	
	
	/**
	 * Home Page
	 *
	 * Redirects to the home page if no user is logged in,
	 * or redirects to the dashboard section of the current user.
	 */
	public function index() {
		if($this->Auth->user()) {
			$this->redirect(array('controller' => $this->Session->read('Dashboard.current_section'), 'action' => 'index'));
		}
		else {
			$this->redirect(array('action' => 'display', 'home'));
		}
	}
	

	/**
	 * Displays Static View
	 */
	public function display() {
		$path = func_get_args();

		$count = count($path);
		if (!$count) {
			$this->redirect('/');
		}
		$page = $subpage = $title_for_layout = null;

		if (!empty($path[0])) {
			$page = $path[0];
		}
		if (!empty($path[1])) {
			$subpage = $path[1];
		}
		if (!empty($path[$count - 1])) {
			$title_for_layout = Inflector::humanize($path[$count - 1]);
		}
		
		if($path[0] == 'home') {
			$title_for_layout = __('Home');
		}
		else $title_for_layout = Configure::read('Sections.footer.'.$path[0].(!empty($path[1])? '.'.$path[1] : ''));
			
		$this->set(compact('page', 'subpage', 'title_for_layout'));
		$this->render(implode('/', $path));
	}
}
