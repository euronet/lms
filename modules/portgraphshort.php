<?php

/*
 *  $Id: portgraphshort.php,v 1.0 2010/02/12 00:00:00 emers Exp $
 */

$portstatus['ip'] = $_GET['ip'];
$portstatus['port'] = $_GET['port'];

$port = $portstatus['port'];
$iptmp = str_replace(".","-",$portstatus['ip']);
$iptmp = str_replace("10-100","sw",$iptmp);
$iptmp = str_replace("77-46","sw",$iptmp);

if ($CONFIG[phpui][wykresy_url])
	$url=$CONFIG[phpui][wykresy_url].'/';
else
	$url="https://w3cache.euro-net.pl/wykresy/";

$url = $url.$iptmp;
$url = $url.'/';
$url1 = $url.$portstatus['ip'].'_'.$port.'-day.png';
$url2 = $url.$portstatus['ip'].'_'.$port.'-week.png';
$url3 = $url.$portstatus['ip'].'_'.$port.'-month.png';
$url4 = $url.$portstatus['ip'].'_'.$port.'-year.png';
$url5 = $url.$portstatus['ip'].'_'.$port.'-hour.png';

$SMARTY->assign('url1', $url1);
$SMARTY->assign('url2', $url2);
$SMARTY->assign('url3', $url3);
$SMARTY->assign('url4', $url4);
$SMARTY->assign('url5', $url5);


$SMARTY->display('portgraphshort.html');

?>
