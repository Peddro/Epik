<?php
/**
 * Imports
 */
App::uses('AppController', 'Controller');

/**
 * LMS Controller
 *
 * @package app.Controller
 * @property LMS $LMS
 * @author Bruno Sampaio
 */
class LMSController extends AppController {
	
	/**
	 * @var array Components used by this Controller
	 */
	public $components = array('LMSServices');
	
	
	/**
	 * Extends the parent method.
	 */
	public function beforeFilter() {
		parent::beforeFilter();
	}
	
	
	/**
	 * Extends the parent method.
	 */
	public function beforeRender() {
		parent::beforeRender();
	}
	
	
	/**
	 * Choose Section
	 *
	 * This action is a chooser exception case.
	 * It requests the data to be listed from a LMS and then sends it to the element view.
	 * No filters are used.
	 * @return array - list of values to be sent to an element view.
	 */
	public function choose() {
		if($this->params['requested']) {
			$this->autoRender = false;
			
			$lms = $this->LMSServices->getLMS();
			if($lms) {
				$this->LMSServices = $this->Components->load($lms['name'].'Services');

				// Set Args
				$args = $this->request->params['named'];

				$list = array();
				$functionArgs = array();
				switch($args['request']) {
					case $this->LMSServices->requests[0]:
						$requestParams = 'userid';
						$args['id'] = isset($args['course_id'])? $args['course_id'] : null;
						break;

					case $this->LMSServices->requests[1]:
						$requestParams = array('courseid' => $args['course_id']);
						array_push($functionArgs, $args['content_type']);
						$args['id'] = isset($args['content_id'])? $args['content_id'] : null;
						break;

					case $this->LMSServices->requests[2]:
						$requestParams = array('activityid' => $args['content_id']);
						$args['ids'] = isset($args['questions_ids'])? $args['questions_ids'] : null;
						break;
				}
				$list = $this->LMSServices->request($args['request'], $requestParams, $functionArgs);

				// Determines if can choose only one or multiple
				$multiple = $args['multiple'];

				// Determines the value to be selected
				$selected = array('ids' => null);
				if($multiple) {
					if(isset($args['ids']) && $args['ids']) {
						$selected['ids'] = get_object_vars($args['ids']);
					}
				}
				else {
					if(isset($args['id']) && $args['id']) {
						$selected['ids'] = $args['id'];
					}
				}

				// Send data to Element view
				return array('list' => $list, 'store' => array(), 'selected' => $selected, 'multiple' => $multiple, 'specific' => false);
			}
		}
	}
}