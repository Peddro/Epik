<?php
/**
 * Imports
 */
App::uses('AppModel', 'Model');

/**
 * QuestionsGroup Model
 *
 * @package app.Model
 * @property Activity $Activity
 * @property Question $Question
 * @author Bruno Sampaio
 */
class QuestionsGroup extends AppModel {

	/**
	 * @var string Display field
	 */
	public $displayField = 'activity_id';
	

	/**
	 * @var array Validation rules
	 */
	public $validate = array(
		'activity_id' => array(
			'numeric' => array(
				'rule' => array('numeric')
			),
		),
		'question_id' => array(
			'numeric' => array(
				'rule' => array('numeric')
			),
		),
	);
	

	/**
	 * @var array belongsTo associations
	 */
	public $belongsTo = array(
		'Activity' => array(
			'className' => 'Activity',
			'foreignKey' => 'activity_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'Question' => array(
			'className' => 'Question',
			'foreignKey' => 'question_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);
	
	
	/**
	 * Get a Question Group Data
	 *
	 * @param int $activityId - the activity id.
	 * @return array
	 */
	public function getData($activityId) {
		$db = $this->getDataSource();
		$data = $db->fetchAll(
		    'SELECT 
				Activity.id, Activity.name, Activity.description, Activity.user_id,
				Question.id, Type.id, Type.icon,
				QuestionActivity.id, QuestionActivity.name
			FROM 
				activities as Activity
				INNER JOIN questions_groups as QuestionsGroup ON (QuestionsGroup.activity_id = Activity.id)
				INNER JOIN questions as Question ON (QuestionsGroup.question_id = Question.id)
				INNER JOIN activities as QuestionActivity ON (QuestionActivity.id = Question.activity_id)
				INNER JOIN questions_types as Type ON (Question.type_id = Type.id)
			WHERE Activity.id = :id
			ORDER BY QuestionActivity.name',
		    array('id' => $activityId)
		);
		
		if(count($data) > 0) {
			$group = array(
				'Activity' => array(
					'id' => $data[0]['Activity']['id'],
					'name' => $data[0]['Activity']['name'],
					'description' => $data[0]['Activity']['description'],
					'user_id' => $data[0]['Activity']['user_id']
				),
				'Question' => array()
			);
			
			foreach($data as $item) {
				$question = array(
					'id' => $item['Question']['id'],
					'name' => $item['QuestionActivity']['name'],
					'icon' => $item['Type']['icon'],
					'activity_id' => $item['QuestionActivity']['id']
				);
				array_push($group['Question'], $question);
			}
			
			$data = $group;
		}
		
		return $data;
	}
	
	
	/**
	 * Get a Random Questions from Questions Group to Load to a Project
	 *
	 * @param array $ids - list of questions groups ids.
	 * @param int $userId - identifier of the user requesting this data.
	 * @return array
	 */
	public function getProjectData($ids, $userId) {
		
		$db = $this->getDataSource();
		$data = $db->fetchAll(
		    'SELECT QuestionsGroup.id, QuestionsGroup.name, Question.content, Type.icon, Answer.id, Answer.content
			FROM
			    (SELECT Activity.id, Activity.name,
			        (SELECT Question.id
			        FROM questions as Question
					INNER JOIN questions_groups as QuestionsGroup ON (QuestionsGroup.question_id = Question.id)
			        WHERE QuestionsGroup.activity_id = Activity.id
			        ORDER BY RAND() 
					LIMIT 1) AS question_id
			    FROM activities Activity
				WHERE Activity.id IN ('.implode(', ', $ids).') AND Activity.user_id = :user) QuestionsGroup
				INNER JOIN questions as Question ON (Question.id = QuestionsGroup.question_id)
				INNER JOIN questions_types as Type ON (Question.type_id = Type.id)
				INNER JOIN answers as Answer ON (Answer.question_id = Question.id)
			ORDER BY QuestionsGroup.id',
		    array('user' => $userId)
		);
		
		if(count($data) > 0) {
			$questions = array();
			
			$stored = array();
			$answers = array();
			foreach($data as $item) {
				$id = $item['QuestionsGroup']['id'];
				
				if(!isset($stored[$id])) {
					$questions[$id] = array(
						'name' => $item['QuestionsGroup']['name'],
						'type' => $item['Type']['icon'],
						'question' => $item['Question']['content'],
						'answers' => array()
					);
					$stored[$id] = true;
					$answers = array();
				}
				
				$answerId = $item['Answer']['id'];
				if(!isset($answers[$answerId])) {
					$questions[$id]['answers'][$answerId] = $item['Answer']['content'];
					$answers[$answerId] = true;
				}
			}
			
			$data = $questions;
		}
		
		return $data;
	}
	
	
	/**
	 * Get All Questions Data from the specified Questions Groups to Store into Games Database
	 *
	 * @param array $ids - list of questions groups ids.
	 * @param array $hintsIds - list of questions hints ids.
	 * @param array $resourcesIds - list of questions resources ids.
	 * @param int $userId - identifier of the user requesting this data.
	 * @return array
	 */
	public function getGameData($ids, $hintsIds, $resourcesIds, $userId) {
		$db = $this->getDataSource();
		$data = array('hints' => array(), 'resources' => array());
		
		$data['questions'] = $this->find('all', array(
			'fields' => array('QuestionsGroup.activity_id', 'Question.id', 'Question.activity_id'),
			'conditions' => array('QuestionsGroup.activity_id' => $ids, 'Activity.user_id' => $userId),
			'order' => array('QuestionsGroup.activity_id')
		));
		
		if($hintsIds) {
			$data['hints'] = $db->fetchAll(
			    "SELECT 
					Hint.id
				FROM 
					questions_groups as QuestionsGroup
					INNER JOIN questions as Question ON (QuestionsGroup.question_id = Question.id)
					INNER JOIN activities as Activity ON (Activity.id = Question.activity_id)
					INNER JOIN activities_hints as Hint ON (Activity.id = Hint.activity_id)
				WHERE 
					QuestionsGroup.activity_id IN (".implode(', ', $hintsIds).")
				ORDER BY Hint.id"
			);
		}
		
		if($resourcesIds) {
			$data['resources'] = $db->fetchAll(
			    "SELECT 
					Resource.id
				FROM 
					questions_groups as QuestionsGroup
					INNER JOIN questions as Question ON (QuestionsGroup.question_id = Question.id)
					INNER JOIN activities as Activity ON (Activity.id = Question.activity_id)
					INNER JOIN activities_resources ON (activities_resources.activity_id = Activity.id)
					INNER JOIN resources as Resource ON (activities_resources.resource_id = Resource.id)
				WHERE 
					QuestionsGroup.activity_id IN (".implode(', ', $resourcesIds).")
				ORDER BY Resource.id"
			);
		}
		
		return $data;
	}
}
