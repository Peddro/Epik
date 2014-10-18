<div class="one-column">
	<div class="column section">
		<?php
			$errorMessage = 
				$this->Html->para(false, __('You can only access to items that you created or that are public.')). 
				$this->Html->para(false, __('Use the button below to return to the Dashboard.'));

			if($ajax) {
				$buttons = array(
					array(
						'name' => 'cancel',
						'type' => 'button',
						'color' => 'button big yellow',
						'display' => __('Close Window')
					)
				);

				echo $errorMessage;
				echo $this->Modal->buttons($buttons);
			}
			else {
				$buttons = array(
					array(
						'color' => 'yellow',
						'type' => 'link',
						'display' => __('Return to Dashboard'),
						'url' => '/',
						'ajax' => false
					)
				);

				echo $this->Html->div('one-column', $this->Html->div('column section error', $errorMessage . $this->Modal->buttons($buttons)));
			}
		?>
	</div>
</div>