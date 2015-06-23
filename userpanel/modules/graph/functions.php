<?php

/*
 *  $Id: functions.php,v 1.0 2010/03/23 21:00:00 emers Exp $
 */

function module_main()
{
    global $LMS,$SMARTY,$SESSION,$DB;
    $userinfo = $LMS->GetCustomer($SESSION->id);
    $ipaddr = $_SERVER["REMOTE_ADDR"];
    $nodeid = $LMS->GetNodeIDByIP($ipaddr);
    $node = $LMS->GetNode($nodeid);
    $netdevips = $LMS->GetNetDevIPs($node['netdev']);
    if ($node['port'] > 0) 
    {
        $graph['show'] = 1;
        $graph['port'] = $node['port'];
        $graph['ip'] = $netdevips[0]['ip'];

        $iptmp = str_replace(".","-",$graph['ip']);
        $iptmp = str_replace("10-100","sw",$iptmp);
        $iptmp = str_replace("77-46","sw",$iptmp);
    
        if ($CONFIG[phpui][wykresy_url])
	    $urltmp = $CONFIG[phpui][wykresy_url].'/';
	else
            $urltmp = "https://w3cache.euro-net.pl/wykresy/";

	$urltmp = $urltmp.$iptmp;
	$urltmp = $urltmp.'/';
	$graph['urld'] = 'modules/graph/graph.php?p=d';
	$graph['urlw'] = 'modules/graph/graph.php?p=w';
	$graph['urlm'] = 'modules/graph/graph.php?p=m';
	$graph['urly'] = 'modules/graph/graph.php?p=y';
//	$graph['urld'] = $urltmp.$graph['ip'].'_'.$graph['port'].'-day.png';
//	$graph['urlw'] = $urltmp.$graph['ip'].'_'.$graph['port'].'-week.png';
//	$graph['urlm'] = $urltmp.$graph['ip'].'_'.$graph['port'].'-month.png';
//	$graph['urly'] = $urltmp.$graph['ip'].'_'.$graph['port'].'-year.png';

//	$imaged = imagecreatefrompng("$urld");
//header('Content-type: image/png');

//imagepng($im);
//imagedestroy($im);

    } else
        $graph['show'] = 0;
                    
    $SMARTY->assign('graph_year', $LMS->CONFIG['userpanel']['graph_year']);
    $SMARTY->assign('graph_month', $LMS->CONFIG['userpanel']['graph_month']);
    $SMARTY->assign('graph_week', $LMS->CONFIG['userpanel']['graph_week']);
    $SMARTY->assign('graph_day', $LMS->CONFIG['userpanel']['graph_day']);
    $SMARTY->assign('graph',$graph);
    $SMARTY->display('module:graph.html');
}

if (defined('USERPANEL_SETUPMODE'))
{
    function module_setup()
    {
        global $SMARTY,$LMS;
        $SMARTY->assign('graph_year', $LMS->CONFIG['userpanel']['graph_year']);
        $SMARTY->assign('graph_month', $LMS->CONFIG['userpanel']['graph_month']);
        $SMARTY->assign('graph_week', $LMS->CONFIG['userpanel']['graph_week']);
        $SMARTY->assign('graph_day', $LMS->CONFIG['userpanel']['graph_day']);
	$SMARTY->display('module:graph:setup.html');
    }

    function module_submit_setup()
    {
        global $SMARTY,$DB;
        if ($_POST['graph_year']) {
	    $DB->Execute('UPDATE uiconfig SET value = \'1\' WHERE section = \'userpanel\' AND var = \'graph_year\'');
        } else {
            $DB->Execute('UPDATE uiconfig SET value = \'0\' WHERE section = \'userpanel\' AND var = \'graph_year\'');
        }
        if ($_POST['graph_month']) {
            $DB->Execute('UPDATE uiconfig SET value = \'1\' WHERE section = \'userpanel\' AND var = \'graph_month\'');
        } else {
            $DB->Execute('UPDATE uiconfig SET value = \'0\' WHERE section = \'userpanel\' AND var = \'graph_month\'');
        }
        if ($_POST['graph_week']) {
            $DB->Execute('UPDATE uiconfig SET value = \'1\' WHERE section = \'userpanel\' AND var = \'graph_week\'');
        } else {
            $DB->Execute('UPDATE uiconfig SET value = \'0\' WHERE section = \'userpanel\' AND var = \'graph_week\'');
        }
        if ($_POST['graph_day']) {
            $DB->Execute('UPDATE uiconfig SET value = \'1\' WHERE section = \'userpanel\' AND var = \'graph_day\'');
        } else {
            $DB->Execute('UPDATE uiconfig SET value = \'0\' WHERE section = \'userpanel\' AND var = \'graph_day\'');
        }
        header('Location: ?m=userpanel&module=graph');
    }
}
?>
