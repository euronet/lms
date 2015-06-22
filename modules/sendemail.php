<?php
passthru("/usr/bin/perl -T /etc/lms/lms-sendinvoicesrok --fakeid=".$_GET['id']." --data=".$_GET['data']."");
$SESSION->redirect('?m=customerinfo&id='.$_GET['id']);
?>
