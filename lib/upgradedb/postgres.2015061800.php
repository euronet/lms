<?php
$DB->BeginTrans();
$DB->Execute("CREATE SEQUENCE woj_macarch_id_seq");
$DB->Execute("CREATE TABLE woj_macarch (
    id integer DEFAULT nextval(('woj_macarch_id_seq'::text)::regclass) NOT NULL,
    ip bigint NOT NULL,
    mac character varying(20) NOT NULL,
    port integer NOT NULL,
    vlan integer,
    lastonline integer,
	PRIMARY KEY (id)
)");


$DB->Execute("CREATE SEQUENCE woj_macchange_id_seq");
$DB->Execute("CREATE TABLE woj_macchange (
    id integer DEFAULT nextval(('woj_macchange_id_seq'::text)::regclass) NOT NULL,
    ip bigint NOT NULL,
    mac character varying(20) NOT NULL,
    port integer NOT NULL,
    vlan integer,
    lastonline integer,
	PRIMARY KEY (id)
)");


$DB->Execute("CREATE SEQUENCE woj_macok_id_seq");
$DB->Execute("CREATE TABLE woj_macok (
    id integer DEFAULT nextval(('woj_macok_id_seq'::text)::regclass) NOT NULL,
    ip bigint NOT NULL,
    mac character varying(20) DEFAULT NULL::character varying NOT NULL,
    port integer NOT NULL,
    vlan integer,
    lastonline integer,
	PRIMARY KEY (id)
)");

$DB->Execute("UPDATE dbinfo SET keyvalue = ? WHERE keytype = ?", array('2015061800', 'dbversion'));


$DB->CommitTrans();

?>



