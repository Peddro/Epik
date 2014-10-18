<?php 
	echo $this->Form->create('Resource', array('url' => array('controller' => 'resources', 'action' => 'reload', $this->data['Resource']['id'])));
	
	$buttons = array();
	
	// LMS Fields
	echo $this->Form->hidden('LMS.name');
	
	// Resource Fields
	echo $this->Form->hidden('Resource.id');
	echo $this->Form->hidden('Resource.name');
	echo $this->Form->hidden('Resource.description');
	echo $this->Form->hidden('Resource.source');
	echo $this->Form->hidden('Resource.lms_id');
	echo $this->Form->hidden('Resource.lms_url');
	echo $this->Form->hidden('Resource.external_id');
	echo $this->Form->hidden('Resource.user_id');
	echo $this->Form->hidden('Resource.imported');
	
	// Type Fields
	echo $this->Form->hidden('Type.mime');
	
	switch($params['current']) {
		
		case $sections[0]:
		
			echo $this->Html->para('', __('reload-connect-list-message', __('resource'), $this->data['LMS']['name']));
		
			echo $this->Modal->lmsConnectionForm(null, $this->data['LMS'], false);
			
			$this->Modal->previousButton($buttons);
			$this->Modal->nextButton($buttons, false);
			break;
			
		case $sections[1]:
			
			echo $this->Modal->lmsConnectionForm(null, null, false, true);
			
			// File Fields
			echo $this->Form->hidden('File.name');
			echo $this->Form->hidden('File.type');
			echo $this->Form->hidden('File.size');
			echo $this->Form->hidden('File.url');
			
			// Resource Data
			echo 
				'<table>'.
					$this->Html->tableCells(array(
						array(__('Name'), $this->data['Resource']['name']),
						array(__('Preview'), $this->Elements->filePreview($this->data['File']['url'], $this->data['Type']['mime'], null, false, true)),
						array(__('Description'), $this->Elements->paragraphs($this->data['Resource']['description']))
					)).
				'</table>';
			
			$this->Modal->previousButton($buttons, true);
			$this->Modal->nextButton($buttons);
			break;
			
		case $sections[2]:
		
			echo $this->Modal->successMessage(__('resource'), __('reloaded'), __('view'), __('resources'), false);
			$this->Modal->linkButton($buttons, __('View Resource'), array('controller' => 'resources', 'action' => 'view', $this->data['Resource']['id']));
			break;
	}
	
	echo $this->Modal->setSections($params['previous'], $params['current']);
	echo $this->Modal->buttons($buttons);
	echo $this->Form->end();
?>