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
		$consideredFields = array(
			'firstName' => false,
			'lastName' => false,
			'street' => false,
			'house' => false,
			'apartment' => false,
			'postalCode' => false,
			'city' => false,
			'portalPassword' => false,
			'NIP' => false,
			'email' => false,
			'hasPortal' => false,
			'portalLogin' => false,
			'externalId' => true,
			'comments' => false,
			'privateDataRestricted' => false
		);
		$searchValues = array(
			'firstName' => '',
			'lastName' => '',
			'street' => '',
			'house' => '',
			'apartment' => '',
			'postalCode' => '',
			'city' => '',
			'portalPassword' => '',
			'NIP' => '',
			'email' => '',
			'hasPortal' => '',
			'portalLogin' => '',
			'externalId' => $SESSION->id,
			'comments' => '',
			'privateDataRestricted' => ''
		);
		$result = $client->simpleFindSubscriber($consideredFields, $searchValues);
		$abonent_id = $result->i[0];
		$nu_id = 1 ;
		foreach ($voip_acc['accounts']['phone'] as $numer) {
			$bil_cfg['number_id'] = $nu_id;
			$bil_cfg['number'] = $numer;
		}
		$numer = $bil_cfg['number'];
		$biling = $client->getBillingTable($abonent_id,$numer,(int)$bil_cfg['year'],(int)$bil_cfg['month']);
		if(sizeof($biling->BillingLine) == 1)
			$SMARTY->assign('biling',$biling);
		else
			$SMARTY->assign('biling',$biling->billingRecord);
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
