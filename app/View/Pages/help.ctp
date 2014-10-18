<style type="text/css">
	.static-page #content h2 {
		margin-bottom: 5px;
	}
	
	.static-page #content tr.item {
		cursor: pointer;
	}
	
	.static-page #content tr.description {
		display: none;
	}
	
	.static-page #content .separator {
		margin-top: 10px;
	}
</style>
<?php 
	$name = Configure::read('System.name');
	$linkOptions = array('target' => '_blank');
	$rpPage = $this->Html->link(__('this page'), array('controller' => 'users', 'action' => 'reset'));
	$howToDevelop = $this->Html->link(__('this page'), array('controller' => 'pages', 'action' => 'display', 'howtos', 'develop'));
?>
<div class="one-column">
	<div class="column section select">
		<h2><?php echo __('General'); ?></h2>
		<table>
			<tr class="item">
				<td class="login">
					<div class="icon"></div>
				</td>
				<td>
					<?php echo $this->Html->link(__('How can I recover my password?'), ''); ?>
				</td>
			</tr>
			<tr class="description">
				<td colspan="2">
					<?php echo __('Go to %s and provide the e-mail address you used when you created your %s account.', $rpPage, $name); ?>
				</td>
			</tr>
			<tr class="item">
				<td class="course">
					<div class="icon"></div>
				</td>
				<td>
					<?php echo $this->Html->link(__('What is a LMS?'), ''); ?>
				</td>
			</tr>
			<tr class="description">
				<td colspan="2">
					<?php echo __('A Learning Management System (LMS) is simply a software application for the administration, documentation, tracking, reporting and delivery of education courses or training programs. Some examples of existing LMS are Moodle, Blackboard, Sakai and Desire2Learn.'); ?>
				</td>
			</tr>
		</table>
		<div class="separator"></div>
		
		<h2><?php echo __('Activities'); ?></h2>
		<table>
			<tr class="item">
				<td class="activity">
					<div class="icon"></div>
				</td>
				<td>
					<?php echo $this->Html->link(__('help-activities-title'), ''); ?>
				</td>
			</tr>
			<tr class="description">
				<td colspan="2">
					<?php echo __('help-activities-desc-1'); ?>
				</td>
			</tr>
			<tr class="item">
				<td class="hint">
					<div class="icon"></div>
				</td>
				<td>
					<?php echo $this->Html->link(__('What are activity hints for?'), ''); ?>
				</td>
			</tr>
			<tr class="description">
				<td colspan="2">
					<?php echo __('An activity with hints associated will be eligible for the %s help, available on some types of games. Use hints to help the players remember something or understand something without telling them the answer. You can associate up to 10 hints to an activity, but you don\'t need to create that many, two are enought.', __('activity-help-hints')); ?>
				</td>
			</tr>
			<tr class="item">
				<td class="resource">
					<div class="icon"></div>
				</td>
				<td>
					<?php echo $this->Html->link(__('Why can resources be associated to activities?'), ''); ?>
				</td>
			</tr>
			<tr class="description">
				<td colspan="2">
					<?php echo __('An activity with resources associated will be eligible for the %s help, available on some types of games. Use resources to provide more information about the activity, or like a diferent kind of hint. You can associate as many resources as you want to an activity, but one is enough.', __('activity-help-resource')); ?>
				</td>
			</tr>
			<tr class="item">
				<td class="import">
					<div class="icon"></div>
				</td>
				<td>
					<?php echo $this->Html->link(__('Which activities types can I import from a LMS?'), ''); ?>
				</td>
			</tr>
			<tr class="description">
				<td colspan="2">
					<?php echo __('Currently you can only import questions from Moodle and they must be short answer, multiple choice, or true or false questions.'); ?>
				</td>
			</tr>
		</table>
		<div class="separator"></div>
		
		<h2><?php echo __('Resources'); ?></h2>
		<table>
			<tr class="item">
				<td class="resource">
					<div class="icon"></div>
				</td>
				<td>
					<?php echo $this->Html->link(__('help-resources-title'), ''); ?>
				</td>
			</tr>
			<tr class="description">
				<td colspan="2">
					<?php echo __('help-resources-desc-1'); ?>
				</td>
			</tr>
			<tr class="item">
				<td class="audio">
					<div class="icon"></div>
				</td>
				<td>
					<?php echo $this->Html->link(__('How can I convert an audio file to a format supported by %s?', $name), ''); ?>
				</td>
			</tr>
			<tr class="description">
				<td colspan="2">
					<?php 
						$audioExt = implode(', ', Configure::read('Files.types.audio'));
						$avcPage = $this->Html->link('Any Video Converter', 'http://www.any-video-converter.com/products/for_video_free/', $linkOptions);
						$fsmPage = $this->Html->link('Free Studio Manager', 'http://www.dvdvideosoft.com/free-dvd-video-software.htm', $linkOptions);
						$uciPage = $this->Html->link('You Convert it', 'http://www.youconvertit.com/ConvertFiles.aspx', $linkOptions);
						$zamPage = $this->Html->link('Zamzar', 'http://www.zamzar.com/', $linkOptions);
						echo __('%s supports the following audio file types: %s. If you have an audio file on a different format you can use free applications to convert it into other formats. Some examples of desktop applications are %s (for Windows and Mac) and %s (for Windows), and examples of web applications are %s, and %s.', $name, $audioExt, $avcPage, $fsmPage, $uciPage, $zamPage); 
					?>
				</td>
			</tr>
			<tr class="item">
				<td class="image">
					<div class="icon"></div>
				</td>
				<td>
					<?php echo $this->Html->link(__('How can I convert an image file to a format supported by %s?', $name), ''); ?>
				</td>
			</tr>
			<tr class="description">
				<td colspan="2">
					<?php 
						$imgExt = implode(', ', Configure::read('Files.types.image'));
						$pbPage = $this->Html->link('Paintbrush', 'http://paintbrush.sourceforge.net/', $linkOptions);
						$foicPage = $this->Html->link('Free Online Images Converter', 'http://www.pictureresize.org/online-images-converter.html', $linkOptions);
						echo __('%s supports the following image file types: %s. If you have an image file on a different format you can use free applications to convert it into other formats. Some examples of desktop applications are Paint (for Windows) and %s (for Mac), and an example of a web application is the %s. On desktop applications you just need to open the image and use the Save As option to save them on a different format, while for web applications you just need to select the new format and the file, then click on convert and the new file will appear on your downloads folder.', $name, $imgExt, $pbPage, $foicPage); 
					?>
				</td>
			</tr>
			<tr class="item">
				<td class="video">
					<div class="icon"></div>
				</td>
				<td>
					<?php echo $this->Html->link(__('How can I convert a video file to a format supported by %s?', $name), ''); ?>
				</td>
			</tr>
			<tr class="description">
				<td colspan="2">
					<?php 
						$videoExt = implode(', ', Configure::read('Files.types.video'));
						$ytPage = $this->Html->link('Youtube', 'http://www.youtube.com/', $linkOptions);
						$vimPage = $this->Html->link('Vimeo', 'http://vimeo.com/', $linkOptions);
						$pbPage = $this->Html->link('Paintbrush', 'http://paintbrush.sourceforge.net/', $linkOptions);
						$foicPage = $this->Html->link('Free Online Images Converter', 'http://www.pictureresize.org/online-images-converter.html', $linkOptions);
						echo __('%s supports the following video file types: %s. Even if you have a file on those formats we recommend that you upload them to %s or %s, and then provide their URL on %s when you create your resources. But if you really want to upload your files to %s and they are on an unsupported format you can use free applications to convert them into other formats. Some examples of desktop applications are %s (for Windows and Mac) and %s (for Windows), and an example of a web application is %s.', $name, $videoExt, $ytPage, $vimPage, $name, $name, $avcPage, $fsmPage, $zamPage); 
					?>
				</td>
			</tr>
			<tr class="item">
				<td class="pdf">
					<div class="icon"></div>
				</td>
				<td>
					<?php echo $this->Html->link(__('How can I convert a text or presentation file to PDF?', $name), ''); ?>
				</td>
			</tr>
			<tr class="description">
				<td colspan="2">
					<?php 
						$pdf24Page = $this->Html->link('pdf24', 'http://en.pdf24.org/', $linkOptions);
						echo __('%s supports PDF files in order to allow you to insert files with textual content on your games. Sometimes those files format can be txt, rtf, doc, docx, ppt, etc. To convert them to pdf you can use some existing free tools, but first try the Save As (or Export) option on the program you use to edit your files, and check if it allows you to save as pdf. If not, an example of a desktop application is %s (for Windows), and an example of a web application is %s.', $name, $pdf24Page, $zamPage); 
					?>
				</td>
			</tr>
			<tr class="item">
				<td class="youtube">
					<div class="icon"></div>
				</td>
				<td>
					<?php echo $this->Html->link(__('What do I need to use Youtube or Vimeo videos?'), ''); ?>
				</td>
			</tr>
			<tr class="description">
				<td colspan="2">
					<?php echo __('You just need to provide one of the following URL\'s: the video page URL, the share URL, or the embedd URL.'); ?>
				</td>
			</tr>
			<tr class="item">
				<td class="import">
					<div class="icon"></div>
				</td>
				<td>
					<?php echo $this->Html->link(__('Which resources types can I import from a LMS?'), ''); ?>
				</td>
			</tr>
			<tr class="description">
				<td colspan="2">
					<?php echo __('Currently you can import audio, images, videos, and PDF files. Presentations and other types of files are not yet supported.'); ?>
				</td>
			</tr>
		</table>
		<div class="separator"></div>
		
		<h2><?php echo __('Projects'); ?></h2>
		<table>
			<tr class="item">
				<td class="project">
					<div class="icon"></div>
				</td>
				<td>
					<?php echo $this->Html->link(__('help-projects-title'), ''); ?>
				</td>
			</tr>
			<tr class="description">
				<td colspan="2">
					<?php echo __('help-projects-desc-1'); ?>
				</td>
			</tr>
			<tr class="item">
				<td class="scenario">
					<div class="icon"></div>
				</td>
				<td>
					<?php echo $this->Html->link(__('help-scenarios-title'), ''); ?>
				</td>
			</tr>
			<tr class="description">
				<td colspan="2">
					<?php echo __('help-scenarios-desc-1'); ?>
				</td>
			</tr>
			<tr class="item">
				<td class="play">
					<div class="icon"></div>
				</td>
				<td>
					<?php echo $this->Html->link(__('How can I test my project?'), ''); ?>
				</td>
			</tr>
			<tr class="description">
				<td colspan="2">
					<?php echo __('Currently you can only test your project after converting it to game format. If you\'ve never done it before use the "Export as Game" option found on the development area, or on the Dashboard. Or if you want more information about this consult %s.', $howToDevelop); ?>
				</td>
			</tr>
		</table>
		<div class="separator"></div>
		
		<h2><?php echo __('Games'); ?></h2>
		<table>
			<tr class="item">
				<td class="game">
					<div class="icon"></div>
				</td>
				<td>
					<?php echo $this->Html->link(__('help-games-title'), ''); ?>
				</td>
			</tr>
			<tr class="description">
				<td colspan="2">
					<?php echo __('help-games-desc-1'); ?>
				</td>
			</tr>
			<tr class="item">
				<td class="add">
					<div class="icon"></div>
				</td>
				<td>
					<?php echo $this->Html->link(__('How can I create and distribute a game?'), ''); ?>
				</td>
			</tr>
			<tr class="description">
				<td colspan="2">
					<?php echo __('You can find all the information you need about games development on %s on %s.', $name, $howToDevelop); ?>
				</td>
			</tr>
		</table>
		<div class="separator"></div>
		
		<h2><?php echo __('Sessions Logs'); ?></h2>
		<table>
			<tr class="item">
				<td class="logs">
					<div class="icon"></div>
				</td>
				<td>
					<?php echo $this->Html->link(__('help-sessions-title'), ''); ?>
				</td>
			</tr>
			<tr class="description">
				<td colspan="2">
					<?php echo __('help-sessions-desc-1'); ?>
				</td>
			</tr>
			<tr class="item">
				<td class="help">
					<div class="icon"></div>
				</td>
				<td>
					<?php echo $this->Html->link(__('What are public sessions?'), ''); ?>
				</td>
			</tr>
			<tr class="description">
				<td colspan="2">
					<?php echo __('Public sessions are sessions played through %s instead of a LMS. They are public because anyone can play a game you create if they know its URL.', $name); ?>
				</td>
			</tr>
			<tr class="item">
				<td class="help">
					<div class="icon"></div>
				</td>
				<td>
					<?php echo $this->Html->link(__('How do I know which information will be logged?'), ''); ?>
				</td>
			</tr>
			<tr class="description">
				<td colspan="2">
					<?php echo __('When you are developing a game you can choose which information to log by selecting the "Log" field. If you leave all fields empty, no information will be logged but a session will still be created and it will appear on your sessions logs page.'); ?>
				</td>
			</tr>
			<tr class="item">
				<td class="help">
					<div class="icon"></div>
				</td>
				<td>
					<?php echo $this->Html->link(__('Which information can I choose to log?'), ''); ?>
				</td>
			</tr>
			<tr class="description">
				<td colspan="2">
					<?php echo __('You can choose to log the team score, total rewards received, total penalties received, player total score, bonus received on each scenario, helps used and given, and the points earned and lost for each activity.'); ?>
				</td>
			</tr>
		</table>
		<div class="separator"></div>
	</div>
</div>
<?php
	echo $this->Html->scriptBlock(
	    '$(document).ready(function() {
			$("#"+ids.content).find("tr.item").click(function(event) {
				var dom = $(this).next(".description");
				(dom.is(":visible"))? dom.hide() : dom.show();
			});
			
			$("#"+ids.content).find("tr.item a").click(function() {
				event.preventDefault();
			});
		})',
	    array('inline' => false)
	);
?>