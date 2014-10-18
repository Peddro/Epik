<?php 
	$editing = $this->params['action'] == 'edit';
	$url = array('controller' => 'questions', 'action' => 'add');
	if($editing) {
		$url['action'] = 'edit';
		$url[0] = $this->data['Activity']['id'];
	}
	else {
		$url[0] = $this->data['Activity']['type_id'];
		$url[1] = $this->data['Question']['type_id'];
	}
	echo $this->Form->create('Question', array('url' => $url));
	
	$buttons = array();
	$hiddenFields = array('type_id', 'user_id');
	
	// Question Type Fields
	echo $this->Form->hidden('Type.id');
	echo $this->Form->hidden('Type.name');
	echo $this->Form->hidden('Type.icon');
	echo $this->Form->hidden('Type.max_answers');
	
	switch($params['current']) {
		
		case $sections[0]:
		
			// Activity Fields
			echo $this->Modal->baseItemForm('Activity', $editing, $hiddenFields);
			
			// Question Fields
			if($editing) echo $this->Form->hidden('Question.id');
			echo $this->Form->hidden('Question.content');
			echo $this->Form->hidden('Question.type_id');
			if($editing) echo $this->Form->hidden('Question.activity_id');
			
			// Answers Fields
			if(isset($this->data['Answer'])) {
				for($i = 0; $i < count($this->data['Answer']); $i++) {
					$field = 'Answer.'.$i;
					if($editing) {
						echo $this->Form->hidden($field.'.id');
						echo $this->Form->hidden($field.'.question_id');
					}
					echo $this->Form->hidden($field.'.content');
					echo $this->Form->hidden($field.'.is_correct');
				}
				
				if(isset($this->data['Answers']['correct'])) {
					echo $this->Form->hidden('Answers.correct');
				}
			}
			
			$this->Modal->previousButton($buttons, !$editing);
			$this->Modal->nextButton($buttons, false);
			break;
			
		case $sections[1]:
		
			// Activity Fields
			echo $this->Modal->baseItemForm('Activity', $editing, $hiddenFields, true);
		
			// Question Fields
			if($editing) echo $this->Form->hidden('Question.id');
			echo $this->Form->input('Question.content', array('label' => __('Question')));
			echo $this->Form->hidden('Question.type_id');
			if($editing) echo $this->Form->hidden('Question.activity_id');
			
			// Answer Fields
			$html = '<table class="input text required">';
			
			// Answers Table Headers
			$headers = array();
			if($this->data['Type']['max_answers'] > 1) {
				array_push($headers, '');
				$label = __('Answers');
			}
			else {
				$label = __('Answer');
			}
			array_push($headers, $this->Form->label('Answer.content', $label));
			$html.= $this->Html->tableHeaders($headers);
			
			// Answers Table Cells
			$attributes = array('hiddenField' => false, 'legend' => false, 'value' => 0);
			if(isset($this->data['Answers']['correct'])) {
				$attributes['value'] = $this->data['Answers']['correct'];
			}
			
			$options = array('label' => false);
			if($this->data['Type']['icon'] == 'truefalse') $options['disabled'] = 'disabled';
			for($i = 0; $i < $this->data['Type']['max_answers']; $i++) {
				$field = 'Answer.'.$i;
				
				$this->Form->unlockField($field.'.content');
				
				$cells = array();
				$inputs = $this->Form->input($field.'.content', $options);
				if($editing) $inputs.= $this->Form->hidden($field.'.id').$this->Form->hidden($field.'.question_id');
					
				if($this->data['Type']['max_answers'] > 1) {
					$this->Form->unlockField('Answers.correct');
					array_push($cells, $this->Form->radio('Answers.correct', array($i => $i), $attributes));
				}
				else {
					$this->Form->unlockField($field.'.is_correct');
					$inputs.= $this->Form->hidden($field.'.is_correct', array('value' => 1));
				}
				array_push($cells, $inputs);
				$html.= $this->Html->tableCells(array($cells));
			}
			$html.= '</table>';
			echo $html;
			
			$this->Modal->previousButton($buttons, true);
			$this->Modal->nextButton($buttons);
			break;
			
		case $sections[2]:
		
			$action = $editing? __('changed') : __('created');
			echo $this->Modal->successMessage(__('question'), $action, __('view'), __('activities'));
			
			$this->Modal->activityButtons($buttons, $this->data['Activity']['id'], 'questions', true, true);
			break;
	}
	
	
	echo $this->Modal->setSections($params['previous'], $params['current']);
	echo $this->Modal->buttons($buttons);
	echo $this->Form->end();
?>