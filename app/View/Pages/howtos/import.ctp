<div class="one-column">
	<div class="column section">
		<h2><?php echo __('What are we talking about?'); ?></h2>
		<?php
			$name = Configure::read('System.name');
			$options = array('target' => '_blank');
			
			echo $this->Html->para('', __('%s allows users to create contents such as Activities and Resources on the Dashboard area, which can later be used for educational games development. These contents can also be imported from a LMS, you can for example import questions or a PDF file you use on a LMS course you manage to create an activity and a resource, respectively. This way you can reuse your data without having to repeat it. However, for this to work you need some permissions that only your LMS administrators can provide you. Currently, this functionality is only supported for Moodle users.', $name));
			
			echo $this->element('lms_info', array('name' => $name, 'options' => $options));
		?>
		
		<h2><?php echo __('I understand, what do I need to do?'); ?></h2>
		<?php
			echo $this->Html->para('', __('First of all, you must make sure that you are using Moodle 2.2 or later, that it provides you with the required permissions to import contents from it and that it has the necessary software installed for contents import. The best way to do this is by contacting your Moodle administrators and provide them this page URL. On this page last setion they can find all the information needed to prepare Moodle for contents import.'));
			
			echo $this->Html->para('', __('If you are sure that your Moodle system satisfies all the requirements mentioned above, you can now try to import something into %s. But first you must also know that your LMS activities or resources shouldn\'t have the same names as other %s activities or resources, respectively, because if this happens the import will fail. The steps to import an activity or resource are the following:', $name, $name));
			
			$list = array(
				$this->Html->para('', __('Login into %s;', $name)),
				$this->Html->para('', __('On your Dashboard select the Activities or Resources section;')),
				$this->Html->para('', __('Then click on Add (the plus button near the page title) to open a window;')),
				$this->Html->para('', __('On this window choose the option "Import Activity/Resource from LMS";')),
				$this->Html->para('', __('Now provide the information about your LMS system, this is, the type of LMS, the base URL (the url to access your LMS Home Page) and your LMS username and password;')),
				$this->Html->para('', __('If the previous step worked, after some time you should be able to see the courses you manage on your LMS. If this isn\'t the case the information you provided could be incorrect or your LMS doesn\'t satisfy the requirements mentioned before;')),
				$this->Html->para('', __('Assuming that you can see your courses, you must now select one of them, and then a list of activities/resources in the selected course should be displayed;')),
				$this->Html->para('', __('If you are adding a resource, now you just need to select the resources you want to import, but if you are adding an activity you must select the one that contains what you want and only then you can select the activities to import;')),
				$this->Html->para('', __('If everything went well you can now find the imported contents on your Activities/Resources list in the Dashboard.'))
			);
			echo $this->Html->nestedList($list, array(), array(), 'ol');
			
			echo $this->Html->para('', __('If you are having problems with contents import please contact us via e-mail.'));
		?>
		
		<h2><?php echo __('I am a Moodle Administrator and I want to prepare it for contents import'); ?></h2>
		<?php
			echo $this->Html->para('', __('The steps to provide your users with contents import capability are listed below, it seems a lot to do but actually you can do it in 5 minutes (you need basic programming knowledge to perform some of these steps):'));
			
			$package = $this->Html->link(__('here'), '/files/documents/moodle/web_services.zip');
			$list = array(
				$this->Html->para('', __('Download the zip file that contains the files to install on Moodle from %s;', $package)),
				$this->Html->para('', __('Uncompress the downloaded file to some place on your system and you will see a folder with name "mod", inside this folder you will find the "quiz" and "resource" folders. This structure is the same that you can find on your Moodle system, so what you need to do is to copy these folders contents to their equivalents on your system. You don\'t need to replace anything! What you need to do step by step is:')) =>
					array(
						$this->Html->para('', __('Copy the file externallib.php inside quiz folder and paste it on your Moodle system inside /mod/quiz;')),
						$this->Html->para('', __('Copy the file externallib.php inside resource folder and paste it on your Moodle system inside /mod/resource;')),
						$this->Html->para('', __('Copy the file services.php inside quiz/db folder and paste it on your Moodle system inside /mod/quiz/db;')),
						$this->Html->para('', __('Copy the file services.php inside resource/db folder and paste it on your Moodle system inside /mod/resource/db;')),
						$this->Html->para('', __('Now instead of copying the version.php files inside "quiz" and "resource" folders, open their equivalents on your Moodle system, this is /mod/quiz/version.php and /mod/resource/version.php and increment the $module->version value. For example if this variable has the value 2011112900 you must change it to 2011112901;')),
						$this->Html->para('', __('In case you already have files with the names mentioned above you must copy our files contents into the ones you have, but be carefull with this because those files must satisfy a certain structure.')),
					),
				$this->Html->para('', __('When everything is in place login as administrator on your Moodle system and a notifications page should appear telling you that the Quiz and Resource plugins must be upgraded (this will only happen if you increment the version value inside the version.php files). If you did so and the page didn\'t open go to (Site administration > Notifications) and it should work;')),
				$this->Html->para('', __('On this page click on upgrade and wait for the plugins to be upgraded;')),
				$this->Html->para('', __('Now it is time to configure Moodle settings, first go to (Site Administration > Plugins > Web Services > Manage Protocols) and enable the REST protocol;')),
				$this->Html->para('', __('Then go to (Site Administration > Plugins > Web Services > External Services), click on Add, then click on "Show advanced" and provide the following information: (Name: Epik Services, Shortname: moodle_epik_app, Enabled: Yes, Can download files: Yes), if there is no field for shortname attribute you must set it later, but before that click on "Add Service";')),
				$this->Html->para('', __('Now you must add functions to this service. From the displayed list you must choose the functions listed below (the last 3 will only be available if you performed the steps 1 - 4):')) => 
					array(
						$this->Html->para('', 'core_enrol_get_users_courses'),
						$this->Html->para('', 'core_course_get_contents'),
						$this->Html->para('', 'core_webservice_get_site_info'),
						$this->Html->para('', 'mod_quiz_get_questions_info'),
						$this->Html->para('', 'mod_quiz_get_questions_data'),
						$this->Html->para('', 'mod_resource_get_info')
					),
				$this->Html->para('', __('If you couldn\'t set the service shortname previously you need to do it manually, this is, you need to set it directly in the database. This step is very important! To do this you just need to access your database using your Database Management System and then on table with name "external_services" you must edit the field "shortname" for the external service just created. The value for this field is, as mentioned before, moodle_epik_app;')),
				$this->Html->para('', __('Now go to (Site Administration > Users > Permissions > Define Roles) and create a new role. You can give any name to it, I chose "Web Services User" and you must select the following capabilities:')) =>
					array(
						$this->Html->para('', 'moodle/webservice:createtoken'),
						$this->Html->para('', 'webservice/rest:use')
					),
				$this->Html->para('', __('Finally, you must assign this role to all users that you want to be able to import contents, so go to (Site Administration > Users > Permissions > Assign System Roles), select the "Web Services Role" and then add the users to it.'))
			);
			
			echo $this->Html->nestedList($list, array(), array(), 'ol');
			
			echo $this->Html->para('', __('If you couldn\'t perform some of the actions described above, please contact us via e-mail.'));
		?>
	</div>
</div>