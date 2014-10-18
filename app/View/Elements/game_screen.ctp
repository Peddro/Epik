<?php $title = "<h1 class=\"title\">$name</h1>"; ?>

<div id="screen">
	<img id="logo" src="<?php echo $files['logo']['url']; ?>" />
	
	<div id="players">
		<div class="list">
			<ul></ul>
		</div>
	</div>
	
	<!-- Start Screen -->
	<div class="start scenario">
		<?php echo $title; ?>
		<img class="icon" src="<?php echo $files['icon']['url']; ?>" />
		<div class="buttons center">
			<div class="button play"><?php echo __('Play'); ?></div>
			<div class="button help"><?php echo __('Instructions'); ?></div>
			<div class="button exit"><?php echo __('Exit'); ?></div>
		</div>
	</div>
	
	<!-- Instructions Screen -->
	<div class="instructions scenario">
		<?php echo $title; ?>
		<div class="text">
			<?php echo $instructions; ?>
		</div>
		<div class="buttons bottom">
			<div class="button back"><?php echo __('Back'); ?></div>
			<div class="button exit"><?php echo __('Exit'); ?></div>
		</div>
	</div>
	
	<!-- Waiting Room Screen -->
	<div class="wait scenario">
		<?php echo $title; ?>
		<form>
			<input name="player-name" type="text" placeholder="<?php echo __('Your Nickname'); ?>" autofocus />
			<h2><?php echo __('Choose your avatar:'); ?></h2>
			<div class="choose">
				<div class="arrow-left">
					<img src="<?php echo $files['arrow-left']['url']; ?>" />
				</div>
				<div class="arrow-right">
					<img src="<?php echo $files['arrow-right']['url']; ?>" />
				</div>
				<div class="current">
					<div class="list">
					<?php
						$avatarsOptions['class'] = 'selected';
						foreach($files['avatars'] as $key => $value) {
							echo '<img src="'.$value['url'].'" class="'.($avatarsOptions['class']? $avatarsOptions['class'] : '').'" />';
							$avatarsOptions['class'] = false;
						}
					?>
					</div>
				</div>
			</div>
		</form>
		<div class="buttons bottom">
			<div class="button back"><?php echo __('Back'); ?></div>
			<div class="button start"><?php echo __('Start'); ?></div>
		</div>
	</div>
	
	<!-- Canvas Screen -->
	<div class="game scenario"></div>
	
	<!-- Scores Screen -->
	<div class="rankings scenario">
		<h1 class="title"><?php echo ($mode == 1)? __('Players Rankings') : __('Teams Rankings'); ?></h1>
		<div class="text">
			<div class="center">
				<span class="position">1</span><span class="superscript">st</span>&nbsp;-&nbsp;<span class="value"><?php if(!$isGame) echo 1000; ?></span>
			</div>
			<div class="center">
				<span class="position">2</span><span class="superscript">nd</span>&nbsp;-&nbsp;<span class="value"><?php if(!$isGame) echo 900; ?></span>
			</div>
			<div class="center">
				<span class="position">3</span><span class="superscript">rd</span>&nbsp;-&nbsp;<span class="value"><?php if(!$isGame) echo 500; ?></span>
			</div>
			<div class="center ellipsis">...</div>
			<div class="center">
				<span class="position">50</span><span class="superscript">th</span>&nbsp;-&nbsp;<span class="value"><?php if(!$isGame) echo 100; ?></span>
			</div>
		</div>
		<div class="buttons bottom">
			<div class="button repeat"><?php echo __('Try Again'); ?></div>
			<div class="button exit"><?php echo __('Exit'); ?></div>
		</div>
	</div>
	
	<!-- Game Over Screen -->
	<div class="gameover scenario">
		<h1 class="title"><?php echo __('Game Over'); ?></h1>
		<div class="text">
			<?php echo $gameover; ?>
			<img src="<?php echo $files['gameover']['url']; ?>" />
		</div>
		<div class="buttons bottom">
			<div class="button repeat"><?php echo __('Try Again'); ?></div>
			<div class="button exit"><?php echo __('Exit'); ?></div>
		</div>
	</div>
	
	<!-- Loading Bar -->
	<div id="loading">
		<div></div>
		<p></p>
	</div>
	
	<?php if($isGame) { ?>
		
		<!-- Timer -->
		<div class="timer">
			<b><?php echo __('Time').': '; ?></b>
			<span class="value"></span>
		</div>

		<!-- Game Modal Window for General Content -->
		<div class="modal window general">
			<div class="close">
				<span>x</span>
			</div>
			<div class="content"></div>
		</div>

		<!-- Game Modal Window for Waiting -->
		<div class="modal window waiting">
			<table>
				<tr>
					<td><img src="<?php echo $files['ajax']['url']; ?>" /></td>
					<td><p class="content"></p></td>
				</tr>
			</table>
		</div>

		<!-- Game Modal Window for Warnings -->
		<div class="modal window warning">
			<div class="close">
				<span>x</span>
			</div>
			<table>
				<tr>
					<td><img src="<?php echo $files['warning']['url']; ?>" /></td>
					<td><p class="content"></p></td>
				</tr>
			</table>
		</div>

		<!-- Game Modal Window for Errors -->
		<div class="modal window error">
			<div class="close">
				<span>x</span>
			</div>
			<table>
				<tr>
					<td><img src="<?php echo $files['error']['url']; ?>" /></td>
					<td><p class="content"></p></td>
				</tr>
			</table>
		</div>
	
	<?php } ?>
	
</div>