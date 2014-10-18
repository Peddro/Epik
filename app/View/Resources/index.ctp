<?php echo $this->element('navigation'); ?>

<div class="one-column">
	<div class="column section resources">
		<div class="header">
			<div class="title left">
				<table>
					<tr>
						<td><h2><?php echo __('My Resources'); ?></h2></td>
						<td class="upload"><?php echo $this->Html->link('', array('controller' => 'resources', 'action' => 'select'), array('class' => 'icon modal')); ?></td>
					</tr>
				</table>
			</div>
			<div class="options right">
				<?php 
					echo $this->Form->create('Resource', array('url' => array('controller' => 'resources', 'action' => 'listing')));
					$this->Form->unlockField('Resource.keyword');
					echo $this->Form->input('keyword', array('label' => false, 'type' => 'search', 'placeholder' => __('Search')));
					echo $this->Form->end();
				?>
			</div>
		</div>
		<div class="list">
			<?php echo $this->element('list', array('model' => 'Resource', 'controller' => 'resources')); ?>
		</div>
	</div>
</div>