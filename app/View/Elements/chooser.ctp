<?php
if(!isset($list)) {
	$data = $this->requestAction(array('controller' => $controller, 'action' => 'choose'), array('named' => $pass));
	$list = $data['list'];
	$store = $data['store'];
	$selected = $data['selected'];
	$multiple = $data['multiple'];
	$specific = $data['specific'];
}
?>
<div class="chooser">
	<div>
	<?php

		// Store filters as hidden fields
		foreach($store as $key => $val) {
			echo $this->Form->hidden($key, array('value' => $val));
		}
	
		// Iterate over each item on the list
		foreach($list as $item) {
			$id = $item[$model]['id'];
			$html = '';
		
			// If is an activity set the name and description attributes to current model.
			if(isset($item['Activity'])) {
				$item[$model]['name'] = $item['Activity']['name'];
				$item[$model]['description'] = $item['Activity']['description'];
			}
		
			// Image or Icon
			if(isset($item[$model]['image'])) {
				$html.= $this->Html->div('picture', $this->Html->image($item[$model]['image']));
			}
			else {
				$icon = isset($item[$model]['icon'])? $item[$model]['icon'] : (isset($item['Type'])? $item['Type']['icon'] : lcfirst($model));
				if($icon) $html = $this->Html->div('picture '.$icon, $this->Html->div('icon', ''));
			} 
		
			$attributes = array('hiddenField' => false);
			$isSelected = false;
		
			// Check if current item is selected
			if($selected['ids']) {
				if($multiple) {
					if(isset($selected['ids'][$id])) {
						$isSelected = true;
						unset($selected['ids'][$id]);
					}
				}
				else {
					if($id == $selected['ids']) {
						$isSelected = true;
					}
				}
			}
			else if(isset($selected['name']) && $item[$model]['name'] == $selected['name']) {
				$isSelected = true;
			}
		
			// Set item div classes
			$classes = 'item';
			if($isSelected) {
				$classes.= ' selected yellow';
			}
		
			// If checkboxes
			if($multiple) {
				if($isSelected) {
					$attributes['checked'] = 'checked';
				}
				$attributes['value'] = $id;
				$html.= $this->Form->checkbox($model.'.'.$id.'.id', $attributes).$this->Form->label('id', $item[$model]['name']);
			}
		
			// If radios
			else {
				if($isSelected) {
					$attributes['value'] = $id;
				}
				$attributes['legend'] = false;
				$html.= $this->Form->radio($model.'.id', array($id => $item[$model]['name']), $attributes);
			}
		
			// Create item box
			echo $this->Html->div($classes, $html, array('title' => $item[$model]['description']));
		}
	
	
		// Store the remaining ids
		if($multiple && $selected['ids']) {
			foreach($selected['ids'] as $key => $value) {
				echo $this->Form->hidden($model.'.'.$key.'.id', array('value' => $key));
			}
		}
	?>
	</div>
	<?php
		// Set Specific Data
		if($specific) {
			echo $this->Html->div('description', '<h2>'.__('Description').'</h2>' . $this->Html->div('separator', false) . $this->Html->div(false, $specific['description']));
		}	
	?>
</div>
<script type="text/javascript">
	E.modal.setChooserEvents();
</script>