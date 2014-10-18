<?php 
	$editing = $this->params['action'] == 'edit';
	$url = array('controller' => 'resources');
	if($editing) {
		$url['action'] = 'edit';
		$url[0] = $this->data['Resource']['id'];
		$url[1] = $this->data['Resource']['type_id'];
	}
	else {
		$url['action'] = 'add';
		$url[0] = $this->data['Resource']['type_id'];
	}
	echo $this->Form->create('Resource', array('type' => 'file', 'url' => $url));
	
	$buttons = array();
	$hiddenFields = array('source', 'lms_id', 'lms_url', 'external_id', 'type_id', 'user_id', 'file_url', 'external', 'imported');
	
	// Resource Type Fields
	echo $this->Form->hidden('Type.id');
	echo $this->Form->hidden('Type.name');
	echo $this->Form->hidden('Type.mime');
	
	switch($params['current']) {
		
		case $sections[0]:
		
			// Resource Fields
			echo $this->Modal->baseItemForm('Resource', $editing, $hiddenFields);
			
			// File Fields
			echo $this->Form->hidden('File.source');
			
			$this->Modal->previousButton($buttons, !$editing);
			$this->Modal->nextButton($buttons, false);
			break;
			
		case $sections[1]:

			if($this->data['Type']['mime'] == 'application') {
				$accept = 'application/pdf';
			}
			else {
				$accept = $this->data['Type']['mime'].'/*';
			}
			$types = Configure::read('Files.types.'.$this->data['Type']['mime']);
			
			echo $this->HTML->para('', __('create-file-formats-message', implode(', ', $types)));
			
			if($this->data['Type']['mime'] == 'video') {
				echo $this->HTML->para('', __('You can also provide a video url from Youtube or Vimeo.'));
			}
			
			// Resource Fields
			echo $this->Modal->baseItemForm('Resource', $editing, $hiddenFields, true);
			
			// File Fields
			$this->Form->unlockField('File.source');
			echo $this->Form->input('File.source', array('label' => _('Method'), 'options' => $methods));
			
			$this->Form->unlockField('Resource.file');
			$class = !$this->data['File']['source']? 'selected' : '';
			echo $this->Html->div("upload $class", $this->Form->input('Resource.file', array('type' => 'file', 'accept' => $accept, 'div' => array('class' => 'input file required'))));
			
			$class = strlen($class) == 0? 'selected' : '';
			echo $this->Html->div("external $class", $this->Form->input('Resource.source'));
			
			if(isset($this->data['Resource']['file_url']) && $this->data['Resource']['file_url'] && isset($this->data['Resource']['external'])) {
				echo $this->Elements->filePreview($this->data['Resource']['file_url'], $this->data['Type']['mime'], null, true, $this->data['Resource']['external']);
			}

			$this->Modal->previousButton($buttons, true);
			$this->Modal->nextButton($buttons, true, false);
			break;
			
		case $sections[2]:

			echo $this->Modal->successMessage(__('resource'), __('uploaded'), __('view'), __('resources'));
			$this->Modal->linkButton($buttons, __('View Resource'), array('controller' => 'resources', 'action' => 'view', $this->data['Resource']['id']));
			break;
	}
	
	echo $this->Modal->setSections($params['previous'], $params['current']);
	echo $this->Modal->buttons($buttons);
	echo $this->Form->end();
?>