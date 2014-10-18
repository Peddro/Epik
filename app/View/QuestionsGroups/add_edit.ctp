<?php 
	$editing = $this->params['action'] == 'edit';
	$url = array('controller' => 'questions_groups', 'action' => 'add');
	if($editing) {
		$url['action'] = 'edit';
		$url[0] = $this->data['Activity']['id'];
	}
	echo $this->Form->create('QuestionsGroup', array('url' => $url));
	
	$buttons = array();
	$hiddenFields = array('type_id', 'user_id');
	
	switch($params['current']) {
		
		case $sections[0]:
		
			// Activity Fields
			echo $this->Modal->baseItemForm('Activity', $editing, $hiddenFields);
			
			// Questions Fields
			if(isset($this->data['Question'])) {
				foreach($this->data['Question'] as $question) {
					echo $this->Form->hidden('Question.'.$question['id'].'.id');
				}
			}
			echo $this->Form->hidden('Chooser.filters.type_id');
			
			$this->Modal->previousButton($buttons, !$editing);
			$this->Modal->nextButton($buttons, false);
			break;
			
		case $sections[1]:
			
			// Activity Fields
			echo $this->Modal->baseItemForm('Activity', $editing, $hiddenFields, true);
			
			// Questions Fields
			$pass['ids'] = array();
			if(isset($this->data['Question'])) {
				foreach($this->data['Question'] as $question) {
					$pass['ids'][$question['id']] = true;
				}
			}
		
			// Chooser Filters
			$pass['filters']['type_id'] = null;
			if(isset($this->data['Chooser']['filters']['type_id']) && $this->data['Chooser']['filters']['type_id']) {
				$pass['filters']['type_id'] = $this->data['Chooser']['filters']['type_id'];
			}
			
			// Questions List
			$pass['multiple'] = $this->data['Chooser']['multiple'];
			echo $this->element('chooser', array('model' => 'Question', 'controller' => 'questions', 'pass' => $pass));
			
			$this->Modal->previousButton($buttons, true);
			$this->Modal->nextButton($buttons);
			break;
			
		case $sections[2]:
		
			$action = $editing? __('changed') : __('created');
			echo $this->Modal->successMessage(__('questions group'), $action, __('view'), __('activities'));
			
			$this->Modal->activityButtons($buttons, $this->data['Activity']['id'], 'questions_groups', false, false);
			break;
	}
	
	echo $this->Modal->setSections($params['previous'], $params['current']);
	echo $this->Modal->buttons($buttons);
	echo $this->Form->end();
?>