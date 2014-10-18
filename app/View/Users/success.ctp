<div class="one-column">
	<div class="column section">
		<p><?php echo __('To sign in you just need to click on the button below and use the username or e-mail and password you provided.'); ?></p>
		<p><?php echo __('If you want to access %s from an external service, you can find information about how to do so on your profile page.', Configure::read('System.name')); ?></p>
		<?php echo $this->Html->link(__('Start using %s', Configure::read('System.name')), array('controller' => 'users', 'action' => 'signin'), array('class' => 'button black big')); ?>
	</div>
</div>