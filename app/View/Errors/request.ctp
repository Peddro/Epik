<div class="one-column">
	<div class="column section">
		<?php
			$errorMessage = 
				$this->Html->para(false, __('You cannot access this action this way.')). 
				$this->Html->para(false, __('Please use the button below.'));

			$buttons = array(array('color' => 'yellow', 'type' => 'link', 'url' => $url, 'display' => __('Try this')));

			if($ajax) {
				$buttons[0]['ajax'] = false;

				echo $errorMessage;
				echo $this->Modal->buttons($buttons);
			}
			else {
				$buttons[0]['color'].= ' modal';
				$buttons[0]['ajax'] = true;

				echo $this->Html->div('one-column', $this->Html->div('column section error', $errorMessage . $this->Modal->buttons($buttons)));
			}
		?>
	</div>
</div>