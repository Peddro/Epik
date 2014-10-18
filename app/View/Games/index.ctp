<?php echo $this->element('navigation'); ?>

<div class="one-column">
	<div class="column section games">
		<div class="header">
			<div class="title left">
				<table>
					<tr>
						<td><h2><?php echo __('My Games'); ?></h2></td>
					</tr>
				</table>
			</div>
			<div class="options right">
				<?php 
					echo $this->Form->create('Game', array('url' => array('controller' => 'games', 'action' => 'listing')));
					$this->Form->unlockField('Game.keyword');
					echo $this->Form->input('keyword', array('label' => false, 'type' => 'search', 'placeholder' => __('Search')));
					echo $this->Form->end();
				?>
			</div>
		</div>
		<div class="list">
			<?php echo $this->element('list', array('model' => 'Game', 'controller' => 'games')); ?>
		</div>
	</div>
</div>