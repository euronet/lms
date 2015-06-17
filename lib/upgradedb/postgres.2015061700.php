<?php
$DB->BeginTrans();
$DB->Execute("CREATE SEQUENCE woj_device_id_seq");
$DB->Execute("CREATE TABLE woj_device (
	id integer DEFAULT nextval('woj_device_id_seq'::text) NOT NULL,
	model varchar(128)	DEFAULT '',
	serialnumber varchar(128)	DEFAULT '',
	buydate varchar(11)	DEFAULT '',
        vendor varchar(128)	DEFAULT '',
        price integer DEFAULT 0 NOT NULL,
        customerid integer,
        info text,   
	PRIMARY KEY (id)
)");


$DB->Execute("UPDATE dbinfo SET keyvalue = ? WHERE keytype = ?", array('2015061700', 'dbversion'));


$DB->CommitTrans();

?>