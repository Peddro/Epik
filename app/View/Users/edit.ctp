<div class="two-columns create profile">
	<div class="column edit left">
		<div class="section">
			<?php echo $this->Form->create('User', array('type' => 'file')); ?>
			<div id="picture" class="left">
				<?php
					$this->Form->unlockField('User.picture_url');
					
					$info = $this->Form->hidden('picture_url');
					if($this->data['User']['picture_url']) {
						$info = $this->Elements->filePreview($this->data['User']['picture_url'], 'image', $info);
					}
					echo $this->Form->input('picture', array('type' => 'file', 'accept' => 'image/*', 'between' => $info));
					?>
			</div>
			<div id="info">
				<?php
					echo $this->Form->input('id', array('type' => 'hidden'));
					echo $this->Form->input('firstname', array('label' => __('First name')));
					echo $this->Form->input('lastname', array('label' => __('Surname')));
					echo $this->Form->input('email', array('label' => 'E-mail', 'type' => 'email'));
					echo $this->Form->input('password');
					echo $this->Form->input('confirm_password', array('label' => __('Confirm Password'), 'type' => 'password', 'div' => array('class' => 'input password required')));
					echo $this->Form->input('lms_id', array('label' => __('LMS Name'), 'empty' => ''));
					echo $this->Form->input('lms_url', array('label' => __('LMS URL'), 'title' => __('help-lms-url', Configure::read('System.name'))));
					
					echo $this->Form->input('username', array('type' => 'hidden'));
					echo $this->Form->input('secret', array('type' => 'hidden'));
					
					echo $this->Form->submit(__('Submit'));
				?>
			</div>
			<?php echo $this->Form->end();?>
		</div>
	</div>

	<div class="column lms right">
		<div class="section">
			<?php 
				$title = __('user-lti-title');
				$desc = __('user-lti-desc', Configure::read('System.name'));
				$url = '/users/oauthentication';
				echo $this->Elements->imsLtiTable($title, $desc, $url, Configure::read('System.icon'), $this->data['User']['username'], $this->data['User']['secret'], 'login');
			?>
		</div>
	</div>
</div>