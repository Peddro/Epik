<div class="one-column">
	<div class="column section">
		<div id="name">
			<?php
				// Create Page Title
				$lms = $this->data['GameSession']['lms_id']? $this->data['LMS']['url'] : false;
				$game = $this->data['GameSession']['game_id'];
			?>
			<span title="<?php echo __('Back'); ?>" class="back">
				<?php echo $this->Html->link('', array('controller' => 'sessions', 'action' => 'index', $game), array('class' => 'icon')); ?>
			</span>
			<h1>
				<?php echo ($lms? __('Session from <u>%s</u>', $this->Html->link($lms, $lms)) : __('Public Session')); ?>
			</h1>
		</div>
		<div class="highlights">
		<?php
			
			// Create Context
			if($this->data['GameSession']['context_name']) {
				$context = $this->data['GameSession']['context_name'];
			}
			else if($this->data['GameSession']['context_id']) {
				$context = __('Course with ID').': '.$this->data['GameSession']['context_id'];
			}
			else $context = Configure::read('System.name');
			echo $this->Html->div(false, '<b>'.__('Context').'</b>: '.$context);
			
			// Create Date
			echo $this->Html->div(false, '<b>'.__('Played on').'</b>: '.$this->Time->nice(h($this->data['GameSession']['created'])));
			
			// Create Team Score
			if(count($players) > 1 && $this->data['GameSession']['score']) {
				echo $this->Html->div(false, '<b>'.__('session-score-team').'</b>: '.$this->data['GameSession']['score']);
			}
		
			// Set Tables Data to later create them
			$tables = array();
			
			// Set Scores Table Data
			$best = 0;
			foreach($scores as $item) {
				$playerId = $item['Score']['player_id'];
				if(!isset($tables[$playerId])) {
					$tables[$playerId] = array();
				}
				if(!isset($tables[$playerId]['scores'])) {
					$tables[$playerId]['scores'] = array('headers' => array(), 'cells' => array(0 => array()));
					$list =& $tables[$playerId]['scores'];
					$i = 0;
				}
				
				// Add Score Value
				if($item['Score']['value'] != NULL) {
					$list['headers'][$i] = __('session-score-'.$item['Type']['name']);
					$list['cells'][0][$i] = $item['Score']['value'];
					$i++;
					
					if($item['Type']['name'] == 'total' && $item['Score']['value'] > $best) {
						$best = $item['Score']['value'];
					}
				}
			}
			
			
			// Create Best Score
			if(count($players) > 1 && $best > 0) {
				echo $this->Html->div(false, '<b>'.__('session-best-score').'</b>: '.$best);
			}
		?>
		</div>
		<?php
			
			// Set Bonus Table Data
			foreach($bonus as $item) {
				$playerId = $item['Bonus']['player_id'];
				if(!isset($tables[$playerId])) {
					$tables[$playerId] = array();
				}
				if(!isset($tables[$playerId]['bonus'])) {
					$tables[$playerId]['bonus'] = array('headers' => array('scenario' => __('session-bonus-scenario')), 'cells' => array());
					$currentScenario = null;
					$i = -1;
				}
				if($currentScenario != $item['Bonus']['scenario']) {
					$currentScenario = $item['Bonus']['scenario'];
					$tables[$playerId]['bonus']['cells'][] = array($currentScenario);
					$list =& $tables[$playerId]['bonus'];
					$i++;
				}
				
				// Add Bonus Value
				if($item['Bonus']['value'] != NULL) {
					$name = $item['Type']['name'];
					if(!isset($list['headers'][$name])) {
						$list['headers'][$name] = __('session-bonus-'.$item['Type']['name']);
					}
					$list['cells'][$i][] = $item['Bonus']['value'];
				}
			}
			
			function setActivityScore($name, &$item, &$headers, &$cells) {
				if(!isset($list['headers'][$name])) {
					$headers[$name] = __("session-activity-$name");
				}
				$cells[] = ($item['ActivityLog'][$name] != NULL)? $item['ActivityLog'][$name] : __('session-not-logged');
			}
			
			// Set Activities Table Data
			foreach($activities as $item) {
				$playerId = $item['ActivityLog']['player_id'];
				if(!isset($tables[$playerId])) {
					$tables[$playerId] = array();
				}
				if(!isset($tables[$playerId]['activities'])) {
					$tables[$playerId]['activities'] = array('headers' => array('name' => __('session-activity-name')), 'cells' => array());
					$list =& $tables[$playerId]['activities'];
					$i = -1;
				}
				
				// Add Activity Name
				$list['cells'][] = array($item['Activity']['name']);
				$i++;
				
				// Add Activity Reward
				setActivityScore('reward', $item, $list['headers'], $list['cells'][$i]);
				
				// Add Activity Penalty
				setActivityScore('penalty', $item, $list['headers'], $list['cells'][$i]);
				
				// Add Activity Attempts
				setActivityScore('attempts', $item, $list['headers'], $list['cells'][$i]);
			}
			
			foreach($players as $item): ?>
			<?php $id = $item['Player']['id']; ?>
			<div class="header">
				<div class="title left">
					<h2>
						<?php 
							$playerName = $item['Player']['name'];
							if($playerName) echo h($playerName);
							if($item['Player']['user_id']) {
								if($playerName) echo ' ';
								echo '(ID on LMS: '.$item['Player']['user_id'].')';
							}
						?>
					</h2>
				</div>
				<div class="options right">
					<div title="<?php echo __('Expand/Colapse'); ?>" class="minimize">
						<div class="icon-small"></div>
					</div>
				</div>
			</div>
			<div class="list">
				<h3><?php echo __('Global Scores'); ?></h3>
				<table class="scores">
					<?php 
						// Create Scores Table
						echo $this->Html->tableHeaders($tables[$id]['scores']['headers']);
						echo $this->Html->tableCells($tables[$id]['scores']['cells']);
					?>
				</table>
				
				<?php if($item['Player']['helps_used'] !== NULL || $item['Player']['helps_given'] !== NULL): ?>
				<h3><?php echo __('Helps'); ?></h3>
				<table class="helps">
					<?php 
						// Create Helps Table
						echo $this->Html->tableHeaders(array(0 => __('session-helps-used'), 1 => __('session-helps-given')));
						echo $this->Html->tableCells(array(
							array(
								0 => ($item['Player']['helps_used'] !== NULL)? $item['Player']['helps_used'] : __('session-not-logged'), 
								1 => ($item['Player']['helps_given'] !== NULL)? $item['Player']['helps_given'] : __('session-not-logged')
							)
						));
					?>
				</table>
				<?php endif; ?>
				
				<?php if(isset($tables[$id]['bonus'])): ?>
				<h3><?php echo __('Received Bonus'); ?></h3>
				<table class="bonus">
					<?php
						// Create Bonus Table
						echo $this->Html->tableHeaders($tables[$id]['bonus']['headers']);
						echo $this->Html->tableCells($tables[$id]['bonus']['cells']);
					?>
				</table>
				<?php endif; ?>
				
				<?php if(isset($tables[$id]['activities'])): ?>
				<h3><?php echo __('Activities Scores'); ?></h3>
				<table class="activities">
					<?php
						// Create Activities Table
						echo $this->Html->tableHeaders($tables[$id]['activities']['headers']);
						echo $this->Html->tableCells($tables[$id]['activities']['cells']);
					?>
				</table>
				<?php endif; ?>
				
				<?php 
					if($lms && $this->data['LMS']['outcome']) {
						echo '<h3>'.__('Send grade to LMS').'</h3>';
						echo $this->Form->create('Player', array('type' => 'post', 'default' => false));
						echo $this->Form->input('grade', array('options' => $grades, 'default' => '1.0'));
						echo $this->Js->submit(__('Send'), array('class' => 'small', 'url' => array('controller' => 'players', 'action' => 'send', $id)));
						echo $this->Form->end();
					}
				?>
			</div>
		<?php endforeach; ?>
	</div>
</div>