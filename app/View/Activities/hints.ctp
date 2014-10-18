<?php 
	echo $this->Form->create('Activity', array('url' => array('controller' => 'activities', 'action' => 'hints', $this->data['Activity']['id'])));
	
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
		
			$first = true;
			for($i = 0; $i < 10; $i++) {
				$class = ($i % 2 == 0)? 'left' : 'right';
				echo $this->Form->hidden('Hint.'.$i.'.id');
				echo $this->Form->input('Hint.'.$i.'.content', array('label' => __('Hint'), 'div' => array('class' => 'input text '.$class)));
				echo $this->Form->hidden('Hint.'.$i.'.activity_id', array('value' => $this->data['Activity']['id']));
				$first = false;
			}
			
			$this->Modal->previousButton($buttons);
			$this->Modal->nextButton($buttons);
			break;
			
		case $sections[1]:
		
			echo $this->Modal->successMessage(__('activity hints'), __('changed'), __('view'), __('activities'), true);
			
			$this->Modal->activityButtons($buttons, $this->data['Activity']['id'], $this->data['Type']['controller'], $this->data['Type']['allows_hints'], $this->data['Type']['allows_resources']);
			break;
	}
	
	echo $this->Modal->setSections($params['previous'], $params['current']);
	echo $this->Modal->buttons($buttons);
	echo $this->Form->end();
?>