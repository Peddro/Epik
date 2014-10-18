<?php
	$options = array(
		'cache' => '+1 hour',
		'types' => $types,
		'title' => 'select-item-create-question',
		'url' => array('controller' => 'questions', 'action' => 'add', $this->data['Activity']['type_id']),
		'pass' => array('Type' => 'id')
	);
	echo $this->element('selector', $options);
?>