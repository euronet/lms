<?php

/*
 * LMS version 1.11-git
 *
 *  (C) Copyright 2001-2013 LMS Developers
 *
 *  Please, see the doc/AUTHORS for more information about authors!
 *
 *  This program is free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License Version 2 as
 *  published by the Free Software Foundation.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program; if not, write to the Free Software
 *  Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA 02111-1307,
 *  USA.
 *
 *  $Id$
 */

if($layout['module'] != 'customeredit')
{
	$customerinfo = $LMS->GetCustomer($customerid);

    if(!$customerinfo)
    {
        $SESSION->redirect('?m=customerlist');
    }

	$SMARTY->assignByRef('customerinfo', $customerinfo);

}


$expired = !empty($_GET['expired']) ? true : false;
$assignments = $LMS->GetCustomerAssignments($customerid, !empty($expired) ? $expired : NULL);
$customergroups = $LMS->CustomergroupGetForCustomer($customerid);
$othercustomergroups = $LMS->GetGroupNamesWithoutCustomer($customerid);
$balancelist = $LMS->GetCustomerBalanceList($customerid);
$customervoipaccounts = $LMS->GetCustomerVoipAccounts($customerid);
$documents = $LMS->GetDocuments($customerid, 10);
$taxeslist = $LMS->GetTaxes();
$allnodegroups = $LMS->GetNodeGroupNames();
$messagelist = $LMS->GetMessages($customerid, 10);
$eventlist = $LMS->EventSearch(array('customerid' => $customerid), 'date,desc', true);
$customernodes = $LMS->GetCustomerNodes($customerid);
$othercustomerdevice = $LMS->GetDeviceListU();
$customerdevice = $LMS->GetDeviceListC($customerid);

$iptv_enable = 0;
foreach($customergroups as $line)
{
	if($line['name'] == ConfigHelper::getConfig('phpui.iptv_group'))
		$iptv_enable = 1;
}

if($iptv_enable) {
	$service=ConfigHelper::getConfig('phpui.iptv_wsdl');
	try {
		$client = @new SoapClient($service,array("exceptions" => 1));

		$param = array(
			"arg0" => ConfigHelper::getConfig('phpui.iptv_operator'),
			"arg1" => sprintf("%04s",$customerid));
		$result=$client->getSubscriber($param);
		$iptv_subscriber['name'] = $result->return->data->subscriberSurname;
		$iptv_subscriber['surname'] = $result->return->data->subscriberName;

		$param = array(
			"arg0" => ConfigHelper::getConfig('phpui.iptv_operator'),
			"arg1" => sprintf("%04s",$customerid));
		$result=$client->getDevicesForSubscriberUid($param);
		if(sizeof($result->return->data)>1) {
			$tab = 0;
			foreach($result->return->data as $line)
			{
				$iptv_device[$tab]['type'] = $line->deviceTypeDesc;
				$iptv_device[$tab]['mac'] = $line->deviceUid;
				$iptv_device[$tab]['display'] = $line->displayTypeDesc;
				$iptv_device[$tab]['ratio'] = $line->videoTypeDesc;
				$tab++;
			}
		} elseif(sizeof($result->return->data)==1)
		{
			$iptv_device[0]['type'] = $result->return->data->deviceTypeDesc;
			$iptv_device[0]['mac'] = $result->return->data->deviceUid;
			$iptv_device[0]['display'] = $result->return->data->displayTypeDesc;
			$iptv_device[0]['ratio'] = $result->return->data->videoTypeDesc;
			$tab = 1;
		} else
			$iptv_device = null;
		$iptv_device_total = $tab;

		$param = array(
			"arg0" => ConfigHelper::getConfig('phpui.iptv_operator'),
			"arg1" => sprintf("%04s",$customerid));
		$result=$client->getAllMarketingPackageUidsForSubscriber($param);
		$iptv_package_total = sizeof($result->return->data);
		$pakiety = $result->return->data;
		$pakiety = ( !is_array($pakiety) ) ? array_fill(0,1,$pakiety) : $pakiety;
		$tablica = array();
		$tablica[1] = "Pakiet Podstawowy";
		$tablica[2] = "Euronet";
		$tablica[3] = "Pakiet Socjalny";
		$tablica[4] = "Pakiet Canal+";
		$tablica[5] = "Pakiet Canal+ HD";
		$tablica[6] = "Pakiet Mini";
		$tablica[7] = "Pakiet Canal+ HD/3D";
		$tablica[8] = "Pakiet Komfort HD";
		$tablica[9] = "Pakiet Optima HD";
		$tablica = array_flip($tablica);
		$iptv_package = array_flip(array_intersect($tablica, $pakiety));

		$param = array(
			"arg0" => ConfigHelper::getConfig('phpui.iptv_operator'),
			"arg1" => sprintf("%04s",$customerid));
		$result=$client->getSubscriberServices($param);
		$tab = 0;
		if(sizeof($result->return->data)>1) {
			foreach($result->return->data as $line)
			{
				$name = $line->serviceName;
				$from = $line->subscriberServiceActivated;
				$to = $line->subscriberServiceDeactivated;
				$from = str_replace("T"," ",$from);
				$to = str_replace("T"," ",$to);
				$tmp = explode("+",$from);
				$from = $tmp[0];
				$tmp = explode(".",$from);
				$from = $tmp[0];
				$tmp = explode("+",$to);
				$to = $tmp[0];
				$tmp = explode(".",$to);
				$to = $tmp[0];

				if(strcmp($from,$to))
				{
					$iptv_service[$tab]['name'] = $name;
					$iptv_service[$tab]['from'] = $from;
					$iptv_service[$tab]['to'] = $to;
					$tab++;
				}
			}
		} else
		{
			$name = $line->serviceName;
			$from = $line->subscriberServiceActivated;
			$to = $line->subscriberServiceDeactivated;
			$from = str_replace("T"," ",$from);
			$to = str_replace("T"," ",$to);
			$tmp = explode("+",$from);
			$from = $tmp[0];
			$tmp = explode(".",$from);
			$from = $tmp[0];
			$tmp = explode("+",$to);
			$to = $tmp[0];
			$tmp = explode(".",$to);
			$to = $tmp[0];

			if(strcmp($from,$to))
			{
				$iptv_service[0]['name'] = $name;
				$iptv_service[0]['from'] = $from;
				$iptv_service[0]['to'] = $to;
				$tab = 1;
			}
		}
		$iptv_service_total = $tab;
	} catch (SoapFault $E) {
		echo $E->faultstring;
	}
}


if ($SYSLOG && (ConfigHelper::checkConfig('privileges.superuser') || ConfigHelper::checkConfig('privileges.transaction_logs'))) {
	$trans = $SYSLOG->GetTransactions(array('key' => $SYSLOG_RESOURCE_KEYS[SYSLOG_RES_CUST], 'value' => $customerid));
	if (!empty($trans))
		foreach ($trans as $idx => $tran)
			$SYSLOG->DecodeTransaction($trans[$idx]);
	$SMARTY->assign('transactions', $trans);
	$SMARTY->assign('resourcetype', SYSLOG_RES_CUST);
	$SMARTY->assign('resourceid', $customerid);
}

if(!empty($documents))
{
        $SMARTY->assign('docrights', $DB->GetAllByKey('SELECT doctype, rights
	        FROM docrights WHERE userid = ? AND rights > 1', 'doctype', array($AUTH->id)));
}

$SMARTY->assign(array(
	'expired' => $expired, 
	'time' => $SESSION->get('addbt'),
	'value' => $SESSION->get('addbv'),
	'taxid' => $SESSION->get('addbtax'),
	'comment' => $SESSION->get('addbc'),
	'sourceid' => $SESSION->get('addsource'),
));

// zmiany bartek
$new = 0;
$open = 0;
$resolved = 0;
$all = 0;
 $sth    = $DB->GetAll('SELECT * FROM rttickets WHERE customerid=?', array($customerid));
foreach ($sth as $idx => $row) {
				
				// summary
				if ($row['state'] == 0)
					$new=$new + 1;
				elseif ($row['state'] == 1)
					$open = $open + 1;
				elseif ($row['state'] == 2)
					$resolved = $resolved + 1;
		
			}
$all=count($sth);
 $SMARTY->assignByRef('new', $new);
 $SMARTY->assignByRef('open', $open);
 $SMARTY->assignByRef('resolved', $resolved);
 $SMARTY->assignByRef('all', $all);


// zmiany na karcie komputerow

foreach ($customernodes as $key => $val)
{
$sth= $DB->GetOne('SELECT ipaddr FROM nodes WHERE ownerid = 0 AND netdev=?', array($val['netid']));

$customernodes[$key]['sw']=long2ip($sth);

		}
$grupy = $DB->GetAll('SELECT * from customergroups where id not in
(SELECT customergroupid from customerassignments where customerid = ?) order by name', array($customerid));
$SMARTY->assignByRef('grupy', $grupy);
$SMARTY->assignByRef('customernodes', $customernodes);
$SMARTY->assignByRef('customerdevice', $customerdevice);
$SMARTY->assignByRef('othercustomerdevice', $othercustomerdevice);
$SMARTY->assign('sourcelist', $DB->GetAll('SELECT id, name FROM cashsources ORDER BY name'));
$SMARTY->assignByRef('customernodes', $customernodes);
$SMARTY->assignByRef('assignments', $assignments);
$SMARTY->assignByRef('customergroups', $customergroups);
$SMARTY->assignByRef('othercustomergroups', $othercustomergroups);
$SMARTY->assignByRef('balancelist', $balancelist);
$SMARTY->assignByRef('customervoipaccounts', $customervoipaccounts);
$SMARTY->assignByRef('documents', $documents);
$SMARTY->assignByRef('taxeslist', $taxeslist);
$SMARTY->assignByRef('allnodegroups', $allnodegroups);
$SMARTY->assignByRef('messagelist', $messagelist);
$SMARTY->assignByRef('eventlist', $eventlist);
$SMARTY->assignByRef('iptv', $iptv_enable);
$SMARTY->assignByRef('iptv_subscriber', $iptv_subscriber);
$SMARTY->assignByRef('iptv_device', $iptv_device);
$SMARTY->assignByRef('iptv_device_total', $iptv_device_total);
$SMARTY->assignByRef('iptv_package', $iptv_package);
$SMARTY->assignByRef('iptv_package_total', $iptv_package_total);
$SMARTY->assignByRef('iptv_service', $iptv_service);
$SMARTY->assignByRef('iptv_service_total', $iptv_service_total);

?>
