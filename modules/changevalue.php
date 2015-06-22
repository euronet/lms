<?php
$val=$DB->GetOne('SELECT odlaczenieaktywne FROM customers WHERE id=?',array($_GET['id']));
switch($val)
{
    case 1:
	$val=0;
	break;
    default:
	$val=1;
}
$DB->Execute('UPDATE customers SET odlaczenieaktywne = ? WHERE id=?',array($val,$_GET['id']));
$SESSION->redirect('?m=customerinfo&id='.$_GET['id']);
?>
