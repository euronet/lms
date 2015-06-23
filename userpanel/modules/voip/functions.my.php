<?php

/*
 *  $Id: functions.php,v 1.0 2010/03/23 21:00:00 emers Exp $
 */

function module_main()
{
    global $LMS,$SMARTY,$SESSION,$DB;
    $curr_date['day'] = date("j");
    $curr_date['month'] = date("n");
    $curr_date['year'] = date("Y");
    $bil_cfg['number_id'] = 0;
    $bil_cfg['number'] = "";
    $bil_cfg['month'] = $curr_date['month'];
    $bil_cfg['year'] = $curr_date['year'];
    $bil_cfg['id'] = 0;
    if (isset($_POST['voip_acc'])&&isset($_POST['month'])&&isset($_POST['year'])) {
	$bil_cfg['number_id'] = $_POST['voip_acc'];
	$acc_info = $LMS->GetVoipAccount($_POST['voip_acc']);
	$bil_cfg['number'] = $acc_info['phone'];
	$bil_cfg['month'] = $_POST['month'];
	$bil_cfg['year'] = $_POST['year'];
    }
    $userinfo = $LMS->GetCustomer($SESSION->id);
    $voip_group = $DB->GetOne('SELECT value FROM uiconfig WHERE section = \'userpanel\' AND var = \'voip_group\'');
    $tariff = $DB->GetAll('SELECT assignments.id AS id, tariffid, assignments.customerid, value, tariffs.name FROM assignments, tariffs WHERE tariffid=tariffs.id AND tariffs.type=4 AND assignments.customerid=?',array($SESSION->id));
    if ($DB->GetOne('SELECT 1 FROM customerassignments WHERE customergroupid = ? AND customerid = ?',array($voip_group,$SESSION->id)))
    {
$login=ConfigHelper::getConfig('phpui.iqcst_login');
$password=ConfigHelper::getConfig('phpui.iqcst_password');
        $voip['show'] = 1;
        $voip_acc = $LMS->GetCustomerVoipAccounts($SESSION->id);
	unset($voip_acc['total']);
	unset($voip_acc['ownerid']);
	$service=ConfigHelper::getConfig('phpui.iqcst_wsdl');
	$client = new SoapClient($service, array("login" => $login,
							    "password" => $password,
							    "trace" => TRUE,
							    "exceptions" => 0,
							    "encoding" => "UTF-8",
							    'features' => SOAP_SINGLE_ELEMENT_ARRAYS
							    ));
	
	$abonent_structure = array("id"=>"",
	        		"nazwisko"=>"",
	                        "imie"=>"",
	                        "ulica"=>"",
	                        "nr_domu"=>"",
	                        "nr_mieszkania"=>"",
	                        "kod_pocztowy"=>"",
	                        "miasto"=>"",
	                        "p_haslo"=>"__nie_istotny__",
	                        "data_start"=>"__nie_istotny__",
	                        "data_stop"=>"__nie_istotny__",
	                        "status"=>"",
	                        "nip"=>"",
	                        "email"=>"",
	                        "uprawnienia"=>"",
	                        "login"=>$voip_acc['accounts'][0]['login'],
	                        "kod"=>$SESSION->id,
	                        "uwagi"=>"",
	                        "grupa_operatorska"=>"__nie_istotny__",
	                        "linie"=>array()
                                );
	$qr = $client->sFindAbonent($abonent_structure);
	$linie = $qr->AbonentStruct->linie;
	if(sizeof($linie) == 1)
	{
	    if (!$bil_cfg['number_id']) {
		$bil_cfg['number_id'] = $linie->id;
		$bil_cfg['number'] = $linie->numer;
	    }
	} 
	else {
	    if (!$bil_cfg['number_id']) {
		$bil_cfg['number_id'] = $linie[0]->id;
		$bil_cfg['number'] = $linie[0]->numer;
	    }
	}
	$abonent_id = $qr->AbonentStruct->id;
	$bil_data=array('month'=>$bil_cfg['month'],'year'=>$bil_cfg['year'],'abonent_id'=>$abonent_id,'phone_number'=>$bil_cfg['number']);
	$biling=$client->sGetBillingTable($bil_data);
	if(sizeof($biling->sBillingLine) == 1)
    	    $SMARTY->assign('biling',$biling);
    	else
    	    $SMARTY->assign('biling',$biling->sBillingLine);
        $SMARTY->assign('bil_cfg',$bil_cfg);
        $SMARTY->assign('linie',$linie);
        $SMARTY->assign('tariff',$tariff);
        $SMARTY->assign('curr_date',$curr_date);
        $SMARTY->assign('voip_acc',$voip_acc);
        $SMARTY->assign('voip',$voip);
    } else
        $voip['show'] = 0;

    $SMARTY->display('module:voip.html');
}

if (defined('USERPANEL_SETUPMODE'))
{
    function module_setup()
    {
        global $SMARTY,$LMS,$DB;
	$group = $LMS->CustomergroupGetList();
	unset($group['total']);
	unset($group['totalcount']);
	$voip_group = $DB->GetOne('SELECT value FROM uiconfig WHERE section = \'userpanel\' AND var = \'voip_group\'');
        $SMARTY->assign('group', $group);
        $SMARTY->assign('voip_group', $voip_group);
	$SMARTY->display('module:voip:setup.html');
    }

    function module_submit_setup()
    {
        global $SMARTY,$DB;
	$DB->Execute('UPDATE uiconfig SET value = ? WHERE section = \'userpanel\' AND var = \'voip_group\'',array($_POST['voip_group']));
        header('Location: ?m=userpanel&module=voip');
    }
}
?>
