<?php echo $this->Html->css('modal'); ?>
<div id="<?php echo $this->params['controller'].'-'.$this->params['action']; ?>" class="section <?php if(isset($params['current'])) echo $params['current']; ?>">
	<div class="header">
		<div class="title">
			<h1><?php echo $params['title']; ?></h1>
		</div>
		<div class="options">
			<div class="left">
				<p><?php echo $params['message']; ?></p>
			</div>
			<div class="right">
				<?php 
					if(isset($params['filters'])) {
						$model = $params['filters']['model'];
						echo $this->Form->create($model, array('url' => $params['filters']['url']));
					
						foreach($params['filters']['fields'] as $field) {
							if(isset($field['label'])) {
								echo $this->Form->label($field['name'], $field['label']);
							}
						
							if($field['type'] == 'select') {
								echo $this->Form->select($field['name'], $field['options'], array('empty' => $field['empty']));
							}
							else if($field['type'] == 'text') {
								echo $this->Form->input($field['name']);
							}
							else if($field['type'] == 'hidden') {
								echo $this->Form->hidden($field['name']);
							}
						}
					
						echo $this->Js->submit(__('Send'), array('update' => $this->Modal->update.' .list'));
						echo $this->Form->end();
					}
					else if(isset($params['icons']) && is_array($params['icons'])) {
						echo $this->Elements->options($this->data, $params['model'], $this->params['controller'], $params['icons'], 'small');
					}
				?>
			</div>
		</div>
	</div>
	<div class="list">
		<?php 
			echo $this->Session->flash();
			echo $this->fetch('content');
		?>
	</div>
</div>
<?php 
	echo $this->Js->writeBuffer();
	
	// Debug
	/*$whiteList = array('localhost', '127.0.0.1');
	if(in_array($_SERVER['HTTP_HOST'], $whiteList) && in_array($_SERVER['REMOTE_ADDR'], $whiteList)) {
		echo $this->element('sql_dump');
	}*/
?>
<script>
	E.modal.generalEvents();
</script>