<div class="one-column">
	<div class="column section select">
		<table>
			<?php
				$sections = Configure::read('Sections.footer.howtos');
				foreach($sections as $key => $section) {
					if($key != $this->params['pass'][1]): ?>
					
					<tr class="item">
						<td class="<?php echo $key; ?>"><div class="icon"></div></td>
						<td><?php echo $this->Html->link($section, array('controller' => 'pages', 'action' => 'display', $this->params['pass'][0], $key)); ?></td>
					</tr>
					
			<?php endif; } ?>
		</table>
	</div>
</div>