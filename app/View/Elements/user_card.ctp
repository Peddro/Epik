<?php
$options = array('escape' => false);
if(isset($url_target)) {
	$options['target'] = $url_target;
}
?>
<div id="user">
	<table>
		<tr>
			<td class="name">
				<?php echo $this->Html->link(AuthComponent::user('name'), array('controller' => 'users', 'action' => 'view', AuthComponent::user('id')), $options); ?>
			</td>
			<td class="picture" rowspan="3">
				<?php
					if(AuthComponent::user('picture_url')) {
						$image = $this->Html->image(AuthComponent::user('picture_url'));
					}
					else {
						$image = $this->Html->image(Configure::read('Default.user.img'));
					}
					
					echo $this->Html->link($image, array('controller' => 'users', 'action' => 'view', AuthComponent::user('id')), $options);
				?>
			</td>
		</tr>
		<tr>
			<td class="lms">
				<?php 
					echo $this->Html->link('My Dashboard', array('controller' => AuthComponent::user('section'), 'action' => 'index'), $options);
					if(AuthComponent::user('lms_name') && AuthComponent::user('lms_url')) {
						echo ' - <a href="'.AuthComponent::user('lms_url').'" target="_blank">'.__('My').' '.AuthComponent::user('lms_name').'</a>';
					}
				?>
			</td>
		</tr>
		<tr>
			<td class="logout">
				<?php echo $this->Html->link('Logout', array('controller' => 'users', 'action' => 'signout')); ?>
			</td>
		</tr>
	</table>
</div>