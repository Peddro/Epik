<div class="section login">
<?php 
echo $this->Form->create('User');

$username = $this->Form->input('username', array('label' => __('Username or E-mail'), 'autofocus' => 'autofocus'));
$password = $this->Form->input('password');
$forgot = array('display' => __('Forgot your password?'), 'url' => array('controller' => 'users', 'action' => 'reset'));

$signin = __('Sign in');
$options = array();
if($ajax) {
	switch($params['current']) {
		case $sections[0]:
		
			echo $username;
			echo $password;
		
			$options['target'] = '_blank';
			echo $this->Modal->buttons(array(
				'div' => false,
				array('type' => 'submit', 'ajax' => true, 'display' => $signin)
			));
			
			echo $this->Html->link($forgot['display'], $forgot['url'], $options);
			echo $this->Modal->setSections($params['previous'], $params['current']);
			break;
			
		case $sections[1]:
			
			echo $this->Html->para('', __('success-list-message-signin-first'));
			echo $this->Html->para('', __('success-list-message-signin-second'));
			
			echo $this->Modal->buttons(array(
				array('type' => 'button', 'name' => 'cancel', 'color' => 'button big yellow', 'display' => __('Close Window'))
			));
			
			break;
	}
}
else {
	echo $username;
	echo $password;
	echo $this->Form->submit($signin);
	echo $this->Html->link($forgot['display'], $forgot['url'], $options);
}

echo $this->Form->end();
?>
</div>