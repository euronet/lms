<?php

/*
 * LMS version 1.11-git
 *
 *  (C) Copyright 2001-2012 LMS Developers
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



$layout['pagetitle'] = 'STB information';


$ip = $_GET['ip'];

  if (!function_exists("ssh2_connect")) die("function ssh2_connect doesn't exist");
// log in at server1.example.com on port 22
if(!($con = ssh2_connect($ip, 22))){
    $error =  "fail: unable to establish connection<br>";
} else {
    // try to authenticate with username root, password secretpassword
    if(!ssh2_auth_password($con, "root", "satlan")) {
        $error =  "fail: unable to authenticate<br>";
    } else {
        // allright, we're in!
        $error =  "okay: logged in...<br>";

        // execute a command
          if (!($stream_one = ssh2_exec($con, "uptime" ))) {
           $error =  "fail: unable to execute command<br>";
        } else {
            // collect returning data from command
            stream_set_blocking($stream_one, true);
            $data_one = "";
            while ($buf = fgets($stream_one,4096)) {
                $data_one .= $buf . '<br>';
            }
            fclose($stream_one);

           
        }
        
        if (!($stream_two = ssh2_exec($con, "toish getstatus" ))) {
            $error =  "fail: unable to execute command<br>";
        } else {
            // collect returning data from command
            stream_set_blocking($stream_two, true);
            $data_two = "";
            while ($buf = fgets($stream_two,4096)) {
                $data_two .= $buf . '<br>';
            }
            fclose($stream_two);

          
        }
        
         if (!($stream_three = ssh2_exec($con, "toish getobject all" ))) {
            $error =  "fail: unable to execute command<br>";
        } else {
            // collect returning data from command
            stream_set_blocking($stream_three, true);
            $data_three = "";
            while ($buf = fgets($stream_three,4096)) {
                          $data_three .= $buf;
            }
            fclose($stream_three);

        $new = htmlspecialchars("$data_three", ENT_QUOTES);
//$new = nl2br($new);


$newa = substr($new, 0, strpos($new, "const.sw.date:"));
$newaa = explode("\n", $newa);
$ile=count($newaa) -2 ;


    }
     if (!($stream_four = ssh2_exec($con, "exit" ))) {
            $error =  "fail: unable to execute command<br>";
	}
else {
            // collect returning data from command
            stream_set_blocking($stream_four, true);
                      fclose($stream_four);


}
}

}

$SMARTY->assign('err', $error);
$SMARTY->assign('one', $data_one);
$SMARTY->assign('two', $data_two);
$SMARTY->assign('three', $newaa[$ile]);
$SMARTY->display('stbinfo.html');

?>
