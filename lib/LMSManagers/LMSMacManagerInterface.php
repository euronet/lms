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
 * LMSMessageManagerInterface
 * 
 * @author Maciej Lew <maciej.lew.1987@gmail.com>
 */
interface LMSMacManagerInterface
{
   public function MacExists($id, $table);
   public function GetMac($id, $table);
   public function DeleteMac($id, $table);
   public function DeleteMacList($table=0, $search=NULL, $sqlskey='AND');
   public function UpdateMac($mac, $ip, $port, $vlan, $lastonline);
   public function ArchMac($id, $table);
   public function GetMacList($order='mac,asc',$table=0, $search=NULL, $sqlskey='AND', $details, $limit);
}
