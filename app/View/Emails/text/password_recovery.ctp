<?php
echo __('Hello').',';
echo __('Your request to reset your password was received and realized with success.');
echo __('Your new password is: ').$password;
echo __('If you didn\'t ask for a new password please inform us about this situation.');
echo __('Best regards').',<br/>'.Configure::read('System.name').' Team';
?>