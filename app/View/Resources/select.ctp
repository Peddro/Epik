<?php
	$options = array(
		'cache' => '+1 hour',
		'types' => $types,
		'title' => 'select-item-upload',
		'url' => array('controller' => 'resources', 'action' => 'add'),
		'pass' => array('Type' => 'id'),
		'after' => array(
			array('title' => __('select-item-import', __('Resource')), 'icon' => 'import', 'url' => array('controller' => 'resources', 'action' => 'import'))
		)
	);
	echo $this->element('selector', $options);
?>