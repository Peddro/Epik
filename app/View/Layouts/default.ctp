<?php $properties = $this->Layouts->getProperties($this->params['controller'], $this->params['action'], $this->params['pass'], isset($dashboard_section)); ?>

<!DOCTYPE html>
<html>
<head>
	<?php echo $this->Html->charset(); ?>
	<title><?php echo $this->Layouts->getTitle($title_for_layout); ?></title>
	<?php
		//Meta
		echo $this->Html->meta('icon');
		echo $this->fetch('meta');

		//Styles
		echo $this->Html->css(array('default', 'pages'));
		echo $this->fetch('css');
	?>
</head>
<body id="<?php echo $properties['page']; ?>" class="<?php echo implode(' ', $properties['classes']); ?>" >

	<?php if($properties['page'] != 'projects-view') { ?>
		<div id="header">
			<div id="logo">
				<?php echo $this->Html->image('logos/epik-big.png', array('alt' => Configure::read('System.name'), 'url' => '/')); ?>
			</div>
			
			<?php if($properties['page'] != 'pages-home') { ?>
			<div id="title">
				<h1><?php echo '- '.__($title_for_layout); ?></h1>
			</div>
			<?php } ?>

			<?php if(!AuthComponent::user()) { ?>
				<div id="login">
					<?php echo $this->Html->link(__('Create Account'), array('controller' => 'users', 'action' => 'signup'), array('class' => 'button blue big')); ?>
					<?php echo $this->Html->link(__('Sign in'), array('controller' => 'users', 'action' => 'signin'), array('class' => 'button yellow big')); ?>
				</div>
			<?php }
				else
					echo $this->element('user_card');
			?>
		</div>
		<div class="feedback">
			<a href="https://docs.google.com/spreadsheet/viewform?formkey=dDhuNnNnSUdqYlBqZnNNZWNJdGhNR2c6MA#gid=0" target="_blanck"><?php echo __('Feedback'); ?></a>
		</div>
	<?php }
		else {
			echo $this->fetch('toolbar');
		}
	?>

	<?php echo $this->fetch('navigation'); ?>
	<div id="content">
		<?php 
			echo $this->Session->flash(); 
			echo $this->fetch('content');
		?>
	</div>

	<div id="footer">
		<div class="left">&nbsp;</div>
		<div class="centered">
			<p><b>&copy;</b> 2013 <b>Epik</b></p>
		</div>
		<div class="right">
			<?php 
				$sections = Configure::read('Sections.footer');
				echo 
					$this->Html->link($sections['howtos']['index'], array('controller' => 'pages', 'action' => 'display', 'howtos', 'index')) . '|' .
					$this->Html->link($sections['help'], array('controller' => 'pages', 'action' => 'display', 'help')) . '|' .
					$this->Html->link($sections['about'], array('controller' => 'pages', 'action' => 'display', 'about')) . '|' .
					$this->Html->link($sections['credits'], array('controller' => 'pages', 'action' => 'display', 'credits'));
			?>
		</div>
	</div>

	<!--  Boxes -->
	<div id="boxes">
		<div id="modal" class="window">
			<div class="close"><span>x</span></div>
			<div id="modal_content"></div>
		</div>
		<div id="mask"></div>
	</div>

	<?php
		// Scripts
		$this->Js->setVariable = 'E';
		echo $this->Html->script(array('libs/jquery', 'libs/plugins/jquery.tipTip', 'libs/plugins/jquery.masonry'));
		$this->Js->set(
			array(
				'system' => array(
					'name' => Configure::read('System.name'),
					'version' => Configure::read('System.version'),
					'server' => $properties['url'],
					'controller' => $properties['controller'],
					'action' => $properties['action']
				),
				'selectors' => array(
					'ids' => array(
						'header' => 'header',
						'navigation' => 'navigation',
						'content' => 'content',
						'footer' => 'footer',
						'modal' => 'modal',
						'mask' => 'mask'
					),
					'classes' => array(
						'column' => 'column',
						'section' => 'section',
						'header' => 'header',
						'list' => 'list',
						'item' => 'item',
						'paging' => 'paging',
						'ajax' => 'ajax',
						'modal' => 'modal',
						'selected' => 'selected'
					)
				)
			)
		);
		echo $this->Js->writeBuffer();
		echo $this->Html->script(array('extensions', 'default'));
		echo $this->fetch('script');
	?>
	
	<?php
		// Debug
		/*$whiteList = array('localhost', '127.0.0.1');
		if(in_array($_SERVER['HTTP_HOST'], $whiteList) && in_array($_SERVER['REMOTE_ADDR'], $whiteList)) {
			echo $this->element('sql_dump');
		}*/
	?>
</body>
</html>
