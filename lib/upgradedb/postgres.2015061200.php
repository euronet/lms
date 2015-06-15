<?php
$DB->BeginTrans();
$DB->Execute("CREATE SEQUENCE gponauthlog_id_seq");
$DB->Execute("CREATE table gponauthlog (
        id              integer DEFAULT nextval('gponauthlog_id_seq'::text) NOT NULL,
        time            TIMESTAMP with time zone,
        onuid           integer NOT NULL,
        nas             INET,
        oltport         integer,
        onuoltid        integer,
        version         varchar(20),
        PRIMARY KEY (id)
)");
$DB->Execute("create index gponauthlog_onuid_time on gponauthlog (onuid, time DESC)");

$DB->Execute("CREATE TYPE AUTH_PROTOCOL AS ENUM ('MD5', 'SHA', '')");
$DB->Execute("CREATE TYPE SEC_LEVEL AS ENUM ('noAuthNoPriv','authNoPriv','authPriv','')");
$DB->Execute("CREATE TYPE PRIVACY_PROTOCOL AS ENUM ('DES','AES','')");
$DB->Execute("CREATE SEQUENCE gponolt_id_seq");
$DB->Execute("CREATE TABLE gponolt (
  id INTEGER DEFAULT nextval('gponolt_id_seq'::text) NOT NULL,
  snmp_version SMALLINT DEFAULT 2 NOT NULL,
  snmp_description VARCHAR(255) DEFAULT ''::character varying NOT NULL,
  snmp_host VARCHAR(100) DEFAULT ''::character varying NOT NULL,
  snmp_community VARCHAR(100) DEFAULT ''::character varying NOT NULL,
  snmp_auth_protocol AUTH_PROTOCOL DEFAULT '' NOT NULL,
  snmp_username VARCHAR(255) DEFAULT ''::character varying NOT NULL,
  snmp_password VARCHAR(255) DEFAULT ''::character varying NOT NULL,
  snmp_sec_level SEC_LEVEL DEFAULT '' NOT NULL,
  snmp_privacy_passphrase VARCHAR(255) DEFAULT ''::character varying NOT NULL,
  snmp_privacy_protocol PRIVACY_PROTOCOL DEFAULT '' NOT NULL,
  CONSTRAINT gponolt_pkey PRIMARY KEY(id)
)");

$DB->Execute("CREATE SEQUENCE gponoltports_id_seq");
$DB->Execute("CREATE TABLE gponoltports (
  id INTEGER DEFAULT nextval('gponoltports_id_seq'::text) NOT NULL,
  gponoltid INTEGER DEFAULT 0 NOT NULL,
  numport INTEGER DEFAULT 0 NOT NULL,
  maxonu INTEGER DEFAULT 0 NOT NULL,
  CONSTRAINT gponoltports_pkey PRIMARY KEY(id)
)");
$DB->Execute("CREATE INDEX gponoltports_gponoltid_idx ON gponoltports (gponoltid)");

$DB->Execute("CREATE SEQUENCE gponoltprofiles_id_seq;
CREATE TABLE gponoltprofiles (
  id INTEGER DEFAULT nextval('gponoltprofiles_id_seq'::text) NOT NULL,
  name VARCHAR(100) DEFAULT ''::character varying NOT NULL,
  CONSTRAINT gponoltprofiles_pkey PRIMARY KEY(id)
)");

$DB->Execute("CREATE SEQUENCE gpononu_id_seq");
$DB->Execute("CREATE TABLE gpononu (
  id INTEGER DEFAULT nextval('gpononu_id_seq'::text) NOT NULL,
  name VARCHAR(100) DEFAULT ''::character varying NOT NULL,
  location VARCHAR(255) DEFAULT ''::character varying NOT NULL,
  gpononumodelsid INTEGER DEFAULT 0 NOT NULL,
  description TEXT DEFAULT '' NOT NULL,
  serialnumber VARCHAR(32) DEFAULT ''::character varying NOT NULL,
  purchasetime INTEGER DEFAULT 0 NOT NULL,
  guaranteeperiod SMALLINT DEFAULT 0 NOT NULL,
  password VARCHAR(100) DEFAULT ''::character varying NOT NULL,
  onuid SMALLINT DEFAULT 0 NOT NULL,
  autoprovisioning SMALLINT DEFAULT NULL,
  onudescription VARCHAR(32) DEFAULT ''::character varying NOT NULL,
  gponoltprofilesid INTEGER DEFAULT NULL,
  voipaccountsid1 INTEGER DEFAULT NULL,
  voipaccountsid2 INTEGER DEFAULT NULL,
  autoscript SMALLINT DEFAULT 0 NOT NULL,
  host_id1 integer,
  host_id2 integer,
  creationdate integer    DEFAULT 0 NOT NULL,
  moddate integer         DEFAULT 0 NOT NULL,
  creatorid integer       DEFAULT 0 NOT NULL,
  modid integer           DEFAULT 0 NOT NULL,
  CONSTRAINT gpononu_pkey PRIMARY KEY(id),
  UNIQUE (name)
)");
$DB->Execute("CREATE INDEX gpononu_gpononumodelsid_idx ON gpononu (gpononumodelsid)");

$DB->Execute("CREATE SEQUENCE gpononu2customers_id_seq");
$DB->Execute("CREATE TABLE gpononu2customers (
  id INTEGER DEFAULT nextval('gpononu2customers_id_seq'::text) NOT NULL,
  gpononuid INTEGER DEFAULT 0 NOT NULL,
  customersid INTEGER DEFAULT 0 NOT NULL,
  CONSTRAINT gpononu2customers_pkey PRIMARY KEY(id)
)");
$DB->Execute("CREATE INDEX gpononu2customers_gpononuid_idx ON gpononu2customers (gpononuid)");
$DB->Execute("CREATE INDEX gpononu2customers_customersid_idx ON gpononu2customers (customersid)");

$DB->Execute("CREATE SEQUENCE gpononu2olt_id_seq");
$DB->Execute("CREATE TABLE gpononu2olt (
  id INTEGER DEFAULT nextval('gpononu2olt_id_seq'::text) NOT NULL,
  netdevicesid INTEGER DEFAULT 0 NOT NULL,
  gpononuid INTEGER DEFAULT 0 NOT NULL,
  numport SMALLINT DEFAULT 0 NOT NULL,
  CONSTRAINT gpononu2olt_pkey PRIMARY KEY(id),
  CONSTRAINT gpononu2olt_gpononuid_key UNIQUE (gpononuid)
)");
$DB->Execute("CREATE INDEX gpononu2olt_gpononuid_idx ON gpononu2olt (gpononuid)");
$DB->Execute("CREATE INDEX gpononu2olt_netdevicesid_idx ON gpononu2olt (netdevicesid)");

$DB->Execute(" SEQUENCE gpononumodels_id_seq");
$DB->Execute("CREATE TABLE gpononumodels (
  id INTEGER DEFAULT nextval('gpononumodels_id_seq'::text) NOT NULL,
  name VARCHAR(32) DEFAULT ''::character varying NOT NULL,
  description TEXT DEFAULT NULL,
  producer VARCHAR(64) DEFAULT ''::character varying NOT NULL,
  CONSTRAINT gpononumodels_pkey PRIMARY KEY(id)
)");

$DB->Execute("CREATE SEQUENCE gpononuport_id_seq");
$DB->Execute("CREATE table gpononuport (
    id integer	DEFAULT nextval('gpononuport_id_seq'::text) NOT NULL,
    onuid integer   NOT NULL
	REFERENCES gpononu (id) ON DELETE CASCADE ON UPDATE CASCADE,
    typeid integer  DEFAULT NULL,
    portid integer  DEFAULT NULL,
    portdisable smallint,
    PRIMARY KEY (id),
    UNIQUE (onuid, typeid, portid)
)");

$DB->Execute("CREATE SEQUENCE gpononuportstype_id_seq");
$DB->Execute("CREATE TABLE gpononuportstype (
  id INTEGER DEFAULT nextval('gpononuportstype_id_seq'::text) NOT NULL,
  name VARCHAR(100) DEFAULT ''::character varying NOT NULL,
  CONSTRAINT gpononuportstype_pkey PRIMARY KEY(id)
)");

$DB->Execute("CREATE TABLE gpononuportstype2models (
  gpononuportstypeid INTEGER DEFAULT 0 NOT NULL,
  gpononumodelsid INTEGER DEFAULT 0 NOT NULL,
  portscount INTEGER DEFAULT 0 NOT NULL
)");
$DB->Execute("CREATE INDEX gpononuportstype2models_gpononuportstypeid_idx ON gpononuportstype2models (gpononuportstypeid,gpononumodelsid)");

$DB->Execute("CREATE SEQUENCE gpononutv_id_seq;
CREATE TABLE gpononutv (
  id INTEGER DEFAULT nextval('gpononutv_id_seq'::text) NOT NULL,
  ipaddr bigint DEFAULT 0 NOT NULL,
  canal VARCHAR(100) DEFAULT ''::character varying NOT NULL,
  CONSTRAINT gpononutv_pkey PRIMARY KEY(id),
  UNIQUE (ipaddr)
)");

$DB->Execute("CREATE OR REPLACE FUNCTION log_onu_auth (username varchar(100), nas_ip varchar(15), olt integer, onu integer, ver varchar(20))
    RETURNS void AS $$
    DECLARE
        dev_id integer;
        onu_id integer;
    BEGIN
        SELECT INTO dev_id netdev FROM nodes n WHERE inet_ntoa(ipaddr) = nas_ip AND ownerid = 0;
        SELECT INTO onu_id id FROM gpononu WHERE name = username;
        INSERT INTO gponauthlog(time, onuid, nas, oltport, onuoltid, version) VALUES(NOW(), onu_id, nas_ip::inet, olt, onu, ver);
        UPDATE gpononu SET onuid = onu WHERE id = onu_id;

        UPDATE gpononu2olt SET numport = olt WHERE gpononuid = onu_id AND netdevicesid = dev_id;
        IF NOT FOUND THEN
                INSERT INTO gpononu2olt(netdevicesid, gpononuid, numport)
                        VALUES(dev_id, onu_id, olt);
        END IF;
    END;
$$ LANGUAGE plpgsql");


$DB->Execute("INSERT INTO uiconfig (section, var, value, description, disabled) VALUES ('phpui','gpon_max_onu_to_olt','64','GPON - Domyślna maksymalna liczba ONU przypisanych do portu OLT',0)");
$DB->Execute("INSERT INTO uiconfig (section, var, value, description, disabled) VALUES ('phpui','gpon_onumodels_pagelimit','100','Limit wyświetlanych rekordów na jednej stronie listy modeli ONU.',0)");
$DB->Execute("INSERT INTO uiconfig (section, var, value, description, disabled) VALUES ('phpui','gpon','1','Moduł GPON',0)");
$DB->Execute("INSERT INTO uiconfig (section, var, value, description, disabled) VALUES ('phpui','gpon_onu_pagelimit','100','Limit wyświetlanych rekordów na jednej stronie listy ONU.',0)");
$DB->Execute("INSERT INTO uiconfig (section, var, value, description, disabled) VALUES ('phpui','gpon_olt_pagelimit','100','Limit wyświetlanych rekordów na jednej stronie listy OLT.',0)");
$DB->Execute("INSERT INTO uiconfig (section, var, value, description, disabled) VALUES ('phpui','gpon_onu_customerlimit','5','Maksymalna liczba Klientów przypisanych do ONU',0)");
$DB->Execute("INSERT INTO uiconfig (section, var, value, description, disabled) VALUES ('phpui','gpon_tx_output_power_weak','-26','Niski poziom mocy optycznej RX Output Power',0)");
$DB->Execute("INSERT INTO uiconfig (section, var, value, description, disabled) VALUES ('phpui','gpon_onu_autoscript_debug','1','',1)");
$DB->Execute("insert into uiconfig (section, var, value, description) VALUES ('phpui', 'gpon_use_radius', 0, 'Czy gpon (olty) mają używać radiusa')");
$DB->Execute("insert into uiconfig (section, var, value, description) VALUES ('phpui', 'gpon_syslog', 0, 'Jeśli mamy tabele syslog to możemy logować zdarzenia (custom lms).  syslog(time integer, userid integer, level smallint, what character varying(128), xid integer, message text, detail text)')");

$DB->Execute("insert into gpononuportstype(name) values('eth')");
$DB->Execute("insert into gpononuportstype(name) values('pots')");
$DB->Execute("insert into gpononuportstype(name) values('ces')");
$DB->Execute("insert into gpononuportstype(name) values('video')");
$DB->Execute("insert into gpononuportstype(name) values('virtual-eth')");
$DB->Execute("insert into gpononuportstype(name) values('wifi')");


$DB->Execute("ALTER TABLE netdevices ADD COLUMN gponoltid integer DEFAULT NULL");
$DB->Execute("create index netdevices_gponoltid_idx ON netdevices (gponoltid)");

$DB->Execute("UPDATE dbinfo SET keyvalue = ? WHERE keytype = ?", array('2015061200', 'dbversion'));


$DB->CommitTrans();

?>