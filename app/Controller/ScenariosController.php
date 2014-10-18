<?php
/**
 * Imports
 */
App::uses('AppController', 'Controller');
App::uses('File', 'Utility');

/**
 * Scenarios Controller
 *
 * @package app.Controller
 * @property Scenario $Scenario
 * @author Bruno Sampaio
 */
class ScenariosController extends AppController {
	
	/**
	 * @var array Models used by this Controller
	 */
	public $uses = array('ScenarioTemplate', 'GenreScenario');
	
	
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
	 * Choose Section
	 *
	 * This action prepares the options to be sent to the chooser method.
	 * It is invoked on a situation where the user must select one or more projects to continue.
	 * @return array - list of values to be sent to an element view.
	 */
	public function choose() {
		$options = array();
		
		// Filters Default Values
		$options['filters'] = array('genre_id' => null);
		
		// Unbind Options
		$options['unbind'] = array();
		
		// List Options
		$fields = array('ScenarioTemplate.id', 'ScenarioTemplate.name', 'ScenarioTemplate.description');
		$order = array('ScenarioTemplate.name');
		$options['list'] = array('fields' => $fields, 'conditions' => array(), 'order' => $order);
		
		// Selected Options
		$options['selected'] = array();
		
		return $this->chooser($this->GenreScenario->name, $this->name, $this->GenreScenario, $options);
	}
	
	
	/**
	 * Choose Scenario to be added to Project
	 *
	 * @param int $genre_id - project genre id.
	 * @throws InvalidRequestException if the request type is not supported.
	 */
	public function choose_type($genre_id) {
		if($this->RequestHandler->isAjax()) {
			$this->layout = 'modal';
			$sections = array('choose');
			$params['current'] = $sections[0];
			$params['previous'] = false;
			
			// Set section properties
			if($params['current'] == $sections[0]) {
				$params['title'] = __('add-title', __('Scenario'));
				$params['message'] = __('choose-message', __('scenario template'));

				$params['filters'] = array();
				$params['filters']['model'] = 'Chooser';
				$params['filters']['url'] = array('controller' => $this->name, 'action' => 'choose');
				$params['filters']['fields'] = array();
				
				$this->request->data['Chooser']['filters'] = array('genre_id' => $genre_id);
				$this->request->data['Chooser']['multiple'] = false;
			}

			$this->set(compact('params', 'sections'));
		}
		else throw new InvalidRequestException();
	}
	
	
	/**
	 * Get scenario template data
	 *
	 * @param int $id - the scenario template id.
	 * @return string - returns a string in json format.
	 * @throws NotFoundException if the scenario template doesn't exist.
	 * @throws InvalidRequestException if the request type is not supported.
	 */
	public function get($id) {
		$this->ScenarioTemplate->id = $id;
		if (!$this->ScenarioTemplate->exists()) {
			throw new NotFoundException(__('Invalid Scenario'));
		}
		
		if($this->RequestHandler->isAjax()) {
			$this->autoRender = false;
			
			$file = new File(FILES.Configure::read('Folders.files.scenarios').DS.$id.'.json');
			
			if($file->open()) {
				$template = $file->read();
				$file->close();
				return $template;
			}
		}
		else throw new InvalidRequestException();
	}
	
}
