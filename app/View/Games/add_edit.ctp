<?php 
	$editing = $this->params['action'] == 'edit';
	$url = array('controller' => 'games');
	if($editing) {
		$url['action'] = 'edit';
		$url[0] = $this->data['Game']['id'];
	}
	else {
		$url['action'] = 'add';
		$url[0] = $this->data['Project']['id'];
	}
	echo $this->Form->create('Game', array('type' => 'file', 'url' => $url));
	
	$buttons = array();
	$icon_url = $this->Form->hidden('Game.icon_url');
	$hiddenFields = array('icon', 'user_id');
	
	switch($params['current']) {
		
		case $sections[0]:
		
			echo 
				'<ul>
					<li>'.$this->Html->para(null, __('info-list-export-game-message-1')).'</li>
					<li>'.$this->Html->para(null, __('info-list-export-game-message-2')).'</li>
					<li>'.$this->Html->para(null, __('info-list-export-game-message-3')).'</li>
				</ul>';
			
			$this->Modal->previousButton($buttons);
			$this->Modal->nextButton($buttons, false);
			break;
		
		case $sections[1]:
		
			// Game Fields
			$hiddenFields[] = 'visibility_id';
			echo $this->Modal->baseItemForm('Game', true, $hiddenFields);
			echo $icon_url;
			
			$this->Modal->previousButton($buttons, !$editing);
			$this->Modal->nextButton($buttons, false);
			break;
			
		case $sections[2]:

			echo $this->HTML->para('', __('create-file-formats-message', implode(', ', Configure::read('Files.types.image'))));
			
			// Game Fields
			echo $this->Modal->baseItemForm('Game', true, $hiddenFields, true);
			echo $this->Form->input('Game.visibility_id', array('options' => $visibilities, 'selected' => $this->data['Game']['visibility_id'], 'empty' => false));

			// File Fields
			$this->Form->unlockField('Game.file');
			echo $this->Form->input('Game.file', array('type' => 'file', 'label' => __('Icon'), 'accept' => 'image/*', 'div' => array('class' => 'input file')));

			if(isset($this->data['Game']['icon']) && $this->data['Game']['icon']) {
				$this->Form->unlockField('Game.icon_url');
				echo $this->Elements->filePreview($this->data['Game']['icon_url'], 'image', $icon_url, true, false);
			}
			else echo $icon_url;
			
			$this->Modal->previousButton($buttons, true);
			$this->Modal->nextButton($buttons, true, false);
			break;
			
		case $sections[3]:
		
			$action = $editing? __('changed') : __('created');
			echo $this->Modal->successMessage(__('game'), $action, __('view or play'), __('games'));
			
			$this->Modal->linkButton($buttons, __('View Game'), array('controller' => 'games', 'action' => 'view', $this->data['Game']['id']), false);
			$this->Modal->linkButton($buttons, __('Play Game'), array('controller' => 'games', 'action' => 'play', $this->data['Game']['id']), false, 'yellow');
			break;
			
		case $sections[4]:
			echo $this->element('errors', array('list' => $errors));
			break;
	}
	
	echo $this->Modal->setSections($params['previous'], $params['current']);
	
	echo $this->Modal->buttons($buttons);
	echo $this->Form->end();	
?>