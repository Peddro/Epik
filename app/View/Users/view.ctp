<div class="two-columns profile">
	<div class="column view left">
		<div class="section">
			<div id="picture" class="left">
				<?php echo $this->Html->image($this->data['User']['picture_url']); ?>
				
				<div class="options">
					<?php $this->Elements->options($this->data, 'User', 'users', $options); ?>
				</div>
			</div>
			<div id="info">
				<div id="name">
					<h1><?php echo $this->data['User']['name']; ?></h1>
				</div>
				
				<table>
					<tr>
						<td><?php echo __('Username'); ?></td>
						<td><?php echo $this->data['User']['username']; ?></td>
					</tr>
					<tr>
						<td><?php echo __('E-mail'); ?></td>
						<td><?php echo $this->data['User']['email']; ?></td>
					</tr>
					<?php if(isset($this->data['LMS']) && isset($this->data['User']['lms_url'])) { ?>
						<tr>
							<td><?php echo __('LMS'); ?></td>
							<td><?php echo $this->data['LMS']['name'].' - <a href="'.$this->data['User']['lms_url'].'" target="_blank">'.$this->data['User']['lms_url'].'</a>'; ?></td>
						</tr>
					<?php } ?>
					<tr>
						<td><?php echo __('Password'); ?></td>
						<td><?php echo $this->Html->link(__('Change Password'), array('controller' => 'users', 'action' => 'edit', $this->data['User']['id'])); ?></td>
					</tr>
				</table>
			</div>
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