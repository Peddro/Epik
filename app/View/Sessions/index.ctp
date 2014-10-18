<div class="one-column">
	<div class="column section">
		<div id="name">
			<span title="<?php echo __('Back'); ?>" class="back">
				<?php echo $this->Html->link('', array('controller' => 'games', 'action' => 'index'), array('class' => 'icon')); ?>
			</span>
			<h1><?php echo __('%s Sessions', $game['name']); ?></h1>
		</div>
		<?php
			if(count($this->data) > 0) {
				$model = 'GameSession';
				$currentLMS = false;
				
				// Create Highest Score
				if($winner) {
					echo $this->Html->div('highlights', $this->Html->div(false, '<b>'.__('session-highest-score').'</b>: '.$winner));
				}
			
				foreach($this->data as $index => $item) {
					if($currentLMS !== $item[$model]['lms_id']) {
						$currentLMS = $item[$model]['lms_id'];
						
						// Close list
						if($index > 0) {
							echo '</div>';
						}	
						
					?>
						<div class="header">
							<div class="title left">
								<?php echo '<h2>'.($currentLMS? __('Sessions from %s', $this->Html->link($item['LMS']['url'], $item['LMS']['url'])) : __('Public Sessions')).'</h2>'; ?>
							</div>
							<div class="options right">
								<div title="<?php echo __('Expand/Colapse'); ?>" class="minimize">
									<div class="icon-small"></div>
								</div>
							</div>
						</div>
						<?php echo '<div class="list">'; ?>
							<div class="item">
								<div class="name">
									<b><?php echo __('Context Name or ID'); ?></b>
								</div>
								<div class="date">
									<b><?php echo __('Date'); ?></b>
								</div>
								<div class="score">
									<b><?php echo __('Final Score'); ?></b>
								</div>
								<div class="options">
									<b><?php echo __('Options'); ?></b>
								</div>
							</div>
					<?php } ?>
					<div class="item">
						<div class="name">
							<?php 
								if($item[$model]['context_name'] || $item[$model]['context_id']) {
									echo $item[$model]['context_name']? $item[$model]['context_name'] : $item[$model]['context_id'];
								}
								else {
									echo Configure::read('System.name');
								}
							?>
						</div>
						<div class="date">
							<?php echo $this->Time->nice(h($item[$model]['created'])); ?>
						</div>
						<div class="score">
							<?php 
								if($item[$model]['score']) {
									echo ($item[$model]['score'] == $winner)? '<b>'.$item[$model]['score'].'</b>' : $item[$model]['score'];
								}
								else echo __('session-not-logged');
							?>
						</div>
						<div class="options">
							<div class="forward">
								<?php 
									$url = array('controller' => 'sessions', 'action' => 'view', $item['GameSession']['id']);
									echo $this->Html->link('', $url, array('class' => 'icon-small', 'title' => __('More')));
								?>
							</div>
						</div>
					</div>
				
				<?php }
				echo '</div>';
			}
			else {
				echo $this->Html->div('list', $this->Html->para('empty', __('list-empty-sessions-message')));
			}
		?>
	</div>
</div>