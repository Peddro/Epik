<div class="one-column">
	<div class="column section">
		<h2><?php echo __('What are we talking about?'); ?></h2>
		<?php
			$name = Configure::read('System.name');
			$options = array('target' => '_blank');
			$oauth = $this->Html->link(__('OAuth protocol'), 'http://oauth.net/', $options);
			$lti = $this->Html->link(__('IMS Learning Tools Inteorperability'), 'http://www.imsglobal.org/toolsinteroperability2.cfm', $options);
			
			echo $this->Html->para('', __('%s supports the %s and the %s (version 1.1) standard. These allow users to access other systems tools from inside their own LMS, not needing this way to open a new window to access a certain service. Thanks to this you can distribute %s games as LMS activities and you can also login into %s from a LMS.', $name, $oauth, $lti, $name, $name));
			
			echo $this->element('lms_info', array('name' => $name, 'options' => $options));
		?>
		
		<h2><?php echo __('I understand, what do I need to do?'); ?></h2>
		<?php
			echo $this->Html->para('', __('First of all, you must make sure that your LMS supports the IMS LTI standard, if it is Moodle 2.2 or later then it certainly does. But if it isn\'t and you aren\'t sure, you should ask your LMS administrators about this. When you are sure that it supports the standard, if it is a Moodle system you must also be able to create new course activities. If that\'s the case the steps to distribute a game as a learning activity on Moodle are:'));
			
			$externaltool = $this->Html->link(__('External Tool'), 'http://docs.moodle.org/23/en/External_tool', $options);
			$list = array(
				$this->Html->para('', __('Go to a course page and turn on editing mode;')),
				$this->Html->para('', __('Now you can choose to add a resource or activity. Choose an activity and select the "%s" option, this will send you to a new page (On this page there are several fields that you probably already know, and some of them are not even important for this to work, so we will just talk about the recommended ones on the following lines);', $externaltool)),
				$this->Html->para('', __('The "Launch URL" field is where you provide the URL to access %s games. This URL can be found on your game page here on %s;', $name, $name)),
				$this->Html->para('', __('For the "Launch container" field select the "Embed, without blocks" option;')),
				$this->Html->para('', __('The "Consumer Key" is your game unique identifier, you can also find it on your game page here on %s;', $name)),
				$this->Html->para('', __('The "Shared Secret" is like your game password, you can also find it on your game page here on %s;', $name)),
				$this->Html->para('', __('(Optional) If you want your game icon to be displayed as your activity icon, you can use the "Icon URL" field to provide the link for the game icon. You can also find this URL on your game page here on %s;', $name)),
				$this->Html->para('', __('On the privacy box we recommend that you select all the available fields. If you don\'t understand what they are for you can find a good explanation by clicking their help button in Moodle;')),
				$this->Html->para('', __('Finally, you just need to click on Save and your activity will be available to your students.', $name))
			);
			
			echo $this->Html->nestedList($list, array(), array(), 'ol');
			
			echo $this->Html->para('', __('When your students finish a game their results will be store on %s and you can consult them by clicking on the "Sessions Log" icon on your game page. A result can also be sent to your LMS but it won\'t be as detailed as the data we provide.', $name));
			
			echo $this->Html->para('', __('We hope you and your students enjoy the experience!'));
		?>
	</div>
</div>