<?php
/**
 * Imports
 */
App::uses('Component', 'Controller');

/**
 * LMSServices Component
 *
 * @package app.Controller.Component
 * @author Bruno Sampaio
 */
class MoodleServicesComponent extends LMSServicesComponent {
	
	
	/**
	 * Connect to Moodle
	 * 
	 * @param array $lms - lms id and url.
	 * @param array $user - user data.
	 * @return bool
	 */
	public function connect($lms, $user) {	
		$id = $lms['id'];
		$url = $lms['url'];
		
		$username = $user['username'];
		$password = $user['password'];
		
		if($lms) {
			
			// Get user token
			$curl = new curl;
			$request = '/login/token.php?username='.$username.'&password='.$password.'&service=moodle_epik_app';
			$resp = json_decode($curl->get($url . $request));
			
			if(isset($resp->token)) {
				
				// Get user and website info
				$lmsInfo = $this->sendRequest('get', $url, $resp->token, 'core_webservice_get_site_info');
				
				if(isset($lmsInfo->SINGLE)) {
					$data = array('id' => $lms['id'], 'name' => $lms['name'], 'token' => $resp->token);
					foreach($lmsInfo->SINGLE->KEY as $item) {
						$name = (string) $item->attributes()->name;
						if($name == 'siteurl') {
							$name = 'url';
						}
						
						if(isset($item->VALUE)) {
							$data[$name] = (string) $item->VALUE;
						}
					}
					
					// Store the LMS info
					$this->Session->write('LMS', $data);
				}
				else if(isset($lmsInfo->MESSAGE)) {
					$this->Session->setFlash($lmsInfo->MESSAGE);
				}
				return true;
			}
			else if(isset($resp->error)) {
				$this->Session->setFlash($resp->error);
			}
			else {
				$this->Session->setFlash(__('error-lms-url', $lms['name']));
			}
		}
		return false;
	}
	
	
	/**
	 * Request Data from Moodle
	 * 
	 * @param string $data_type - the type of data to be requested (must belong to $requests array).
	 * @param array $params - the params to be passed to the external service function.
	 * @param array $args - the arguments to be passed to parsing function.
	 * @return array - parsed information.
	 */
	public function request($data_type, $params=array(), $args=array()) {
		$lms = $this->getLMS();
		if($lms) {
			
			$function = null;
			$execute = null;
			
			switch($data_type) {
				case $this->requests[0]:
					$function = 'core_enrol_get_users_courses';
					$execute = 'parseCourses';
					break;
				
				case $this->requests[1]:
					$function = 'core_course_get_contents';
					$execute = 'parseCourseContents';
					break;
					
				case $this->requests[2]:
					$function = 'mod_quiz_get_questions_info';
					$execute = 'parseQuestionsInfo';
					break;
					
				case $this->requests[3]:
					$function = 'mod_quiz_get_questions_data';
					$execute = 'parseQuestionsData';
					array_unshift($args, $lms);
					break;
					
				case $this->requests[4]:
					$function = 'mod_resource_get_info';
					$execute = 'parseResourcesInfo';
					array_unshift($args, $lms);
					break;
			}
			
			if($function && $execute) {
				if(!is_array($params)) {
					$params = array($params => $lms[$params]);
				}
				$contents = $this->sendRequest('get', $lms['url'], $lms['token'], $function, $params);

				if(isset($contents->MULTIPLE)) {
					array_unshift($args, $contents);
					return call_user_func_array(__CLASS__.'::'.$execute, $args);
				}
				else if(isset($contents->MESSAGE)) {
					$this->Session->setFlash($contents->MESSAGE);
				}
			}
			else {
				$this->Session->setFlash(__('error-lms-unknown-operation', $lms['name'].' 2.2+'));
			}
		}
		else {
			$this->Session->setFlash(__('error-lms-lost-user'));
		}
		return array();
	}
	
	
	/**
	 * Send Request
	 * 
	 * Creates the request URL and sends it. When the answer is received converts it into array format.
	 * @param string $method - HTTP Method to use.
	 * @param string $domain - the lms base url.
	 * @param string $token - the user token.
	 * @param string $function - the external service function to be requested.
	 * @param array $params - the parameters to send with the request.
	 * @param string $format - the output format.
	 * @return array - the data received already converted to array.
	 */
	private function sendRequest($method, $domain, $token, $function, $params=array(), $format='xml') {
		$url = "$domain/webservice/rest/server.php?wstoken=$token&wsfunction=$function&moodlewsrestformat=$format";
		return $this->requestURL($method, $url, $params);
	}
	
	
	/**
	 * Parse Courses
	 * 
	 * @param array $contents - the courses XML received from the server.
	 * @return array with parsed information.
	 */
	protected function parseCourses($contents) {
		$list = array();
		if(isset($contents->MULTIPLE->SINGLE)) {
			foreach($contents->MULTIPLE->SINGLE as $course) {
				$item = array('description' => null, 'icon' => 'course');
				
				foreach($course as $key) {
					$name = (string) $key->attributes()->name;
					if($name == 'fullname') {
						$name = 'name';
					}
					else if($name == 'summary') {
						$name = 'description';
					}
					$item[$name] = (string) $key->VALUE;
				} 
				array_push($list, array('Course' => $item));
			}
		}
		else {
			$this->Session->setFlash(__('warning-not-found', __('courses')), 'default', array('class' => 'message notice'));
		}
		return $list;
	}
	
	
	/**
	 * Parse Course Contents
	 * 
	 * @param array $contents - the course contents XML received from the server.
	 * @param string $type - the type of content to store.
	 * @return array with parsed information containing just the type of contents specified.
	 */
	protected function parseCourseContents($contents, $type) {
		$list = array();
		
		// Iterate over each course section
		if(isset($contents->MULTIPLE->SINGLE)) {
			foreach($contents->MULTIPLE->SINGLE as $section) {
				
				// Get course modules
				$modules = $section->KEY[$section->count()-1]->MULTIPLE->SINGLE;

				// Iterate over each content
				foreach($modules as $content) {
					$item = array();
					
					// Iterate only over contents of a certain type
					foreach($content as $key) {
						$name = (string) $key->attributes()->name;
						if($name == 'modname') {
							if($type != (string) $key->VALUE) break;
						}
						else if($name == 'modicon') {
							$name = 'image';
						}

						if(isset($key->VALUE)) {
							$item[$name] = (string) $key->VALUE;
						}
					}
					
					if(isset($item['modname'])) {
						array_push($list, array('Content' => $item));
					}
				}
			}
		}
		
		if(count($list) == 0) {
			$this->Session->setFlash(__('warning-not-found', __('contents')), 'default', array('class' => 'message notice'));
		}
		
		return $list;
	}
	
	
	/**
	 * Parse Questions Info
	 * 
	 * @param array $contents - the questions info XML received from the server.
	 * @return array with parsed information.
	 */
	protected function parseQuestionsInfo($contents) {
		$list = array();
		
		// Iterate over each course section
		if(isset($contents->MULTIPLE->SINGLE)) {
			foreach($contents->MULTIPLE->SINGLE as $question) {
				$item = array();
				
				foreach($question as $key) {
					$name = (string) $key->attributes()->name;
					$value = (string) $key->VALUE;
					
					if($name == 'content') {
						$name = 'description';
						$value = strip_tags($value);
					}
					else if($name == 'type') {
						$name = 'icon';
					}
					$item[$name] = $value;
				} 
				array_push($list, array('Question' => $item));
			}
		}
		else {
			$this->Session->setFlash(__('warning-not-found', __('questions')), 'default', array('class' => 'message notice'));
		}
		
		return $list;
	}
	
	
	/**
	 * Parse Questions Data
	 * 
	 * @param array $contents - the questions data XML received from the server.
	 * @param array $lms - the current lms data.
	 * @return array with parsed information.
	 */
	protected function parseQuestionsData($contents, $lms) {
		$list = array();
		
		// Iterate over each course section
		if(isset($contents->MULTIPLE->SINGLE)) {
			foreach($contents->MULTIPLE->SINGLE as $question) {
				
				$activity = array(
					'name' => (string) $question->KEY[1]->VALUE, 
					'description' => '',
					'lms_id' => $lms['id'], 
					'lms_url' => $lms['url'], 
					'external_id' => (int) $question->KEY[0]->VALUE
				);
				$item = array('content' => strip_tags((string) $question->KEY[2]->VALUE), 'type_icon' => (string) $question->KEY[3]->VALUE);
				
				// Answers
				$answers = array();
				if(isset($question->KEY[6]->MULTIPLE->SINGLE)) {
					foreach($question->KEY[6]->MULTIPLE->SINGLE as $answer) {
						array_push($answers, array(
							'content' => strip_tags((string) $answer->KEY[0]->VALUE),
							'is_correct' => floor((double) $answer->KEY[1]->VALUE)? 1 : 0
						));
					}
				}
				
				// Hints
				$hints = array();
				if(isset($question->KEY[7]->MULTIPLE->SINGLE)) {
					foreach($question->KEY[7]->MULTIPLE->SINGLE as $hint) {
						array_push($hints, array(
							'content' => strip_tags((string) $hint->KEY[0]->VALUE)
						));
					}
				}
				
				array_push($list, array('Activity' => $activity, 'Question' => $item, 'Answer' => $answers, 'Hint' => $hints));
			}
		}
		else {
			$this->Session->setFlash(__('warning-not-found', __('questions')), 'default', array('class' => 'message notice'));
		}
		return $list;
	}
	
	
	/**
	 * Parse Resource Info
	 * 
	 * @param array $contents - the resource info XML received from the server.
	 * @param array $lms - the current lms data.
	 * @param bool $download - determines if the resource must be downloaded to the server.
	 * @return array with parsed information.
	 */
	protected function parseResourcesInfo($contents, $lms, $download=false) {
		$list = array();
		
		// Iterate over each course section
		if(isset($contents->MULTIPLE->SINGLE)) {
			foreach($contents->MULTIPLE->SINGLE as $content) {
				
				// Set Resource Data
				$resource = array(
					'name' => (string) $content->KEY[1]->VALUE,
					'description' => strip_tags((string) $content->KEY[2]->VALUE),
					'lms_id' => $lms['id'], 
					'lms_url' => $lms['url'], 
					'external_id' => (int) $content->KEY[0]->VALUE
				);
				
				// Set File Data
				$file = array(
					'name' => $content->KEY[4]->VALUE,
					'type' => $content->KEY[3]->VALUE,
					'path' => $content->KEY[5]->VALUE,
					'size' => $content->KEY[6]->VALUE,
					'url' => $content->KEY[7]->VALUE.'&token='.$lms['token']
				);
				
				if($download) {
					$tmp_name = $resource['name'].$resource['external_id'].$file['size'].date('Y-m-d-His');
					$file = $this->Files->download(FILES.Configure::read('Folders.files.resources'), $tmp_name, $file);
				}
				
				// If no error with the file
				if(!isset($file['error'])) {
					array_push($list, array('Resource' => $resource, 'File' => $file));
				}
				else {
					$this->Session->setFlash($file['error']);
				}
			}
		}
		else {
			$this->Session->setFlash(__('warning-not-found', __('resources')), 'default', array('class' => 'message notice'));
		}
		return $list;
	}
	
}