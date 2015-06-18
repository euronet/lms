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

/*
 * LMSMacManager
 *
 * @author Bartosz Walendziuk <bartoszwalendziuk@gmail.com>
 */
class LMSMacManager extends LMSManager implements LMSMacManagerInterface
{
    /**
     * Returns mac from databases
     * 
     * @param string order
     * @return array Devices data
     */
  
	

	public function MacExists($id, $table)
	{
		switch($table)
		{
			case 0:
				return $this->db->GetOne('SELECT * FROM woj_macchange WHERE id = ?', array($id));
				break;
			case 2:
				return $this->db->GetOne('SELECT * FROM woj_macarch WHERE id = ?', array($id));
				break;
			case 1:
			default:
				return $this->db->GetOne('SELECT * FROM woj_macok WHERE id = ?', array($id));
		}
	}

	public function GetMac($id, $table)
	{
		switch($table)
		{
			case 0:
				return $this->db->GetRow('SELECT id, inet_ntoa(ip) AS ip, mac, port, vlan, lastonline FROM woj_macchange WHERE id = ?', array($id));
				break;
			case 2:
				return $this->db->GetRow('SELECT id, inet_ntoa(ip) AS ip, mac, port, vlan, lastonline FROM woj_macarch WHERE id = ?', array($id));
				break;
			case 1:
			default:
				return $this->db->GetRow('SELECT id, inet_ntoa(ip) AS ip, mac, port, vlan, lastonline FROM woj_macok WHERE id = ?', array($id));
		}
	}

	public function DeleteMac($id, $table)
	{
		switch($table)
		{
			case 0:
				return $this->db->Execute('DELETE FROM woj_macchange WHERE id = ?', array($id));
				break;
			case 2:
				return $this->db->Execute('DELETE FROM woj_macarch WHERE id = ?', array($id));
				break;
			case 1:
			default:
				return $this->db->Execute('DELETE FROM woj_macok WHERE id = ?', array($id));
		}
	}

	public function DeleteMacList($table=0, $search=NULL, $sqlskey='AND')
	{
		if(sizeof($search))
		{
			foreach($search as $key => $value)
			{
				$valoryg = trim($value);
				$value = str_replace(' ','%',trim($value));
				if($value!='')
				{
					$value2 = "'".$value."'";
					$value = "'%".$value."%'";
					switch($key)
					{
						case 'mac':
							$value = str_replace('-',':',$value);
							$searchargs[] = "(mac LIKE $value)";
						break;
						case 'ip':
							$searchargs[] = "(ip = inet_aton('$valoryg'))";
						break;
						case 'port':
							$searchargs[] = "(port = $value2)";
						break;
						case 'vlan':
							$searchargs[] = "(vlan = $value2)";
						break;
						case 'from':
							$valoryg = strtotime($valoryg);
							$searchargs[] = "(lastonline >= $valoryg)";
						break;
						case 'to':
							$valoryg = strtotime($valoryg);
							$searchargs[] = "(lastonline <= $valoryg)";
						break;
						default:
							$searchargs[] = "$key LIKE $value";
					}
				}
			}
		}
		
		if(isset($searchargs))
			$sqlsarg = implode(' '.$sqlskey.' ',$searchargs);

		switch($table)
		{
			case 0: 
				$sqltable = 'woj_macchange';
				break;
			case 2:
				$sqltable = 'woj_macarch';
				break;
			case 1:
			default:
				$sqltable = 'woj_macok';
		}
		
		$maclist = $this->db->Execute(
				'DELETE FROM '.$sqltable.' '
				.'WHERE 1=1 '
				.($sqlsarg !='' ? ' AND ('.$sqlsarg.')' :''));
		return $maclist;
	}

	public function UpdateMac($mac, $ip, $port, $vlan, $lastonline)
	{
		$this->db->Execute('DELETE FROM woj_macchange WHERE mac = ? AND ip = inet_aton(?)', array($mac, $ip));
		return $this->db->Execute('UPDATE woj_macok SET port = ?, vlan = ?, lastonline = ? WHERE mac = ? AND ip = inet_aton(?)', array($port, $vlan, strtotime($lastonline), $mac, $ip));
	}

	public function ArchMac($id, $table)
	{
		$maclist = $this->GetMac($id, $table);

		$this->db->Execute('INSERT INTO woj_macarch VALUES(NULL, inet_aton(?), ?, ?, ?, ?)', array($maclist['ip'],$maclist['mac'],$maclist['port'],$maclist['vlan'],strtotime($maclist['lastonline'])));

		switch($table)
		{
			case 0:
				return $this->db->Execute('DELETE FROM woj_macchange WHERE id = ?', array($id));
				break;
			case 2:
				break;
			case 1:
			default:
				return $this->db->Execute('DELETE FROM woj_macok WHERE id = ?', array($id));
		}
	}

	public function GetMacList($order='mac,asc',$table=0, $search=NULL, $sqlskey='AND', $details, $limit)
	{
		list($order,$direction) = sscanf($order, '%[^,],%s');
		($direction != 'desc') ? $direction = 'asc' : $direction = 'desc';

		switch($order)
		{
			case 'id':
				$sqlord = 'ORDER BY id';
			break;
			case 'ip':
				$sqlord = 'ORDER BY ip';
			break;
			case 'port':
				$sqlord = 'ORDER BY port';
			break;
			case 'vlan':
				$sqlord = 'ORDER BY vlan';
			break;
			case 'lastonline':
				$sqlord = 'ORDER BY lastonline';
			break;
			case 'compact':
				$sqlord = 'GROUP by mac ORDER BY lastonline';
			break;
			default:
				$sqlord = 'ORDER BY mac';
			break;
		}

		$over = 0; $below = 0; $sqlsarg = '';

		if(sizeof($search))
			foreach($search as $key => $value)
			{
				$valoryg = $value;
				$value = str_replace(' ','%',trim($value));
				if($value!='')
				{
					$value2 = "'".$value."'";
					$value = "'%".$value."%'";
					switch($key)
					{
						case 'mac':
							$value = str_replace('-',':',$value);
							$searchargs[] = "(mac LIKE $value)";
						break;
						case 'ip':
							$searchargs[] = "(ip = inet_aton('$valoryg'))";
						break;
						case 'port':
							$searchargs[] = "(port = $value2)";
						break;
						case 'vlan':
							$searchargs[] = "(vlan LIKE $value2)";
						break;
						case 'from':
							$valoryg = strtotime($valoryg);
							$searchargs[] = "(lastonline >= $valoryg)";
						break;
						case 'to':
							$valoryg = strtotime($valoryg);
							$searchargs[] = "(lastonline <= $valoryg)";
						break;
						default:
							$searchargs[] = "$key LIKE $value";
					}
				}
			}

		if(isset($searchargs))
			$sqlsarg = implode(' '.$sqlskey.' ',$searchargs);
		
		switch($table)
		{
			case 0: 
				$sqltable = 'woj_macchange';
				break;
			case 2:
				$sqltable = 'woj_macarch';
				break;
			case 1:
			default:
				$sqltable = 'woj_macok';
		}

		$all = $this->db->GetOne(
				'SELECT count(*) AS ile '
				.'FROM '.$sqltable.' '
				.'WHERE 1=1 '
				.($sqlsarg !='' ? ' AND ('.$sqlsarg.') ' :'')
				.'GROUP BY mac '
				.($sqlord !='' ? $sqlord.' '.$direction:''));
				
		$maclist = $this->db->GetAll(
				'SELECT id, inet_ntoa(ip) AS ip, mac, port, vlan, lastonline '
				.'FROM '.$sqltable.' '
				.'WHERE 1=1 '
				.($sqlsarg !='' ? ' AND ('.$sqlsarg.') ' :'')
				.($sqlord !='' ? $sqlord.' '.$direction:'')
				.($limit ? ' LIMIT '.$limit : ''));

		if(isset($details)&&sizeof($maclist))
		{
			foreach($maclist as $idx => $row)
			{
				$nodeid = $LMS->GetNodeIDByMAC($row['mac']);
				$node = $LMS->GetNode($nodeid);

				if( $node['ownerid'])
				{
					$customer = $LMS->GetCustomer($node['ownerid']);
					$maclist[$idx]['custnodeid'] = $customer['id'];
					$maclist[$idx]['custnodename'] = $customer['customername'];
					if($customer['phone3'])
						$maclist[$idx]['custnodeaddr'] = $customer['phone3'];
					else
						$maclist[$idx]['custnodeaddr'] = $customer['address'];
					$maclist[$idx]['custnodetype'] = 0;
				} else if( $node['netdev'])
				{
					$netdev = $LMS->GetNetDev($node['netdev']);
					$maclist[$idx]['custnodeid'] = $netdev['id'];
					$maclist[$idx]['custnodename'] = $netdev['name'].' ('.$netdev['producer'].')';
					$maclist[$idx]['custnodetype'] = 1;
				}
			}
		}

		$maclist['total'] = sizeof($maclist);
		$maclist['state'] = $state;
		$maclist['order'] = $order;
		$maclist['direction'] = $direction;
		$maclist['below']= $below;
		$maclist['over']= $over;
		$maclist['all'] = $all;

		return $maclist;
	}


    }


