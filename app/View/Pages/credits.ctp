<style type="text/css">
	li {
		margin-bottom: 10px;
	}
</style>
<?php
	$name = Configure::read('System.name');
	$options = array('target' => '_blank');
	
	// Icons
	$bwPage = $this->Html->link(__('here'), 'http://gentleface.com/free_icon_set.html', $options);
	$bwLicense = $this->Html->link('Creative Commons (Attribution-Noncommercial 3.0 Unported)', 'http://creativecommons.org/licenses/by-nc/3.0/', $options);
	$fcPage = $this->Html->link(__('here'), 'http://deleket.deviantart.com/art/Face-Avatars-107881096', $options);
	$fcLicense = $this->Html->link('Creative Commons (Attribution-Noncommercial-Share Alike 3.0 Unported)', 'http://creativecommons.org/licenses/by-nc-sa/3.0/', $options);
	$icPage = $this->Html->link(__('here'), 'http://findicons.com/pack/2579/iphone_icons/1', $options);
	
	// Sounds
	$caPage = $this->Html->link(__('here'), 'http://freesound.org/people/KIZILSUNGUR/sounds/72127/', $options);
	$iaPage = $this->Html->link(__('here'), 'http://freesound.org/people/Ultranova105/sounds/136755/', $options);
	$crPage = $this->Html->link(__('here'), 'http://freesound.org/people/ramjac/sounds/21389/', $options);
	$bmPage = $this->Html->link(__('here'), 'http://www.youtube.com/watch?feature=player_embedded&v=wwbQtQDnXCk', $options);
	
	// Technologies
	$cakePage = $this->Html->link(__('here'), 'http://cakephp.org/', $options);
	$nodePage = $this->Html->link(__('here'), 'http://nodejs.org/', $options);
	$socketPage = $this->Html->link(__('here'), 'http://socket.io/', $options);
	$jqueryPage = $this->Html->link(__('here'), 'http://jquery.com/', $options);
	$jqueryuiPage = $this->Html->link(__('here'), 'http://jqueryui.com/', $options);
	$kineticPage = $this->Html->link(__('here'), 'http://kineticjs.com/', $options);
?>
<div class="one-column">
	<div class="column section">
		<h2><?php echo __('People'); ?></h2>
		<p><?php echo __('This work wouldn\'t be so awesome and great whithout some people help, so I would like to thank to everyone that contributed with new ideas and contents.'); ?></p>
		<p><?php echo __('In particular I would like to thank to:'); ?></p>
		<ul>
			<li>
				<b>Carmen Morgado</b> and <b>Fernanda Barbosa</b> - <?php echo __('for all their dedication, help and support;'); ?>
			</li>
			<li>
				<b>Inês Sampaio</b> - <?php echo __('for creating all the images used for the development of the Abstract Data Types Quiz and for her help on some design issues;'); ?>
			</li>
			<li>
				<b>Sónia Martins</b> - <?php echo __('for designing most of the images and icons used on the games and also for her help on some game design issues.'); ?>
			</li>
		</ul>
		
		
		<h2><?php echo __('Icons'); ?></h2>
		<p><?php echo __('Some of the icons and images used were created and designed by the people mentioned above, but most of the other icons used were found on the internet. Below is a list of the icon sets used and where you can find them:'); ?></p>
		<ul>
			<li>
				<b>Gentleface Toolbar Icon Set</b> - 
				<?php echo __('those are the black and white icons used everywhere in this application. You can find them %s, they are distributed under the %s.', $bwPage, $bwLicense); ?>
			</li>
			<li>
				<b>Face Avatars</b> - 
				<?php echo __('those are the images used as face avatars in the games. You can find them %s, they are distributed under the %s.', $fcPage, $fcLicense); ?>
			</li>
			<li>
				<b>iPhone Icons</b> - <?php echo __('from this set only the align horizontal and vertical icons were used. You can find them %s.', $icPage); ?>
			</li>
		</ul>
		<p><?php echo __('The %s logo and icon were created by me (Bruno Sampaio).', $name); ?></p>
		
		
		<h2><?php echo __('Sounds'); ?></h2>
		<p><?php echo __('The sounds used on the games are also available on the internet. Below is a list with their respective links:'); ?></p>
		<ul>
			<li>
				<b>Correct Answer</b> - 
				<?php echo __('this is the sound played when a player answers correctly to a question. It can be found %s.', $caPage); ?>
			</li>
			<li>
				<b>Incorrect Answer</b> - 
				<?php echo __('this is the sound played when a player answers incorrectly to a question. It can be found %s.', $iaPage); ?>
			</li>
			<li>
				<b>Collaboration Request</b> - 
				<?php echo __('this is the sound played when a player receives a collaboration request from another player (for example asking for help). It can be found %s.', $crPage); ?>
			</li>
			<li>
				<b>Background Music</b> - 
				<?php echo __('this is the sound used as background music. It can be found %s.', $bmPage); ?>
			</li>
		</ul>
		
		
		<h2><?php echo __('Technologies'); ?></h2>
		<p><?php echo __('For the development of this web application the following libraries were used:'); ?></p>
		<ul>
			<li>
				<b>CakePHP 2.2</b> - 
				<?php echo __('it is a PHP framework based on the MVC architecture pattern, it was used for server side logic development. If you are interested you can find more about it %s.', $cakePage); ?>
			</li>
			<li>
				<b>NodeJS 0.8.16</b> and <b>Socket.IO 9.11</b> - 
				<?php echo __('NodeJS is a platform built on Chrome\'s JavaScript runtime for easily building fast, scalable network applications. Socket.io is a plugin for NodeJS that aims to make realtime apps possible in every browser and mobile device, blurring the differences between the different transport mechanisms. They are both used for the games server development based on the WebSockects protocol. If you are interested you can find more about both %s and %s, respectively.', $nodePage, $socketPage); ?>
			</li>
			<li>
				<b>jQuery 1.8.0</b> and <b>jQueryUI 1.10.0</b>- 
				<?php echo __('jQuery is a Javascript framework and jQueryUI is just an extension that provides a set of user interface interactions, effects, widgets, and themes built on top of jQuery. You can find more about both of them %s and %s, respectively.', $jqueryPage, $jqueryuiPage); ?>
			</li>
			<li>
				<b>KineticJS 4.0.5</b> - 
				<?php echo __('it is an HTML5 Canvas JavaScript framework that enables high performance animations, transitions, node nesting, layering, filtering, caching, event handling for desktop and mobile applications, and much more. It was used for the games client logic development. If you are interested you can find more information about it %s.', $kineticPage); ?>
			</li>
		</ul>
		
	</div>
</div>