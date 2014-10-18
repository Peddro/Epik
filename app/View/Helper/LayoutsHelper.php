<?php
/**
 * Imports
 */
App::uses('AppHelper', 'View/Helper');

/**
 * Layouts Helper
 *
 * @package app.View.Helper
 * @author Bruno Sampaio
 */
class LayoutsHelper extends AppHelper {
	
	/**
	 * @var array Helpers used by this Helper
	 */
	var $helpers = array();
	
	
	/**
	 * Constructor
	 * @param View $view
	 * @param array $settings
	 */
	public function __construct(View $view, $settings = array()) {
		parent::__construct($view, $settings);
	}
	
	
	/**
	 * Get Page Properties
	 *
	 * @param string $controller - the view controller.
	 * @param string $action - the controller action.
	 * @param array $pass - the arguments passed to the controller action.
	 * @param bool $isDashboard - determines if is a dashboard page.
	 * @return array[protocol, server, url, controller, action, page, classes].
	 */
	public function getProperties($controller, $action, $pass, $isDashboard=false) {
		$properties = array('controller' => $controller, 'classes' => array());
		
		$isStatic = $controller == 'pages';
		if($isStatic) {
			$properties['action'] = $pass[0];
			$properties['classes'][] = 'static-page';
		}
		else {
			$properties['action'] = $action;
		}
		
		// Set Body Classes
		if($isStatic || $controller == 'sessions') {
			$properties['classes'][] = 'overflow';
		}
		else if($isDashboard) {
			$properties['classes'][] = 'dashboard';
		}

		$properties['protocol'] = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
		$properties['url'] = $properties['protocol'] . $_SERVER['SERVER_NAME'] . $this->base . '/';
		$properties['page'] = $controller.'-'.$properties['action'];
		
		return $properties;
	}
	
	
	/**
	 * Get Page Title
	 *
	 * @param string $section - the page full name.
	 * @return string
	 */
	public function getTitle($section) {
		return Configure::read('System.name').': '.$section;
	}
	
}