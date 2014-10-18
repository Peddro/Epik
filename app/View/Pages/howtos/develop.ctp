<style type="text/css">
	#content img {
		max-width: 95%;
		-webkit-box-shadow: 0 0 10px black;
		-moz-box-shadow: 0 0 10px black;
		-o-box-shadow: 0 0 10px black;
		box-shadow: 0 0 10px black;
	}
	img.center {
		margin: 0 auto;
		display: block;
		clear: both;
	}
</style>
<?php
	$name = Configure::read('System.name');
	$imgURL = 'static/howtos/develop';
	$imgLeft = array('class' => 'left');
	$imgCenter = array('class' => 'center');
	$imgRight = array('class' => 'right');
	$howToImport = $this->Html->link(__('this page'), array('controller' => 'pages', 'action' => 'display', 'howtos', 'import'));
	$howToDistribute = $this->Html->link(__('this page'), array('controller' => 'pages', 'action' => 'display', 'howtos', 'distribute'));
?>
<div class="one-column">
	<div class="column section">
		
		<?php echo $this->Html->para(false, __('In this tutorial you will find all the information you need to start developing and using your games in %s. We will start by explaining what can be done on the dashboard; then how the development screen works, and finally how to create, generate and distribute your games. If at any moment you want to leave this page because you want to try something we mentioned, or for some other reason, you can always come back by selecting the "How to ... ?" link on the bottom of all %s pages, and then choose "%s".', $name, $name, $title_for_layout)); ?>
		
		<h2><?php echo __('Understanding the Dashboard'); ?></h2>
		<?php 
			echo $this->Html->para(false, __('The dashboard is the section presented to you each time you sign in into %s. Understanding this section is crucial because everything you create on %s can be managed with it. This section is divided into four different subsections: Activities, Resources, Projects, and Games (image below). A more detailed explanation regarding each one is presented on the following subsections.', $name, $name));
			echo $this->Html->image("$imgURL/navigation.png", $imgLeft);
		?>
		
		<h3><?php echo __('Activities'); ?></h3>
		<?php 
			echo $this->Html->para(false, __('help-activities-desc-1'));
			echo $this->Html->para(false, __('help-activities-desc-2'));
			echo $this->Html->para(false, __('The image below is an example of this section with some questions listed. The plus button on the top (to the right of the section title) can be used to create new activities or import them from a LMS. The smaller buttons displayed on the right of the hovered question can be used to edit, associate hints, associate resources, or remove the activity, respectively. A reload button can also be displayed on that area in case the activity was imported from a LMS. You can only associate hints and resources to questions. Those options are not available for groups because the hints and resources belong to the question and not to the group. To associate resources you must first create them on the Resources Section in the Dashboard.'));
			echo $this->Html->image("$imgURL/dashboard_activities.png", $imgCenter);
		?>
		
		<h3><?php echo __('Resources'); ?></h3>
		<?php 
			echo $this->Html->para(false, __('help-resources-desc-1'));
			echo $this->Html->para(false, __('help-resources-desc-2'));
			echo $this->Html->para(false, __('The image below is an example of this section with some resources listed (on this case images). The upload button on the top (to the right of the section title) can be used to create new resources or import them from a LMS. The smaller buttons displayed on the right of the hovered resource can be used to edit, or remove it, respectively. As mentioned for activities, a reload button can also be present on this area in case the resource was imported from a LMS.'));
			echo $this->Html->image("$imgURL/dashboard_resources.png", $imgCenter);
			
			echo $this->Html->para(false, __('If you want to know how activities and resources can be imported from a LMS please consult %s. You may need your LMS administrators intervention to do so, because your LMS may not support this feature by default.', $howToImport));
		?>

		<h3><?php echo __('Projects'); ?></h3>
		<?php 
			echo $this->Html->para(false, __('help-projects-desc-1'));
			echo $this->Html->para(false, __('help-projects-desc-2'));
			echo $this->Html->para(false, __('The image below is an example of this section with some projects listed. As for activities and resources, the plus button on the top can be used to create new projects, and the smaller buttons can be used to edit, export, or remove a project, respectively. The export as game option must only be used when your project is finished and ready to become a game, we will explain how it works later on.'));
			echo $this->Html->image("$imgURL/dashboard_projects.png", $imgCenter);
		?>

		<h3><?php echo __('Games'); ?></h3>
		<?php 
			echo $this->Html->para(false, __('help-games-desc-1'));
			echo $this->Html->para(false, __('help-games-desc-2'));
			echo $this->Html->para(false, __('The image below is an example of this section with some games listed. The actions mentioned above are displayed on the right of the hovered game. By clicking on the game name its profile will be displayed, where you can find the information needed to distribute it through %s or through a LMS.', $name));
			echo $this->Html->image("$imgURL/dashboard_games.png", $imgCenter);
			
			echo $this->Html->para(false, __('Now that you understand how the Dashboard works and what it can be used for, its time to start developing your first game.'));
		?>
		
		
		<h2><?php echo __('Developing a Game'); ?></h2>
		
		<h3><?php echo __('Creating the Project'); ?></h3>
		<?php 
			echo $this->Html->para(false, __('To start developing a game you must create a project. To do so, open the Projects Section in your Dashboard and click the plus button. This will open a window asking you to provide a name and a description for your project (you can change both fields later). Let\'s call it "My first Project" for now and then click on continue.')); 
			echo $this->Html->para(false, __('Now you must choose the game type and a template for your project. As you can see on the image below, you can choose the type of game by using the select box on the right. The templates and a description for this type of game will then appear below. If you hover a template, a description of its contents is also displayed. You can start with a blank template, or with a template that already defines some of the game properties. If this is your first project, to keep it simple, choose the "Collaborative Quiz" type and the "4 Lectures + 12 Questions + 4 Hard Questions" template as displayed below.')); 
			
			echo $this->Html->image("$imgURL/choose_template.png", $imgCenter);
			
			echo $this->Html->para(false, __('Now press "Finish" and on the following window click on "Open Project", or in case you close the window accidentally click on the project name in your Dashboard.'));
		?>
		
		<h3><?php echo __('The Development Screen'); ?></h3>
		<?php
			echo $this->Html->para(false, __('You are now on the development page (image below). Here you can still perform most of the actions available on the Dashboard as, for example, create new activities and resources, or export your project as a game without leaving the page. But you can\'t, remove a project or consult a game sessions log. This page is divided into 4 different sections: the toolbar (on the top), the explorer (on the left), the canvas (in the center), and the properties (on the right). Each section is described in more detail on the following sections.'));
			echo $this->Html->image("$imgURL/project_screen.png", $imgCenter);
		?>
		
		<h3><?php echo __('The Toolbar'); ?></h3>
		<?php
			echo $this->Html->para(false, __('The toolbar, as shown below, provides you with several tools for creation, edition, and insertion. Most of those tools are probably familiar to you, so we\'ll just introduce the most important and less obvious.'));
			echo $this->Html->image("$imgURL/toolbar.png", $imgCenter);
			echo $this->Html->para(false, __('The first tool on the left is the "New Menu", presented below on the left. It provides tools to create new scenarios, projects, activities, and resources. The last three are the options already available on the Dashboard. However, the scenarios were never mentioned before.'));
			echo $this->Html->image("$imgURL/new_menu.png", $imgLeft);
			echo $this->Html->para(false, __('help-scenarios-desc-1').' '.__('As you can see on the image, you can create Lecture Scenarios, Activities Scenarios, or choose a scenario template.'));
			echo $this->Html->para(false, __('Basically, a lecture scenario must be used to present a concept, or topic, to the players using text and resources. Whilst an activities scenario must contain activities for the players to solve, although they can also contain text and all the other available elements for lecture scenarios. Choosing to create one of those types of scenarios from the menu in the left would create a blank scenario, but if you choose to create from a template, you can then choose one of those types of scenarios with some empty elements on it which you can then fill.'));
			echo $this->Html->para(false, __('Next to the "New Menu" is the "Import Menu" and "Export Menu" (displayed on the right), as their name implies they can be used to import activities and resources from a LMS, and export a project to other formats, respectively.'));
			echo $this->Html->image("$imgURL/import_export_menu.png", $imgRight);
			echo $this->Html->para(false, __('Lastly, there are the insertion menus, identified on the toolbar image above by: texts, shapes, resources, and activities. If you choose to insert a title, or a square, it will be immediately created and added to the current scenario. But if you choose to insert an image or a question, a window will be displayed from where you must choose an image, or a question you previously created, to be inserted on the current scenario.'));
		?>
		
		<h3><?php echo __('The Canvas'); ?></h3>
		<?php
			echo $this->Html->para(false, __('The canvas is the area in the center of the page where the contents of the current scenario are displayed. Almost every edition action you perform with the toolbar is related with the current element selected on the canvas, be it a scenario, or an element contained in it. You can use this area to select, reposition or resize the elements on a scenario. Besides that, it can also be used to cut, copy, and paste, for example, by using the right click context menu.'));
			echo $this->Html->para(false, __('The players panel is also always visible on this area, but you can\'t interact with it, unless when the players general property is selected. We will talk more about this on the following sections.'));
		?>
		
		<h3><?php echo __('The Explorer Panel'); ?></h3>
		<?php
			echo $this->Html->image("$imgURL/explorer_panel.png", $imgLeft);
			echo $this->Html->para(false, __('This is the panel on the left side of the page that allows you to change between the game scenarios and properties. The first two items on this panel refer to the general game properties and general game scenarios, respectively. Below them, after the horizontal separator, are listed all the game scenarios you created and their respective contents. The image on the left is an example of what you will find when you create a project using the template mentioned before.'));
			echo $this->Html->para(false, __('The available game properties are: the logo image position, the scores and helps names and values, the players panel position and colors, and the game sounds and music. They can be used to change general aspects of the game that are independent from the current scenario. Their meaning and how you can use them is explained in more detail on the next section.'));
			echo $this->Html->para(false, __('The general scenarios are common to all games you create on %s and they are displayed here just for you to be able to preview them, since they do not provide any customization options. The existing general scenarios are: the game start, the game instructions, the waiting room, the teams or players rankings, and the game over. On the image below is displayed how they are linked between them.', $name));
			echo $this->Html->para(false, __('Your scenarios, as mentioned above, are listed below the horizontal separator. Their order is not important for the game flow, as you can order them as you want on this panel. Inside each scenario there are 3 or 4 folders that separate their contents by texts, shapes, resources, and activities. By clicking on a scenario or on its contents on this panel, it will select them on the canvas, and the inverse also works: when you select an element on the canvas it is immediately selected and displayed on the explorer panel. Besides that, you can also drag your scenarios to sort them, and use the right click context menu to cut, copy, paste, remove, etc.'));
			echo $this->Html->image("$imgURL/general_game_flow.png", $imgCenter);
		?>
		
		<h3><?php echo __('The Properties Panel'); ?></h3>
		<?php
			echo $this->Html->image("$imgURL/properties_panel.png", $imgRight);
			echo $this->Html->para(false, __('This is the panel where the current selected element properties are displayed. There are some properties common to almost all elements, such as the element name, the width and height, the position, the background color, etc. And there are also several properties specific for each type of element. The image on the right is an example of this panel when an activities scenario is selected.'));
			echo $this->Html->para(false, __('The list below explains what can be found on the properties panel for each general game property (Remember: you can change between them using the explorer panel).'));
		?>
		<ul>
			<li><b><?php echo __('property-logo'); ?></b> - <?php echo __('it allows you to reposition the %s logo on the game screen;', $name); ?></li>
			<li><b><?php echo __('property-scores'); ?></b> - <?php echo __('it allows you to determine the name for each type of score and also which ones you want to be registered on the activity logs. It also allows you to define the maximum number of helps available throughout the game. For activities scenarios you will then be able to limit the number of helps the players are allowed to use based on the value you specify here;'); ?></li>
			<li><b><?php echo __('property-players'); ?></b> - <?php echo __('it allows you to change the players panel position and colors;'); ?></li>
			<li><b><?php echo __('property-sounds'); ?></b> - <?php echo __('it allows you to change the game background music and the sounds played when a player answers correctly, or incorrectly to a question, and when he receives a help request, or response.'); ?></li>
		</ul>
		<?php
			echo $this->Html->para(false, __('All the properties specified above already have default values even when you start a game from scratch (by choosing a blank template) and you don\'t need to modify them if you don\'t want to.'));
			echo $this->Html->para(false, __('When a scenarios is selected, besides some styles properties, there will be two kinds of properties available that aren\'t present on any other type of element: the rules and the flow (you can see them in the image on the right). The rules (only available for activities scenarios) allow you to limit the number of helps players are allowed to use, the types of bonus they can receive and if you want the points received for each bonus type on this scenario to be logged. The flow can contain different fields for each type of scenario and it must be used to link your scenarios between them. In total you have the following flow options:'));
		?>
		<ul>
			<li>
				<b><?php echo __('Game start'); ?></b> - 
				<?php echo __('this field can only be selected for one scenario in your game, since you can only have a starting scenario. The scenario with this property selected will be the first to appear after the waiting room scenario, mentioned before.'); ?>
			</li>
			<li>
				<b><?php echo __('Timeout'); ?></b> - 
				<?php echo __('it allows you to specify a time limit for the current scenario, when the time is over it jumps to the specified scenario on the select field;'); ?>
			</li>
			<li>
				<b><?php echo __('Everyone finished activities'); ?></b> - 
				<?php echo __('it allows you to specify to which scenario the players must be sent when all of them finished the activities on the current scenario;'); ?>
			</li>
			<li>
				<b><?php echo __('Continue'); ?></b> - 
				<?php echo __('it is only available on lecture scenarios; it allows you to create a continue button that must be clicked by all players before sending them to the specified scenario;'); ?>
			</li>
			<li><b><?php echo __('Skip'); ?></b> - <?php echo __('it is only available on activities scenarios; it allows you to create a skip button that if clicked by at least one player all will be sent to the specified scenario, even if they finished all activities.'); ?></li>
		</ul>
		<?php
			echo $this->Html->para(false, __('The properties for all the other elements that a scenario can contain are similar, except for questions, and questions groups. Besides the styles properties, those types of elements provide the scores and helps properties. The scores properties allow you to specify the question reward score, the penalty percentage applied to the reward score each time a player makes an incorrect attempt, and the collaboration timeout. This timeout is the time a player has to answer a question after receiving a help from another player. If he answers on time, a penalty won\'t be applied to the question score and the other player can receive a bonus for collaborating, but only if you specified it on the scenario rules. Whereas the helps properties allow you to specify which helps will be available for the selected question. For the %s help you can also select the resource to be used, and for the %s help you can select the hints to be used (these two last properties are not available for questions groups because they will select a random resource or hint for those two types of helps).', __('activity-help-resource'), __('activity-help-hints')));
			
			echo $this->Html->para(false, __('Finally, it is also important to note that template elements (like the ones on the image below) have a very important property, the "Choose button". Those elements are empty resources or activities and to fill them you must use an already existing resource or activity. But instead of using the button on the toolbar to insert a new element, you must select them and use the choose button on the properties panel.'));
			
			echo $this->Html->image("$imgURL/elements_templates.png", $imgCenter);
		?>
		
		
		<h2><?php echo __('Creating and Distributing the Game'); ?></h2>
		<?php
			echo $this->Html->para(false, __('You are now ready to create your first game. On the project you created before, you just need to select each scenario and fill the text fields with your own text and then fill all resources and activities templates with your own. If you want you can also add new scenarios, change the game flow, change the general properties, etc. When you are finished, it is time to convert your project to game format.'));
			echo $this->Html->para(false, __('Before trying to convert a project to a game, you must make sure that your project satisfies the following requisites:')); 
		?>
		<ul>
			<li>
				<b><?php echo __('There is a start'); ?></b> - <?php echo __('you must specify which will be the first scenario using the flow options;'); ?>
			</li>
			<li>
				<b><?php echo __('No isolated scenarios'); ?></b> - 
				<?php echo __('you mustn\'t have isolated scenarios in your project, this is, all scenarios must be linked to some other scenario using at least one option from the flow properties;'); ?>
			</li>
			<li>
				<b><?php echo __('No cycles'); ?></b> - 
				<?php echo __('you can\'t create cycles in the game flow, this is, you can\'t go back to a scenario already displayed to players. If you want to do something like that you must create two scenarios with the same contents for each scenario you want to be repeated.'); ?>
			</li>
			<li>
				<b><?php echo __('No template elements'); ?></b> - 
				<?php echo __('all resources and activities on your project must reference a resource or activity created by you. Empty elements are not allowed in the game, if you don\'t want them just delete them from the scenario.'); ?>
			</li>
		</ul>
		<?php 
			echo $this->Html->para(false, __('In addition to the requisites specified above, there are also other aspects that must be satisfied. But don\'t worry, if you forget about some of them, when you try to create your game, the creation will fail and the errors found in the project will be shown so you can correct them before trying again.'));
			echo $this->Html->para(false, __('It is also important to note that a game cannot be edited as you can only change its name or icon later, so make sure that your project is ready and saved before continuing.'));
		?>
		
		<h3><?php echo __('Converting a Project into a Game'); ?></h3>
		<?php
			echo $this->Html->para(false, __('Assuming your project is ready, let\'s try to convert it to game format. If you are on the development page, select the export menu on the toolbar (mentioned before on the toolbar section) and then choose "Export as Game". If you are in the Dashboard, hover your project and on the right click on the gamepad button. On the opened window you must provide a name for your game and a description, the name will be displayed on the first game scenario, but the description will be visible only for you. Click on continue and then you must specify if your game is public or private (this option is not yet fully working) and you can choose an icon for your game (this is not mandatory as there is a default icon for all %s games). Lastly, click on finish and wait. If everything goes well a page with two buttons to view and play your game will be displayed, otherwise a page with a list of errors will be displayed (if this is the case you must solve those errors before trying again).', $name));
			echo $this->Html->para(false, __('After you create a game, it will be placed onto the Games Section in your Dashboard. If you want to test it, just click the play button displayed on the right, and in case it is a multiplayer game you must ask someone else to play with you.'));
		?>
		
		<h3><?php echo __('Distributing the Game to Students'); ?></h3>
		<?php echo $this->Html->para(false, __('If you want to distribute your game to your students you can do it in two ways:')); ?>
		<ul>
			<li>
				<b><?php echo __('Use %s', $name); ?></b> - <?php echo __('for this you just need to open your game play page by clicking on the play button, copy this page URL and then send it to your students. Session logs generated from this approach will be organized into the Public Sessions category (more about this on the following section).'); ?>
			</li>
			<li>
				<b><?php echo __('Use a LMS'); ?></b> - 
				<?php echo __('for this your LMS must support the IMS LTI standard. If you want to know more about this consult, %s. The main advantages for this approach are that by creating an activity on a course (or context) in a LMS only students from this course will be able to play between them, and when the session logs are stored they will be organized by course and LMS, so you can easily identify who played on a certain game session. In addition, when the players finish the game, a grade of 100 is sent to the LMS to indicate that they completed the activity. This grade can be changed later through the LMS, or through the session logs page here on %s.', $howToDistribute, $name); ?>
			</li>
		</ul>
		
		<h3><?php echo __('Consulting the Sessions Logs'); ?></h3>
		<?php 
			echo $this->Html->para(false, __('help-sessions-desc-1'));
			echo $this->Html->para(false, __('On this page, if anyone has already played the game, you\'ll find all the sessions organized in tables (image below). Public sessions are always on the beginning. Below, if you have ever distributed your game through a LMS, you will find one table for each. Each row from those tables represents a session and the first column for each table always contains the course or context name.'));
			echo $this->Html->image("$imgURL/game_sessions.png", $imgCenter);
			echo $this->Html->para(false, __('If you hover a session a "More" button will appear on the right, just like on the Dashboard, click on it to consult the session logs. On this next page you will find the session information divided by player/student. For each player, there is a table for the global scores, helps, received bonus by scenario, and activities scores by activity (image below). If you distributed your game through a LMS you\'ll also find on this page a field or each student that allows you to send a grade to the LMS between 0% and 100%.'));
			echo $this->Html->image("$imgURL/session_logs.png", $imgCenter);
		?>
		
	</div>
</div>