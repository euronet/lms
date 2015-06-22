<?php
global $SMARTY;
global $DB, $LMS;

$customerid = $_GET['id'];
$newbee = $_GET['bee'];

$DB->Execute('UPDATE customers SET beesmart=? WHERE id=?',array($newbee,$customerid));

$SESSION->redirect('?m=customerinfo&id='.$customerid);
?>
