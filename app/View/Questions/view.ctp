<div class="two-columns">
	<div class="column section left">
		<div class="box info">
			<div class="item question">
				<?php echo $this->data['Question']['content']; ?>
			</div>
			<table>
			<?php
				foreach($this->data['Answer'] as $answer) {
					echo $this->Modal->questionAnswers($answer);
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
	<div class="column section right">
		<?php if(isset($this->data['Activity']['imported']) && $this->data['Activity']['imported']) { ?>
			<div class="box source">
				<h3><?php echo __('Source'); ?></h3>
				<p>
					<?php
						$link = $this->Html->link($this->data['LMS']['name'], $this->data['Activity']['lms_url'], array('target' => '_blank'));
						echo __('Imported from %s.', $link); 
					?>
				</p>
			</div>
		<?php } ?>
		<div class="box hints">
			<h3><?php echo __('Hints'); ?></h3>
			<?php
				if(count($this->data['Hint']) > 0) {
					echo '<ul>';
					foreach($this->data['Hint'] as $hint) {
						echo '<li>'.$hint['content'].'</li>';
					}
					echo '</ul>';
				}
				else {
					echo $this->Html->para('', __('This activity has no hints associated.'));
				}
			?>
		</div>
		<div class="box resources">
			<h3><?php echo __('Resources'); ?></h3>
			<?php
				if(count($this->data['Resource']) > 0) {
					echo '<ul>';
					foreach($this->data['Resource'] as $resource) {
						echo '<li>'.$this->Js->link($resource['name'], array('controller' => 'resources', 'action' => 'view', $resource['id']), array('update' => '#modal_content')).'</li>';
					}
					echo '</ul>';
				}
				else {
					echo $this->Html->para('', __('This activity has no resources associated.'));
				}
			?>
		</div>
	</div>
</div>