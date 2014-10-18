<?php
/**
 * Imports
 */
App::uses('AppModel', 'Model');

/**
 * ActivityHint Model
 *
 * @package app.Model
 * @property Activity $Activity
 * @author Bruno Sampaio
 */
class ActivityHint extends AppModel {
	
	/**
	 * @var string Model name
	 */
	public $name = 'ActivityHint';
	
	
	/**
	 * @var string Table name
	 */
	public $useTable = 'activities_hints';
	
	
	/**
	 * @var string Display field
	 */
	public $displayField = 'content';


	/**
	 * Validation rules
	 *
	 * @var array
	 */
	public $validate = array(
		'content' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'Mandatory field.'
			),
			'maxlength' => array(
				'rule' => array('maxlength', 200),
				'message' => 'Maximum 200 characters long.'
			)
		),
		'activity_id' => array(
			'numeric' => array(
				'rule' => array('numeric')
			),
		),
	);


	/**
	 * belongsTo associations
	 *
	 * @var array
	 */
	public $belongsTo = array(
		'Activity' => array(
			'className' => 'Activity',
			'foreignKey' => 'activity_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);
	
	
	/**
	 * Get Hint data to be stored into the games database.
	 *
	 * @param array $ids - the hints ids list.
	 * @param array $activitiesById - the list of activities in the game sorted by id.
	 * @param array $activitiesData - the game activities data.
	 */
	public function getGameData($ids, &$activitiesById, &$activitiesData) {
		$ids = implode(', ', $ids);
		
		$db = $this->getDataSource();
		$data = $db->fetchAll(
			"SELECT Hint.id, Hint.content, Hint.activity_id FROM activities_hints as Hint WHERE Hint.id IN ($ids) ORDER BY Hint.activity_id"
		);
		
		foreach($data as $item) {
			$activityId = $item['Hint']['activity_id'];
			if(isset($activitiesById[$activityId])) {
				$activityData =& $activitiesData[$activitiesById[$activityId]];
				$activityData['Activity']['has_hints'] = true;
				$activityData['Hint'][] = array(
					'content' => $item['Hint']['content'], 
					'original_id' => $item['Hint']['id']
				);
			}
		}
	}
}
