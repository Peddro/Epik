<?php 
	echo $this->Form->create('Project', array('url' => array('controller' => 'projects', 'action' => 'add')));
	
	$buttons = array();
	$hiddenFields = array('user_id');
	
	switch($params['current']) {
		
		case $sections[0]:
		
			// Project Fields
			echo $this->Modal->baseItemForm('Project', false, $hiddenFields);
			
			// Template Fields
			echo $this->Form->hidden('Template.id');
			echo $this->Form->hidden('Chooser.filters.genre_id');
			
			$this->Modal->previousButton($buttons);
			$this->Modal->nextButton($buttons, false);
			break;
			
		case $sections[1]:
			
			// Project Fields
			echo $this->Modal->baseItemForm('Project', false, $hiddenFields, true);
			
			// Template Fields
			$pass['id'] = null;
			if(isset($this->data['Template']['id'])) {
				$pass['id'] = $this->data['Template']['id'];
			}
		
			// Chooser Fields
			$pass['filters']['genre_id'] = 1;
			if(isset($this->data['Chooser']['filters']['genre_id']) && $this->data['Chooser']['filters']['genre_id']) {
				$pass['filters']['genre_id'] = $this->data['Chooser']['filters']['genre_id'];
			}
			
			// Templates List
			$this->Form->unlockField('Template.id');
			$this->Form->unlockField('Chooser.filters.genre_id');
			$pass['multiple'] = $this->data['Chooser']['multiple'];
			echo $this->element('chooser', array('model' => 'Template', 'controller' => 'templates', 'pass' => $pass));
			
			$this->Modal->previousButton($buttons, true);
			$this->Modal->nextButton($buttons);
			break;
			
		case $sections[2]:
		
			echo $this->Modal->successMessage(__('project'), __('created'), __('open'), __('projects'));
			$this->Modal->linkButton($buttons, __('Open Project'), array('controller' => 'projects', 'action' => 'view', $this->data['Project']['id']), false);
			break;
	}
	
	echo $this->Modal->setSections($params['previous'], $params['current']);
	echo $this->Modal->buttons($buttons);
	echo $this->Form->end();
?>