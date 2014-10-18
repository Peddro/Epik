<?php 
	echo $this->Form->create('Resource', array('url' => array('controller' => 'resources', 'action' => 'import')));
	
	$buttons = array();
	
	switch($params['current']) {
		
		case $sections[0]:
			echo $this->Modal->lmsConnectionForm($lms, $this->data['LMS']);
			
			$this->Modal->previousButton($buttons, true);
			$this->Modal->nextButton($buttons, false);
			break;
			
		case $sections[1]:
		case $sections[2]:
		
			echo $this->Modal->lmsConnectionForm(null, null, false, true);
		
			$pass = array('request' => $request);

			// Course Fields
			$course_id = null;
			if(isset($this->data['Course']['id'])) {
				echo $this->Form->hidden('Course.id');
				$course_id = $this->data['Course']['id'];
				$pass['course_id'] = $course_id;
			}
			
			// Items List
			$pass['content_type'] = $filter;
			$pass['multiple'] = $this->data['Chooser']['multiple'];
			echo $this->element('chooser', array('model' => $model, 'controller' => 'LMS', 'pass' => $pass));
			
			$this->Modal->previousButton($buttons, true);
			$this->Modal->nextButton($buttons, $params['current'] == $sections[2]);
			break;
			
		case $sections[3]:
		
			echo $this->Modal->successMessage(__('resources'), __('imported'), __('view'), __('resources'), true);
			
			foreach($this->data['Resources'] as $key => $val) {
				$this->Modal->linkButton($buttons, $val['name'], array('controller' => 'resources', 'action' => 'view', $val['id']));
			}
			break;
	}
	
	echo $this->Modal->setSections($params['previous'], $params['current']);
	echo $this->Modal->buttons($buttons);
	echo $this->Form->end();
?>