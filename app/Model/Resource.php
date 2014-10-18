<?php
/**
 * Imports
 */
App::uses('AppModel', 'Model');

/**
 * Resource Model
 *
 * @package app.Model
 * @property ResourceType $Type
 * @property User $User
 * @property Project $Project
 * @property LearningSubject $Subject
 * @author Bruno Sampaio
 */
class Resource extends AppModel {

	/**
	 * @var string Display field
	 */
	public $displayField = 'name';
	

	/**
	 * @var array Validation rules
	 */
	public $validate = array(
		'name' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'Mandatory field.'
			),
			'minlength' => array(
				'rule' => array('minlength', 2),
				'message' => 'Minimum 2 characters long.'
			),
			'maxlength' => array(
				'rule' => array('maxlength', 50),
				'message' => 'Maximum 50 characters long.'
			)
		),
		/*'source' => array(
			'url' => array(
				'rule' => array('url'),
				'message' => 'Must be a URL.',
				'allowEmpty' => true
			)
		),*/
		/*'lms_url' => array(
			'url' => array(
				'rule' => array('url'),
				'message' => 'Must be a URL.',
				'allowEmpty' => true
			)
		),*/
		'type_id' => array(
			'numeric' => array(
				'rule' => array('numeric')
			),
		),
		'user_id' => array(
			'numeric' => array(
				'rule' => array('numeric')
			),
		),
	);


	/**
	 * @var array belongsTo associations
	 */
	public $belongsTo = array(
		'LMS' => array(
			'className' => 'LMS',
			'foreignKey' => 'lms_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'Type' => array(
			'className' => 'ResourceType',
			'foreignKey' => 'type_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'User' => array(
			'className' => 'User',
			'foreignKey' => 'user_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);


	/**
	 * @var array hasAndBelongsToMany associations
	 */
	public $hasAndBelongsToMany = array(
		'Project' => array(
			'className' => 'Project',
			'joinTable' => 'projects_resources',
			'foreignKey' => 'resource_id',
			'associationForeignKey' => 'project_id',
			'unique' => 'keepExisting',
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'finderQuery' => '',
			'deleteQuery' => '',
			'insertQuery' => ''
		),
		'Subject' => array(
			'className' => 'LearningSubject',
			'joinTable' => 'resources_subjects',
			'foreignKey' => 'resource_id',
			'associationForeignKey' => 'subject_id',
			'unique' => 'keepExisting',
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'finderQuery' => '',
			'deleteQuery' => '',
			'insertQuery' => ''
		)
	);
	
	
	/**
	 * Validates External File URL
	 *
	 * Validates a external URL by verifying if its mime type and extension are correct.
	 * In case it is a video, this method also verifies if it is a Youtube or Vimeo video
	 * and if yes parses the URL and converts it into the respective embed URL.
	 *
	 * @return mixed
	 */
	public function validateExternalFile() {
		if(isset($this->data['Resource']['source']) && $this->data['Resource']['source']) {
			if(isset($this->data['Type'])) {
				
				$file = $this->data['Resource']['source'];
				$ext = pathinfo($file, PATHINFO_EXTENSION);
				$types = Configure::read('Files.types.'.$this->data['Type']['mime']);
				
				if(!empty($types)) {
					if(in_array($ext, $types)) {
						return true;
					}
					else if($this->data['Type']['mime'] == 'video') {
						$urls = parse_url($file);
						
						//Youtube
						if($urls['host'] == 'www.youtube.com' && !empty($urls['query'])) {
							parse_str($urls['query']);
							$id = $v;
							$this->data['Resource']['source'] = 'http://www.youtube.com/embed/'.$id;
							return true;
						}
						else if($urls['host'] == 'youtu.be' && isset($urls['path'])) {
							$id = substr($urls['path'], 1);
							$this->data['Resource']['source'] = 'http://www.youtube.com/embed/'.$id;
							return true;
						}
						//Vimeo
						else if($urls['host'] == 'vimeo.com') {
							$id = substr($urls['path'], 1, strlen($urls['path']));

							$this->data['Resource']['source'] = 'http://player.vimeo.com/video/'.$id;
							return true;
						}
						//Embed URL
						else if(($urls['host'] == 'www.youtube.com' && strpos($urls['path'], 'embed')) || $urls['host'] == 'player.vimeo.com') {
							return true;
						}
						//Unknown
						else{
							return array('error' => __('error-unknown-provider'));
						}
					}
					else {
						$types = implode(', ', $types);
						return array('error' => __('error-file-type', $types));
					}
				}
				else {
					return array('error' => __('error-file-unknown', $ext));
				}
			}
		}
		else {
			return array('error' => __('error-file-url'));
		}
	}
	
	
	/**
	 * Set the 'file_url', 'external' and 'imported' attributes for each resource.
	 * 
	 * @param array $results
	 * @param bool $primary
	 * @return array
	 */
	public function afterFind($results, $primary = false) {
		foreach($results as $key => &$value) {
			$current = &$value['Resource'];
			if(isset($current['id'])) {
				if($current['source']) {
					$current['file_url'] = $current['source'];
					$current['external'] = true;
				}
				else {
					$this->data = $value;
					$current['file_url'] = $this->getFile($value['Type']);
					$current['external'] = false;
				}
				$current['imported'] = $this->wasImported($current);
			}
		}
		return $results;
	}
	
	
	/**
	 * Make sure the 'source' and 'lms_url' attributes are a full string.
	 * If not change those attributes values to null.
	 * 
	 * @param array $options
	 * @return bool
	 */
	public function beforeSave($options = array()) {
	    if (isset($this->data['Resource']['source'])) {
			$source = $this->data['Resource']['source'];
	        $this->data['Resource']['source'] = (strlen($source) == 0)? null : $source;
	    }
		
		if (isset($this->data['Resource']['lms_url'])) {
			$url = $this->data['Resource']['lms_url'];
			$this->data['Resource']['lms_url'] = (strlen($url) == 0)? null : $url;
	    }
		
	    return true;
	}
	
	
	/**
	 * Get Resource File Path
	 *
	 * @param array $type - the resource type data.
	 * @return string
	 */
	public function getFile($type) {
		$folder = Configure::read('Folders.files.resources');
		
		foreach(Configure::read('Files.types.'.$type['mime']) as $ext) {
			$filename = $folder.DS.$this->data['Resource']['id'].'.'.$ext;
			if(file_exists(FILES.$filename)) {
				return $filename;
			}
		}
		return null;
	}
	
	
	/**
	 * Get Resources Data to Load to a Project
	 *
	 * @param array $ids - list of resources ids.
	 * @param int $userId - identifier of the user requesting this data.
	 * @param string $folder - the resource path prefix.
	 */
	public function getProjectData($ids, $userId, $folder) {
		$fields = array('Resource.id', 'Resource.name', 'Resource.source', 'Resource.lms_id', 'Resource.lms_url', 'Resource.external_id', 'Type.mime', 'Type.icon');
		$conditions = array('Resource.id' => $ids, 'Resource.user_id' => $userId);
		
		$this->unbindModel(array('belongsTo' => array('LMS', 'User'), 'hasAndBelongsToMany' => array('Project', 'Subject')));
		$data = $this->find('all', array('fields' => $fields, 'conditions' => $conditions));
		
		$resources = array();
		foreach($data as $item) {
			$url = $item['Resource']['external']? $item['Resource']['file_url'] : $folder.$item['Resource']['file_url'];
			$resources[$item['Resource']['id']] = array(
				'name' => $item['Resource']['name'], 
				'type' => $item['Type']['icon'], 
				'url' => $url, 
				'external' => $item['Resource']['external']
			);
		}
		
		return $resources;
	}
	
	
	/**
	 * Get Resources Data to Store into Games Database
	 *
	 * @param array $ids - list of resources ids.
	 * @param int $userId - identifier of the user requesting this data.
	 * @param int $gameId - the game id.
	 * @return array
	 */
	public function getGameData($ids, $userId, $gameId) {
		$fields = array('Resource.id', 'Resource.source', 'Resource.type_id', 'Resource.lms_id', 'Resource.lms_url', 'Resource.external_id', 'Type.mime');
		$conditions = array('Resource.id' => $ids, 'Resource.user_id' => $userId);
		
		$this->unbindModel(array('belongsTo' => array('LMS', 'User'), 'hasAndBelongsToMany' => array('Project', 'Subject')));
		$data = $this->find('all', array('fields' => $fields, 'conditions' => $conditions));
		
		$resources = array();
		foreach($data as $item) {
			$resources[] = array('Resource' => array(
				'url' => $item['Resource']['file_url'],
				'external' => $item['Resource']['external'],
				'original_id' => $item['Resource']['id'],
				'type_id' => $item['Resource']['type_id'],
				'game_id' => $gameId
			));
		}
		
		return $resources;
	}
}
