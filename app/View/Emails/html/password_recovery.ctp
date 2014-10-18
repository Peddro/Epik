<html>
	<body>
		<p><?php echo __('Hello').','; ?></p>
		<p><?php echo __('Your request to reset your password was received and realized with success.'); ?></p>
		<p><?php echo __('Your new password is: ').$password; ?></p>
		<p><?php echo __('If you didn\'t ask for a new password please inform us about this situation.'); ?></p>
		<p><?php echo __('Best regards').',<br/>'.Configure::read('System.name').' Team'; ?></p>
	</body>
</html>