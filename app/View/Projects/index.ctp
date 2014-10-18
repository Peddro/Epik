<?php echo $this->element('navigation'); ?>

<div class="one-column">
	<div class="column section projects">
		<div class="header">
			<div class="title left">
				<table>
					<tr>
						<td><h2><?php echo __('My Projects'); ?></h2></td>
						<td class="add"><?php echo $this->Html->link('', array('controller' => 'projects', 'action' => 'add'), array('class' => 'icon modal')); ?></td>
					</tr>
				</table>
			</div>
			<div class="options right">
				<?php 
					echo $this->Form->create('Project', array('url' => array('controller' => 'projects', 'action' => 'listing')));
					$this->Form->unlockField('Project.keyword');
					echo $this->Form->input('keyword', array('label' => false, 'type' => 'search', 'placeholder' => __('Search')));
					echo $this->Form->end();
				?>
			</div>
		</div>
		<div class="list">
			<?php echo $this->element('list', array('model' => 'Project', 'controller' => 'projects')); ?>
		</div>
	</div>
</div>