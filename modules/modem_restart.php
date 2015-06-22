<?php

$cpes = snmpset($_GET['ip'], "vmo4gjnionh4C#fger#F", "mib-2.69.1.1.3.0", "i", "1");
$SESSION->redirect('?m=nodeinfo&id=' . $_GET['id']);


?>
