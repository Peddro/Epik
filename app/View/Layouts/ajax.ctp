<?php
	echo $this->Session->flash();
	echo $this->fetch('content');
	echo $this->Js->writeBuffer();
?>
