<?php 
	echo $this->Form->create('Resource');
	
	$buttons = array();
	
	switch($params['current']) {
		
		case $sections[0]:
			
			// Resources List
			$pass['filters'] = $this->data['Chooser']['filters'];
			$pass['multiple'] = $this->data['Chooser']['multiple'];
			echo $this->element('chooser', array('model' => 'Resource', 'controller' => 'resources', 'pass' => $pass));
			
			$this->Modal->previousButton($buttons);
			$this->Modal->insertButton($buttons);
			break;
			
	}
	
	echo $this->Modal->setSections($params['previous'], $params['current']);
	echo $this->Modal->buttons($buttons);
	echo $this->Form->end();
?>