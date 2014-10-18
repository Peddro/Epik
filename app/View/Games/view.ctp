<div class="two-columns profile">
	<div class="column view left">
		<div class="section">
			<div id="picture" class="left">
				<?php echo $this->Elements->filePreview($this->data['Game']['icon_url'], 'image', false, $this->data['Game']['icon']); ?>
				
				<div class="options">
					<?php $this->Elements->options($this->data, 'Game', 'games', $options); ?>
				</div>
			</div>
			<div id="info">
				<div id="name">
					<h1><?php echo $this->data['Game']['name']; ?></h1>
				</div>
				
				<table>
					<tr>
						<td class="genre-label"><?php echo __('Genre'); ?></td>
						<td class="genre-text"><?php echo $this->data['Genre']['name']; ?></td>
					</tr>
					<tr>
						<td class="visibility-label"><?php echo __('Visibility'); ?></td>
						<td class="visibility-text"><?php echo __('visibility-type-'.$this->data['Visibility']['name']); ?></td>
					</tr>
					<tr>
						<td class="created-label"><?php echo __('Created by'); ?></td>
						<td class="created-text">
							<?php echo $this->data['Game']['user_id']? $this->data['User']['name'] : Configure::read('System.name'); ?>
						</td>
					</tr>
					<tr>
						<td class="description-label"><?php echo __('Description'); ?></td>
						<td></td>
					</tr>
					<tr>
						<td class="description-text" colspan="2">
							<?php echo $this->Elements->paragraphs($this->data['Game']['description']); ?>
						</td>
					</tr>
				</table>
			</div>
		</div>
	</div>
	<div class="column lms right">
		<div class="section">
			<div class="section">
				<h1><?php echo __('DISTRIBUTE AS %s ACTIVITY', strtoupper(Configure::read('System.name'))); ?></h1>
				<p><?php echo __('This method allows you to distribute the game publicly. This means that anyone in the world with access to the game URL can play with any other person. For this you just need to provide the URL below to your students, or to the people you want to play the game.'); ?></p>
				<table class="ims-info">
					<tr>
						<td><?php echo __('Game URL').':'; ?></td>
						<td class="url">
						<?php 
							echo Router::url(array('controller' => 'games', 'action' => 'play', $this->data['Game']['id']), true);
						?>
						</td>
					</tr>
				</table>
				<p><?php echo __('Session logs generated with this method will be stored as public.'); ?></p>
				<?php 
					$title = __('game-distribute-lti-title');
					$desc = __('game-distribute-lti-desc', Configure::read('System.name'));
					$iconURL = ($this->data['Game']['icon'])? '/files/'.$this->data['Game']['icon_url'] : '/img/'.$this->data['Game']['icon_url'];
					echo $this->Elements->imsLtiTable($title, $desc, '/games/play/', $iconURL, $this->data['Game']['resource_key'], $this->data['Game']['secret'], 'distribute');
				?>
			</div>
		</div>
	</div>
</div>