<?php
passthru("/usr/bin/perl -T /etc/lms/lms_dodatki/2013.03.02-stazowe/stazowe --id=".$_GET['id']);
$SESSION->redirect('?m=customerinfo&id='.$_GET['id']);
?>
