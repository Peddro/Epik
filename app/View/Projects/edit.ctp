<?php 
	echo $this->Form->create('Project', array('url' => array('controller' => 'projects', 'action' => 'edit', $this->data['Project']['id'])));
	
	$buttons = array();
	$hiddenFields = array('user_id');
	
	switch($params['current']) {
		
		case $sections[0]:
		
			// Project Fields
			echo $this->Modal->baseItemForm('Project', true, $hiddenFields);
			
			$this->Modal->previousButton($buttons);
			$this->Modal->nextButton($buttons);
			break;
			
		case $sections[1]:
		
			echo $this->Modal->successMessage(__('project'), __('changed'), __('open'), __('projects'));
			$this->Modal->linkButton($buttons, __('Open Project'), array('controller' => 'projects', 'action' => 'view', $this->data['Project']['id']), false);
			break;
	}
	
	echo $this->Modal->setSections(null, $params['current']);
	
	echo $this->Modal->buttons($buttons);
	echo $this->Form->end();	
?>