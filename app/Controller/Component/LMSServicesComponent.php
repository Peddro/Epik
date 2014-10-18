<?php
/**
 * Imports
 */
App::uses('Component', 'Controller');

App::import('Vendor', 'curl');

/**
 * LMSServices Component
 *
 * @package app.Controller.Component
 * @author Bruno Sampaio
 */
class LMSServicesComponent extends Component {
	
	/**
	 * @var array Components used by this Component.
	 */
	public $components = array('Session', 'Files');
	
	
	/**
	 * Types of Data to be Requested
	 * @var array $requests
	 */
	public $requests = array('courses', 'contents', 'questions', 'questions_answers_hints', 'resources');
	
	
	/**
	 * Validates LTI Request
	 *
	 * @param object $model - the model object.
	 * @param string $keyField - the key field name.
	 * @param string $secretField - the secret field name.
	 * @param array $requestParams - the request data.
	 * @param array $fields - the query fields.
	 * @return array - entity data from database and the error if any.
	 */
	public function validateLTIRequest($model, $keyField, $secretField, $requestParams, $fields=array()) {
		$entity = false;
		$error = false;

		if(isset($requestParams['oauth_version']) && isset($requestParams['lti_version'])) {
			App::import('Vendor', 'OAuth');

			// Check if entity exists
			$conditions = array($model->name.'.'.$keyField => $requestParams['oauth_consumer_key']);
			$entity = $model->find('first', array('fields' => array(), 'conditions' => $conditions));

			if(isset($entity[$model->name]['id'])) {

				// Consumer Data (LMS)
				$consumerKey = $entity[$model->name][$keyField];
				$consumerSecret = $entity[$model->name][$secretField];
				$consumerToken = '';
				$consumer = new OAuthConsumer($consumerKey, $consumerSecret, null);

				// Provider Data (Tool)
				$providerURL = Router::url(null, true);

				// Signature Method
				$signMethod = null;
				switch($requestParams['oauth_signature_method']) {
					case 'HMAC-SHA1':
						$signMethod = new OAuthSignatureMethod_HMAC_SHA1();
						break;

					case 'RSA_SHA1':
						$signMethod = new OAuthSignatureMethod_RSA_SHA1();
						break;

					case 'PLAINTEXT':
						$signMethod = new OAuthSignatureMethod_PLAINTEXT();
						break;
				}

				// If signature method is known
				if($signMethod) {
					$consumerRequest = OAuthRequest::from_request('POST', $providerURL, $requestParams);

					// Check if request is valid.
					$isConsumer = $signMethod->check_signature($consumerRequest, $consumer, null, $requestParams['oauth_signature']);
					if(!$isConsumer) $error = array('string' => __('error-lti-invalid-signature'), 'code' => 4);
				}
				else $error = array('string' => __('error-lti-invalid-signature-method'), 'code' => 3);
			}
			else $error = array('string' => __('error-lti-invalid-'.lcfirst($model->name).'-key'), 'code' => 2);
		}
		else $error = array('string' => __('error-lti-invalid-request'), 'code' => 1);

		return array('entity' => $entity, 'error' => $error);
	}
	
	
	/**
	 * Sends the Response of a IMS LTI Request received before.
	 *
	 * @param string $url - the url to send the response to.
	 * @param string $id - the request id.
	 * @param string $consumerKey - the consumer key.
	 * @param string $sharedSecret - the shared secret.
	 * @param string $grade - the grade (value between 0.0 and 1.0).
	 */
	public function sendLTIResponse($url, $id, $consumerKey, $sharedSecret, $grade='1.0') {
		App::import('Vendor', 'OAuth');
		
		// Create Body XML
		$xml = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><imsx_POXEnvelopeRequest/>');
	    $xml->addAttribute('xmlns', 'http://www.imsglobal.org/services/ltiv1p1/xsd/imsoms_v1p0');

		// Set Response Header
	    $xmlHeader = $xml->addChild('imsx_POXHeader')->addChild('imsx_POXRequestHeaderInfo');
	    $xmlHeader->addChild('imsx_version', 'V1.0');
	    $xmlHeader->addChild('imsx_messageIdentifier', (string) mt_rand());

		// Set Response Body
	    $xmlBody = $xml->addChild('imsx_POXBody')->addChild('replaceResultRequest')->addChild('resultRecord');
	    $xmlBody->addChild('sourcedGUID')->addChild('sourcedId', (string) $id);
		$result = $xmlBody->addChild('result')->addChild('resultScore');	
	    $result->addChild('language', 'en');
	    $result->addChild('textString', (string) $grade);
		
		// Create HTTP Body
		$httpBody = $xml->asXML();
		
		// Get OAuth Signature Method and Consumer Objects
	    $signatureMethod = new OAuthSignatureMethod_HMAC_SHA1();
	    $consumer = new OAuthConsumer($consumerKey, $sharedSecret, NULL);
	
		// Set OAuth Parameters
		$token = '';
		$params = array('oauth_body_hash' => base64_encode(sha1($httpBody, TRUE)));

		// Sign OAuth Request
	    $request = OAuthRequest::from_consumer_and_token($consumer, $token, 'POST', $url, $params);
	    $request->sign_request($signatureMethod, $consumer, $token);

		// Create HTTP Header
	    $httpHeader = $request->to_header();
	    $httpHeader = $httpHeader . "\r\nContent-type: application/xml\r\n";
	
		// Set HTTP Parameters
		$httpParams = array('http' => array('method' => 'POST', 'header' => $httpHeader, 'content' => $httpBody));
		
		// Send Request
		$ctx = stream_context_create($httpParams);
	    $fp = @fopen($url, 'rb', false, $ctx);
	    if (!$fp) {
	        throw new Exception(__("Problem with $url, $php_errormsg"));
	    }
	
		// Receive Response
	    $response = @stream_get_contents($fp);
	    if ($response === false) {
	        throw new Exception(__("Problem reading data from $url, $php_errormsg"));
	    }
	    return $response;
	}
	
	
	/**
	 * Connect to LMS
	 * 
	 * Given the LMS type, url and user credentials checks if its possible to 
	 * communicate with the LMS and sets the data provided in the Session.
	 * @param array $lms - LMS System data.
	 * @param array $user - LMS User data.
	 * @return bool
	 */
	public function connect($lms, $user) {}
	
	
	/**
	 * Get Current User LMS
	 *
	 * @param array $lms - array with the lms id and url.
	 * @param array $user - array with the user username and password.
	 */
	public function getLMS($lms=false, $user=false) {
		if($lms && $user && isset($user['password'])) {
			if(strlen($lms['url']) > 0 && strlen($user['username']) > 0 && strlen($user['password']) > 0) {

				$this->LMS = ClassRegistry::init('LMS');
				$data = $this->LMS->read(null, $lms['id']);

				if(count($data) > 0) {

					// If is Moodle (Change this when more LMS are supported)
					if($data['LMS']['name'] == 'Moodle') {
						$data['LMS']['url'] = $lms['url'];
						return $data['LMS'];
					}
					else {
						$this->Session->setFlash(__('error-lms-support', $lms['LMS']['name']));
					}

				}
				else {
					$this->Session->setFlash(__('error-lms-unknown'));
				}
			}
			else {
				$this->Session->setFlash(__('error-empty-fields'));
			}
			return false;
		}
		else return $this->Session->read('LMS');
	}
	
	
	/**
	 * Request a External URL
	 * 
	 * @param string $method - HTTP Method to use.
	 * @param string $url - the url to request.
	 * @param array $params - the parameters to send with the request.
	 * @return array - the data received converted to array format.
	 */
	protected function requestURL($method, $url, $params) {
		$curl = new curl;
		if($method == 'get') {
			$response = $curl->get($url, $params);
		}
		else if($method == 'post') {
			$response = $curl->post($url, $params);
		}
		
		return Xml::build($response);
	}
	
	
	/**
	 * Request Data from LMS
	 * 
	 * Retrieves a certain type of data from a LMS and parses it before returning.
	 * @param string $data_type - the type of data to be requested (must belong to $requests array).
	 * @param array $params - the params to be passed to the external service function.
	 * @param array $args - the arguments to be passed to parsing function.
	 * @return array - parsed information.
	 */
	protected function request($data_type, $params=array(), $args=array()) {}
	
	
	/**
	 * Parse Courses
	 * 
	 * @param array $contents - the courses XML received from the server.
	 * @return array with parsed information.
	 */
	protected function parseCourses($contents) {}
	
	
	/**
	 * Parse Course Contents
	 * 
	 * @param array $contents - the course contents XML received from the server.
	 * @param string $type - the type of content to store.
	 * @return array with parsed information containing just the type of contents specified.
	 */
	protected function parseCourseContents($contents, $type) {}
	
	
	/**
	 * Parse Questions Info
	 * 
	 * @param array $contents - the questions info XML received from the server.
	 * @return array with parsed information.
	 */
	protected function parseQuestionsInfo($contents) {}
	
	
	/**
	 * Parse Questions Data
	 * 
	 * @param array $contents - the questions data XML received from the server.
	 * @param array $lms - the current lms data.
	 * @return array with parsed information.
	 */
	protected function parseQuestionsData($contents, $lms) {}
	
	
	/**
	 * Parse Resource Info
	 * 
	 * @param array $contents - the resource info XML received from the server.
	 * @param array $lms - the current lms data.
	 * @param bool $download - determines if the resource must be downloaded to the server.
	 * @return array with parsed information.
	 */
	protected function parseResourcesInfo($contents, $lms, $download=false) {}
	
}