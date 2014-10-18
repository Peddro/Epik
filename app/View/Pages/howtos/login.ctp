<div class="one-column">
	<div class="column section">
		<h2><?php echo __('What are we talking about?'); ?></h2>
		<?php
			$name = Configure::read('System.name');
			$options = array('target' => '_blank');
			$oauth = $this->Html->link(__('OAuth protocol'), 'http://oauth.net/', $options);
			$lti = $this->Html->link(__('IMS Learning Tools Inteorperability'), 'http://www.imsglobal.org/toolsinteroperability2.cfm', $options);
			$pluginApps = $this->Html->link(__('Moodle Applications'), 'https://moodle.org/plugins/view.php?plugin=local_applications', $options);
			$pluginAppsList = $this->Html->link(__('Applications List'), 'https://moodle.org/plugins/view.php?plugin=block_applications_list', $options);
			
			echo $this->Html->para('', __('%s supports the %s and the %s (version 1.1) standard. These allow users to access other systems tools from inside their own LMS, not needing this way to open a new window to access a certain service. Thanks to this you can distribute %s games as LMS activities and you can also login into %s from a LMS.', $name, $oauth, $lti, $name, $name));
			
			echo $this->element('lms_info', array('name' => $name, 'options' => $options));
		?>
		
		<h2><?php echo __('I understand, what do I need to do?'); ?></h2>
		<?php
			echo $this->Html->para('', __('First of all, you must make sure that your LMS supports the IMS LTI standard, if it is Moodle 2.2 or later then it certainly does. But if it isn\'t and you aren\'t sure, you should ask your LMS administrators about this. When you are sure that it supports the standard, if it is a Moodle system you can perform the steps described below, but for other systems you must try it by yourself.'));
			
			echo $this->Html->para('', __('There are two ways to login on %s using your Moodle system, one requires you to be able to create activities on a Moodle course, and the other (if activated and installed by an administrator) just requires you to be a registered user. Independently of the option you choose, when you login for the first time using this method, %s may inform you that your Moodle user information, is different from your %s user information and if that is the case you can change it, or choose to do nothing.', $name, $name, $name));
			
			$externaltool = $this->Html->link(__('External Tool'), 'http://docs.moodle.org/23/en/External_tool', $options);
			echo $this->Html->para('', __('So, if you have the capability to create course activities you just need to create a new %s activity on a course and provide the information requested. Some of this information such as the Launch URL, the Consumer Key and the Shared Secret can be found on your %s user profile page, on the right side. However, this has risks, by creating this activity if it is visible to everyone, anyone could use it to login on %s using your account, so make sure you turn it invisible so only you can see it and use it.', $externaltool, $name, $name));
			
			echo $this->Html->para('', __('If you don\'t like the risks of the above option, or if you haven\'t the capability to create course activities you can use Moodle Applications. However, those aren\'t part of Moodle, they need to be installed by an administrator in order to be possible to use them. %s is a plugin developed by us to provide every Moodle user with a way to manage and access their own applications inside the system. To allow the management of these applications we also created the %s block. When both plugins are installed you just need to create a new application by providing, as mentioned before, the Launch URL, the Consumer Key and the Shared Secret, which can all be found on your %s user profile page. Note: Those plugins can only be installed by your Moodle system administrators.', $pluginApps, $pluginAppsList, $name));	
		?>
	</div>
</div>