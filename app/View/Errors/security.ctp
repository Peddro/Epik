<div class="one-column">
	<div class="column section">
		<?php
			$errorMessage = '';
			switch($type) {
				case 'auth':
					$errorMessage = __('The data sent was invalid, or the action you were trying to perform can\'t be accessed this way.');
					break;

				case 'csrf':
					$errorMessage = __('The request data was invalid, this may happen if you take too long to submit the data.');
					break;

				case 'get': case 'post': case 'put': case 'delete':
					$errorMessage = __('You are trying to access an action using an invalid request type.');
					break;

				case 'secure':
					$errorMessage = __('This action must be accessed securely, make sure the URL starts with https instead of http.');
					break;
			}

			$errorMessage = 
				$this->Html->para('', $errorMessage).
				$this->Html->para('', __('If this problem persists please contact us via e-mail.')).
				$this->Html->para('', __('To try again click the button below.'));

			$buttons = array(
				array(
					'color' => 'yellow',
					'type' => 'link',
					'display' => __('Try Again'),
					'url' => $url
				)
			);

			if($ajax) {
				echo $errorMessage;

				$buttons[0]['ajax'] = true;
				echo $this->Modal->buttons($buttons);
			}
			else {
				$buttons[0]['ajax'] = false;
				echo $this->Html->div('one-column', $this->Html->div('column section error', $errorMessage . $this->Modal->buttons($buttons)));
			}
		?>
	</div>
</div>