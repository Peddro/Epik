<?php
/**
 * Imports
 */
App::uses('ExceptionRenderer', 'Error');

/**
 * Application Exception Renderer
 *
 * @package app.Lib.Error
 * @author Bruno Sampaio
 */
class AppExceptionRenderer extends ExceptionRenderer {
	
	/**
	 * Action executed when a PermissionDeniedException is thrown
	 *
	 * @param array $error - the error data.
	 */
    public function permissionDenied($error) {
		$this->errorAction($error, 'permission');
    }
	
	
	/**
	 * Action executed when a InvalidRequestException is thrown
	 *
	 * @param array $error - the error data.
	 */
	public function invalidRequest($error) {
		$this->errorAction($error, 'request');
	}
	
	
	/**
	 * Action executed when a SecurityBreachException is thrown
	 *
	 * @param array $error - the error data.
	 */
	public function securityBreach($error) {
		$this->controller->set('type', $error->getType());
		$this->errorAction($error, 'security');
    }
	
	
	/**
	 * Handles Exception
	 *
	 * @param array $error - the error data.
	 * @param string $template - the view name.
	 */
	private function errorAction($error, $template) {
		$this->controller->response->statusCode($error->getCode());
		
		// Parse URL
		$urlParams = $this->controller->request->params;
		$url = array_merge(array('controller' => $urlParams['controller'], 'action' => $urlParams['action']), $urlParams['pass']);
		
		// Set General View Vars
		$pass = array(
			'name' => $error->getMessage(),
			'url' => $url,
			'error' => $error,
			'_serialize' => array('name', 'url')
		);
		
		// Set Layout Vars
		if($this->controller->RequestHandler->isAjax()) {
			$pass['params'] = array(
				'title' => $error->getMessage(), 
				'message' => __('error-message', $template), 
				'current' => 'error'
			);
		}
		else {
			$pass['title_for_layout'] = $error->getMessage();
			$pass['_serialize'][] = 'title_for_layout';
		}
		
		$this->controller->set($pass);
		$this->_outputMessage($template);
	}
	
	
	/**
	 * Extends the parent method.
	 *
	 * @param string $template - the view name.
	 */
	protected function _outputMessage($template) {
		if($this->controller->RequestHandler->isAjax()) {
			$this->controller->layout = 'modal';
		}
		else {
			$this->controller->layout = 'default';
		}
		
		parent::_outputMessage($template);
	}
	
	
	/**
	 * Extends the parent method.
	 *
	 * @param string $template - the view name.
	 */
	protected function _outputMessageSafe($template) {
		$this->controller->layoutPath = null;
		$this->controller->subDir = null;
		$this->controller->viewPath = 'Errors/';
		$this->controller->viewClass = 'View';
		$this->controller->helpers = array('Form', 'Html', 'Session');

		$this->controller->render($template);
		$this->controller->response->type('html');
		$this->controller->response->send();
	}
}