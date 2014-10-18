<?php 
	echo $this->Form->create('Project', array('url' => array('controller' => 'projects', 'action' => 'view')));
	
	$buttons = array();
	
	switch($params['current']) {
		
		case $sections[0]:
			
			// Resources List
			$pass['multiple'] = $this->data['Chooser']['multiple'];
			echo $this->element('chooser', array('model' => 'Project', 'controller' => 'projects', 'pass' => $pass));
			
			$this->Modal->previousButton($buttons);
			$this->Modal->redirectButton($buttons);
			break;
			
	}
	
	echo $this->Modal->setSections($params['previous'], $params['current']);
	echo $this->Modal->buttons($buttons);
	echo $this->Form->end();
?>