<?php
$CONFIG_FILE = '/etc/lms/lms.ini';

define('START_TIME', microtime());
define('LMS-UI', true);
ini_set('error_reporting', E_ALL&~E_NOTICE);

if(is_readable('lms.ini'))
        $CONFIG_FILE = 'lms.ini';
elseif(is_readable('/etc/lms/lms-'.$_SERVER['HTTP_HOST'].'.ini'))
        $CONFIG_FILE = '/etc/lms/lms-'.$_SERVER['HTTP_HOST'].'.ini';
elseif(!is_readable($CONFIG_FILE))
        die('Unable to read configuration file ['.$CONFIG_FILE.']!');
$CONFIG = (array) parse_ini_file($CONFIG_FILE, true);

$CONFIG['directories']['sys_dir'] = (!isset($CONFIG['directories']['sys_dir']) ? getcwd() : $CONFIG['directories']['sys_dir']);
$CONFIG['directories']['lib_dir'] = (!isset($CONFIG['directories']['lib_dir']) ? $CONFIG['directories']['sys_dir'].'/lib' : $CONFIG['directories']['lib_dir']);

define('SYS_DIR', $CONFIG['directories']['sys_dir']);
define('LIB_DIR', $CONFIG['directories']['lib_dir']);

$_DBTYPE = $CONFIG['database']['type'];
$_DBHOST = $CONFIG['database']['host'];
$_DBUSER = $CONFIG['database']['user'];
$_DBPASS = $CONFIG['database']['password'];
$_DBNAME = $CONFIG['database']['database'];

require(LIB_DIR.'/LMSDB.php');
require_once(LIB_DIR.'/LMS.class.php');
require_once(LIB_DIR.'/common.php');
require_once(LIB_DIR.'/Auth.class.php');
require_once(LIB_DIR.'/language.php');
require_once(LIB_DIR.'/Session.class.php');

$DB = DBInit($_DBTYPE, $_DBHOST, $_DBUSER, $_DBPASS, $_DBNAME);
if(!$DB)
{
        // can't working without database
    die("aaa");
}

$SESSION = new Session($DB, $CONFIG['phpui']['timeout']);
$AUTH = new Auth($DB, $SESSION);
$LMS = new LMS($DB, $AUTH, $CONFIG);
$LMS->lang = $_language;
$period = $_GET['p'];
$ipaddr = $_SERVER["REMOTE_ADDR"];

$nodeid = $LMS->GetNodeIDByIP($ipaddr);
$node = $LMS->GetNode($nodeid);
$netdevips = $LMS->GetNetDevIPs($node['netdev']);

$graph['port'] = $node['port'];
$graph['ip'] = $netdevips[0]['ip'];
//$graph['port'] = '20';
//$graph['ip'] = '10.100.240.22';

$iptmp = str_replace(".","-",$graph['ip']);
$iptmp = str_replace("10-100","sw",$iptmp);
$iptmp = str_replace("77-46","sw",$iptmp);


//generowanie wykresu
exec("/var/www/htdocs/wykresy/".$iptmp."/create_graph.pl -p=".$graph['port']."");

$urltmp = "wykresy/";

$urltmp = $urltmp.$iptmp;
$urltmp = $urltmp.'/';
$graph['urld'] = 'modules/graph/graph.php?p=d';
$graph['urlw'] = 'modules/graph/graph.php?p=w';
$graph['urlm'] = 'modules/graph/graph.php?p=m';
$graph['urly'] = 'modules/graph/graph.php?p=y';

switch($period)
{
    case 'y':
	$url = $urltmp.$graph['ip'].'_'.$graph['port'].'-year.png';
	break;
    case 'm':
	$url = $urltmp.$graph['ip'].'_'.$graph['port'].'-month.png';
	break;
    case 'w':
	$url = $urltmp.$graph['ip'].'_'.$graph['port'].'-week.png';
	break;
    default:
    case 'd':
	$url = $urltmp.$graph['ip'].'_'.$graph['port'].'-day.png';
}
$image = imagecreatefrompng("$url");
header('Content-type: image/png');
imagepng($image);
imagedestroy($image);
$SESSION->close();
$DB->Destroy();
?>