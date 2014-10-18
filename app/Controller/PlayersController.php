<?php
/**
 * Imports
 */
App::uses('AppController', 'Controller');

/**
 * Players Controller
 *
 * @package app.Controller
 * @property Player $Player
 * @author Bruno Sampaio
 */
class PlayersController extends AppController {
	
	/**
	 * Extends the parent method.
	 */
	public function beforeFilter() {
		parent::beforeFilter();
		
		$this->Auth->allow('send');
		$this->Security->requirePost('send');
		
		if($this->request->action == 'send') {
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
	 * Send to LMS
	 *
	 * @param int $id - the session id.
	 * @throws NotFoundException if the player doesn't exist.
	 * @throws InvalidRequestException if the request type is not supported.
	 */
	public function send($id) {
		if($this->request->is('post') || $this->request->is('put')) {
			$this->Player->id = $id;
			if (!$this->Player->exists()) {
				throw new NotFoundException(__('Invalid player'));
			}

			$this->autoRender = false;

			// Get Player
			$this->Player->unbindModel(array('hasMany' => array('ActivityLog', 'Bonus', 'Score')));
			$player = $this->Player->read(array('Session.lms_id', 'Session.game_id', 'Player.instance_id'), $id);

			// If Session has a LMS
			if($player['Session']['lms_id']) {

				// If LMS has a Outcome URL
				$lms = $this->Player->Session->LMS->read(array('LMS.outcome'), $player['Session']['lms_id']);
				if($lms['LMS']['outcome']) {

					$this->LMSServicies = $this->Components->load('LMSServices');

					// Get Game Consumer Key and Shared Secret
					$this->loadModel('Game');
					$this->Game->recursive = -1;
					$game = $this->Game->find('first', array('fields' => array('resource_key', 'secret', 'user_id'), 'conditions' => array('id' => $player['Session']['game_id'])));

					// Get Player Grade
					$grade = '1.0';
					if(isset($this->request->data['Player']) && isset($this->request->data['Player']['grade']) && $this->itemBelongsToUser($game['Game'])) {
						$grade = (string) $this->request->data['Player']['grade'];
					}

					// Send LTI Response
					if(isset($game['Game'])) {
						return $this->LMSServicies->sendLTIResponse($lms['LMS']['outcome'], $player['Player']['instance_id'], $game['Game']['resource_key'], $game['Game']['secret'], $grade);
					}
				}
			}

			return false;
		}
		else throw new InvalidRequestException();
	}
}