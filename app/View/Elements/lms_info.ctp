<?php
	$lms = $this->Html->link(__('Learning Management Systems (LMS)'), 'http://en.wikipedia.org/wiki/Learning_management_system', $options);
	$moodle = $this->Html->link('Moodle', 'https://moodle.org/', $options);
	$blackboard = $this->Html->link('Blackboard', 'http://www.blackboard.com/', $options);
	$sakai = $this->Html->link('Sakai', 'http://www.sakaiproject.org/', $options);
	$desire2learn = $this->Html->link('Desire2Learn', 'http://www.desire2learn.com/', $options);
	$about = $this->Html->link('here', array('controller' => 'pages', 'actions' => 'display', 'about'));

	echo $this->Html->para('', __('For those who don\'t know, a %s is simply a software application for the administration, documentation, tracking, reporting and delivery of education courses or training programs. Some examples of existing LMS are %s, %s, %s and %s. However, we can only guarantee Moodle 2.2+ support because %s was never tested on other versions, or other LMS systems. Although, if your system supports IMS LTI standard it will probably work.', $lms, $moodle, $blackboard, $sakai, $desire2learn, $name));
	
	echo $this->Html->div(
		'logos', 
		$this->Html->image('lms/moodle.png', array('title' => 'Moodle', 'alt' => 'Moodle')).
		$this->Html->image('lms/blackboard.png', array('title' => 'Blackboard', 'alt' => 'Blackboard')).
		$this->Html->image('lms/sakai.png', array('title' => 'Sakai', 'alt' => 'Sakai')).
		$this->Html->image('lms/desire2learn.png', array('title' => 'Desire2Learn', 'alt' => 'Desire2Learn'))
	);
?>

<h2><?php echo __('I don\'t understand, this is all strange stuff to me.'); ?></h2>
<?php
	echo $this->Html->para('', __('Don\'t worry, if you are still interested on understanding it, please send an e-mail to us with your questions and we will be pleased to explain it to you in a better way (our contacts can be found %s). Or you could also send this page to your LMS administrators and ask them if they understand what we mean.', $about));
?>