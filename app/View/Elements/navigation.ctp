<?php $this->start('navigation'); ?>
<div id="navigation">
	<?php 
		foreach(Configure::read('Sections.dashboard') as $key => $value) {
			$options = array(
				'title' => '<b>'.__('help-'.$key.'-title').'</b>' . '<p>'.__('help-'.$key.'-desc-1').'</p>' . '<p>'.__('help-'.$key.'-desc-2').'</p>', 
				'class' => $key, 
				'escape' => false
			);
			if($dashboard_section == $key) {
				$options['class'].= ' selected';
			}
			echo $this->Html->link(
				$this->Html->div('icon-big', '').$this->Html->div('name', $value), 
				array('controller' => $key, 'action' => 'index'),
				$options
			);
		} 
	?>
</div>
<div id="helper">
	<div class="picture help">
		<div class="icon-big"></div>
	</div>
	<div class="item">
		<?php echo $this->Html->link(Configure::read('Sections.footer.howtos.develop'), array('controller' => 'pages', 'action' => 'display', 'howtos', 'develop')); ?>
	</div>
</div>
<?php $this->end(); ?>
