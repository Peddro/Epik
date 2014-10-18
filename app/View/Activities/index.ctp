<?php echo $this->element('navigation'); ?>

<div class="one-column">
	<div class="column section activities">
		<div class="header">
			<div class="title left">
				<table>
					<tr>
						<td><h2><?php echo __('My Activities'); ?></h2></td>
						<td class="add"><?php echo $this->Html->link('', array('controller' => 'activities', 'action' => 'select'), array('class' => 'icon modal')); ?></td>
					</tr>
				</table>
			</div>
			<div class="options right">
				<?php 
					echo $this->Form->create('Activity', array('url' => array('controller' => 'activities', 'action' => 'listing')));
					$this->Form->unlockField('Activity.keyword');
					echo $this->Form->input('keyword', array('label' => false, 'type' => 'search', 'placeholder' => __('Search')));
					echo $this->Form->end();
				?>
			</div>
		</div>
		<div class="list">
			<?php echo $this->element('list', array('model' => 'Activity', 'controller' => 'activities')); ?>
		</div>
	</div>
</div>