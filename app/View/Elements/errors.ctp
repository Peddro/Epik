<?php
	foreach($list as $error) {
		echo $this->Html->div('item', '- '.$error);
	}

	echo $this->Html->para(null, __('errors-list-message'));
?>