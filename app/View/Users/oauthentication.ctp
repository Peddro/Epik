<div class="one-column">
	<div class="column section">
	<?php
		if(isset($error)) {
	
			$messages = array();
			switch($error) {
				case 1:
					$messages[0] = __('For an external system to gain access to this service it must support OAuth and IMS LTI standards. Although, the system you are accessing from doesn\'t support at least one of them.');
					$messages[1] = __('If you are an administrator or developer and are interested on supporting those standards, please consult the documentation for OAuth %s and for IMS LTI %s.', $this->Html->link('here', 'http://oauth.net/'), $this->Html->link('here', 'http://developers.imsglobal.org/'));
					break;

				case 2:
					$messages[0] = __('The username "%s" cannot be found.', $this->request->data['oauth_consumer_key']);
					$messages[1] = __('You must provide your %s account username (usually referred to as key) as the request key to be able to access this service from an external system.', Configure::read('System.name'));
					$messages[2] = __('You can find your username or key on your %s profile page.', Configure::read('System.name'));
					break;

				case 3:
					$messages[0] = __('The signature methods supported are PLAINTEXT, HMAC_SHA1 and RSA_SHA1.');
					break;

				case 4:
					$messages[0] = __('There was some problem with the request received. The key provided was recognized but the secret must be incorrect, or the request was hacked.');
					$messages[1] = __('Please, make sure the secret provided to your system corresponds to the provided key.');
					break;

				case 5:
					$messages[0] == __('There wasn\'t any problem with the request and data provided, but something went wrong while trying to sign you in.');
					break;
			}


			foreach($messages as $message) {
				echo "<p>$message</p>";
			}

			echo $this->Html->para(null, __('To try again press the button below.'));
		?>
		
			<form method="post" action="<?php echo $this->here; ?>">
				<?php foreach($this->request->data as $key => $value) {
					echo "<input type=\"hidden\" name=\"$key\" value=\"$value\">";
				} ?>
				<input type="submit" value="<?php echo __('Click to refresh the page'); ?>">
			</form>
			
		<?php } else if(isset($update)) { ?>
			<p><?php echo __('There are some differences in the data received from the LMS compared to the data about you stored in %s.', Configure::read('System.name')); ?></p>
			<p><?php echo __('Would you like to update your data to the one provided?'); ?></p>
	
			<?php echo $this->Form->create('User', array('action' => 'update')); ?>
			<table id="user-data-comparison">
				<tr>
					<th></th>
					<th><?php echo Configure::read('System.name').__(' User'); ?></th>
					<th><?php echo __('LMS User'); ?></th>
				</tr>
				<?php if(isset($update['firstname'])) { 
					echo $this->Form->input('firstname', array('type' => 'hidden', 'value' => $update['firstname'])); ?>
					<tr>
						<th><?php echo __('First Name'); ?></th>
						<td><?php echo AuthComponent::user('firstname'); ?></td>
						<td><?php echo $update['firstname']; ?></td>
					</tr>
				<?php } if(isset($update['lastname'])) { 
					echo $this->Form->input('lastname', array('type' => 'hidden', 'value' => $update['lastname'])); ?>
					<tr>
						<th><?php echo __('Surname'); ?></th>
						<td><?php echo AuthComponent::user('lastname'); ?></td>
						<td><?php echo $update['lastname']; ?></td>
					</tr>
				<?php } if(isset($update['email'])) { 
					echo $this->Form->input('email', array('type' => 'hidden', 'value' => $update['email'])); ?>
					<tr>
						<th><?php echo __('E-mail'); ?></th>
						<td><?php echo AuthComponent::user('email'); ?></td>
						<td><?php echo $update['email']; ?></td>
					</tr>
				<?php } ?>
				<tr>
					<td></td>
					<td><?php echo $this->Form->button(__('Do nothing'), array('name' => 'submit', 'value' => 'redirect')); ?></td>
					<td><?php echo $this->Form->button(__('Update my Info'), array('name' => 'submit', 'value' => 'update')); ?></td>
				</tr>
			</table>
			<?php echo $this->Form->end(); ?>
		<?php } ?>
	</div>
</div>