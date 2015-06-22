<?php
$DB->Execute('UPDATE customers SET typodlaczenia=? WHERE id=?',array($_GET['type'],$_GET['id']));
$SESSION->redirect('?m=customerinfo&id='.$_GET['id']);
?>
