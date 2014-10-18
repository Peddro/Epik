<?php
/**
 * Imports
 */
App::uses('AppController', 'Controller');

/**
 * Sessions Controller
 *
 * @package app.Controller
 * @property GameSession $GameSession
 * @author Bruno Sampaio
 */
class SessionsController extends AppController {
	
	/**
	 * @var array Models used by this Controller
	 */
	public $uses = array('GameSession');
	
	
	/**
	 * @var array Components used by this Controller
	 */
	public $components = array();
	
	
	/**
	 * Extends the parent method.
	 */
	public function beforeFilter() {
		parent::beforeFilter();
		
		$this->Auth->allow(array('index', 'view'));
	}
	
	
	/**
	 * Extends the parent method.
	 */
	public function beforeRender() {
		parent::beforeRender();
	}
	
	
	/**
	 * List Game Sessions
	 *
	 * @param int $gameId - the game id.
	 * @throws NotFoundException if the game doesn't exist.
	 */
	public function index($gameId) {
		$this->GameSession->Game->id = $gameId;
		if (!$this->GameSession->Game->exists()) {
			throw new NotFoundException(__('Invalid game'));
		}
		
		// Get Game Data
		$game = $this->GameSession->Game->read(array('name', 'visibility_id', 'user_id'), $gameId);
		
		// Check if Sessions are visible to the current user
		if($this->itemIsVisibleToUser($game['Game'])) {
			
			// Get Sessions Data
			$conditions = array('game_id' => $gameId);
			$this->GameSession->unbindModel(array('belongsTo' => array('Game'), 'hasMany' => array('Player')));
			$this->request->data = $this->GameSession->find('all', array(
				'fields' => array('GameSession.*, LMS.url'), 
				'conditions' => $conditions, 
				'order' => array('LMS.url, created DESC, GameSession.score DESC')
			));

			if(count($this->request->data) > 0) {
				$winner = $this->GameSession->find('first', array('fields' => array('MAX(GameSession.score) as winner'), 'conditions' => $conditions));
				$this->set('winner', $winner[0]['winner']);
			}

			$this->set('game', $game['Game']);
			$this->set('title_for_layout', __('Game Sessions'));
		}
	}
	
	
	/**
	 * View Session
	 *
	 * @param int $id - the session id.
	 * @throws NotFoundException if the game session doesn't exist.
	 */
	public function view($id) {
		$this->GameSession->id = $id;
		if (!$this->GameSession->exists()) {
			throw new NotFoundException(__('Invalid session'));
		}
		
		// Get Session Data
		$this->GameSession->unbindModel(array('hasMany' => array('Player')));
		$this->request->data = $this->GameSession->read(null, $id);
		
		// Check if Session is visible to the current user
		if($this->itemIsVisibleToUser($this->request->data['Game'])) {
			
			// Get Players Data
			$this->GameSession->Player->recursive = -1;
			$players = $this->GameSession->Player->find('all', array(
				'fields' => array('id', 'name', 'helps_used', 'helps_given', 'user_id'), 
				'conditions' => array('Player.session_id' => $id),
				'order' => array('name')
			));

			// Get Players Ids
			$playersIds = array();
			foreach($players as $item) {
				$playersIds[] = $item['Player']['id'];
			}

			// Get Players Bonus
			$this->GameSession->Player->Bonus->unbindModel(array('belongsTo' => array('Player')));
			$bonus = $this->GameSession->Player->Bonus->find('all', array(
				'fields' => array('Bonus.scenario', 'Bonus.value', 'Bonus.player_id', 'Type.name'), 
				'conditions' => array('Bonus.player_id' => $playersIds),
				'order' => array('Bonus.player_id', 'Bonus.scenario')
			));

			// Get Players Scores
			$this->GameSession->Player->Score->unbindModel(array('belongsTo' => array('Player')));
			$scores = $this->GameSession->Player->Score->find('all', array(
				'fields' => array('Score.value', 'Score.player_id', 'Type.name'), 
				'conditions' => array('Score.player_id' => $playersIds),
				'order' => array('Score.player_id', 'Type.id')
			));

			// Get Players Activities Logs
			$this->GameSession->Player->ActivityLog->unbindModel(array('belongsTo' => array('Player')));
			$activities = $this->GameSession->Player->ActivityLog->find('all', array(
				'fields' => array('Activity.name', 'ActivityLog.reward', 'ActivityLog.penalty', 'ActivityLog.attempts', 'ActivityLog.player_id'), 
				'conditions' => array('ActivityLog.player_id' => $playersIds),
				'order' => array('ActivityLog.player_id', 'Activity.name')
			));

			// Set Grades List
			$grades = array();
			for($i = 1.0; $i >= 0; $i-= 0.1) {
				if($i < 0.1) $i = 0;
				$grades[($i == 1)? '1.0' : (($i == 0)? '0.0' : (string) $i)] = ($i * 100).'%';
			}

			$this->set(compact('players', 'bonus', 'scores', 'activities', 'grades'));
			$this->set('title_for_layout', __('Session Log'));
		}
	}
	
}