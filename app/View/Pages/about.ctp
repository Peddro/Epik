<?php 
	$name = Configure::read('System.name');
	$author = Configure::read('System.author');
	$imgURL = 'static/about';
	$options = array('target' => '_blank', 'escape' => false);
?>
<div class="one-column">
	<div class="column section">
		
		<h2><?php echo __('What is %s?', $name); ?></h2>
		<?php
			echo $this->Html->para('', __('%s is a web application for educational games development. It allows teachers to create activities for their students in the form of games. Those activities can be provided using a URL and for each one are generated activity logs that could be used for students assessment.', $name.' ('.Configure::read('System.acronym').')'));
			
			echo $this->Html->para('', __('Currently, %s only allows the creation of Individual and Collaborative Quiz Games. Those games can be related with one or more learning subjects and they can be composed by several scenarios with activities for the players. Those activities are mainly multiple choice questions and for each one are provided several forms of collaboration and competition (referred to as helps).', $name));
			
			echo $this->Html->para('', __('%s also offers the possibility to import contents from a Learning Management System (LMS), such as activities and resources, and also provides a way to distribute the games as LMS activities. For now just Moodle is supported but others could be supported in the future.', $name));
			
			echo $this->Html->para('', __('To try it you just need to create an account and then you can start developing your own games!', $name));
		?>
		
		<h2><?php echo __('Who are we?'); ?></h2>
		<table class="authors">
			<tr>
				<td>
					<?php echo $this->Html->link($this->Html->image("$imgURL/bens.jpg", array('title' => $author, 'alt' => $author)), 'http://pessoa.fct.unl.pt/b.sampaio/', $options); ?>
				</td>
				<td>
					<?php
						
						$author1 = $this->Html->link($author, 'http://pessoa.fct.unl.pt/b.sampaio/', $options);
						$author2 = $this->Html->link('Carmen Morgado', 'http://asc.di.fct.unl.pt/~cpm/', $options);
						$author3 = $this->Html->link('Fernanda Barbosa', '', $options);
						$thesis = $this->Html->link('here', '/files/documents/thesis.pdf', $options);

						echo $this->Html->para('', __('This application was developed by %s, a student from Universidade Nova de Lisboa in Portugal, during his master\'s thesis project in Informatics Engineering on 2012/13. The professors %s and %s proposed the realization of this work and also contributed for its development. Besides this website, several Moodle plugins were developed with the objective to provide users with new forms of social awareness and communication. For more information about the author\'s work and it\'s future you can find a preliminary version of his thesis report %s (just in Portuguese).', $author1, $author2, $author3, $thesis));	
					?>
				</td>
			</tr>
		</table>
		<?php
			echo $this->Html->div(
				'logos',
				$this->Html->link($this->Html->image("$imgURL/unl.png", array('title' => 'Universidade Nova de Lisboa', 'alt' => 'UNL')), 'http://www.unl.pt/', $options).
				$this->Html->link($this->Html->image("$imgURL/fct.png", array('title' => 'Faculdade de Ciências e tecnologias', 'alt' => 'FCT')), 'http://www.fct.unl.pt/', $options)
			);
		?>
		
		<h2><?php echo __('Our Contacts'); ?></h2>
		<?php 
			$bscontact = $this->Html->link('b.sampaio@campus.fct.unl.pt', 'mailto:b.sampaio@campus.fct.unl.pt', $options);
			echo $this->Html->para('', __('If you want to contact us send an e-mail to %s.', $bscontact)); 
		?>
	</div>
</div>