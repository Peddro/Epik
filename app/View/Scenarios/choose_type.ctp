<?php 
	echo $this->Form->create('Scenario');
	
	$buttons = array();
	
	switch($params['current']) {
		
		case $sections[0]:
			
			// Scenarios List
			$pass['filters'] = $this->data['Chooser']['filters'];
			$pass['multiple'] = $this->data['Chooser']['multiple'];
			echo $this->element('chooser', array('model' => 'ScenarioTemplate', 'controller' => 'scenarios', 'pass' => $pass));
			
			$this->Modal->previousButton($buttons);
			$this->Modal->insertButton($buttons);
			break;
			
	}
	
	echo $this->Modal->setSections($params['previous'], $params['current']);
	echo $this->Modal->buttons($buttons);
	echo $this->Form->end();
?>