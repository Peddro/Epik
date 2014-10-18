<div class="one-column">
	<div class="column">
		<div class="box info">
			<?php echo $this->Elements->filePreview($this->data['Resource']['file_url'], $this->data['Type']['mime'], null, true, $this->data['Resource']['external']); ?>
		</div>
		<?php if(isset($this->data['Resource']['imported']) && $this->data['Resource']['imported']) { ?>
			<div class="box source">
				<h3><?php echo __('Source'); ?></h3>
				<p>
					<?php
						$link = $this->Html->link($this->data['LMS']['name'], $this->data['Resource']['lms_url'], array('target' => '_blank'));
						echo __('Imported from %s.', $link); 
					?>
				</p>
			</div>
		<?php } if(strlen($this->data['Resource']['description']) > 0) { ?>
			<div class="box description">
				<h3><?php echo __('Description'); ?></h3>
				<?php echo $this->Elements->paragraphs($this->data['Resource']['description']); ?>
			</div>
		<?php } ?>
	</div>
</div>