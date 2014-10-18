<?php 
	echo $this->Form->create('Activity');
	
	$buttons = array();
	
	switch($params['current']) {
		
		case $sections[0]:
			
			// Activities List
			$pass['filters'] = $this->data['Chooser']['filters'];
			$pass['multiple'] = $this->data['Chooser']['multiple'];
			echo $this->element('chooser', array('model' => 'Activity', 'controller' => 'activities', 'pass' => $pass));
			
			$this->Modal->previousButton($buttons);
			$this->Modal->insertButton($buttons);
			break;
			
	}
	
	echo $this->Modal->setSections($params['previous'], $params['current']);
	echo $this->Modal->buttons($buttons);
	echo $this->Form->end();
?>