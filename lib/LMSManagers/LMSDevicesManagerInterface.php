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
 *
 * @author Bartosz Walendziuk <bartoszwalendziuk@gmail.com>
 */
interface LMSDevicesManagerInterface {

    public function GetDeviceListS($order, $search, $sqlskey);

    public function GetDeviceList($order);

    public function GetDeviceListU();

    public function GetDeviceListC($id);

    public function CustomerDeviceAdd($id, $deviceid);

    public function CustomerDeviceDelete($deviceid);

    public function DeviceUpdate($devicedata);

    public function GetDevice($id);

    public function GetDeviceBySN($serialnumber);

    public function GetModels();

    public function GetModel($id);

    public function GetVendors();

    public function GetVendor($id);

    public function DeviceAdd($devicedata);

    public function DeviceExists($id);

    public function DeleteDevice($id);

    public function GetDeviceListB($order, $search, $sqlskey);
}
