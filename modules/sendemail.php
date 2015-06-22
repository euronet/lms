<?php
passthru("/usr/bin/perl -T /etc/lms/lms-sendinvoices --fakeid=".$_GET['id']." --data=".$GET['data']."");
$SESSION->redirect('?m=customerinfo&id='.$_GET['id']);
?>
