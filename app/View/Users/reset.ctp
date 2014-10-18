<div class="section reset">
<?php
echo $this->Form->create('User');

echo $this->Form->input('email', array('type' => 'email', 'label' => __('E-mail')));

echo $this->Form->end(__('Reset Password'));
?>
</div>