<?php

/*
 *  LMS version 1.11-git
 *
 *  Copyright (C) 2001-2013 LMS Developers
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

/**
 * LMSDevicesManager
 *
 * @author Bartosz Walendziuk <bartoszwalendziuk@gmail.com>
 */
class LMSDevicesManager extends LMSManager implements LMSDevicesManagerInterface
{
    /**
     * Returns customers devices from databases
     * 
     * @param string order
     * @return array Devices data
     */
   public function GetDeviceListS($order, $search, $sqlskey)
	{
		if($order=='')
			$order='model,asc';

		list($order,$direction) = sscanf($order, '%[^,],%s');

		($direction=='desc') ? $direction = 'desc' : $direction = 'asc';

		switch($order)
		{
			case 'id':
				$sqlord = ' ORDER BY id';
			break;
			case 'vendor':
				$sqlord = ' ORDER BY vendor';
			break;
			case 'buydate':
				$sqlord = ' ORDER BY buydate';
			break;
			case 'price':
				$sqlord = ' ORDER BY price';
			break;
			case 'serialnumber':
				$sqlord = ' ORDER BY serialnumber';
			break;
			case 'customername':
				$sqlord = ' ORDER BY customername';
			break;
			default:
				$sqlord = ' ORDER BY model';
			break;
		}
		if(sizeof($search))
			foreach($search as $idx => $value)
			{
				if($value!='')
				{
					$searchargs[] = "UPPER(model) ?LIKE? UPPER('%".$value."%') OR UPPER(serialnumber) ?LIKE? UPPER('%".$value."%') OR UPPER(vendor) ?LIKE? UPPER('%".$value."%')";
				}
			}

		if($searchargs)
			$searchargs = ' AND ('.implode(' '.$sqlskey.' ',$searchargs).')';

		if($devicelist = $this->db->GetAll('SELECT woj_device.id as id, model, serialnumber, buydate, vendor, price, customerid, '
			.$this->db->Concat('lastname',"' '",'name').' as customername '
			.'FROM woj_device LEFT JOIN customers ON customerid=customers.id WHERE 1=1 '
			.$searchargs
			.($sqlord != '' ? $sqlord.' '.$direction : '')))

		$devicelist['total'] = sizeof($devicelist);
		$devicelist['order'] = $order;
		$devicelist['direction'] = $direction;

		return $devicelist;
	}

	 public function GetDeviceList($order)
	{
		list($order,$direction) = sscanf($order, '%[^,],%s');

		($direction=='desc') ? $direction = 'desc' : $direction = 'asc';

		switch($order)
		{
			case 'id':
				$sqlord = ' ORDER BY id';
			break;
			case 'vendor':
				$sqlord = ' ORDER BY vendor';
			break;
			case 'buydate':
				$sqlord = ' ORDER BY buydate';
			break;
			case 'price':
				$sqlord = ' ORDER BY price';
			break;
			case 'serialnumber':
				$sqlord = ' ORDER BY serialnumber';
			break;
			case 'customername':
				$sqlord = ' ORDER BY customername';
			break;
			default:
				$sqlord = ' ORDER BY model';
			break;
		}

		if($devicelist = $this->db->GetAll('SELECT woj_device.id as id, model, serialnumber, buydate, vendor, price, customerid, '
			.$this->db->Concat('lastname',"' '",'name').' as customername '
			.'FROM woj_device LEFT JOIN customers ON customerid=customers.id '.($sqlord != '' ? $sqlord.' '.$direction : '')))

		$devicelist['total'] = sizeof($devicelist);
		$devicelist['order'] = $order;
		$devicelist['direction'] = $direction;
		return $devicelist;
	}

	 public function GetDeviceListU()
	{
		if($devicelist = $this->db->GetAll('SELECT id, model, serialnumber, buydate, vendor, price, customerid FROM woj_device WHERE customerid=0 OR customerid is NULL ORDER BY model'));
		return $devicelist;
	}

	 public function GetDeviceListC($id)
	{
		if($devicelist = $this->db->GetAll('SELECT id, model, serialnumber, buydate, vendor, price FROM woj_device WHERE customerid=? ORDER BY model', array($id)));
		return $devicelist;
	}

	 public function CustomerDeviceAdd($id,$deviceid)
	{
		$this->db->Execute('UPDATE woj_device SET customerid=? WHERE id=?', array($id, $deviceid) );
	}

	 public function CustomerDeviceDelete($deviceid)
	{
		$this->db->Execute('UPDATE woj_device SET customerid=0 WHERE id=?', array($deviceid) );
	}

	 public function DeviceUpdate($devicedata)
	{
		$this->db->Execute('UPDATE woj_device SET model=?, serialnumber=?, buydate=?, vendor=?, price=?, info=?, customerid=? WHERE id=?', array( $devicedata['model'], $devicedata['serialnumber'], $devicedata['buydate'], $devicedata['vendor'], $devicedata['price'], $devicedata['info'], $devicedata['customerid'], $devicedata['id'] ) );
	}

	 public function GetDevice($id)
	{
		$result = $this->db->GetRow('SELECT id, model, serialnumber, buydate, vendor, price, info, customerid FROM woj_device WHERE id=?', array($id));
		return $result;
	}

	 public function GetDeviceBySN($serialnumber)
	{
		$result = $this->db->GetRow('SELECT id, model, serialnumber, buydate, vendor, price, info, customerid FROM woj_device WHERE UPPER(serialnumber)=?', array(strtoupper($serialnumber)));
		return $result;
	}

	 public function GetModels()
	{
		$result = $this->db->GetAll('SELECT max(id) AS id, model FROM woj_device GROUP BY model ORDER BY model');
		return $result;
	}

	 public function GetModel($id)
	{
		$result = $this->db->GetRow('SELECT model FROM woj_device WHERE id=?', array($id));
		return $result;
	}

	 public function GetVendors()
	{
		$result = $this->db->GetAll('SELECT max(id) AS id, vendor FROM woj_device GROUP BY vendor ORDER BY vendor');
		return $result;
	}

	 public function GetVendor($id)
	{
		$result = $this->db->GetRow('SELECT vendor FROM woj_device WHERE id=?', array($id));
		return $result;
	}

	 public function DeviceAdd($devicedata)
	{
		if($this->db->Execute('INSERT INTO woj_device (model, serialnumber, buydate, vendor, price, info, customerid ) VALUES (?, ?, ?, ?, ?, ?, ?)', array($devicedata['model'],$devicedata['serialnumber'],$devicedata['buydate'],$devicedata['vendor'],$devicedata['price'],$devicedata['info'],$devicedata['customerid'])))
//			return $this->db->GetLastInsertID('woj_device');
			return $this->db->GetOne('SELECT MAX(id) FROM woj_device');
		else
			return FALSE;
	}

	 public function DeviceExists($id)
	{
		return ($this->db->GetOne('SELECT * FROM woj_device WHERE id=?', array($id)) ? TRUE : FALSE);
	}

	 public function DeleteDevice($id)
	{
		return $this->db->Execute('DELETE FROM woj_device WHERE id=?', array($id));
	}
public function GetDeviceListB($order, $search, $sqlskey) {
		if ($order == '')
			$order = 'model,asc';

		list($order, $direction) = sscanf($order, '%[^,],%s');

		($direction == 'desc') ? $direction = 'desc' : $direction = 'asc';

		switch ($order) {
			case 'id':
				$sqlord = ' ORDER BY id';
			break;
			case 'vendor':
				$sqlord = ' ORDER BY vendor';
			break;
			case 'buydate':
				$sqlord = ' ORDER BY buydate';
			break;
			case 'price':
				$sqlord = ' ORDER BY price';
			break;
			case 'serialnumber':
				$sqlord = ' ORDER BY serialnumber';
			break;
			case 'customername':
				$sqlord = ' ORDER BY customername';
			break;
			default:
				$sqlord = ' ORDER BY model';
			break;
		}

		if (sizeof($search))
			foreach ($search as $idx => $value) {
				if ($value != '') {
					switch ($idx) {
						case 'name' :
							$searchargs[] = 'model ?LIKE? ' . $this->db->Escape("%$value%");
							break;
						case 'serialnumber' :
							$searchargs[] = 'serialnumber ?LIKE? ' . $this->db->Escape("%$value%");
							break;
						case 'vendor' :
							$searchargs[] = 'vendor ?LIKE? ' . $this->db->Escape("%$value%");
							break;
						case 'info' :
							$searchargs[] = 'info ?LIKE? ' . $this->db->Escape("%$value%");
							break;
						default :
							$searchargs[] = $idx . ' ?LIKE? ' . $this->db->Escape("%$value%");
					}
				}
			}
$sqlskey='AND';
		if (isset($searchargs))
			$searchargs = ' WHERE ' . implode(' ' . $sqlskey . ' ', $searchargs);

		$voipaccountlist =
				$this->db->GetAll('SELECT id, model, vendor, serialnumber, customerid, info, buydate
				FROM woj_device'
				. (isset($searchargs) ? $searchargs : '')
				. ($sqlord != '' ? $sqlord . ' ' . $direction : ''));

		$voipaccountlist['total'] = sizeof($voipaccountlist);
		$voipaccountlist['order'] = $order;
		$voipaccountlist['direction'] = $direction;

		return $voipaccountlist;
	}


    }


