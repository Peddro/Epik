<!DOCTYPE html>
<html>
	<head>
		<?php echo $this->Html->charset(); ?>
		<title><?php echo $page_title; ?></title>

		<?php if (Configure::read('debug') == 0) { ?>
			<meta http-equiv="Refresh" content="<?php echo $pause; ?>;url=<?php echo $url; ?>"/>
		<?php } ?>
	</head>
	<body>
		<p><a href="<?php echo $url; ?>"><?php echo $message; ?></a></p>
	</body>
</html>