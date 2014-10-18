<div class="two-columns create">
	<div class="column edit left">
		<div class="section">
		<?php
			echo $this->Form->create('User', array('type' => 'file'));

			echo $this->Form->input('firstname', array('label' => __('First name')));
			echo $this->Form->input('lastname', array('label' => __('Surname')));
			echo $this->Form->input('username');
			echo $this->Form->input('email', array('label' => 'E-mail', 'type' => 'email'));
			echo $this->Form->input('password');
			echo $this->Form->input('confirm_password', array('label' => __('Confirm Password'), 'type' => 'password', 'div' => array('class' => 'input password required')));
			echo $this->Form->input('lms_id', array('label' => __('LMS Name'), 'empty' => ''));
			echo $this->Form->input('lms_url', array('label' => __('LMS URL'), 'title' => __('help-lms-url', Configure::read('System.name'))));
			echo $this->Form->input('picture', array('type' => 'file', 'accept' => 'image/*'));

			echo $this->Form->input('agrees', array('label' => __('I have read and agree to the Terms & Conditions.'), 'type' => 'checkbox'));

			echo $this->Form->submit(__('Sign up'), array('after' => $this->Form->button(__('Reset'), array('type' => 'reset'))));
			echo $this->Form->end();
		?>
		</div>
	</div>
	
	<div class="column terms right">
		<div class="section">
			<?php echo $this->element('terms'); ?>
		</div>
	</div>
</div>