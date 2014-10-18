<?php
	$options = array(
		'cache' => '+1 hour',
		'types' => $types,
		'title' => 'select-item-create',
		'url' => array('action' => 'add'),
		'controller' => array('Type' => 'controller'),
		'pass' => array('Type' => 'id'),
		'after' => array(
			array('title' => __('select-item-import', __('Activity')), 'icon' => 'import', 'url' => array('controller' => 'activities', 'action' => 'import'))
		)
	);
	echo $this->element('selector', $options);
?>