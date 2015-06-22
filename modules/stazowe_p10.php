<?php
$DB->Execute('UPDATE customers SET stazowe = ( stazowe + 10 ) WHERE id=?',array($_GET['id']));
$SESSION->redirect('?m=customerinfo&id='.$_GET['id']);
?>
