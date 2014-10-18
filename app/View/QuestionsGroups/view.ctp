<div class="select">
	<div class="box info">
		<table>
			<?php 
				foreach($this->data['Question'] as $question) { 
					echo $this->Modal->selectorItem($question['name'], $question['icon'], array('controller' => 'questions', 'action' => 'view', $question['activity_id']));
				}
			?>
		</table>
	</div>
	
	<?php if(strlen($this->data['Activity']['description']) > 0) { ?>
		<div class="box description">
			<h3><?php echo __('Description'); ?></h3>
			<?php echo $this->Elements->paragraphs($this->data['Activity']['description']); ?>
		</div>
	<?php } ?>
</div>