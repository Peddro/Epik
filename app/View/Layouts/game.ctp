<?php $properties = $this->Layouts->getProperties($this->params['controller'], $this->params['action'], $this->params['pass']); ?>

<!DOCTYPE html>
<html>
<head>
	<?php echo $this->Html->charset(); ?>
	<title>
		<?php echo $this->Layouts->getTitle($title_for_layout); ?>
	</title>
	<?php
		//Meta
		echo $this->Html->meta('icon');
		echo $this->fetch('meta');

		//Styles
		echo $this->Html->css(array('games'));
		echo $this->fetch('css');
	?>
</head>
<body id="<?php echo $properties['page']; ?>">
	<?php 
		$category = $defaults['category'];
		
		// Create Game Screen
		echo $this->element('game_screen', array(
			'name' => $game['name'], 
			'files' => $files,
			'instructions' => $genre['instructions'],
			'gameover' => $genre['gameover'],
			'mode' => $genre['mode_id'],
			'isGame' => true
		));
		
		// Set Strings
		$strings = array(
			'alerts' => array(
				'noSupport' => __('alert-no-support', Configure::read('System.name')),
				'close' => __('alert-leaving-game-page')
			),
			'errors' => array(
				'ELC' => __('error-lost-connection', Configure::read('System.name')),
				'EG1' => __('error-corrupted-game-identifier'),
				'EG2' => __('error-corrupted-game', $game['name']),
				'EH1' => __('error-unknown-help'),
				'EP1' => __('error-corrupted-player'),
				'EP2' => __('error-corrupted-player-lms'),
			),
			'labels' => array(
				'help' => array(),
				'helps' => __('Helps'),
				'helpTimeout' => __('activity-help-timeout'),
				'points' => __('Points'),
				'playerStatusDefault' => __('player-status-default'),
				'select' => __('Select # item(s).'),
				'time' => __('Time').':'
			),
			'loading' => array(
				'game' => __('loading-game'),
				'resources' => __('loading-game-resources')
			),
			'waiting' => array(
				'start' => __('waiting-start')
			),
			'warnings' => array(
				'WH1' => __('warning-incorrect-help-status'),
				'WH2' => __('warning-no-helps'),
				'WH3' => __('warning-no-scenario-helps'),
				'WH4' => __('warning-no-players-available'),
				'WH5' => __('warning-already-waiting-for-help'),
				'WH6' => __('warning-use-helps')
			)
		);
		
		// Set Helps Strings
		if(count($defaults['helps']['all']) > 0) {
			$strings['labels']['playerStatusHelp'] = __('player-status-help');
			$strings['labels']['playerStatusHelping'] = __('player-status-helping');
			
			foreach($defaults['helps']['all'] as $helpType => $helpUse) {
				$fieldName = 'help'.ucfirst($helpType);
				
				if($genre['mode_id'] == 2) {
					$strings['labels'][$fieldName.'RequestTitle'] = __("activity-help-$helpType-request-title");
					$strings['labels'][$fieldName.'RequestDesc'] = __("activity-help-$helpType-request-desc");
				}
				
				$strings['labels']['help'][$helpType] = __("activity-help-$helpType");
				
				$strings['labels'][$fieldName.'ResponseTitle'] = __("activity-help-$helpType-response-title");
				$strings['labels'][$fieldName.'ResponseDesc'] = __("activity-help-$helpType-response-desc");
				
				$strings['waiting'][$fieldName] = __("waiting-help-$helpType-".$genre['mode_id']); 
			}
			
			unset($defaults['helps']['all']);
		}
		
		// Set Jumps Strings
		foreach($defaults['types']['jumps'] as $jumpsTypes) {
			foreach($jumpsTypes as $jumpType) {
				$jumpUppercase = ucfirst($jumpType);
				$strings['labels']["playerStatus$jumpUppercase"] = __("player-status-$jumpType");
				$strings['waiting']["jump$jumpUppercase"] = __("waiting-jump-$jumpType");
			}
		}
		
		// Scripts
		$this->Js->setVariable = 'E';
		echo $this->Html->script(array('libs/jquery', 'libs/plugins/jquery.tipTip', 'libs/jquery-ui', 'libs/kinetic', 'libs/socket.io'));
		$this->Js->set(array(
			'defaults' => $defaults,
			'selectors' => array(
				'ids' => new stdClass(),
				'classes' => array(
					'ajax' => 'ajax',
					'selected' => 'selected'
				)
			),
			'strings' => $strings,
			'system' => array(
				'server' => $properties['url'],
				'sockets' => Configure::read('System.sockets')
			),
			'tmp' => array('data' => $data, 'player' => $player)
		));
		echo $this->Js->writeBuffer();
		echo $this->Html->script(array(
			'extensions', 
			'games/general', 
			"games/$category/properties", 
			"games/$category/elements", 
			"games/$category/logic", 
			'games/communication', 
			'games/modal', 
			'games/time', 
			'games/utils', 
			'games/init'
		));
		echo $this->fetch('script');
	?>
</body>
</html>
