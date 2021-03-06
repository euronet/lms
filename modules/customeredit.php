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
$action = isset($_GET['action']) ? $_GET['action'] : '';
$exists = $LMS->CustomerExists($_GET['id']);

if($exists < 0 && $action != 'recover')
{
	$SESSION->redirect('?m=customerinfo&id='.$_GET['id']);
}
elseif(!$exists)
{
	$SESSION->redirect('?m=customerlist');
}
elseif (isset($_POST['customerdata']))
{
	$customerdata = $_POST['customerdata'];
	foreach($customerdata as $key=>$value)
		if($key != 'uid' && $key != 'contacts')
			$customerdata[$key] = trim($value);

	if($customerdata['lastname'] == '')
		$error['lastname'] = trans('Last/Company name cannot be empty!');

    if($customerdata['name'] == '' && !$customerdata['type'])
        $error['name'] = trans('First name cannot be empty!');

	if($customerdata['address']=='')
		$error['address'] = trans('Address required!');

	if($customerdata['ten'] !='' && !check_ten($customerdata['ten']) && !isset($customerdata['tenwarning']))
	{
		$error['ten'] = trans('Incorrect Tax Exempt Number! If you are sure you want to accept it, then click "Submit" again.');
		$tenwarning = 1;
	}

	if($customerdata['ssn'] != '' && !check_ssn($customerdata['ssn']) && !isset($customerdata['ssnwarning']))
	{
		$error['ssn'] = trans('Incorrect Social Security Number! If you are sure you want to accept it, then click "Submit" again.');
		$ssnwarning = 1;
	}

	if($customerdata['regon'] != '' && !check_regon($customerdata['regon']))
		$error['regon'] = trans('Incorrect Business Registration Number!');

	if($customerdata['icn'] != '' && !check_icn($customerdata['icn']))
		$error['icn'] = trans('Incorrect Identity Card Number!');

	if($customerdata['zip'] !='' && !check_zip($customerdata['zip']) && !isset($customerdata['zipwarning']))
	{
		$error['zip'] = trans('Incorrect ZIP code! If you are sure you want to accept it, then click "Submit" again.');
		$zipwarning = 1;
	}
	if($customerdata['post_zip'] !='' && !check_zip($customerdata['post_zip']) && !isset($customerdata['post_zipwarning']))
	{
		$error['post_zip'] = trans('Incorrect ZIP code! If you are sure you want to accept it, then click "Submit" again.');
		$post_zipwarning = 1;
	}

	if($customerdata['email']!='' && !check_emails($customerdata['email']))
		$error['email'] = trans('Incorrect email!');

	if($customerdata['pin'] == '')
		$error['pin'] = trans('PIN code is required!');
	elseif(!preg_match('/^[0-9]{4,6}$/',$customerdata['pin']))
		$error['pin'] = trans('Incorrect PIN code!');

	if($customerdata['status'] == 1 && $LMS->GetCustomerNodesNo($customerdata['id'])) 
		$error['status'] = trans('Interested customers can\'t have computers!');

	foreach($customerdata['uid'] as $idx => $val)
	{
		$val = trim($val);
		switch($idx)
		{
			case IM_GG:
				if($val!='' && !check_gg($val))
					$error['gg'] = trans('Incorrect IM uin!');
			break;
			case IM_YAHOO:
				if($val!='' && !check_yahoo($val))
					$error['yahoo'] = trans('Incorrect IM uin!');
			break;
			case IM_SKYPE:
				if($val!='' && !check_skype($val))
					$error['skype'] = trans('Incorrect IM uin!');
			break;
		}

		if($val) $im[$idx] = $val;
	}

	foreach($customerdata['contacts'] as $idx => $val)
    {
	        $phone = trim($val['phone']);
	        $name = trim($val['name']);
            $type = !empty($val['type']) ? array_sum($val['type']) : NULL;

            $customerdata['contacts'][$idx]['type'] = $type;

	        if($name && !$phone)
	                $error['contact'.$idx] = trans('Phone number is required!');
	        elseif($phone)
	                $contacts[] = array('name' => $name, 'phone' => $phone, 'type' => $type);
	}

	if ($customerdata['cutoffstop'] == '')
		$cutoffstop = 0;
	elseif (check_date($customerdata['cutoffstop'])) {
		list ($y, $m, $d) = explode('/', $customerdata['cutoffstop']);
		if (checkdate($m, $d, $y))
			$cutoffstop = mktime(23, 59, 59, $m, $d, $y);
		else
			$error['cutoffstop'] = trans('Incorrect date of cutoff suspending!');
	} else
		$error['cutoffstop'] = trans('Incorrect date of cutoff suspending!');

        $hook_data = $LMS->executeHook(
            'customeredit_validation_before_submit', 
            array(
                'customerdata' => $customerdata,
                'error' => $error
            )
        );
        $customerdata = $hook_data['customerdata'];
        $error = $hook_data['error'];
        
	if(!$error) {
		$customerdata['cutoffstop'] = $cutoffstop;

		if(!isset($customerdata['consentdate']))
			$customerdata['consentdate'] = 0;
		else {
			$consent = $DB->GetOne('SELECT consentdate FROM customers WHERE id = ?', array($customerdata['id']));
			if ($consent)
				$customerdata['consentdate'] = $consent;
		}

		if(!isset($customerdata['divisionid']))
			$customerdata['divisionid'] = 0;

		$LMS->CustomerUpdate($customerdata);

                $hook_data = $LMS->executeHook(
                    'customeredit_after_submit', 
                    array(
                        'customerdata' => $customerdata,
                    )
                );
                $customeradd = $hook_data['customeradd'];
                $id = $hook_data['id'];
                
		if ($SYSLOG) {
			$imids = $DB->GetCol('SELECT id FROM imessengers WHERE customerid = ?', array($customerdata['id']));
			if (!empty($imids))
				foreach ($imids as $imid) {
					$args = array(
						$SYSLOG_RESOURCE_KEYS[SYSLOG_RES_IMCONTACT] => $imid,
						$SYSLOG_RESOURCE_KEYS[SYSLOG_RES_CUST] => $customerdata['id']
					);
					$SYSLOG->AddMessage(SYSLOG_RES_IMCONTACT, SYSLOG_OPER_DELETE, $args, array_keys($args));
				}
		}
		$DB->Execute('DELETE FROM imessengers WHERE customerid = ?', array($customerdata['id']));
		if(isset($im))
			foreach($im as $idx => $val) {
				$DB->Execute('INSERT INTO imessengers (customerid, uid, type)
					VALUES(?, ?, ?)', array($customerdata['id'], $val, $idx));
				if ($SYSLOG) {
					$imid = $DB->GetLastInsertID('imessengers');
					$args = array(
						$SYSLOG_RESOURCE_KEYS[SYSLOG_RES_IMCONTACT] => $imid,
						$SYSLOG_RESOURCE_KEYS[SYSLOG_RES_CUST] => $customerdata['id'],
						'uid' => $val,
						'type' => $idx
					);
					$SYSLOG->AddMessage(SYSLOG_RES_IMCONTACT, SYSLOG_OPER_ADD, $args,
						array($SYSLOG_RESOURCE_KEYS[SYSLOG_RES_IMCONTACT],
							$SYSLOG_RESOURCE_KEYS[SYSLOG_RES_CUST]));
				}
			}

		if ($SYSLOG) {
			$contactids = $DB->GetCol('SELECT id FROM customercontacts WHERE customerid = ?', array($customerdata['id']));
			if (!empty($contactids))
				foreach ($contactids as $contactid) {
					$args = array(
						$SYSLOG_RESOURCE_KEYS[SYSLOG_RES_CUSTCONTACT] => $contactid,
						$SYSLOG_RESOURCE_KEYS[SYSLOG_RES_CUST] => $customerdata['id']
					);
					$SYSLOG->AddMessage(SYSLOG_RES_CUSTCONTACT, SYSLOG_OPER_DELETE, $args, array_keys($args));
				}
		}
		$DB->Execute('DELETE FROM customercontacts WHERE customerid = ?', array($customerdata['id']));
		if(isset($contacts))
			foreach($contacts as $contact) {
				$DB->Execute('INSERT INTO customercontacts (customerid, phone, name, type)
					VALUES(?, ?, ?, ?)', array($customerdata['id'], $contact['phone'], $contact['name'], $contact['type']));
				if ($SYSLOG) {
					$contactid = $DB->GetLastInsertID('customercontacts');
					$args = array(
						$SYSLOG_RESOURCE_KEYS[SYSLOG_RES_CUSTCONTACT] => $contactid,
						$SYSLOG_RESOURCE_KEYS[SYSLOG_RES_CUST] => $customerdata['id'],
						'phone' => $contact['phone'],
						'name' => $contact['name'],
						'type' => $contact['type']
					);
					$SYSLOG->AddMessage(SYSLOG_RES_CUSTCONTACT, SYSLOG_OPER_ADD, $args,
						array($SYSLOG_RESOURCE_KEYS[SYSLOG_RES_CUSTCONTACT],
							$SYSLOG_RESOURCE_KEYS[SYSLOG_RES_CUST]));
				}
			}

		$SESSION->redirect('?m=customerinfo&id='.$customerdata['id']);
	}
	else
	{
		$olddata = $LMS->GetCustomer($_GET['id']);

		$customerinfo = $customerdata;
		$customerinfo['createdby'] = $olddata['createdby'];
		$customerinfo['modifiedby'] = $olddata['modifiedby'];
		$customerinfo['creationdateh'] = $olddata['creationdateh'];
		$customerinfo['moddateh'] = $olddata['moddateh'];
		$customerinfo['customername'] = $olddata['customername'];
		$customerinfo['balance'] = $olddata['balance'];
		$customerinfo['stateid'] = isset($olddata['stateid']) ? $olddata['stateid'] : 0;
		$customerinfo['post_stateid'] = isset($olddata['post_stateid']) ? $olddata['post_stateid'] : 0;
		$customerinfo['zipwarning'] = empty($zipwarning) ? 0 : 1;
		$customerinfo['post_zipwarning'] = empty($post_zipwarning) ? 0 : 1;
		$customerinfo['tenwarning'] = empty($tenwarning) ? 0 : 1;
		$customerinfo['ssnwarning'] = empty($ssnwarning) ? 0 : 1;

		$SMARTY->assign('error',$error);
	}
}
else
{
	$customerinfo = $LMS->GetCustomer($_GET['id']);

	if ($customerinfo['cutoffstop'])
		$customerinfo['cutoffstop'] = strftime('%Y/%m/%d', $customerinfo['cutoffstop']);
	else
		$customerinfo['cutoffstop'] = 0;

	if($customerinfo['messengers'])
		foreach($customerinfo['messengers'] as $idx => $val)
			$customerinfo['uid'][$idx] = $val['uid'];

	if (empty($customerinfo['contacts']))
		$customerinfo['contacts'][] = array();
}

$layout['pagetitle'] = trans('Customer Edit: $a',$customerinfo['customername']);

$SESSION->save('backto', $_SERVER['QUERY_STRING']);

$customerid = $customerinfo['id'];
include(MODULES_DIR.'/customer.inc.php');
include(MODULES_DIR.'/gpononu.inc.php');

$hook_data = $LMS->executeHook(
    'customeredit_before_display', 
    array(
        'customerinfo' => $customerinfo,
        'smarty' => $SMARTY
    )
);
$customerinfo = $hook_data['customerinfo'];

$SMARTY->assign('customerinfo',$customerinfo);
$SMARTY->assign('cstateslist',$LMS->GetCountryStates());
$SMARTY->assign('countrieslist',$LMS->GetCountries());
$SMARTY->assign('divisions', $DB->GetAll('SELECT id, shortname, status FROM divisions ORDER BY shortname'));
$SMARTY->assign('recover',($action == 'recover' ? 1 : 0));
$SMARTY->display('customer/customeredit.html');

?>
