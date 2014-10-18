<?php
/**
 * Imports
 */
App::uses('AppController', 'Controller');

/**
 * Genres Controller
 *
 * @package app.Controller
 * @property GameGenre $GameGenre
 * @author Bruno Sampaio
 */
class GenresController extends AppController {
	
	/**
	 * @var array Models used by this Controller
	 */
	public $uses = array('GameGenre');
	
	
	/**
	 * Extends the parent method.
	 */
	public function beforeFilter() {
		parent::beforeFilter();
		
		$this->Auth->allow('view');
		$this->Security->requireGet('view');
	}
	
	
	/**
	 * Extends the parent method.
	 */
	public function beforeRender() {
		parent::beforeRender();
	}
	
	
	/**
	 * View Genre
	 *
	 * @param int $id
	 * @throws NotFoundException if the genre doesn't exist.
	 */
	public function view($id) {
		$this->GameGenre->id = $id;
		if (!$this->GameGenre->exists()) {
			throw new NotFoundException(__('Invalid game genre'));
		}
		
		// Get Genre
		$data = $this->GameGenre->read(array('name', 'instructions'), $id)['GameGenre'];
		
		if($this->RequestHandler->isAjax()) {
			$this->layout = 'modal';
			
			$this->set('params', array('title' => $data['name'], 'message' => null, 'current' => 'view'));
		}
		else {
			$this->set('title_for_layout', $data['name']);
		}
		
		$this->set('data', $data);
	}
}
