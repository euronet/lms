<?php

/*
 *  $Id: devicelist.php,v 1.0 2010/02/12 00:00:00 emers Exp $
 */

$layout['pagetitle'] = trans('Customer devices');

if(!isset($_GET['o']))
	$SESSION->restore('ndlo', $o);
else
	$o = $_GET['o'];
$SESSION->save('dndlo', $o);

$devicelist = $LMS->GetDeviceList($o);
$listdata['total'] = $devicelist['total'];
$listdata['order'] = $devicelist['order'];
$listdata['direction'] = $devicelist['direction'];
unset($devicelist['total']);
unset($devicelist['order']);
unset($devicelist['direction']);

if ($SESSION->is_set('dndlp') && !isset($_GET['page']))
        $SESSION->restore('ndlp', $_GET['page']);
	
$page = (!isset($_GET['page']) ? 1 : $_GET['page']);
$pagelimit = ConfigHelper::getConfig('phpui.nodelist_pagelimit', $listdata['total']);
$start = ($page - 1) * $pagelimit;


$SESSION->save('dndlp', $page);

$SESSION->save('backto', $_SERVER['QUERY_STRING']);

$SMARTY->assign('page',$page);
$SMARTY->assign('pagelimit',$pagelimit);
$SMARTY->assign('start',$start);
$SMARTY->assign('devicelist',$devicelist);
$SMARTY->assign('listdata',$listdata);
$SMARTY->display('devicelist.html');

?>
