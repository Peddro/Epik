<?php
	$category = $defaults['category'];

	echo $this->Html->css(array('projects', 'games'), null, array('inline' => false));
	echo $this->Html->script(
		array(
			'libs/jquery-ui', 
			'libs/plugins/jquery.hotkeys', 
			'libs/kinetic',
			'games/general', 
			"games/$category/properties", 
			"games/$category/elements", 
			'games/utils', 
			'projects/html', 
			"projects/$category/html", 
			'projects/tools_menus', 
			'projects/sections', 
			"projects/$category/sections", 
			'projects/loader', 
			"projects/$category/converter", 
			'projects/elements', 
			'projects/init'
		), 
		array('inline' => false)
	);
	
	$mainKey = (strpos($_SERVER['HTTP_USER_AGENT'], 'Macintosh') !== false)? 'Cmd' : 'Ctrl';
	$collections = &$defaults['collections'];
	$properties = &$defaults['properties'];
	$screens = &$defaults['screens'];
	$playersTypes = &$defaults['types'][$properties[3]];
	$soundsTypes = &$defaults['types'][$properties[4]];
	$scenariosTypes = &$defaults['types'][$collections[0]];
	$scenariosBonus = &$defaults['types']['bonus'];
	$scenariosJumps = &$defaults['types']['jumps'];
	$icons = &$defaults['icons'];
	$tools = &$defaults['elementsByCollection'];
	$mode = $genre['mode_id'];
	
	// Set Default Collections Labels
	$collectionsNames = array();
	for($i = 1; $i < count($collections); $i++) {
		$collectionsNames[$collections[$i]] = __('collection-'.$collections[$i]);
	}
	
	// Set Screens Labels
	$screensLabels = array();
	foreach($screens as $screenName) { 
		if($screenName != 'game') {
			$screensLabels[$screenName.'Desc'] = __("screen-$screenName-desc");
		}
	}
	
	// Set Bonus Labels
	$bonusLabels = array();
	foreach($scenariosBonus as $bonusTypes) {
		foreach($bonusTypes as $bonusType) {
			$bonusLabels[$bonusType] = __("scenario-bonus-$bonusType");
		}
	}
	
	// Set Jumps Labels
	$jumpsLabels = array();
	foreach($scenariosJumps as $jumpsTypes) {
		foreach($jumpsTypes as $jumpType) {
			$jumpsLabels[$jumpType] = __("scenario-jump-$jumpType");
			$jumpsLabels[$jumpType.'Desc'] = __("scenario-jump-$jumpType-desc");
		}
	}
	
	// Set Helps Labels
	$helpsLabels = array();
	foreach($defaults['helps']['all'] as $helpType => $helpValue) {
		$helpsLabels[$helpType] = __("activity-help-$helpType");
		$helpsLabels[$helpType.'Desc'] = __("activity-help-$helpType-project-desc-$mode");
	}
	
	// Set Sounds Labels
	$soundsLabels = array();
	foreach($soundsTypes as $soundType) {
		$soundsLabels[$soundType] = __("properties-sounds-$soundType");
		$soundsLabels[$soundType.'Desc'] = __("properties-sounds-$soundType-desc");
	}
	
	// Set Colors Labels
	$colorsLabels = array('general' => __('Color'));
	foreach($playersTypes as $playerType) {
		$colorsLabels[$playerType] = __("properties-players-$playerType");
	}
	
	$this->Js->set(array(
		'defaults' => $defaults,
		'strings' => array(
			'alerts' => array(
				'activityDragZoom' => __('alert-activity-drag-zoom'),
				'mozillaZoom' => __('alert-mozilla-zoom'),
				'noSupport' => __('alert-no-support', Configure::read('System.name')),
				'remove' => __('alert-delete', __('item')),
				'close' => __('alert-leaving-project-page')
			),
			'collections' => $collectionsNames,
			'errors' => array(
				'colorCode' => __('error-incorrect-colorcode'),
				'numberCode' => __('error-incorrect-numbercode'),
				'negativeNumber' => __('error-incorrect-number-range'),
				'numOfScenarios' => __('error-remove-last-scenario'),
				'corruptedItem' => __('error-corrupted-item'),
				'notFound' => __('error-notfound-element'),
				'notAllowed' => __('error-notallowed-element'),
			),
			'labels' => array(
				'actions' => __('Actions'),
				'actionsDesc' => __('If you use any of the actions above to change this element properties, you must then refresh the scenario contents.'),
				'align' => __('Align'),
				'associateHints' => __('Associate Hints'),
				'associateResources' => __('Associate Resources'),
				'background' => __('Background'),
				'bar' => __('Bar'),
				'bonus' => $bonusLabels,
				'border' => __('Border'),
				'choose' => __('Choose'),
				'collaboration' => __('Collaboration'),
				'color' => $colorsLabels,
				'content' => __('Content'),
				'dimensions' => __('Dimensions'),
				'edit' => __('Edit'),
				'flow' => __('Flow'),
				'font' => __('Text'),
				'gameStart' => __('Game starts here'),
				'gameStartDesc' => __('Check this if the scenario to come first after the standard initial scenarios is this one.'),
				'gameEnd' => __('Rankings Scenario'),
				'gameEndDesc' => __('<b>Note</b>: The rankings scenario is always the last scenario on a game.'),
				'group' => __('Group'),
				'height' => __('Height'),
				'help' => $helpsLabels,
				'helps' => __('Helps'),
				'hints' => __('Hints'),
				'hintsDesc' => __('Select the hints you want to be used on the Request Hints help.'),
				'jump' => $jumpsLabels,
				'image' => __('Image'),
				'length' => __('Length'),
				'log' => __('Log'),
				'logTeamScoreDesc' => __('Select this field if you want to store in the activity log the team score.'),
				'logGlobalDesc' => __('Select this field if you want to store in the activity log the points the player received for this category.'),
				'logHelpsDesc' => __('Select this field if you want to store in the activity log the number of helps the player used.'),
				'logQuestionRewardDesc' => __('Select this field if you want to store in the activity log the points the player received when he answered to this question.'),
				'logQuestionPenaltyDesc' => __('Select this field if you want to store in the activity log the points the player lost with failed attemps on this question.'),
				'name' => __('Name'),
				'nobcolor' => __('No Border Color'),
				'nobgcolor' => __('No Background Color'),
				'none' => __('None'),
				'noquestion' => __('Select me to choose a question using the button that will be displayed on the right panel.'),
				'nosound' => __('Use default sound'),
				'penalty' => __('Penalty'),
				'play' => __('Play'),
				'playerName' => __('Player Name'),
				'playerStatusDefault' => __('player-status-default'),
				'playersNumber' => __('Number of Players'),
				'playersPositionDesc' => __('By changing the players position the elements on your scenarios can be repositioned overlapping each other.'),
				'points' => __('Points'),
				'position' => __('Position'),
				'question' => __('Question'),
				'radius' => __('Radius'),
				'reward' => __('Reward'),
				'resource' => __('Resource'),
				'resourceDesc' => __('Select the resource you want to be used by the Learning Materials Consultation help.'),
				'rotation' => __('Rotation'),
				'rules' => __('Rules'),
				'scenarioHelpsDesc' => __('Number of helps available on this scenario. This number must be smaller than the total number of helps.'),
				'scores' => __('Scores'),
				'screens' => $screensLabels,
				'sounds' => $soundsLabels,
				'specific' => __('Specific'),
				'sync' => __('Sync'),
				'tail' => __('Tail'),
				'team' => __('Team'),
				'thickness' => __('Thickness'),
				'ctimeout' => __('Collaboration timeout'),
				'ctimeoutDesc' => __('If the player that asked for help solves the activity on his first attempt during the time specified here, after receiving the help data, the player that helped him will receive a collaboration bonus.'),
				'total' => __('Total'),
				'transformations' => __('Transformations'),
				'type' => __('Type'),
				'url' => __('URL'),
				'width' => __('Width'),
				'view' => __('View'),
				'x' => __('X'),
				'y' => __('Y'),
				'z' => array(
					'bringToFront' => __('Bring to Front'),
					'sendToBack' => __('Send to Back'),
					'bringForward' => __('Bring Forward'),
					'sendBackward' => __('Send Backward')
				)
			),
			'loading' => array(
				'project' => __('loading-project'),
				'resources' => __('loading-game-resources'),
				'sounds' => __('loading-game-sounds'),
				'scenario' => __('loading-scenario-resources'),
				'newElement' => __('loading-element')
			),
			'units' => array(
				'penalty' => '%',
				'rotation' => 'º',
				'timeout' => 's'
			)
		),
		'tmp' => array('mainKey' => ($mainKey == 'Cmd')? 'meta' : $mainKey),
		'project' => array('id' => $project['id'], 'load' => $data)
	));
	
?>

<?php $this->start('toolbar'); ?>

<!-- Toolbar -->
<div id="toolbar">
	<ul>
		<li class="logo-extended">
			<?php echo $this->Html->link('', '/', array('title' => __('Homepage'), 'class' => 'icon')); ?>
		</li>
		<?php
			echo $this->Elements->separator();
		
			// Set Creation Tools
			echo $this->Elements->tool('new', __('New'), 'expand');
			echo $this->Elements->tool('open', __('Open project').' ('.$mainKey.' + O)', 'button', array('controller' => 'projects', 'action' => 'open'));
			echo $this->Elements->tool('save', __('Save').' ('.$mainKey.' + S)', 'button');
			echo $this->Elements->tool('import', __('Import'), 'expand');
			echo $this->Elements->tool('export', __('Export'), 'expand');
		
			echo $this->Elements->separator();
		
			// Set Editing Tools
			echo $this->Elements->tool('cut', __('Cut').' ('.$mainKey.' + X)', 'button');
			echo $this->Elements->tool('copy', __('Copy').' ('.$mainKey.' + C)', 'button');
			echo $this->Elements->tool('paste', __('Paste').' ('.$mainKey.' + V)', 'button');
			echo $this->Elements->tool('lock', __('Lock/Unlock').' ('.$mainKey.' + L/U)', 'button');
			echo $this->Elements->tool('remove', __('Remove').' ('.$mainKey.' + Backspace)', 'button');
		
			echo $this->Elements->separator();
			
			// Set Zoom Tools
			echo $this->Elements->tool('zoom-in', __('Zoom In').' ('.$mainKey.' + Up)', 'button');
			echo $this->Elements->tool('zoom-out', __('Zoom Out').' ('.$mainKey.' + Down)', 'button');
			
			echo $this->Elements->separator();
		
			// Set Previous/Next Tools
			echo $this->Elements->tool('previous', __('Previous').' ('.$mainKey.' + Z)', 'button');
			echo $this->Elements->tool('next', __('Next').' ('.$mainKey.' + Shift + Z)', 'button');
		
			echo $this->Elements->separator();
			
			// Set Play Tool
			echo $this->Elements->tool('play', __('Play Game').' ('.$mainKey.' + Enter)', 'button');
		
			echo $this->Elements->separator();
		
			// Set Collections Elements Tools
			for($i = 1; $i < count($collections); $i++) {
				$collection = $collections[$i];
				$hasSource = ($collection == $collections[3] || $collection == $collections[4]);
				$url = $hasSource? array('controller' => $collection, 'action' => 'choose_type', $project['genre_id']) : array();
				
				foreach($tools[$collection] as $toolId => $toolName) {
					
					if($toolName != $icons['button']) {
						if($hasSource) $url[1] = $toolId;
						echo $this->Elements->tool($toolName, __('tool-'.$toolName), 'button', $url);
					}
				}
				
				echo $this->Elements->separator();
			}
			
			// Set Info, User and Settings Tools
			echo $this->Elements->tool('info', __('Help'), 'expand');
			echo $this->Elements->tool('user', __('User'), 'expand');
			echo $this->Elements->tool('settings', __('Settings'), 'expand');
		?>
	</ul>
</div>

<!-- Menus -->
<div id="menus">
	<div class="menu new">
		<div class="list">
			<?php
				foreach($scenariosTypes as $scenarioType) {
					$desc = $this->Elements->tip(__("help-$scenarioType-scenarios-title"), array(__("help-$scenarioType-scenarios-desc")));
					echo $this->Elements->menu($icons['scenario'].' '.$scenarioType, false, __("menu-new-scenario-$scenarioType"), false, array(), $desc);
				}
				
				$desc = $this->Elements->tip(__("help-template-scenarios-title"), array(__("help-template-scenarios-desc")));
				$url = array('controller' => 'scenarios', 'action' => 'choose_type', $project['genre_id']);
				echo $this->Elements->menu($icons['scenario'].' template', true, __('menu-new-scenario-template'), false, $url, $desc);
			
				echo $this->Elements->separator('div');
				
				$desc = $this->Elements->tip(__("help-activities-title"), array(__("help-activities-desc-1"), __("help-activities-desc-2")));
				echo $this->Elements->menu('activity', true, __('menu-new-activity'), '(Alt + '.$mainKey.' + A)', array('controller' => 'activities', 'action' => 'select'), $desc);
				
				$desc = $this->Elements->tip(__("help-projects-title"), array(__("help-projects-desc-1"), __("help-projects-desc-2")));
				echo $this->Elements->menu('project', true, __('menu-new-project'), '(Alt + '.$mainKey.' + P)', array('controller' => 'projects', 'action' => 'add'), $desc);
				
				$desc = $this->Elements->tip(__("help-resources-title"), array(__("help-resources-desc-1"), __("help-resources-desc-2")));
				echo $this->Elements->menu('resource', true, __('menu-new-resource'), '(Alt + '.$mainKey.' + R)', array('controller' => 'resources', 'action' => 'select'), $desc);
			?>
		</div>
	</div>
	
	<div class="menu import">
		<div class="list">
			<?php
				echo $this->Elements->menu('activity', true, __('menu-import-activity'), false, array('controller' => 'activities', 'action' => 'import'));
				echo $this->Elements->menu('resource', true, __('menu-import-resource'), false, array('controller' => 'resources', 'action' => 'import'));
			?>
		</div>
	</div>

	<div class="menu export">
		<div class="list">
			<?php
				echo $this->Elements->menu('template', true, __('menu-export-template'), false, array('controller' => 'templates', 'action' => 'add', $project['id']));
				echo $this->Elements->menu('game', true, __('menu-export-game'), false, array('controller' => 'games', 'action' => 'add', $project['id']));
			?>
		</div>
	</div>

	<div class="menu info">
		<div class="list"></div>
	</div>

	<div class="menu user">
		<?php echo $this->element('user_card', array('url_target' => '_blank')); ?>
	</div>

	<div class="menu settings">
		<div class="list">
			<?php
				echo $this->Elements->menu('edit', true, __('Edit Project'), false, array('controller' => 'projects', 'action' => 'edit', $project['id']));
				echo $this->Elements->menu('tools_options', true, __('Tools Options'), false, array('controller' => 'users', 'action' => 'settings'));
			?>
		</div>
	</div>
</div>

<?php $this->end(); ?>


<div class="three-columns">
	
	<!-- Explorer -->
	<div id="explorer" class="column section left">
		<div class="header">
			<div class="title left">
				<h2><?php echo __('section-left-name'); ?></h2>
			</div>
			<div class="options right">
				<div class="minimize-left">
					<a class="icon-small" title="<?php echo __('section-left-option'); ?>"></a>
				</div>
			</div>
		</div>

		<div class="list ajax">
			<ul class="properties">
				<li>
					<?php echo $this->Elements->explorerItem(__('General Properties'), 'properties', false, 'div', true); ?>
					<ul>
						<?php 
							foreach($properties as $propertyName) { 
								if($propertyName != $properties[2] && $propertyName != $properties[5]) {
									echo $this->Elements->explorerItem(__("property-$propertyName"), 'property', $propertyName);
								}
							}
						?>
					</ul>
				</li>
			</ul>
			
			<ul class="screens">
				<li>
					<?php echo $this->Elements->explorerItem(__('General Scenarios'), 'screens', false, 'div', true); ?>
					<ul>
						<?php 
							foreach($screens as $screenName) { 
								if($screenName != 'game') {
									echo $this->Elements->explorerItem(__("screen-$screenName"), 'screen', $screenName);
								}
							}
						?>
					</ul>
				</li>
			</ul>
			
			<div class="separator"></div>
			
			<ul class="scenarios"></ul>
		</div>

		<div class="element-menu">
			<div class="list">
				<div class="edit item">
					<div class="icon-small"></div>
					<div class="name"><?php echo __('Rename'); ?></div>
				</div>
				<?php echo $this->Elements->separator('div'); ?>
				<div class="cut item">
					<div class="icon-small"></div>
					<div class="name"><?php echo __('Cut'); ?></div>
				</div>
				<div class="copy item">
					<div class="icon-small"></div>
					<div class="name"><?php echo __('Copy'); ?></div>
				</div>
				<div class="paste item">
					<div class="icon-small"></div>
					<div class="name"><?php echo __('Paste'); ?></div>
				</div>
				<?php echo $this->Elements->separator('div'); ?>
				<div class="lock item">
					<div class="icon-small"></div>
					<div class="name"><?php echo __('Lock/Unlock'); ?></div>
				</div>
				<?php echo $this->Elements->separator('div'); ?>
				<div class="remove item">
					<div class="icon-small"></div>
					<div class="name"><?php echo __('Remove'); ?></div>
				</div>
			</div>
		</div>
	</div>

	<!-- Properties -->
	<div id="properties" class="column section right">
		<div class="header">
			<div class="options left">
				<div class="minimize-right">
					<a class="icon-small" title="<?php echo __('section-right-option'); ?>"></a>
				</div>
			</div>
			<div class="title right">
				<h2><?php echo __('section-right-name'); ?></h2>
			</div>
		</div>
		<div class="list ajax"></div>
	</div>

	<!-- Canvas -->
	<div id="canvas" class="column section centered">
		<div class="header">
			<div class="tab title left selected">
				<h2><?php echo __('section-center-name'); ?></h2>
			</div>
			<div class="tab title left disabled">
				<h2><?php echo __('Flow'); ?></h2>
			</div>
			<div class="options right">
				<div class="maximize">
					<a class="icon-small" title="<?php echo __('section-center-option'); ?>"></a>
				</div>
			</div>
		</div>
		<div class="list">
		<?php 
			echo $this->element('game_screen', array(
				'name' => __('Game Name'), 
				'files' => $files, 
				'instructions' => $genre['instructions'],
				'gameover' => $genre['gameover'],
				'mode' => $genre['mode_id'],
				'isGame' => false
			));
		?>
		</div>
	</div>
	
</div>