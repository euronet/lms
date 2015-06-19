<?php

if(!empty($_POST['zgloszenie'])) {

$que=$_POST['que'];
if(!empty($que)){
foreach($_POST['zgloszenie'] as $selected) {

 $DB->Execute('UPDATE rttickets SET queueid=? 
					WHERE id=?', array($que,
						$selected
						));

    
    

}
}
}
$SESSION->redirect('?m=rtqueueview&id=' . $_GET['id']);

?>