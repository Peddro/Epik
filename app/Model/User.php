<?php
/**
 * Imports
 */
App::uses('AppModel', 'Model');

/**
 * User Model
 *
 * @package app.Model
 * @property Activity $Activity
 * @property Game $Game
 * @property Project $Project
 * @property Resource $Resource
 * @property Template $Template
 * @author Bruno Sampaio
 */
class User extends AppModel {

	/**
	 * @var string Display field
	 */
	public $displayField = 'name';
	
	
	/**
	 * @var array Virtual fields
	 */
	public $virtualFields = array(
	    'name' => "CONCAT(User.firstname, ' ', User.lastname)"
	);
	
	
	/**
	 * @var array Validation rules
	 */
	public $validate = array(
		'firstname' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'Mandatory field.'
			),
			'alphanumeric' => array(
				'rule' => array('alphanumeric'),
				'message' => 'Must be alphanumeric.'
			),
			'maxlength' => array(
				'rule' => array('maxlength', 100),
				'message' => 'Maximum 100 characters long.'
			)
		),
		'lastname' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'Mandatory field.'
			),
			'alphanumeric' => array(
				'rule' => array('alphanumeric'),
				'message' => 'Must be alphanumeric.'
			),
			'maxlength' => array(
				'rule' => array('maxlength', 100),
				'message' => 'Maximum 100 characters long.'
			)
		),
		'picture' => array(
			'boolean' => array(
				'rule' => array('boolean')
			),
		),
		'lms_url' => array(
			'url' => array(
				'rule' => array('url'),
				'message' => 'Must be a URL.',
				'allowEmpty' => true
			),
		),
		'username' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'Mandatory field.'
			),
			'minlength' => array(
				'rule' => array('minlength', 2),
				'message' => 'Minimun 2 characters long.'
			),
			'maxlength' => array(
				'rule' => array('maxlength', 100),
				'message' => 'Maximum 100 characters long.'
			)
		),
		'email' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'Mandatory field.'
			),
			'email' => array(
				'rule' => array('email'),
				'message' => 'Must be an e-mail address.'
			),
			'maxlength' => array(
				'rule' => array('maxlength', 100),
				'message' => 'Maximum 100 characters long.'
			)
		),
		'password' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'Mandatory field.'
			),
			'minlength' => array(
				'rule' => array('minlength', 5),
				'message' => 'Minimun 5 characters long.'
			)
		),
		'secret' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'Mandatory field.'
			),
		),
		'role' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'Mandatory field.'
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
		)
	);
	
	
	/**
	 * Set the 'picture_url' and 'lms_name' attributes for each user.
	 * 
	 * @param array $results
	 * @param bool $primary
	 * @return array
	 */
	public function afterFind($results, $primary = false) {
		foreach($results as $key => $val) {
			if(isset($val[$this->name]['picture'])) {
				$this->data = $val;
				$results[$key][$this->name]['picture_url'] = $this->getPicture();
			}
			
			if(isset($val['LMS']['name'])) {
				$results[$key][$this->name]['lms_name'] = $val['LMS']['name'];
			}
		}
		return $results;
	}
	
	
	/**
	 * Make sure the 'lms_url' attribute has the 'http://' prefix before validating it.
	 *
	 * @param array $options
	 * @return bool
	 */
	public function beforeValidate($options = array()) {
		if(isset($this->data[$this->name]['lms_url'])) {
			if(!$this->startsWith($this->data[$this->name]['lms_url'], 'http')) {
				$this->data[$this->name]['lms_url'] = 'http://'.$this->data[$this->name]['lms_url'];
			}
		}
		return true;
	}
	
	
	/**
	 * Encrypt the user password before saving it.
	 *
	 * @param array $options
	 * @return bool
	 */
	public function beforeSave($options = array()) {
		if(isset($this->data[$this->name]['password'])) {
			$this->data[$this->name]['password'] = AuthComponent::password($this->data[$this->name]['password']);
		}
		return true;
	}
	
	
	/**
	 * Get User Full Name
	 *
	 * @return string
	 */
	public function getName() {
		return $this->data[$this->name]['firstname'].' '.$this->data[$this->name]['lastname'];
	}
	
	
	/**
	 * Get User Picture Path
	 * 
	 * @return string
	 */
	public function getPicture() {
		$data = $this->data[$this->name];
		return $this->getImagePath($data, $this->name, IMAGES, Configure::read('Folders.img.users').DS.$data['id'], 'picture');
	}


	/**
	 * Create New Password from Random Characters
	 * 
	 * @param int $len - password length.
	 * @return string
	 */
	public function createTempPassword($len) {
		$pass = '';
		$lchar = 0;
		$char = 0;

		for($i = 0; $i < $len; $i++) {
			while($char == $lchar){
				$char = rand(48, 109);
				if($char > 57) $char += 7;
				if($char > 90) $char += 6;
			}
			$pass .= chr($char);
			$lchar = $char;
		}
		return $pass;
	}

}
