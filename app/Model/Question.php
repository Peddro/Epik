<?php
/**
 * Imports
 */
App::uses('AppModel', 'Model');

/**
 * Question Model
 *
 * @package app.Model
 * @property QuestionType $Type
 * @property Activity $Activity
 * @property Answer $Answer
 * @property QuestionGroup $Group
 * @property Resource $Resource
 * @author Bruno Sampaio
 */
class Question extends AppModel {

	/**
	 * @var string Display field
	 */
	public $displayField = 'content';


	/**
	 * @var array Validation rules
	 */
	public $validate = array(
		'content' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'Mandatory field.'
			),
			'minlength' => array(
				'rule' => array('minlength', 5),
				'message' => 'Minimum 5 characters long.'
			),
			'maxlength' => array(
				'rule' => array('maxlength', 200),
				'message' => 'Maximum 200 characters long.'
			),
		),
		'activity_id' => array(
			'numeric' => array(
				'rule' => array('numeric')
			),
		)
	);


	/**
	 * @var array belongsTo associations
	 */
	public $belongsTo = array(
		'Type' => array(
			'className' => 'QuestionType',
			'foreignKey' => 'type_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'Activity' => array(
			'className' => 'Activity',
			'foreignKey' => 'activity_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);
	

	/**
	 * @var array hasMany associations
	 */
	public $hasMany = array(
		'Answer' => array(
			'className' => 'Answer',
			'foreignKey' => 'question_id',
			'dependent' => false,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		)
	);
	
	
	/**
	 * Get a Question Data and its respective Hints and Resources
	 *
	 * @param int $activityId - the activity id.
	 * @return array
	 */
	public function getData($activityId) {
		$db = $this->getDataSource();
		$data = $db->fetchAll(
		    'SELECT 
				Activity.id, Activity.name, Activity.description, Activity.lms_id, Activity.lms_url, Activity.external_id, Activity.user_id,
				LMS.id, LMS.name,
				Question.id, Question.content,
				Type.id, Type.name, Type.max_answers,
				Answer.id, Answer.content, Answer.is_correct,
				Hint.id, Hint.content,
				Resource.id, Resource.name, Resource.lms_id, Resource.source
			FROM 
				activities as Activity
				INNER JOIN questions as Question ON (Question.activity_id = Activity.id)
				INNER JOIN questions_types as Type ON (Question.type_id = Type.id)
				INNER JOIN answers as Answer ON (Answer.question_id = Question.id)
				LEFT JOIN lms as LMS ON (LMS.id = Activity.lms_id)
				LEFT JOIN activities_hints as Hint ON (Hint.activity_id = Activity.id)
				LEFT JOIN activities_resources ON (activities_resources.activity_id = Activity.id)
				LEFT JOIN resources as Resource ON (activities_resources.resource_id = Resource.id)
			WHERE Activity.id = :id',
		    array('id' => $activityId)
		);
		
		if(count($data) > 0) {
			$activity = $data[0]['Activity'];
			$quest = $data[0]['Question'];
			$type = $data[0]['Type'];
			
			$question = array(
				'Activity' => array(
					'id' => $activity['id'],
					'name' => $activity['name'],
					'description' => $activity['description'],
					'lms_id' => $activity['lms_id'],
					'lms_url' => $activity['lms_url'],
					'external_id' => $activity['external_id'],
					'user_id' => $activity['user_id'],
					'imported' => $this->wasImported($activity),
				),
				'Question' => array(
					'id' => $quest['id'],
					'content' => $quest['content']
				),
				'Type' => array(
					'id' => $type['id'],
					'name' => $type['name'],
					'max_answers' => $type['max_answers']
				),
				'Answer' => array(),
				'Hint' => array(),
				'Resource' => array()
			);
			
			if($data[0]['LMS']['id']) {
				$question['LMS'] = array('id' => $data[0]['LMS']['id'], 'name' => $data[0]['LMS']['name']);
			}
			
			$answers = array(); $a = 0;
			$hints = array(); $h = 0;
			$resources = array(); $r = 0;
			foreach($data as $item) {
				$answerId = $item['Answer']['id'];
				if(!isset($answers[$answerId])) {
					$question['Answer'][$a]['id'] = $answerId; 
					$question['Answer'][$a]['content'] = $item['Answer']['content'];
					$question['Answer'][$a]['is_correct'] = $item['Answer']['is_correct'];
					$answers[$answerId] = true;
					$a++;
				}
				
				if(isset($item['Hint']['id'])) {
					$hintId = $item['Hint']['id'];
					if(!isset($hints[$hintId])) {
						$question['Hint'][$h]['id'] = $hintId; 
						$question['Hint'][$h]['content'] = $item['Hint']['content'];
						$hints[$hintId] = true;
						$h++;
					}
				}
				
				if(isset($item['Resource']['id'])) {
					$resourceId = $item['Resource']['id'];
					if(!isset($resources[$resourceId])) {
						$question['Resource'][$r]['id'] = $resourceId; 
						$question['Resource'][$r]['name'] = $item['Resource']['name'];
						$question['Resource'][$r]['lms_id'] = $item['Resource']['lms_id'];
						$question['Resource'][$r]['source'] = $item['Resource']['source'];
						$resources[$resourceId] = true;
						$r++;
					}
				}
			}
			
			$data = $question;
		}
		
		return $data;
	}
	
	
	/**
	 * Get Questions Data to Load to a Project
	 *
	 * @param array $ids - list of questions ids.
	 * @param int $userId - identifier of the user requesting this data.
	 * @return array
	 */
	public function getProjectData($ids, $userId) {
		
		$db = $this->getDataSource();
		$data = $db->fetchAll(
		    'SELECT 
				Activity.id, Activity.name, Activity.user_id, Question.content, Type.icon, 
				Answer.id, Answer.content,
				Hint.id, Hint.content, 
				Resource.id, Resource.name
			FROM 
				activities as Activity
				INNER JOIN questions as Question ON (Question.activity_id = Activity.id)
				INNER JOIN questions_types as Type ON (Question.type_id = Type.id)
				INNER JOIN answers as Answer ON (Answer.question_id = Question.id)
				LEFT JOIN activities_hints as Hint ON (Hint.activity_id = Activity.id)
				LEFT JOIN activities_resources ON (activities_resources.activity_id = Activity.id)
				LEFT JOIN resources as Resource ON (activities_resources.resource_id = Resource.id)
			WHERE Activity.id IN ('.implode(', ', $ids).') AND Activity.user_id = :user
			ORDER BY Activity.id',
		    array('user' => $userId)
		);
		
		if(count($data) > 0) {
			$questions = array();
			
			$stored = array();
			$answers = array();
			$hints = array();
			$resources = array();
			foreach($data as $item) {
				$id = $item['Activity']['id'];
				
				if(!isset($stored[$id])) {
					$questions[$id] = array(
						'name' => $item['Activity']['name'],
						'type' => $item['Type']['icon'],
						'question' => $item['Question']['content'],
						'answers' => array(),
						'selectable' => array(
							'hints' => new stdClass(),
							'resource' => new stdClass()
						)
					);
					
					$stored[$id] = true;
				}
				
				$answerId = $item['Answer']['id'];
				if(!isset($answers[$answerId])) {
					$questions[$id]['answers'][$answerId] = $item['Answer']['content'];
					$answers[$answerId] = true;
				}
				
				if(isset($item['Hint']['id'])) {
					$hintId = $item['Hint']['id'];
					if(!isset($hints[$hintId])) {
						$questions[$id]['selectable']['hints']->{$hintId} = $item['Hint']['content'];
						$hints[$hintId] = true;
					}
				}
				
				if(isset($item['Resource']['id'])) {
					$resourceId = $item['Resource']['id'];
					if(!isset($resources[$resourceId])) {
						$questions[$id]['selectable']['resource']->{$resourceId} = $item['Resource']['name'];
						$resources[$resourceId] = true;
					}
				}
			}
			
			$data = $questions;
		}
		
		return $data;
	}
	
	
	/**
	 * Get Questions Data to Store into Games Database
	 *
	 * @param array $ids - list of questions ids.
	 * @param array $hintsIds - list of questions hints ids.
	 * @param array $resourcesIds - list of questions resources ids.
	 * @param int $userId - identifier of the user requesting this data.
	 * @param int $gameId - the game id.
	 * @param array $activitiesData - the game activities data.
	 * @param array $ARData - the activities and resources relations data.
	 */
	public function getGameData($ids, $hintsIds, $resourcesIds, $userId, $gameId, &$activitiesData, &$ARData) {
		$questionsIds = implode(',', $ids);
		
		$db = $this->getDataSource();
		$data = $db->fetchAll(
		    "SELECT 
				Activity.id, Activity.name,
				Question.content, Question.type_id, 
				Answer.id, Answer.content, Answer.is_correct
			FROM 
				activities as Activity
				INNER JOIN questions as Question ON (Question.activity_id = Activity.id)
				INNER JOIN questions_types as Type ON (Question.type_id = Type.id)
				INNER JOIN answers as Answer ON (Answer.question_id = Question.id)
			WHERE 
				Activity.id IN ($questionsIds) AND 
				Activity.user_id = $userId
			ORDER BY Activity.id"
		);
		
		if(count($data) > 0) {
			
			// Parse Questions to Game Data
			$i = count($activitiesData) - 1;
			$stored = array();
			$answers = array();
			foreach($data as $item) {
				$id = $item['Activity']['id'];
				
				if(!isset($stored[$id])) {
					$activitiesData[] = array(
						'Activity' => array(
							'name' => $item['Activity']['name'],
							'original_id' => $id,
							'has_hints' => false,
							'has_resources' => false,
							'game_id' => $gameId
						),
						'Question' => array(
							'content' => $item['Question']['content'],
							'type_id' => $item['Question']['type_id'],
							'Answer' => array()
						),
						'Hint' => array()
					);
					
					$i++;
					$stored[$id] = $i;
				}
				
				$answerId = $item['Answer']['id'];
				if(!isset($answers[$answerId])) {
					$activitiesData[$i]['Question']['Answer'][] = array('content' => $item['Answer']['content'], 'is_correct' => $item['Answer']['is_correct']);
					$answers[$answerId] = true;
				}
			}
			
			// Parse Hints to Game Data
			if($hintsIds) {
				$this->Activity->Hint->getGameData($hintsIds, $stored, $activitiesData);
			}
			
			// Parse Resources to Game Data
			if($resourcesIds) {
				$this->Activity->bindModel(array('hasOne' => array('ActivityResource' => array('className' => 'ActivityResource', 'foreignKey' => 'activity_id'))));
				$this->Activity->ActivityResource->getGameData($resourcesIds, $questionsIds, $stored, $activitiesData, $ARData);
			}
		}
	}
}
