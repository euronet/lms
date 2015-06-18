<?php

/*
 *  $Id: macdellist.php,v 1.0 2010/02/12 00:00:00 emers Exp $
 */

$layout['pagetitle'] = trans('MACs Remove');

$SESSION->restore('macsearch', $macsearch);
$SESSION->restore('uslk', $k);
    
if($_GET['is_sure']!=1)
{
	switch($_GET['t'])
	{
		case 1:
			$table=trans('correct');
			break;
		case 2:
			$table=trans('archived');
			break;
		case 0:
		default:
			$table=trans('suspect');
	}

	$body .= '<P>'.trans('Do you want to remove this list of MACs from $0 table?',$table).'</P>';
	$body .= '<P><A HREF="?m=macdellist&t='.$_GET['t'].'&is_sure=1">'.trans('Yes, I do.').'</A></P>';
}else{
	$body .= '<P>'.trans('MAC addresses has been removed.').'</P>';
	$LMS->DeleteMacList($_GET['t'], $macsearch, $k);
}

$SMARTY->assign('body',$body);
$SMARTY->display('dialog.html');
?>
