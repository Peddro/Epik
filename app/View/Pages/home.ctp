<?php $name = Configure::read('System.name'); ?>
<div id="screenshot">
	<div class="picture">
		<?php echo $this->Html->image('static/home/screenshot.png'); ?>
		<div class="featured-message">
			<p><?php echo __('home-screenshot-tools', $name); ?></p>
		</div>
	</div>
</div>

<div id="featured" class="three-columns liquid">
	<?php 
	echo $this->Elements->featured(
		__('home-games-title'), 
		__('home-games-title-highlight'), 
		'<p>'.__('home-games-desc-0', $name).'</p><p>'.__('home-games-desc-1', $this->Html->link(__('page'), array('controller' => 'pages', 'action' => 'display', 'howtos', 'develop'))).'</p>', 
		true, 
		array('before' => 'column', 'color' => 'blue', 'after' => 'left')
	);
	
	echo $this->Elements->featured(
		__('home-collaboration-title'), 
		__('home-collaboration-title-highlight'), 
		'<p>'.__('home-collaboration-desc-0', $name, $name).'</p><p>'.__('home-collaboration-desc-1', $name, $this->Html->link(__('here'), array('controller' => 'genres', 'action' => 'view', 2))).'</p>', 
		true, 
		array('before' => 'column', 'color' => 'orange', 'after' => 'centered')
	);
	
	echo $this->Elements->featured(
		__('home-lms-title'), 
		__('home-lms-title-highlight'), 
		'<p>'.__('home-lms-desc-0', $name, $name).'</p><p>'.__('home-lms-desc-1').'</p><p>'.__('home-lms-desc-2', $this->Html->link(__('here'), array('controller' => 'pages', 'action' => 'display', 'howtos', 'distribute')), $this->Html->link(__('here'), array('controller' => 'pages', 'action' => 'display', 'howtos', 'import'))).'</p>', 
		true, 
		array('before' => 'column', 'color' => 'red', 'after' => 'right')
	);
	
	?>
</div>

<div id="about">
	<p><?php echo __('home-note', $this->Html->link(__('here'), '/files/documents/thesis.pdf', array('target' => '_blank'))); ?></p>
</div>