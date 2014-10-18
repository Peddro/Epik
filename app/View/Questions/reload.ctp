<?php 
	echo $this->Form->create('Question', array('url' => array('controller' => 'questions', 'action' => 'reload', $this->data['Activity']['id'])));
	
	$buttons = array();
	
	// LMS Fields
	echo $this->Form->hidden('LMS.name');
	
	// Resource Fields
	echo $this->Form->hidden('Activity.id');
	echo $this->Form->hidden('Activity.name');
	echo $this->Form->hidden('Activity.description');
	echo $this->Form->hidden('Activity.lms_id');
	echo $this->Form->hidden('Activity.lms_url');
	echo $this->Form->hidden('Activity.external_id');
	echo $this->Form->hidden('Activity.user_id');
	echo $this->Form->hidden('Activity.imported');
	
	// Question Fields
	echo $this->Form->hidden('Question.id');
	echo $this->Form->hidden('Question.content');
	echo $this->Form->hidden('Question.type_id');
	echo $this->Form->hidden('Question.activity_id');
	
	switch($params['current']) {
		
		case $sections[0]:
		
			echo $this->Html->para('', __('reload-connect-list-message', __('question'), $this->data['LMS']['name']));
		
			echo $this->Modal->lmsConnectionForm(null, $this->data['LMS'], false);
			
			$this->Modal->previousButton($buttons);
			$this->Modal->nextButton($buttons, false);
			break;
			
		case $sections[1]:
		
			echo $this->Html->para('', __('The resources associated to this activity won\'t be removed.'));
			
			echo $this->Modal->lmsConnectionForm(null, null, false, true);
			
			// Answers Fields
			$answers = '';
			foreach($this->data['Answer'] as $key => $val) {
				echo $this->Form->hidden("Answer.$key.content");
				echo $this->Form->hidden("Answer.$key.is_correct");
				$answers.= $this->Modal->questionAnswers($val, true);
			}
			
			// Hints Fields
			$hints = '';
			if(isset($this->data['Hint']) && count($this->data['Hint']) > 0) {
				$hints = '<ul>';
				foreach($this->data['Hint'] as $key => $val) {
					echo $this->Form->hidden("Hint.$key.content");
					$hints.= '<li>'.$val['content'].'</li>';
				}
				$hints.= '</ul>';
			}
			
			// Activity Data
			echo 
				'<table>'.
					$this->Html->tableCells(array(
						array(__('Name'), $this->data['Activity']['name']),
						array(__('Question'), $this->data['Question']['content']),
						array(__('Answers'), $answers),
						array(__('Hints'), $hints),
						array(__('Description'), $this->Elements->paragraphs($this->data['Activity']['description']))
					)).
				'</table>';
			
			$this->Modal->previousButton($buttons, true);
			$this->Modal->nextButton($buttons);
			break;
			
		case $sections[2]:
		
			echo $this->Modal->successMessage(__('question'), __('reloaded'), __('view'), __('activities'), false);
			
			$this->Modal->activityButtons($buttons, $this->data['Activity']['id'], 'questions', true, true);
			break;
	}
	
	echo $this->Modal->setSections($params['previous'], $params['current']);
	echo $this->Modal->buttons($buttons);
	echo $this->Form->end();
?>