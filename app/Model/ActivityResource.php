<?php
/**
 * Imports
 */
App::uses('AppModel', 'Model');

/**
 * ActivityResource Model
 *
 * @package app.Model
 * @property Activity $Activity
 * @property Resource $Resource
 * @author Bruno Sampaio
 */
class ActivityResource extends AppModel {
	
	/**
	 * @var string Model name
	 */
	public $name = 'ActivityResource';
	
	
	/**
	 * @var string Table name
	 */
	public $useTable = 'activities_resources';
	
	
	/**
	 * @var string Display field
	 */
	public $displayField = 'activity_id';
	

	/**
	 * @var array Validation rules
	 */
	public $validate = array();


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
		'Resource' => array(
			'className' => 'Resource',
			'foreignKey' => 'resource_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);
	
	
	/**
	 * Get Resources associated to Activities to be stored into the games database.
	 *
	 * @param array $resourcesIds - the resources ids list.
	 * @param array $activitiesIds - the activities ids list.
	 * @param array $activitiesById - the list of activities in the game sorted by id.
	 * @param array $activitiesData - the game activities data.
	 * @param array $ARData - the activities and resources relations data.
	 */
	public function getGameData($resourcesIds, $activitiesIds, &$activitiesById, &$activitiesData, &$ARData) {
		$resourcesIds = implode(',', $resourcesIds);
		
		$db = $this->getDataSource();
		$data = $db->fetchAll(
			"SELECT ActivityResource.activity_id, ActivityResource.resource_id 
			FROM activities_resources as ActivityResource 
			WHERE ActivityResource.activity_id IN ($activitiesIds) AND ActivityResource.resource_id IN ($resourcesIds) 
			ORDER BY ActivityResource.activity_id"
		);
		
		foreach($data as $item) {
			$activityId = $item['ActivityResource']['activity_id'];
			if(isset($activitiesById[$activityId])) {
				$activityData =& $activitiesData[$activitiesById[$activityId]];
				$activityData['Activity']['has_resources'] = true;
				
				$ARData[] = array(
					'GameActivityResource' => array(
						'activity_id' => $activityId, 
						'resource_id' => $item['ActivityResource']['resource_id']
					)
				);
			}
		}
	}
}
