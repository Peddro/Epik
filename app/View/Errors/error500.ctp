<div class="one-column">
	<div class="column section">
		<h2><?php echo $name; ?></h2>
		<p class="error">
			<strong><?php echo __d('cake', 'Error'); ?>: </strong>
			<?php echo __d('cake', 'An Internal Error Has Occurred.'); ?>
		</p>
		<?php
		if (Configure::read('debug') > 0 ):
			echo $this->element('exception_stack_trace');
		endif;
		?>
	</div>
</div>