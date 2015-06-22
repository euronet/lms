<?php
$username = strtolower($_GET['username']);
passthru("/usr/bin/expect /etc/lms/lms_dodatki/2014.05.03/eryk.usun.subs ".$username." >> /tmp/eryk.txt");
$SESSION->redirect('?m=nodeinfo&id=' . $_GET['id']);
?>
