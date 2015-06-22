<?php
$username = strtolower($_GET['id']);
passthru("/etc/lms/lms_dodatki/2013.11.03/pasmo --id ".$username." >> /tmp/pasmo.txt");
$SESSION->redirect('?m=customerinfo&id=' . $_GET['id']);
?>
