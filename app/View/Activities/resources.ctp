<?php 
	echo $this->Form->create('Activity', array('url' => array('controller' => 'activities', 'action' => 'resources', $this->data['Activity']['id'])));
	
	$buttons = array();
	
	// Activity Fields
	echo $this->Form->hidden('Activity.id');
	echo $this->Form->hidden('Activity.name');
	echo $this->Form->hidden('Activity.user_id');
	
	// Activity Type Fields
	echo $this->Form->hidden('Type.controller');
	echo $this->Form->hidden('Type.allows_hints');
	echo $this->Form->hidden('Type.allows_resources');
	
	switch($params['current']) {
		
		case $sections[0]:
		
			// Resources Fields
			$pass['ids'] = array();
			if(isset($this->data['Resource'])) {
				foreach($this->data['Resource'] as $resource) {
					$pass['ids'][$resource['id']] = true;
				}
			}
		
			$pass['filters']['type_id'] = null;
			if(isset($this->data['Chooser']['filters']['type_id']) && $this->data['Chooser']['filters']['type_id']) {
				$pass['filters']['type_id'] = $this->data['Chooser']['filters']['type_id'];
			}
			
			// Resources List
			$pass['multiple'] = $this->data['Chooser']['multiple'];
			echo $this->element('chooser', array('model' => 'Resource', 'controller' => 'resources', 'pass' => $pass));
			
			$this->Modal->previousButton($buttons);
			$this->Modal->nextButton($buttons);
			break;
			
		case $sections[1]:
		
			echo $this->Modal->successMessage(__('activity resources'), __('changed'), __('view'), __('activities'), true);
			
			$this->Modal->activityButtons($buttons, $this->data['Activity']['id'], $this->data['Type']['controller'], $this->data['Type']['allows_hints'], $this->data['Type']['allows_resources']);
			break;
	}
	
	echo $this->Modal->setSections($params['previous'], $params['current']);
	echo $this->Modal->buttons($buttons);
	echo $this->Form->end();
?>