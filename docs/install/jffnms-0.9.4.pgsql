--
-- Translated mysqldump file by mdump2pgsql
--
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE acct (
  id int4 NOT NULL AUTO_INCREMENT,
  usern char(15) NOT NULL DEFAULT '',
  s_name char(30) NOT NULL DEFAULT '',
  c_name char(30) NOT NULL DEFAULT '',
  elapsed_time int4 NOT NULL DEFAULT '0',
  bytes_in int4 DEFAULT '0',
  bytes_out int4 DEFAULT '0',
  date datetime NOT NULL DEFAULT NULL,
  cmd char(250) NOT NULL DEFAULT '',
  type char(15) NOT NULL DEFAULT '0',
  analized int2 NOT NULL DEFAULT '0',
  PRIMARY KEY (id)
);
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE actions (
  id int4 NOT NULL AUTO_INCREMENT,
  description char(40) NOT NULL DEFAULT '',
  command char(60) NOT NULL DEFAULT 'none',
  internal_parameters char(120) NOT NULL DEFAULT '',
  user_parameters char(120) NOT NULL DEFAULT '',
  PRIMARY KEY (id)
);
/*!40101 SET character_set_client = @saved_cs_client */;
INSERT INTO actions (id, description, command, internal_parameters, user_parameters) VALUES (1,'No Action','none','','');
INSERT INTO actions (id, description, command, internal_parameters, user_parameters) VALUES (2,'Send Mail','email','from:nms,to:<profile-email>,subject:NMS','from:From,subject:Subject,comment:Comment');
INSERT INTO actions (id, description, command, internal_parameters, user_parameters) VALUES (3,'Send SMS via Modem','smsclient','smsname:<profile-smsalias>','');
INSERT INTO actions (id, description, command, internal_parameters, user_parameters) VALUES (4,'Send SMS via Mail','email','short:1,from:nms,to:<profile-email>,subject:NMS','from:From,subject:Subject');
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE alarm_states (
  id int4 NOT NULL AUTO_INCREMENT,
  description char(30) NOT NULL DEFAULT '',
  activate_alarm int2 NOT NULL DEFAULT '0',
  sound_in char(30) NOT NULL DEFAULT '',
  sound_out char(30) NOT NULL DEFAULT '',
  state int4 NOT NULL DEFAULT '0',
  PRIMARY KEY (id)
);
/*!40101 SET character_set_client = @saved_cs_client */;
INSERT INTO alarm_states (id, description, activate_alarm, sound_in, sound_out, state) VALUES (1,'down',10,'down.wav','up.wav',1);
INSERT INTO alarm_states (id, description, activate_alarm, sound_in, sound_out, state) VALUES (2,'up',100,'','',2);
INSERT INTO alarm_states (id, description, activate_alarm, sound_in, sound_out, state) VALUES (3,'alert',60,'boing.wav','',3);
INSERT INTO alarm_states (id, description, activate_alarm, sound_in, sound_out, state) VALUES (4,'testing',40,'','',4);
INSERT INTO alarm_states (id, description, activate_alarm, sound_in, sound_out, state) VALUES (5,'running',100,'','',2);
INSERT INTO alarm_states (id, description, activate_alarm, sound_in, sound_out, state) VALUES (6,'not running',20,'','',1);
INSERT INTO alarm_states (id, description, activate_alarm, sound_in, sound_out, state) VALUES (7,'open',100,'','',2);
INSERT INTO alarm_states (id, description, activate_alarm, sound_in, sound_out, state) VALUES (8,'closed',15,'','',1);
INSERT INTO alarm_states (id, description, activate_alarm, sound_in, sound_out, state) VALUES (9,'error',90,'boing.wav','',3);
INSERT INTO alarm_states (id, description, activate_alarm, sound_in, sound_out, state) VALUES (10,'invalid',30,'','',1);
INSERT INTO alarm_states (id, description, activate_alarm, sound_in, sound_out, state) VALUES (11,'valid',110,'','',2);
INSERT INTO alarm_states (id, description, activate_alarm, sound_in, sound_out, state) VALUES (12,'reachable',100,'','',2);
INSERT INTO alarm_states (id, description, activate_alarm, sound_in, sound_out, state) VALUES (13,'unreachable',5,'','',1);
INSERT INTO alarm_states (id, description, activate_alarm, sound_in, sound_out, state) VALUES (14,'lowerlayerdown',10,'down.wav','up.wav',1);
INSERT INTO alarm_states (id, description, activate_alarm, sound_in, sound_out, state) VALUES (15,'synchronized',100,'','',2);
INSERT INTO alarm_states (id, description, activate_alarm, sound_in, sound_out, state) VALUES (16,'unsynchronized',6,'','',1);
INSERT INTO alarm_states (id, description, activate_alarm, sound_in, sound_out, state) VALUES (17,'battery normal',100,'','',2);
INSERT INTO alarm_states (id, description, activate_alarm, sound_in, sound_out, state) VALUES (18,'battery low',4,'','',1);
INSERT INTO alarm_states (id, description, activate_alarm, sound_in, sound_out, state) VALUES (19,'battery unknown',2,'','',1);
INSERT INTO alarm_states (id, description, activate_alarm, sound_in, sound_out, state) VALUES (20,'on battery',3,'','',1);
INSERT INTO alarm_states (id, description, activate_alarm, sound_in, sound_out, state) VALUES (21,'on line',90,'','',2);
INSERT INTO alarm_states (id, description, activate_alarm, sound_in, sound_out, state) VALUES (22,'ok',100,'','',2);
INSERT INTO alarm_states (id, description, activate_alarm, sound_in, sound_out, state) VALUES (23,'out of bounds',10,'','',1);
INSERT INTO alarm_states (id, description, activate_alarm, sound_in, sound_out, state) VALUES (24,'unavailable',10,'down.wav','up.wav',1);
INSERT INTO alarm_states (id, description, activate_alarm, sound_in, sound_out, state) VALUES (25,'available',100,'','',2);
INSERT INTO alarm_states (id, description, activate_alarm, sound_in, sound_out, state) VALUES (26,'battery depleted',3,'','',1);
INSERT INTO alarm_states (id, description, activate_alarm, sound_in, sound_out, state) VALUES (27,'other',10,'','',1);
INSERT INTO alarm_states (id, description, activate_alarm, sound_in, sound_out, state) VALUES (28,'unknown',10,'','',1);
INSERT INTO alarm_states (id, description, activate_alarm, sound_in, sound_out, state) VALUES (29,'noncritical',90,'','',1);
INSERT INTO alarm_states (id, description, activate_alarm, sound_in, sound_out, state) VALUES (30,'critical',10,'','',1);
INSERT INTO alarm_states (id, description, activate_alarm, sound_in, sound_out, state) VALUES (31,'nonrecoverabl',10,'','',1);
INSERT INTO alarm_states (id, description, activate_alarm, sound_in, sound_out, state) VALUES (32,'warning',80,'down.wav','up.wav',1);
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE alarms (
  id int4 NOT NULL AUTO_INCREMENT,
  date_start datetime NOT NULL DEFAULT NULL,
  date_stop datetime NOT NULL DEFAULT NULL,
  interface int4 NOT NULL DEFAULT '0',
  type int4 NOT NULL DEFAULT '0',
  active int4 NOT NULL DEFAULT '1',
  referer_start int4 NOT NULL DEFAULT '0',
  referer_stop int4 NOT NULL DEFAULT '0',
  triggered int2 NOT NULL DEFAULT '0',
  PRIMARY KEY (id)
);
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE auth (
  id int4 NOT NULL AUTO_INCREMENT,
  usern char(60) NOT NULL DEFAULT '',
  passwd varchar(200) DEFAULT NULL,
  fullname char(60) NOT NULL DEFAULT '',
  router int2 NOT NULL DEFAULT '0',
  PRIMARY KEY (id)
);
/*!40101 SET character_set_client = @saved_cs_client */;
INSERT INTO auth (id, usern, passwd, fullname, router) VALUES (1,'No User','$1$txVdymrd$AO3Qa8js9lVNkyscQ552b1','No User Name',0);
INSERT INTO auth (id, usern, passwd, fullname, router) VALUES (2,'admin','adpexzg3FUZAk','Administrator',0);
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE autodiscovery (
  id int4 NOT NULL AUTO_INCREMENT,
  description char(40) NOT NULL DEFAULT '0',
  poller_default int2 NOT NULL DEFAULT '1',
  permit_add int2 NOT NULL DEFAULT '0',
  permit_del int2 NOT NULL DEFAULT '0',
  permit_mod int2 NOT NULL DEFAULT '0',
  permit_disable int2 NOT NULL DEFAULT '0',
  skip_loopback int2 NOT NULL DEFAULT '0',
  check_state int2 NOT NULL DEFAULT '1',
  check_address int2 NOT NULL DEFAULT '1',
  alert_del int2 NOT NULL DEFAULT '1',
  PRIMARY KEY (id)
);
/*!40101 SET character_set_client = @saved_cs_client */;
INSERT INTO autodiscovery (id, description, poller_default, permit_add, permit_del, permit_mod, permit_disable, skip_loopback, check_state, check_address, alert_del) VALUES (1,'No Autodiscovery',1,0,0,0,0,0,1,1,1);
INSERT INTO autodiscovery (id, description, poller_default, permit_add, permit_del, permit_mod, permit_disable, skip_loopback, check_state, check_address, alert_del) VALUES (2,'Standard',1,1,0,0,1,1,1,1,1);
INSERT INTO autodiscovery (id, description, poller_default, permit_add, permit_del, permit_mod, permit_disable, skip_loopback, check_state, check_address, alert_del) VALUES (3,'Automagic',1,1,1,1,0,1,1,1,1);
INSERT INTO autodiscovery (id, description, poller_default, permit_add, permit_del, permit_mod, permit_disable, skip_loopback, check_state, check_address, alert_del) VALUES (4,'Administrative',0,1,1,0,1,1,1,1,1);
INSERT INTO autodiscovery (id, description, poller_default, permit_add, permit_del, permit_mod, permit_disable, skip_loopback, check_state, check_address, alert_del) VALUES (5,'Just Inform',0,0,0,0,0,0,1,1,1);
INSERT INTO autodiscovery (id, description, poller_default, permit_add, permit_del, permit_mod, permit_disable, skip_loopback, check_state, check_address, alert_del) VALUES (6,'Standard (for Switches)',1,1,0,1,0,1,1,0,1);
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE clients (
  id int4 NOT NULL AUTO_INCREMENT,
  username char(60) NOT NULL DEFAULT '',
  password char(30) NOT NULL DEFAULT '',
  name char(60) NOT NULL DEFAULT '',
  shortname char(30) NOT NULL DEFAULT '',
  enabled int2 NOT NULL DEFAULT '1',
  PRIMARY KEY (id)
);
/*!40101 SET character_set_client = @saved_cs_client */;
INSERT INTO clients (id, username, password, name, shortname, enabled) VALUES (1,'unkclient','','Unknown Customer','Unknown',1);
INSERT INTO clients (id, username, password, name, shortname, enabled) VALUES (2,'','','New Customer','Customer1',1);
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE events (
  id int4 NOT NULL AUTO_INCREMENT,
  date datetime NOT NULL DEFAULT NULL,
  type int4 NOT NULL DEFAULT '0',
  host int4 NOT NULL DEFAULT '0',
  interface char(150) NOT NULL,
  state char(40) NOT NULL DEFAULT '',
  username char(40) NOT NULL DEFAULT '',
  info char(150) NOT NULL DEFAULT '',
  referer int4 NOT NULL DEFAULT '0',
  ack int4 NOT NULL DEFAULT '0',
  analized int2 NOT NULL DEFAULT '0',
  PRIMARY KEY (id)
);
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE events_latest (
  id int4 NOT NULL AUTO_INCREMENT,
  date datetime NOT NULL DEFAULT NULL,
  type int4 NOT NULL DEFAULT '0',
  host int4 NOT NULL DEFAULT '0',
  interface char(40) NOT NULL DEFAULT '',
  state char(40) NOT NULL DEFAULT '',
  username char(40) NOT NULL DEFAULT '',
  info char(150) NOT NULL DEFAULT '',
  referencia int4 NOT NULL DEFAULT '0',
  ack int4 NOT NULL DEFAULT '0',
  analized int2 NOT NULL DEFAULT '0',
  PRIMARY KEY (id)
);
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE filters (
  id int4 NOT NULL AUTO_INCREMENT,
  description char(40) NOT NULL DEFAULT '',
  PRIMARY KEY (id)
);
/*!40101 SET character_set_client = @saved_cs_client */;
INSERT INTO filters (id, description) VALUES (1,'All Events');
INSERT INTO filters (id, description) VALUES (4,'Severity Level > Warning');
INSERT INTO filters (id, description) VALUES (5,'Dont Show SLA or Commands');
INSERT INTO filters (id, description) VALUES (8,'Dont Show SLA');
INSERT INTO filters (id, description) VALUES (10,'BGP Events');
INSERT INTO filters (id, description) VALUES (13,'Commands Only');
INSERT INTO filters (id, description) VALUES (17,'Interfaces');
INSERT INTO filters (id, description) VALUES (18,'UnACK Events');
INSERT INTO filters (id, description) VALUES (19,'Windows Events');
INSERT INTO filters (id, description) VALUES (20,'PIX');
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE filters_cond (
  id int4 NOT NULL AUTO_INCREMENT,
  filter_id int4 NOT NULL DEFAULT '1',
  pos int4 NOT NULL DEFAULT '1',
  field_id int4 NOT NULL DEFAULT '1',
  op char(10) NOT NULL DEFAULT '=',
  value char(60) NOT NULL DEFAULT '',
  PRIMARY KEY (id)
);
/*!40101 SET character_set_client = @saved_cs_client */;
INSERT INTO filters_cond (id, filter_id, pos, field_id, op, value) VALUES (1,1,1,1,'=','1');
INSERT INTO filters_cond (id, filter_id, pos, field_id, op, value) VALUES (2,5,1,2,'!=','12');
INSERT INTO filters_cond (id, filter_id, pos, field_id, op, value) VALUES (3,4,1,3,'>','30');
INSERT INTO filters_cond (id, filter_id, pos, field_id, op, value) VALUES (4,5,3,2,'!=','8');
INSERT INTO filters_cond (id, filter_id, pos, field_id, op, value) VALUES (5,5,2,4,'=','');
INSERT INTO filters_cond (id, filter_id, pos, field_id, op, value) VALUES (6,8,1,2,'!=','12');
INSERT INTO filters_cond (id, filter_id, pos, field_id, op, value) VALUES (10,10,1,2,'=','6');
INSERT INTO filters_cond (id, filter_id, pos, field_id, op, value) VALUES (13,13,1,2,'=','8');
INSERT INTO filters_cond (id, filter_id, pos, field_id, op, value) VALUES (20,17,1,11,'>','1');
INSERT INTO filters_cond (id, filter_id, pos, field_id, op, value) VALUES (21,18,1,12,'=','0');
INSERT INTO filters_cond (id, filter_id, pos, field_id, op, value) VALUES (22,19,1,2,'=','46');
INSERT INTO filters_cond (id, filter_id, pos, field_id, op, value) VALUES (23,19,5,5,'=','');
INSERT INTO filters_cond (id, filter_id, pos, field_id, op, value) VALUES (24,19,10,2,'=','47');
INSERT INTO filters_cond (id, filter_id, pos, field_id, op, value) VALUES (25,19,15,5,'=','');
INSERT INTO filters_cond (id, filter_id, pos, field_id, op, value) VALUES (26,19,20,2,'=','48');
INSERT INTO filters_cond (id, filter_id, pos, field_id, op, value) VALUES (27,19,25,5,'=','');
INSERT INTO filters_cond (id, filter_id, pos, field_id, op, value) VALUES (28,19,30,2,'=','49');
INSERT INTO filters_cond (id, filter_id, pos, field_id, op, value) VALUES (29,20,2,5,'=','');
INSERT INTO filters_cond (id, filter_id, pos, field_id, op, value) VALUES (30,20,3,2,'=','63');
INSERT INTO filters_cond (id, filter_id, pos, field_id, op, value) VALUES (31,20,4,5,'=','');
INSERT INTO filters_cond (id, filter_id, pos, field_id, op, value) VALUES (32,20,5,2,'=','62');
INSERT INTO filters_cond (id, filter_id, pos, field_id, op, value) VALUES (33,20,6,5,'=','');
INSERT INTO filters_cond (id, filter_id, pos, field_id, op, value) VALUES (34,20,7,2,'=','65');
INSERT INTO filters_cond (id, filter_id, pos, field_id, op, value) VALUES (35,20,9,2,'=','67');
INSERT INTO filters_cond (id, filter_id, pos, field_id, op, value) VALUES (37,20,10,5,'=','');
INSERT INTO filters_cond (id, filter_id, pos, field_id, op, value) VALUES (38,20,11,2,'=','61');
INSERT INTO filters_cond (id, filter_id, pos, field_id, op, value) VALUES (39,20,12,5,'=','');
INSERT INTO filters_cond (id, filter_id, pos, field_id, op, value) VALUES (40,20,13,2,'=','66');
INSERT INTO filters_cond (id, filter_id, pos, field_id, op, value) VALUES (41,20,1,2,'=','64');
INSERT INTO filters_cond (id, filter_id, pos, field_id, op, value) VALUES (43,20,8,5,'=','');
INSERT INTO filters_cond (id, filter_id, pos, field_id, op, value) VALUES (44,20,14,5,'=','');
INSERT INTO filters_cond (id, filter_id, pos, field_id, op, value) VALUES (45,20,15,2,'=','29');
INSERT INTO filters_cond (id, filter_id, pos, field_id, op, value) VALUES (46,20,16,5,'=','');
INSERT INTO filters_cond (id, filter_id, pos, field_id, op, value) VALUES (47,20,17,2,'=','28');
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE filters_fields (
  id int4 NOT NULL AUTO_INCREMENT,
  description char(40) NOT NULL DEFAULT '',
  field char(40) NOT NULL DEFAULT '',
  PRIMARY KEY (id)
);
/*!40101 SET character_set_client = @saved_cs_client */;
INSERT INTO filters_fields (id, description, field) VALUES (1,'ALL','');
INSERT INTO filters_fields (id, description, field) VALUES (2,'Event Type','types.id');
INSERT INTO filters_fields (id, description, field) VALUES (3,'Severity Level','severity.level');
INSERT INTO filters_fields (id, description, field) VALUES (4,'AND','AND');
INSERT INTO filters_fields (id, description, field) VALUES (5,'OR','OR');
INSERT INTO filters_fields (id, description, field) VALUES (6,'Host','hosts.id');
INSERT INTO filters_fields (id, description, field) VALUES (7,'Zone','zones.id');
INSERT INTO filters_fields (id, description, field) VALUES (11,'Interface ID','interfaces.id');
INSERT INTO filters_fields (id, description, field) VALUES (12,'Acknowledge','events.ack');
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE graph_types (
  id int4 NOT NULL AUTO_INCREMENT,
  description char(30) NOT NULL DEFAULT '',
  type int4 NOT NULL DEFAULT '1',
  graph1 char(60) NOT NULL DEFAULT '',
  graph2 char(60) NOT NULL DEFAULT '',
  sizex1 int4 NOT NULL DEFAULT '0',
  sizey1 int4 NOT NULL DEFAULT '0',
  sizex2 int4 NOT NULL DEFAULT '0',
  sizey2 int4 NOT NULL DEFAULT '0',
  allow_aggregation int2 NOT NULL DEFAULT '0',
  PRIMARY KEY (id)
);
/*!40101 SET character_set_client = @saved_cs_client */;
INSERT INTO graph_types (id, description, type, graph1, graph2, sizex1, sizey1, sizex2, sizey2, allow_aggregation) VALUES (1,'No Graph Selected',1,'','',0,0,0,0,0);
INSERT INTO graph_types (id, description, type, graph1, graph2, sizex1, sizey1, sizex2, sizey2, allow_aggregation) VALUES (3,'Traffic',4,'traffic','',500,150,0,0,1);
INSERT INTO graph_types (id, description, type, graph1, graph2, sizex1, sizey1, sizex2, sizey2, allow_aggregation) VALUES (4,'Utilization',4,'traffic_util','',500,150,0,0,0);
INSERT INTO graph_types (id, description, type, graph1, graph2, sizex1, sizey1, sizex2, sizey2, allow_aggregation) VALUES (5,'Packets',4,'packets','error_packets',275,170,250,170,1);
INSERT INTO graph_types (id, description, type, graph1, graph2, sizex1, sizey1, sizex2, sizey2, allow_aggregation) VALUES (6,'Error Rate',4,'error_rate','',500,170,0,0,0);
INSERT INTO graph_types (id, description, type, graph1, graph2, sizex1, sizey1, sizex2, sizey2, allow_aggregation) VALUES (7,'RTT & Packet Loss',4,'rtt','traffic_pl',275,155,260,140,0);
INSERT INTO graph_types (id, description, type, graph1, graph2, sizex1, sizey1, sizex2, sizey2, allow_aggregation) VALUES (8,'Interface Packet Loss',4,'packetloss','',500,180,0,0,0);
INSERT INTO graph_types (id, description, type, graph1, graph2, sizex1, sizey1, sizex2, sizey2, allow_aggregation) VALUES (9,'Cisco CPU Usage',3,'cpu_util','',500,175,0,0,1);
INSERT INTO graph_types (id, description, type, graph1, graph2, sizex1, sizey1, sizex2, sizey2, allow_aggregation) VALUES (10,'Cisco Memory',3,'memory','',510,150,0,0,0);
INSERT INTO graph_types (id, description, type, graph1, graph2, sizex1, sizey1, sizex2, sizey2, allow_aggregation) VALUES (11,'Drops',4,'drop_packets','',500,175,0,0,0);
INSERT INTO graph_types (id, description, type, graph1, graph2, sizex1, sizey1, sizex2, sizey2, allow_aggregation) VALUES (14,'BGP Updates',6,'bgp_updates','',500,150,0,0,0);
INSERT INTO graph_types (id, description, type, graph1, graph2, sizex1, sizey1, sizex2, sizey2, allow_aggregation) VALUES (15,'Used Storage',8,'storage','',500,150,0,0,1);
INSERT INTO graph_types (id, description, type, graph1, graph2, sizex1, sizey1, sizex2, sizey2, allow_aggregation) VALUES (16,'CSS VIP Hits',9,'css_vip_hits','',500,150,0,0,0);
INSERT INTO graph_types (id, description, type, graph1, graph2, sizex1, sizey1, sizex2, sizey2, allow_aggregation) VALUES (17,'CSS VIP Traffic',9,'css_vip_output_only_traffic','',500,150,0,0,0);
INSERT INTO graph_types (id, description, type, graph1, graph2, sizex1, sizey1, sizex2, sizey2, allow_aggregation) VALUES (18,'Solaris Memory Usage',10,'ucd_memory','',500,180,0,0,0);
INSERT INTO graph_types (id, description, type, graph1, graph2, sizex1, sizey1, sizex2, sizey2, allow_aggregation) VALUES (19,'Solaris Load Average',10,'ucd_load_average','',500,150,0,0,0);
INSERT INTO graph_types (id, description, type, graph1, graph2, sizex1, sizey1, sizex2, sizey2, allow_aggregation) VALUES (20,'Solaris CPU Usage',10,'ucd_cpu_solaris','',500,175,0,0,0);
INSERT INTO graph_types (id, description, type, graph1, graph2, sizex1, sizey1, sizex2, sizey2, allow_aggregation) VALUES (21,'CPU Usage',11,'ucd_cpu_linux','',500,175,0,0,1);
INSERT INTO graph_types (id, description, type, graph1, graph2, sizex1, sizey1, sizex2, sizey2, allow_aggregation) VALUES (22,'Load Average',11,'ucd_load_average','',500,150,0,0,1);
INSERT INTO graph_types (id, description, type, graph1, graph2, sizex1, sizey1, sizex2, sizey2, allow_aggregation) VALUES (23,'Established Connections',2,'tcp_conn_number','',500,175,0,0,1);
INSERT INTO graph_types (id, description, type, graph1, graph2, sizex1, sizey1, sizex2, sizey2, allow_aggregation) VALUES (24,'Connection Delay',2,'tcp_conn_delay','',500,175,0,0,0);
INSERT INTO graph_types (id, description, type, graph1, graph2, sizex1, sizey1, sizex2, sizey2, allow_aggregation) VALUES (25,'IP Accounting',3,'acct_bytes','acct_packets',275,170,260,170,0);
INSERT INTO graph_types (id, description, type, graph1, graph2, sizex1, sizey1, sizex2, sizey2, allow_aggregation) VALUES (26,'Processes / Users',12,'hostmib_users_procs','',500,170,0,0,0);
INSERT INTO graph_types (id, description, type, graph1, graph2, sizex1, sizey1, sizex2, sizey2, allow_aggregation) VALUES (27,'TCP Connection Status',12,'tcpmib_connections','',500,150,0,0,0);
INSERT INTO graph_types (id, description, type, graph1, graph2, sizex1, sizey1, sizex2, sizey2, allow_aggregation) VALUES (28,'Processor Utilization',12,'cpu_util','',500,175,0,0,0);
INSERT INTO graph_types (id, description, type, graph1, graph2, sizex1, sizey1, sizex2, sizey2, allow_aggregation) VALUES (29,'Processes / Users',11,'hostmib_users_procs','',500,170,0,0,0);
INSERT INTO graph_types (id, description, type, graph1, graph2, sizex1, sizey1, sizex2, sizey2, allow_aggregation) VALUES (30,'TCP Connection Status',11,'tcpmib_connections','',500,150,0,0,0);
INSERT INTO graph_types (id, description, type, graph1, graph2, sizex1, sizey1, sizex2, sizey2, allow_aggregation) VALUES (31,'TCP Connection Status',3,'tcpmib_connections','',500,150,0,0,0);
INSERT INTO graph_types (id, description, type, graph1, graph2, sizex1, sizey1, sizex2, sizey2, allow_aggregation) VALUES (32,'Accounted Packets',13,'cisco_mac_packets','',500,150,0,0,0);
INSERT INTO graph_types (id, description, type, graph1, graph2, sizex1, sizey1, sizex2, sizey2, allow_aggregation) VALUES (33,'Accounted Bytes',13,'cisco_mac_bytes','',500,150,0,0,0);
INSERT INTO graph_types (id, description, type, graph1, graph2, sizex1, sizey1, sizex2, sizey2, allow_aggregation) VALUES (34,'SP RTT & Loss',14,'rtt','packetloss',270,170,265,170,0);
INSERT INTO graph_types (id, description, type, graph1, graph2, sizex1, sizey1, sizex2, sizey2, allow_aggregation) VALUES (35,'Median RTT',14,'rtt','',500,175,0,0,0);
INSERT INTO graph_types (id, description, type, graph1, graph2, sizex1, sizey1, sizex2, sizey2, allow_aggregation) VALUES (36,'Packets Lost',14,'packetloss','',500,175,0,0,0);
INSERT INTO graph_types (id, description, type, graph1, graph2, sizex1, sizey1, sizex2, sizey2, allow_aggregation) VALUES (37,'Temperature',17,'temperature','',500,180,0,0,1);
INSERT INTO graph_types (id, description, type, graph1, graph2, sizex1, sizey1, sizex2, sizey2, allow_aggregation) VALUES (38,'SA Agent Round-Trip Latency',19,'cisco_saagent_rtl','',500,150,0,0,0);
INSERT INTO graph_types (id, description, type, graph1, graph2, sizex1, sizey1, sizex2, sizey2, allow_aggregation) VALUES (39,'SA Agent Jitter',19,'cisco_saagent_jitter','',500,150,0,0,0);
INSERT INTO graph_types (id, description, type, graph1, graph2, sizex1, sizey1, sizex2, sizey2, allow_aggregation) VALUES (40,'SA Agent % Packet Loss',19,'cisco_saagent_packetloss','',500,150,0,0,0);
INSERT INTO graph_types (id, description, type, graph1, graph2, sizex1, sizey1, sizex2, sizey2, allow_aggregation) VALUES (41,'Host Round Trip Time',20,'rtt','',500,150,0,0,0);
INSERT INTO graph_types (id, description, type, graph1, graph2, sizex1, sizey1, sizex2, sizey2, allow_aggregation) VALUES (42,'Host Packet Loss',20,'packetloss','',500,175,0,0,0);
INSERT INTO graph_types (id, description, type, graph1, graph2, sizex1, sizey1, sizex2, sizey2, allow_aggregation) VALUES (43,'TC Class Rate',21,'tc_rate','',500,150,0,0,1);
INSERT INTO graph_types (id, description, type, graph1, graph2, sizex1, sizey1, sizex2, sizey2, allow_aggregation) VALUES (44,'Instances/Memory',15,'apps_instances','apps_memory',250,175,280,175,0);
INSERT INTO graph_types (id, description, type, graph1, graph2, sizex1, sizey1, sizex2, sizey2, allow_aggregation) VALUES (45,'Connection Delay',23,'tcp_conn_delay','',500,175,0,0,0);
INSERT INTO graph_types (id, description, type, graph1, graph2, sizex1, sizey1, sizex2, sizey2, allow_aggregation) VALUES (46,'Temperature',26,'temperature','',500,175,0,0,1);
INSERT INTO graph_types (id, description, type, graph1, graph2, sizex1, sizey1, sizex2, sizey2, allow_aggregation) VALUES (47,'Packets New',4,'packets_new','',610,170,0,0,0);
INSERT INTO graph_types (id, description, type, graph1, graph2, sizex1, sizey1, sizex2, sizey2, allow_aggregation) VALUES (48,'Livingston Portmaster Serial',28,'pm_serial','',500,175,0,0,0);
INSERT INTO graph_types (id, description, type, graph1, graph2, sizex1, sizey1, sizex2, sizey2, allow_aggregation) VALUES (49,'CGI Requests',27,'iis_tcgir','',500,150,0,0,0);
INSERT INTO graph_types (id, description, type, graph1, graph2, sizex1, sizey1, sizex2, sizey2, allow_aggregation) VALUES (50,'POSTs and GETs',27,'iis_tptg','',500,150,0,0,0);
INSERT INTO graph_types (id, description, type, graph1, graph2, sizex1, sizey1, sizex2, sizey2, allow_aggregation) VALUES (51,'Files Sent',27,'iis_tfs','',500,150,0,0,0);
INSERT INTO graph_types (id, description, type, graph1, graph2, sizex1, sizey1, sizex2, sizey2, allow_aggregation) VALUES (52,'Bytes Received',27,'iis_tbr','',500,150,0,0,0);
INSERT INTO graph_types (id, description, type, graph1, graph2, sizex1, sizey1, sizex2, sizey2, allow_aggregation) VALUES (53,'Hits',29,'apache_tac','',500,175,0,0,1);
INSERT INTO graph_types (id, description, type, graph1, graph2, sizex1, sizey1, sizex2, sizey2, allow_aggregation) VALUES (54,'KBytes',29,'apache_tkb','',500,175,0,0,1);
INSERT INTO graph_types (id, description, type, graph1, graph2, sizex1, sizey1, sizex2, sizey2, allow_aggregation) VALUES (55,'Apache CPU Load',29,'apache_cplo','',500,175,0,0,0);
INSERT INTO graph_types (id, description, type, graph1, graph2, sizex1, sizey1, sizex2, sizey2, allow_aggregation) VALUES (59,'Bytes Per Request',29,'apache_bpr','',500,175,0,0,0);
INSERT INTO graph_types (id, description, type, graph1, graph2, sizex1, sizey1, sizex2, sizey2, allow_aggregation) VALUES (60,'Workers',29,'apache_workers','',500,175,0,0,0);
INSERT INTO graph_types (id, description, type, graph1, graph2, sizex1, sizey1, sizex2, sizey2, allow_aggregation) VALUES (61,'Load/Capacity',31,'apc_load_capacity','',500,175,0,0,0);
INSERT INTO graph_types (id, description, type, graph1, graph2, sizex1, sizey1, sizex2, sizey2, allow_aggregation) VALUES (62,'Voltages',31,'apc_voltages','',500,150,0,0,0);
INSERT INTO graph_types (id, description, type, graph1, graph2, sizex1, sizey1, sizex2, sizey2, allow_aggregation) VALUES (63,'Time Remaining',31,'apc_time_remaining','',500,150,0,0,0);
INSERT INTO graph_types (id, description, type, graph1, graph2, sizex1, sizey1, sizex2, sizey2, allow_aggregation) VALUES (64,'Temperature',31,'temperature','',500,150,0,0,0);
INSERT INTO graph_types (id, description, type, graph1, graph2, sizex1, sizey1, sizex2, sizey2, allow_aggregation) VALUES (65,'Records',30,'sql_records','',500,150,0,0,0);
INSERT INTO graph_types (id, description, type, graph1, graph2, sizex1, sizey1, sizex2, sizey2, allow_aggregation) VALUES (66,'Traffic',32,'alteon_octets','',500,175,0,0,1);
INSERT INTO graph_types (id, description, type, graph1, graph2, sizex1, sizey1, sizex2, sizey2, allow_aggregation) VALUES (67,'Session Rate',32,'alteon_sessionrate','',500,175,0,0,1);
INSERT INTO graph_types (id, description, type, graph1, graph2, sizex1, sizey1, sizex2, sizey2, allow_aggregation) VALUES (68,'Failures',32,'alteon_failures_sessions','',500,175,0,0,0);
INSERT INTO graph_types (id, description, type, graph1, graph2, sizex1, sizey1, sizex2, sizey2, allow_aggregation) VALUES (69,'Current Sessions',32,'alteon_sessions','',500,175,0,0,1);
INSERT INTO graph_types (id, description, type, graph1, graph2, sizex1, sizey1, sizex2, sizey2, allow_aggregation) VALUES (70,'Current Sessions',33,'alteon_sessions','',500,175,0,0,1);
INSERT INTO graph_types (id, description, type, graph1, graph2, sizex1, sizey1, sizex2, sizey2, allow_aggregation) VALUES (71,'Sessions Rate',33,'alteon_sessionrate','',500,175,0,0,1);
INSERT INTO graph_types (id, description, type, graph1, graph2, sizex1, sizey1, sizex2, sizey2, allow_aggregation) VALUES (72,'Octets',33,'alteon_octets','',500,175,0,0,1);
INSERT INTO graph_types (id, description, type, graph1, graph2, sizex1, sizey1, sizex2, sizey2, allow_aggregation) VALUES (73,'Response Time',34,'response_time','',500,175,0,0,0);
INSERT INTO graph_types (id, description, type, graph1, graph2, sizex1, sizey1, sizex2, sizey2, allow_aggregation) VALUES (74,'Memory',35,'alteon_memory','',500,175,0,0,0);
INSERT INTO graph_types (id, description, type, graph1, graph2, sizex1, sizey1, sizex2, sizey2, allow_aggregation) VALUES (75,'CPU Load',35,'alteon_load_average','',500,175,0,0,0);
INSERT INTO graph_types (id, description, type, graph1, graph2, sizex1, sizey1, sizex2, sizey2, allow_aggregation) VALUES (76,'TCP Connections',35,'tcpmib_connections','',500,150,0,0,0);
INSERT INTO graph_types (id, description, type, graph1, graph2, sizex1, sizey1, sizex2, sizey2, allow_aggregation) VALUES (77,'Sensor Value',36,'brocade_sensor','',500,175,0,0,0);
INSERT INTO graph_types (id, description, type, graph1, graph2, sizex1, sizey1, sizex2, sizey2, allow_aggregation) VALUES (78,'Frames',37,'frames','',500,175,0,0,0);
INSERT INTO graph_types (id, description, type, graph1, graph2, sizex1, sizey1, sizex2, sizey2, allow_aggregation) VALUES (79,'Traffic',37,'traffic_words','',500,175,0,0,0);
INSERT INTO graph_types (id, description, type, graph1, graph2, sizex1, sizey1, sizex2, sizey2, allow_aggregation) VALUES (80,'Cisco Dialup Usage',38,'cisco_serial','',500,175,0,0,0);
INSERT INTO graph_types (id, description, type, graph1, graph2, sizex1, sizey1, sizex2, sizey2, allow_aggregation) VALUES (81,'Time Usage',39,'inf_ldisk_time','',500,150,0,0,0);
INSERT INTO graph_types (id, description, type, graph1, graph2, sizex1, sizey1, sizex2, sizey2, allow_aggregation) VALUES (82,'I/O Rate',39,'inf_ldisk_rate','',500,150,0,0,0);
INSERT INTO graph_types (id, description, type, graph1, graph2, sizex1, sizey1, sizex2, sizey2, allow_aggregation) VALUES (83,'Battery Temperature',40,'temperature','',500,150,0,0,0);
INSERT INTO graph_types (id, description, type, graph1, graph2, sizex1, sizey1, sizex2, sizey2, allow_aggregation) VALUES (84,'Time Remaining',40,'ups_time_remaining','',500,150,0,0,0);
INSERT INTO graph_types (id, description, type, graph1, graph2, sizex1, sizey1, sizex2, sizey2, allow_aggregation) VALUES (85,'UPS Voltage',41,'ups_voltage','',500,150,0,0,1);
INSERT INTO graph_types (id, description, type, graph1, graph2, sizex1, sizey1, sizex2, sizey2, allow_aggregation) VALUES (86,'UPS Current',41,'ups_current','',500,150,0,0,1);
INSERT INTO graph_types (id, description, type, graph1, graph2, sizex1, sizey1, sizex2, sizey2, allow_aggregation) VALUES (87,'UPS Load',41,'ups_load','',500,150,0,0,1);
INSERT INTO graph_types (id, description, type, graph1, graph2, sizex1, sizey1, sizex2, sizey2, allow_aggregation) VALUES (88,'Charge Remaining',40,'ups_charge_remaining','',500,150,0,0,0);
INSERT INTO graph_types (id, description, type, graph1, graph2, sizex1, sizey1, sizex2, sizey2, allow_aggregation) VALUES (89,'IPTables Rate',42,'iptables_rate','',500,150,0,170,1);
INSERT INTO graph_types (id, description, type, graph1, graph2, sizex1, sizey1, sizex2, sizey2, allow_aggregation) VALUES (90,'Routes',6,'bgp_routes','',500,150,0,0,0);
INSERT INTO graph_types (id, description, type, graph1, graph2, sizex1, sizey1, sizex2, sizey2, allow_aggregation) VALUES (91,'PIX Connections',43,'pix_connections','',500,175,0,0,0);
INSERT INTO graph_types (id, description, type, graph1, graph2, sizex1, sizey1, sizex2, sizey2, allow_aggregation) VALUES (92,'NAT Active Binds',44,'cisco_nat_active','',500,150,0,0,0);
INSERT INTO graph_types (id, description, type, graph1, graph2, sizex1, sizey1, sizex2, sizey2, allow_aggregation) VALUES (93,'NAT Packets',44,'cisco_nat_packets','',500,150,0,0,0);
INSERT INTO graph_types (id, description, type, graph1, graph2, sizex1, sizey1, sizex2, sizey2, allow_aggregation) VALUES (94,'Sensor Value',45,'sensor_value','',500,150,0,0,0);
INSERT INTO graph_types (id, description, type, graph1, graph2, sizex1, sizey1, sizex2, sizey2, allow_aggregation) VALUES (95,'OS/400 CPU Usage',46,'cpu_os400','',500,150,0,0,0);
INSERT INTO graph_types (id, description, type, graph1, graph2, sizex1, sizey1, sizex2, sizey2, allow_aggregation) VALUES (96,'Dell OpenManage Ambient Temp',47,'dell_om_temp','',500,150,0,0,0);
INSERT INTO graph_types (id, description, type, graph1, graph2, sizex1, sizey1, sizex2, sizey2, allow_aggregation) VALUES (97,'Dell OpenManage Fan RPM',47,'dell_om_fan','',500,150,0,0,0);
INSERT INTO graph_types (id, description, type, graph1, graph2, sizex1, sizey1, sizex2, sizey2, allow_aggregation) VALUES (98,'UPS Power',41,'ups_power','',500,150,0,0,1);
INSERT INTO graph_types (id, description, type, graph1, graph2, sizex1, sizey1, sizex2, sizey2, allow_aggregation) VALUES (99,'PDU Load',49,'pdu_load','',500,150,0,0,1);
INSERT INTO graph_types (id, description, type, graph1, graph2, sizex1, sizey1, sizex2, sizey2, allow_aggregation) VALUES (102,'Time Usage',57,'inf_ldisk_time','',500,175,0,0,0);
INSERT INTO graph_types (id, description, type, graph1, graph2, sizex1, sizey1, sizex2, sizey2, allow_aggregation) VALUES (103,'I/O Byte Rate',57,'inf_ldisk_rate','',500,175,0,0,0);
INSERT INTO graph_types (id, description, type, graph1, graph2, sizex1, sizey1, sizex2, sizey2, allow_aggregation) VALUES (104,'I/O Ops Rate',57,'inf_ldisk_opsrate','',500,175,0,0,0);
INSERT INTO graph_types (id, description, type, graph1, graph2, sizex1, sizey1, sizex2, sizey2, allow_aggregation) VALUES (105,'FC Frames',52,'frames','',500,175,0,0,0);
INSERT INTO graph_types (id, description, type, graph1, graph2, sizex1, sizey1, sizex2, sizey2, allow_aggregation) VALUES (106,'IBM Blade CPU Temp',51,'ibm_blade_cpu_temp','',500,150,0,0,0);
INSERT INTO graph_types (id, description, type, graph1, graph2, sizex1, sizey1, sizex2, sizey2, allow_aggregation) VALUES (107,'IBM Blade Power',54,'ibm_blade_power','',500,150,0,0,0);
INSERT INTO graph_types (id, description, type, graph1, graph2, sizex1, sizey1, sizex2, sizey2, allow_aggregation) VALUES (101,'Wireless Associated',53,'cisco_80211X_associated','',500,175,0,0,0);
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE hosts (
  id int4 NOT NULL AUTO_INCREMENT,
  ip_tacacs char(16) NOT NULL DEFAULT '',
  ip varchar(39) NOT NULL DEFAULT '',
  name char(255) NOT NULL,
  rocommunity char(100) NOT NULL DEFAULT '',
  rwcommunity char(100) NOT NULL DEFAULT '',
  zone int4 NOT NULL DEFAULT '0',
  tftp char(20) NOT NULL DEFAULT '',
  autodiscovery int4 NOT NULL DEFAULT '1',
  config_type int4 NOT NULL DEFAULT '1',
  autodiscovery_default_customer int4 NOT NULL DEFAULT '1',
  satellite int4 NOT NULL DEFAULT '1',
  dmii char(10) NOT NULL DEFAULT '1',
  lat decimal(12,2) NOT NULL DEFAULT '0.00',
  lon decimal(12,2) NOT NULL DEFAULT '0.00',
  show_host int2 NOT NULL DEFAULT '1',
  poll int2 NOT NULL DEFAULT '1',
  creation_date int4 NOT NULL DEFAULT '0',
  modification_date int4 NOT NULL DEFAULT '0',
  last_poll_date int4 NOT NULL DEFAULT '0',
  last_poll_time int4 NOT NULL DEFAULT '0',
  poll_interval int4 NOT NULL DEFAULT '300',
  dmii_up int2 NOT NULL DEFAULT '1',
  sysobjectid varchar(255) DEFAULT NULL,
  PRIMARY KEY (id)
);
/*!40101 SET character_set_client = @saved_cs_client */;
INSERT INTO hosts (id, ip_tacacs, ip, name, rocommunity, rwcommunity, zone, tftp, autodiscovery, config_type, autodiscovery_default_customer, satellite, dmii, lat, lon, show_host, poll, creation_date, modification_date, last_poll_date, last_poll_time, poll_interval, dmii_up, sysobjectid) VALUES (1,'','','Unknown','','',1,'',1,1,1,1,'1',0.00,0.00,1,1,0,0,1232673711,0,300,1,NULL);
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE hosts_config (
  id int4 NOT NULL AUTO_INCREMENT,
  date datetime NOT NULL DEFAULT NULL,
  host int4 NOT NULL DEFAULT '0',
  config text NOT NULL,
  PRIMARY KEY (id)
);
/*!40101 SET character_set_client = @saved_cs_client */;
INSERT INTO hosts_config (id, date, host, config) VALUES (1,NULL,1,'No Config');
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE hosts_config_types (
  id int4 NOT NULL AUTO_INCREMENT,
  description char(60) NOT NULL DEFAULT '',
  command char(30) NOT NULL DEFAULT '',
  PRIMARY KEY (id)
);
/*!40101 SET character_set_client = @saved_cs_client */;
INSERT INTO hosts_config_types (id, description, command) VALUES (1,'No Configuration Transfer','none');
INSERT INTO hosts_config_types (id, description, command) VALUES (2,'Cisco IOS, Newer than 12.0 (CONFIG-COPY-MIB)','cisco_cc');
INSERT INTO hosts_config_types (id, description, command) VALUES (3,'Cisco IOS, Older than 12.0 (SYS-MIB)','cisco_sys');
INSERT INTO hosts_config_types (id, description, command) VALUES (4,'Cisco CatOS, Catalyst Switches (STACK-MIB)','cisco_catos');
INSERT INTO hosts_config_types (id, description, command) VALUES (5,'Alteon WebOS Switches (DANGEROUS)','alteon_webos');
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE interface_types (
  id int4 NOT NULL AUTO_INCREMENT,
  description varchar(40) NOT NULL DEFAULT '',
  autodiscovery_validate int2 NOT NULL DEFAULT '0',
  autodiscovery_enabled int2 NOT NULL DEFAULT '0',
  autodiscovery_function varchar(40) NOT NULL DEFAULT '',
  autodiscovery_parameters varchar(200) NOT NULL DEFAULT '',
  autodiscovery_default_poller int4 NOT NULL DEFAULT '1',
  have_graph int2 NOT NULL DEFAULT '0',
  rrd_structure_rra text NOT NULL,
  rrd_structure_res varchar(20) NOT NULL DEFAULT '',
  rrd_structure_step int4 NOT NULL DEFAULT '300',
  graph_default int4 NOT NULL DEFAULT '1',
  break_by_card int2 NOT NULL DEFAULT '0',
  update_handler varchar(30) NOT NULL DEFAULT 'none',
  allow_manual_add int2 NOT NULL DEFAULT '0',
  sla_default int4 NOT NULL DEFAULT '1',
  have_tools int2 NOT NULL DEFAULT '0',
  snmp_oid varchar(255) NOT NULL DEFAULT '.',
  PRIMARY KEY (id)
);
/*!40101 SET character_set_client = @saved_cs_client */;
INSERT INTO interface_types (id, description, autodiscovery_validate, autodiscovery_enabled, autodiscovery_function, autodiscovery_parameters, autodiscovery_default_poller, have_graph, rrd_structure_rra, rrd_structure_res, rrd_structure_step, graph_default, break_by_card, update_handler, allow_manual_add, sla_default, have_tools, snmp_oid) VALUES (1,'No Interface Type',0,0,'none','',1,0,'','',300,1,0,'none',0,1,0,'.');
INSERT INTO interface_types (id, description, autodiscovery_validate, autodiscovery_enabled, autodiscovery_function, autodiscovery_parameters, autodiscovery_default_poller, have_graph, rrd_structure_rra, rrd_structure_res, rrd_structure_step, graph_default, break_by_card, update_handler, allow_manual_add, sla_default, have_tools, snmp_oid) VALUES (2,'TCP Ports',0,1,'tcp_ports','-sT -p1-500,600-1024',5,1,'RRA:LAST:0.5:1:<resolution>','103680',300,23,0,'tcp_ports',1,1,1,'');
INSERT INTO interface_types (id, description, autodiscovery_validate, autodiscovery_enabled, autodiscovery_function, autodiscovery_parameters, autodiscovery_default_poller, have_graph, rrd_structure_rra, rrd_structure_res, rrd_structure_step, graph_default, break_by_card, update_handler, allow_manual_add, sla_default, have_tools, snmp_oid) VALUES (3,'Cisco System Info',1,1,'host_information','cisco,9.1,9.5',3,1,'RRA:AVERAGE:0.5:1:<resolution>','103680',300,9,0,'none',0,7,0,'ent.9');
INSERT INTO interface_types (id, description, autodiscovery_validate, autodiscovery_enabled, autodiscovery_function, autodiscovery_parameters, autodiscovery_default_poller, have_graph, rrd_structure_rra, rrd_structure_res, rrd_structure_step, graph_default, break_by_card, update_handler, allow_manual_add, sla_default, have_tools, snmp_oid) VALUES (4,'Physical Interfaces',1,1,'snmp_interfaces','',61,1,'RRA:AVERAGE:0.5:1:<resolution>','103680',300,3,1,'none',0,1,1,'.');
INSERT INTO interface_types (id, description, autodiscovery_validate, autodiscovery_enabled, autodiscovery_function, autodiscovery_parameters, autodiscovery_default_poller, have_graph, rrd_structure_rra, rrd_structure_res, rrd_structure_step, graph_default, break_by_card, update_handler, allow_manual_add, sla_default, have_tools, snmp_oid) VALUES (6,'BGP Neighbors',1,1,'bgp_peers','',8,1,'RRA:AVERAGE:0.5:1:<resolution>','103680',300,90,0,'none',0,1,0,'.');
INSERT INTO interface_types (id, description, autodiscovery_validate, autodiscovery_enabled, autodiscovery_function, autodiscovery_parameters, autodiscovery_default_poller, have_graph, rrd_structure_rra, rrd_structure_res, rrd_structure_step, graph_default, break_by_card, update_handler, allow_manual_add, sla_default, have_tools, snmp_oid) VALUES (8,'Storage',1,1,'storage','',9,1,'RRA:AVERAGE:0.5:1:<resolution>','103680',300,15,0,'none',0,9,0,'.');
INSERT INTO interface_types (id, description, autodiscovery_validate, autodiscovery_enabled, autodiscovery_function, autodiscovery_parameters, autodiscovery_default_poller, have_graph, rrd_structure_rra, rrd_structure_res, rrd_structure_step, graph_default, break_by_card, update_handler, allow_manual_add, sla_default, have_tools, snmp_oid) VALUES (9,'CSS VIPs',0,1,'css_vips','',10,1,'RRA:AVERAGE:0.5:1:<resolution>','103680',300,17,0,'none',0,1,0,'ent.9');
INSERT INTO interface_types (id, description, autodiscovery_validate, autodiscovery_enabled, autodiscovery_function, autodiscovery_parameters, autodiscovery_default_poller, have_graph, rrd_structure_rra, rrd_structure_res, rrd_structure_step, graph_default, break_by_card, update_handler, allow_manual_add, sla_default, have_tools, snmp_oid) VALUES (10,'Solaris System Info',1,1,'host_information','solaris,sparc,sun,11.2.3.10,8072.3.2.3',12,1,'RRA:AVERAGE:0.5:1:<resolution>','103680',300,20,0,'none',0,1,0,'.');
INSERT INTO interface_types (id, description, autodiscovery_validate, autodiscovery_enabled, autodiscovery_function, autodiscovery_parameters, autodiscovery_default_poller, have_graph, rrd_structure_rra, rrd_structure_res, rrd_structure_step, graph_default, break_by_card, update_handler, allow_manual_add, sla_default, have_tools, snmp_oid) VALUES (11,'Linux/Unix System Info',1,1,'host_information','2021.250.10,linux,2021.250.255,freebsd,netSnmp,8072',11,1,'RRA:AVERAGE:0.5:1:<resolution>','103680',300,21,0,'none',0,10,0,'.');
INSERT INTO interface_types (id, description, autodiscovery_validate, autodiscovery_enabled, autodiscovery_function, autodiscovery_parameters, autodiscovery_default_poller, have_graph, rrd_structure_rra, rrd_structure_res, rrd_structure_step, graph_default, break_by_card, update_handler, allow_manual_add, sla_default, have_tools, snmp_oid) VALUES (12,'Windows System Info',1,1,'host_information','enterprises.311',13,1,'RRA:AVERAGE:0.5:1:<resolution>','103680',300,28,0,'none',0,11,0,'.');
INSERT INTO interface_types (id, description, autodiscovery_validate, autodiscovery_enabled, autodiscovery_function, autodiscovery_parameters, autodiscovery_default_poller, have_graph, rrd_structure_rra, rrd_structure_res, rrd_structure_step, graph_default, break_by_card, update_handler, allow_manual_add, sla_default, have_tools, snmp_oid) VALUES (13,'Cisco MAC Accounting',1,1,'cisco_accounting','',14,1,'RRA:AVERAGE:0.5:1:<resolution>','103680',300,33,0,'none',0,1,0,'.');
INSERT INTO interface_types (id, description, autodiscovery_validate, autodiscovery_enabled, autodiscovery_function, autodiscovery_parameters, autodiscovery_default_poller, have_graph, rrd_structure_rra, rrd_structure_res, rrd_structure_step, graph_default, break_by_card, update_handler, allow_manual_add, sla_default, have_tools, snmp_oid) VALUES (14,'Smokeping Host',1,1,'smokeping','/var/lib/smokeping',15,1,'RRA:AVERAGE:0.5:1:<resolution>','103680',300,34,0,'none',0,8,0,'');
INSERT INTO interface_types (id, description, autodiscovery_validate, autodiscovery_enabled, autodiscovery_function, autodiscovery_parameters, autodiscovery_default_poller, have_graph, rrd_structure_rra, rrd_structure_res, rrd_structure_step, graph_default, break_by_card, update_handler, allow_manual_add, sla_default, have_tools, snmp_oid) VALUES (15,'Applications',1,0,'hostmib_apps','',16,1,'RRA:AVERAGE:0.5:1:<resolution>','103680',300,44,0,'none',0,1,0,'.');
INSERT INTO interface_types (id, description, autodiscovery_validate, autodiscovery_enabled, autodiscovery_function, autodiscovery_parameters, autodiscovery_default_poller, have_graph, rrd_structure_rra, rrd_structure_res, rrd_structure_step, graph_default, break_by_card, update_handler, allow_manual_add, sla_default, have_tools, snmp_oid) VALUES (16,'Cisco Power Supply',1,1,'cisco_envmib','PowerSupply,5.1.2,5.1.3',17,0,'','103680',300,1,1,'none',0,1,0,'ent.9');
INSERT INTO interface_types (id, description, autodiscovery_validate, autodiscovery_enabled, autodiscovery_function, autodiscovery_parameters, autodiscovery_default_poller, have_graph, rrd_structure_rra, rrd_structure_res, rrd_structure_step, graph_default, break_by_card, update_handler, allow_manual_add, sla_default, have_tools, snmp_oid) VALUES (17,'Cisco Temperature',1,1,'cisco_envmib','Temperature,3.1.2,3.1.6',18,1,'RRA:AVERAGE:0.5:1:<resolution>','103680',300,37,1,'none',0,1,0,'ent.9');
INSERT INTO interface_types (id, description, autodiscovery_validate, autodiscovery_enabled, autodiscovery_function, autodiscovery_parameters, autodiscovery_default_poller, have_graph, rrd_structure_rra, rrd_structure_res, rrd_structure_step, graph_default, break_by_card, update_handler, allow_manual_add, sla_default, have_tools, snmp_oid) VALUES (18,'Cisco Voltage',1,1,'cisco_envmib','Voltage,2.1.2,2.1.7',19,0,'','103680',300,1,1,'none',0,1,0,'ent.9');
INSERT INTO interface_types (id, description, autodiscovery_validate, autodiscovery_enabled, autodiscovery_function, autodiscovery_parameters, autodiscovery_default_poller, have_graph, rrd_structure_rra, rrd_structure_res, rrd_structure_step, graph_default, break_by_card, update_handler, allow_manual_add, sla_default, have_tools, snmp_oid) VALUES (19,'Cisco SA Agent',1,1,'cisco_saagent','',20,1,'RRA:AVERAGE:0.5:1:<resolution>','103680',300,39,0,'none',0,1,0,'ent.9');
INSERT INTO interface_types (id, description, autodiscovery_validate, autodiscovery_enabled, autodiscovery_function, autodiscovery_parameters, autodiscovery_default_poller, have_graph, rrd_structure_rra, rrd_structure_res, rrd_structure_step, graph_default, break_by_card, update_handler, allow_manual_add, sla_default, have_tools, snmp_oid) VALUES (20,'Reachable',1,1,'reachability','',21,1,'RRA:AVERAGE:0.5:1:<resolution>','103680',300,41,0,'none',0,1,0,'');
INSERT INTO interface_types (id, description, autodiscovery_validate, autodiscovery_enabled, autodiscovery_function, autodiscovery_parameters, autodiscovery_default_poller, have_graph, rrd_structure_rra, rrd_structure_res, rrd_structure_step, graph_default, break_by_card, update_handler, allow_manual_add, sla_default, have_tools, snmp_oid) VALUES (21,'Linux Traffic Control',1,1,'linux_tc','.1.3.6.1.4.1.2021.5001',22,1,'RRA:AVERAGE:0.5:1:<resolution>','103680',300,43,1,'none',0,1,0,'.');
INSERT INTO interface_types (id, description, autodiscovery_validate, autodiscovery_enabled, autodiscovery_function, autodiscovery_parameters, autodiscovery_default_poller, have_graph, rrd_structure_rra, rrd_structure_res, rrd_structure_step, graph_default, break_by_card, update_handler, allow_manual_add, sla_default, have_tools, snmp_oid) VALUES (22,'NTP',0,1,'ntp_client','',23,0,'RRA:AVERAGE:0.5:1:<resolution>','103680',300,1,0,'none',0,1,0,'');
INSERT INTO interface_types (id, description, autodiscovery_validate, autodiscovery_enabled, autodiscovery_function, autodiscovery_parameters, autodiscovery_default_poller, have_graph, rrd_structure_rra, rrd_structure_res, rrd_structure_step, graph_default, break_by_card, update_handler, allow_manual_add, sla_default, have_tools, snmp_oid) VALUES (23,'UDP Ports',0,0,'tcp_ports','-sU -p1-500,600-1024 --host_timeout 15000',24,1,'RRA:AVERAGE:0.5:1:<resolution>','103680',300,45,0,'tcp_ports',1,1,0,'');
INSERT INTO interface_types (id, description, autodiscovery_validate, autodiscovery_enabled, autodiscovery_function, autodiscovery_parameters, autodiscovery_default_poller, have_graph, rrd_structure_rra, rrd_structure_res, rrd_structure_step, graph_default, break_by_card, update_handler, allow_manual_add, sla_default, have_tools, snmp_oid) VALUES (24,'Compaq Physical Drives',0,1,'cpqmib','phydrv',25,0,'RRA:AVERAGE:0.5:1:<resolution>','103680',300,1,0,'none',0,1,0,'.');
INSERT INTO interface_types (id, description, autodiscovery_validate, autodiscovery_enabled, autodiscovery_function, autodiscovery_parameters, autodiscovery_default_poller, have_graph, rrd_structure_rra, rrd_structure_res, rrd_structure_step, graph_default, break_by_card, update_handler, allow_manual_add, sla_default, have_tools, snmp_oid) VALUES (25,'Compaq Fans',0,1,'cpqmib','fans',26,0,'RRA:AVERAGE:0.5:1:<resolution>','103680',300,1,0,'none',0,1,0,'.');
INSERT INTO interface_types (id, description, autodiscovery_validate, autodiscovery_enabled, autodiscovery_function, autodiscovery_parameters, autodiscovery_default_poller, have_graph, rrd_structure_rra, rrd_structure_res, rrd_structure_step, graph_default, break_by_card, update_handler, allow_manual_add, sla_default, have_tools, snmp_oid) VALUES (26,'Compaq Temperature',0,1,'cpqmib','temperature',27,1,'RRA:AVERAGE:0.5:1:<resolution>','103680',300,46,0,'none',0,1,0,'.');
INSERT INTO interface_types (id, description, autodiscovery_validate, autodiscovery_enabled, autodiscovery_function, autodiscovery_parameters, autodiscovery_default_poller, have_graph, rrd_structure_rra, rrd_structure_res, rrd_structure_step, graph_default, break_by_card, update_handler, allow_manual_add, sla_default, have_tools, snmp_oid) VALUES (27,'IIS Webserver Information',0,1,'iis_info','',28,1,'RRA:AVERAGE:0.5:1:<resolution>','103680',300,50,0,'none',0,1,0,'.');
INSERT INTO interface_types (id, description, autodiscovery_validate, autodiscovery_enabled, autodiscovery_function, autodiscovery_parameters, autodiscovery_default_poller, have_graph, rrd_structure_rra, rrd_structure_res, rrd_structure_step, graph_default, break_by_card, update_handler, allow_manual_add, sla_default, have_tools, snmp_oid) VALUES (28,'Livingston Serial Port',0,1,'livingston_serial_port','',29,1,'RRA:AVERAGE:0.5:1:<resolution>','103680',300,48,0,'none',0,1,0,'.');
INSERT INTO interface_types (id, description, autodiscovery_validate, autodiscovery_enabled, autodiscovery_function, autodiscovery_parameters, autodiscovery_default_poller, have_graph, rrd_structure_rra, rrd_structure_res, rrd_structure_step, graph_default, break_by_card, update_handler, allow_manual_add, sla_default, have_tools, snmp_oid) VALUES (29,'Apache',0,1,'apache','',30,1,'RRA:AVERAGE:0.5:1:<resolution>','103680',300,53,0,'none',1,1,0,'');
INSERT INTO interface_types (id, description, autodiscovery_validate, autodiscovery_enabled, autodiscovery_function, autodiscovery_parameters, autodiscovery_default_poller, have_graph, rrd_structure_rra, rrd_structure_res, rrd_structure_step, graph_default, break_by_card, update_handler, allow_manual_add, sla_default, have_tools, snmp_oid) VALUES (30,'SQL Query',0,1,'none','',32,1,'RRA:AVERAGE:0.5:1:<resolution>','103680',300,65,0,'none',1,1,0,'');
INSERT INTO interface_types (id, description, autodiscovery_validate, autodiscovery_enabled, autodiscovery_function, autodiscovery_parameters, autodiscovery_default_poller, have_graph, rrd_structure_rra, rrd_structure_res, rrd_structure_step, graph_default, break_by_card, update_handler, allow_manual_add, sla_default, have_tools, snmp_oid) VALUES (31,'APC',1,1,'apc','enterprises.318',31,1,'RRA:AVERAGE:0.5:1:<resolution>','103680',300,61,0,'none',0,1,0,'.');
INSERT INTO interface_types (id, description, autodiscovery_validate, autodiscovery_enabled, autodiscovery_function, autodiscovery_parameters, autodiscovery_default_poller, have_graph, rrd_structure_rra, rrd_structure_res, rrd_structure_step, graph_default, break_by_card, update_handler, allow_manual_add, sla_default, have_tools, snmp_oid) VALUES (32,'Alteon Real Server',1,1,'alteon_realservers','',33,1,'RRA:AVERAGE:0.5:1:<resolution>','103680',300,66,0,'none',0,1,0,'ent.1872');
INSERT INTO interface_types (id, description, autodiscovery_validate, autodiscovery_enabled, autodiscovery_function, autodiscovery_parameters, autodiscovery_default_poller, have_graph, rrd_structure_rra, rrd_structure_res, rrd_structure_step, graph_default, break_by_card, update_handler, allow_manual_add, sla_default, have_tools, snmp_oid) VALUES (33,'Alteon Virtual Server',0,1,'alteon_virtualservers','',34,1,'RRA:AVERAGE:0.5:1:<resolution>','103680',300,70,0,'none',0,1,0,'ent.1872');
INSERT INTO interface_types (id, description, autodiscovery_validate, autodiscovery_enabled, autodiscovery_function, autodiscovery_parameters, autodiscovery_default_poller, have_graph, rrd_structure_rra, rrd_structure_res, rrd_structure_step, graph_default, break_by_card, update_handler, allow_manual_add, sla_default, have_tools, snmp_oid) VALUES (34,'Alteon Real Services',0,1,'alteon_realservices','',35,1,'RRA:AVERAGE:0.5:1:<resolution>','103680',300,73,0,'none',0,1,0,'ent.1872');
INSERT INTO interface_types (id, description, autodiscovery_validate, autodiscovery_enabled, autodiscovery_function, autodiscovery_parameters, autodiscovery_default_poller, have_graph, rrd_structure_rra, rrd_structure_res, rrd_structure_step, graph_default, break_by_card, update_handler, allow_manual_add, sla_default, have_tools, snmp_oid) VALUES (35,'Alteon System Info',1,1,'host_information','enterprises.1872',36,1,'RRA:AVERAGE:0.5:1:<resolution>','103680',300,75,0,'none',0,1,0,'ent.1872');
INSERT INTO interface_types (id, description, autodiscovery_validate, autodiscovery_enabled, autodiscovery_function, autodiscovery_parameters, autodiscovery_default_poller, have_graph, rrd_structure_rra, rrd_structure_res, rrd_structure_step, graph_default, break_by_card, update_handler, allow_manual_add, sla_default, have_tools, snmp_oid) VALUES (36,'Brocade Sensors',0,0,'brocade_sensors','',37,1,'RRA:AVERAGE:0.5:1:<resolution>','103680',300,77,0,'none',0,1,0,'ent.1588');
INSERT INTO interface_types (id, description, autodiscovery_validate, autodiscovery_enabled, autodiscovery_function, autodiscovery_parameters, autodiscovery_default_poller, have_graph, rrd_structure_rra, rrd_structure_res, rrd_structure_step, graph_default, break_by_card, update_handler, allow_manual_add, sla_default, have_tools, snmp_oid) VALUES (37,'Brocade FC Ports',0,0,'brocade_fcports','',38,1,'RRA:AVERAGE:0.5:1:<resolution>','103680',300,78,0,'none',0,1,0,'ent.1588');
INSERT INTO interface_types (id, description, autodiscovery_validate, autodiscovery_enabled, autodiscovery_function, autodiscovery_parameters, autodiscovery_default_poller, have_graph, rrd_structure_rra, rrd_structure_res, rrd_structure_step, graph_default, break_by_card, update_handler, allow_manual_add, sla_default, have_tools, snmp_oid) VALUES (38,'Cisco Dialup Usage',1,1,'cisco_serial_port','',39,1,'RRA:AVERAGE:0.5:1:<resolution>','103680',300,80,0,'none',0,1,0,'ent.9');
INSERT INTO interface_types (id, description, autodiscovery_validate, autodiscovery_enabled, autodiscovery_function, autodiscovery_parameters, autodiscovery_default_poller, have_graph, rrd_structure_rra, rrd_structure_res, rrd_structure_step, graph_default, break_by_card, update_handler, allow_manual_add, sla_default, have_tools, snmp_oid) VALUES (39,'Windows Logical Disks',1,1,'informant_ldisks','',40,1,'RRA:AVERAGE:0.5:1:<resolution>','103680',300,82,0,'none',0,1,0,'.');
INSERT INTO interface_types (id, description, autodiscovery_validate, autodiscovery_enabled, autodiscovery_function, autodiscovery_parameters, autodiscovery_default_poller, have_graph, rrd_structure_rra, rrd_structure_res, rrd_structure_step, graph_default, break_by_card, update_handler, allow_manual_add, sla_default, have_tools, snmp_oid) VALUES (40,'UPS',1,1,'ups','',41,1,'RRA:AVERAGE:0.5:1:<resolution>','103680',300,84,0,'none',0,1,0,'.');
INSERT INTO interface_types (id, description, autodiscovery_validate, autodiscovery_enabled, autodiscovery_function, autodiscovery_parameters, autodiscovery_default_poller, have_graph, rrd_structure_rra, rrd_structure_res, rrd_structure_step, graph_default, break_by_card, update_handler, allow_manual_add, sla_default, have_tools, snmp_oid) VALUES (41,'UPS Lines',0,1,'ups_lines','',42,1,'RRA:AVERAGE:0.5:1:<resolution>','103680',300,85,0,'none',0,1,0,'.');
INSERT INTO interface_types (id, description, autodiscovery_validate, autodiscovery_enabled, autodiscovery_function, autodiscovery_parameters, autodiscovery_default_poller, have_graph, rrd_structure_rra, rrd_structure_res, rrd_structure_step, graph_default, break_by_card, update_handler, allow_manual_add, sla_default, have_tools, snmp_oid) VALUES (42,'IPTables Chains',1,1,'linux_iptables','.1.3.6.1.4.1.2021.5002',43,1,'RRA:AVERAGE:0.5:1:<resolution>','103680',300,89,1,'none',0,1,0,'.');
INSERT INTO interface_types (id, description, autodiscovery_validate, autodiscovery_enabled, autodiscovery_function, autodiscovery_parameters, autodiscovery_default_poller, have_graph, rrd_structure_rra, rrd_structure_res, rrd_structure_step, graph_default, break_by_card, update_handler, allow_manual_add, sla_default, have_tools, snmp_oid) VALUES (43,'Cisco PIX',1,1,'pix_connections','',44,1,'RRA:AVERAGE:0.5:1:<resolution>','103680',300,91,0,'none',0,1,0,'ent.9');
INSERT INTO interface_types (id, description, autodiscovery_validate, autodiscovery_enabled, autodiscovery_function, autodiscovery_parameters, autodiscovery_default_poller, have_graph, rrd_structure_rra, rrd_structure_res, rrd_structure_step, graph_default, break_by_card, update_handler, allow_manual_add, sla_default, have_tools, snmp_oid) VALUES (44,'Cisco NAT',0,1,'simple','.1.3.6.1.4.1.9.10.77.1.2.1.0,NAT',45,1,'RRA:AVERAGE:0.5:1:<resolution>','103680',300,93,0,'none',0,1,0,'ent.9');
INSERT INTO interface_types (id, description, autodiscovery_validate, autodiscovery_enabled, autodiscovery_function, autodiscovery_parameters, autodiscovery_default_poller, have_graph, rrd_structure_rra, rrd_structure_res, rrd_structure_step, graph_default, break_by_card, update_handler, allow_manual_add, sla_default, have_tools, snmp_oid) VALUES (45,'Sensors',1,1,'sensors','',46,1,'RRA:AVERAGE:0.5:1:<resolution>','103680',300,94,1,'none',0,1,0,'.');
INSERT INTO interface_types (id, description, autodiscovery_validate, autodiscovery_enabled, autodiscovery_function, autodiscovery_parameters, autodiscovery_default_poller, have_graph, rrd_structure_rra, rrd_structure_res, rrd_structure_step, graph_default, break_by_card, update_handler, allow_manual_add, sla_default, have_tools, snmp_oid) VALUES (46,'OS/400 System Info',1,1,'simple','.1.3.6.1.4.1.2.6.4.5.1.0,OS400',47,1,'RRA:AVERAGE:0.5:1:<resolution>','103680',300,95,0,'none',0,1,0,'.');
INSERT INTO interface_types (id, description, autodiscovery_validate, autodiscovery_enabled, autodiscovery_function, autodiscovery_parameters, autodiscovery_default_poller, have_graph, rrd_structure_rra, rrd_structure_res, rrd_structure_step, graph_default, break_by_card, update_handler, allow_manual_add, sla_default, have_tools, snmp_oid) VALUES (47,'Dell Chassis',1,1,'simple','.1.3.6.1.4.1.674.10892.1.200.10.1.2.1,Chassis status',48,0,'RRA:AVERAGE:0.5:1:<resolution>','103680',300,1,0,'none',0,1,0,'.');
INSERT INTO interface_types (id, description, autodiscovery_validate, autodiscovery_enabled, autodiscovery_function, autodiscovery_parameters, autodiscovery_default_poller, have_graph, rrd_structure_rra, rrd_structure_res, rrd_structure_step, graph_default, break_by_card, update_handler, allow_manual_add, sla_default, have_tools, snmp_oid) VALUES (48,'PDU',1,1,'pdu','',49,0,'RRA:AVERAGE:0.5:1:<resolution>','103680',300,1,0,'none',0,1,0,'.');
INSERT INTO interface_types (id, description, autodiscovery_validate, autodiscovery_enabled, autodiscovery_function, autodiscovery_parameters, autodiscovery_default_poller, have_graph, rrd_structure_rra, rrd_structure_res, rrd_structure_step, graph_default, break_by_card, update_handler, allow_manual_add, sla_default, have_tools, snmp_oid) VALUES (49,'PDU Banks',0,1,'pdu_banks','',50,1,'RRA:AVERAGE:0.5:1:<resolution>','103680',300,99,0,'none',0,1,0,'.');
INSERT INTO interface_types (id, description, autodiscovery_validate, autodiscovery_enabled, autodiscovery_function, autodiscovery_parameters, autodiscovery_default_poller, have_graph, rrd_structure_rra, rrd_structure_res, rrd_structure_step, graph_default, break_by_card, update_handler, allow_manual_add, sla_default, have_tools, snmp_oid) VALUES (50,'IBM Component Health',1,0,'ibm_ComponentHealth','',57,0,'RRA:AVERAGE:0.5:1:<resolution>','103680',300,1,0,'none',0,1,0,'.');
INSERT INTO interface_types (id, description, autodiscovery_validate, autodiscovery_enabled, autodiscovery_function, autodiscovery_parameters, autodiscovery_default_poller, have_graph, rrd_structure_rra, rrd_structure_res, rrd_structure_step, graph_default, break_by_card, update_handler, allow_manual_add, sla_default, have_tools, snmp_oid) VALUES (51,'IBM Blade server',1,0,'ibm_blade_servers','',51,1,'RRA:AVERAGE:0.5:1:<resolution>','103680',300,106,0,'none',0,1,0,'.');
INSERT INTO interface_types (id, description, autodiscovery_validate, autodiscovery_enabled, autodiscovery_function, autodiscovery_parameters, autodiscovery_default_poller, have_graph, rrd_structure_rra, rrd_structure_res, rrd_structure_step, graph_default, break_by_card, update_handler, allow_manual_add, sla_default, have_tools, snmp_oid) VALUES (52,'Generic FC Ports',1,0,'fc_ports','',52,1,'RRA:AVERAGE:0.5:1:<resolution>','103680',300,105,0,'none',0,1,0,'.');
INSERT INTO interface_types (id, description, autodiscovery_validate, autodiscovery_enabled, autodiscovery_function, autodiscovery_parameters, autodiscovery_default_poller, have_graph, rrd_structure_rra, rrd_structure_res, rrd_structure_step, graph_default, break_by_card, update_handler, allow_manual_add, sla_default, have_tools, snmp_oid) VALUES (53,'Cisco Wireless Device',1,0,'simple','.1.3.6.1.4.1.9.9.273.1.1.2.1.1.1,Cisco AP',53,1,'RRA:AVERAGE:0.5:1\r\n:<resolution>','103680',300,101,0,'none',0,1,0,'ent.9');
INSERT INTO interface_types (id, description, autodiscovery_validate, autodiscovery_enabled, autodiscovery_function, autodiscovery_parameters, autodiscovery_default_poller, have_graph, rrd_structure_rra, rrd_structure_res, rrd_structure_step, graph_default, break_by_card, update_handler, allow_manual_add, sla_default, have_tools, snmp_oid) VALUES (54,'IBM Blade Power',1,0,'ibm_blade_power','',54,1,'RRA:AVERAGE:0.5:1:<resolution>','103680',300,107,0,'none',0,1,0,'.');
INSERT INTO interface_types (id, description, autodiscovery_validate, autodiscovery_enabled, autodiscovery_function, autodiscovery_parameters, autodiscovery_default_poller, have_graph, rrd_structure_rra, rrd_structure_res, rrd_structure_step, graph_default, break_by_card, update_handler, allow_manual_add, sla_default, have_tools, snmp_oid) VALUES (55,'Compaq Power Supply',1,0,'cpqmib','powersupply',55,0,'RRA:AVERAGE:0.5:1:<resolution>','103680',300,1,0,'none',0,1,0,'.');
INSERT INTO interface_types (id, description, autodiscovery_validate, autodiscovery_enabled, autodiscovery_function, autodiscovery_parameters, autodiscovery_default_poller, have_graph, rrd_structure_rra, rrd_structure_res, rrd_structure_step, graph_default, break_by_card, update_handler, allow_manual_add, sla_default, have_tools, snmp_oid) VALUES (56,'IBM Storage Controller',0,0,'ibm_ds_storage','storagesubsystem',56,0,'RRA:AVERAGE:0.5:1:<resolution>','103680',300,1,0,'none',0,1,0,'.');
INSERT INTO interface_types (id, description, autodiscovery_validate, autodiscovery_enabled, autodiscovery_function, autodiscovery_parameters, autodiscovery_default_poller, have_graph, rrd_structure_rra, rrd_structure_res, rrd_structure_step, graph_default, break_by_card, update_handler, allow_manual_add, sla_default, have_tools, snmp_oid) VALUES (57,'Informant Disks 64',1,1,'informant_adv_ldisks','',58,1,'RRA:AVERAGE:0.5:1:<resolution>','103680',300,102,0,'none',0,1,0,'.');
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE interface_types_field_types (
  id int4 NOT NULL AUTO_INCREMENT,
  description char(30) NOT NULL DEFAULT '',
  handler char(30) NOT NULL DEFAULT '',
  PRIMARY KEY (id)
);
/*!40101 SET character_set_client = @saved_cs_client */;
INSERT INTO interface_types_field_types (id, description, handler) VALUES (1,'Unknown','none');
INSERT INTO interface_types_field_types (id, description, handler) VALUES (3,'Index','text');
INSERT INTO interface_types_field_types (id, description, handler) VALUES (5,'Boolean','bool');
INSERT INTO interface_types_field_types (id, description, handler) VALUES (7,'Description','text');
INSERT INTO interface_types_field_types (id, description, handler) VALUES (8,'Other','text');
INSERT INTO interface_types_field_types (id, description, handler) VALUES (20,'RRDTool DS','rrd_ds');
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE interface_types_fields (
  id int4 NOT NULL AUTO_INCREMENT,
  description char(40) NOT NULL DEFAULT '',
  name char(40) NOT NULL DEFAULT '',
  pos int4 NOT NULL DEFAULT '10',
  itype int4 NOT NULL DEFAULT '1',
  ftype int4 NOT NULL DEFAULT '1',
  showable int2 NOT NULL DEFAULT '1',
  overwritable int2 NOT NULL DEFAULT '1',
  tracked int2 NOT NULL DEFAULT '0',
  default_value char(250) NOT NULL DEFAULT '',
  PRIMARY KEY (id)
);
/*!40101 SET character_set_client = @saved_cs_client */;
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (1,'Unknown','unknown',10,1,1,0,0,0,'');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (3,'SNMP IFIndex','interfacenumber',60,4,3,1,1,0,'');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (4,'Description','description',10,4,7,1,1,1,'');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (5,'IP Address','address',30,4,7,1,1,0,'');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (6,'Input Bandwidth','bandwidthin',50,4,8,1,1,0,'1');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (7,'Port Number','port',10,2,3,1,1,0,'');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (9,'Check Content','check_content',30,2,5,2,1,0,'0');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (10,'Port Description','description',20,2,7,1,1,0,'');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (11,'Input Bytes','input',10,4,20,0,0,0,'DS:input:COUNTER:600:0:<bandwidth>');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (12,'Established Connections','tcp_conn_number',10,2,20,0,0,0,'DS:tcp_conn_number:GAUGE:600:0:10000');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (13,'Check Content URL','check_url',40,2,8,2,1,0,'');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (14,'Output Bytes','output',20,4,20,0,0,0,'DS:output:COUNTER:600:0:<bandwidth>');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (15,'Input Packets','inpackets',70,4,20,0,0,0,'DS:inpackets:COUNTER:600:0:<bandwidth>');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (16,'Flip In Out in Graphs','flipinout',70,4,5,2,1,0,'0');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (20,'Connection Delay','conn_delay',20,2,20,0,0,0,'DS:conn_delay:GAUGE:600:0:100000');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (21,'Output Bandwidth','bandwidthout',51,4,8,2,1,0,'1');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (22,'Peer Address','peer',36,4,8,1,1,0,'');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (23,'Input Errors','inputerrors',30,4,20,0,0,0,'DS:inputerrors:COUNTER:600:0:<bandwidth>');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (24,'Output Errors','outputerrors',40,4,20,0,0,0,'DS:outputerrors:COUNTER:600:0:<bandwidth>');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (25,'Round Trip Time','rtt',50,4,20,0,0,0,'DS:rtt:GAUGE:600:0:10000');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (26,'PacketLoss','packetloss',60,4,20,0,0,0,'DS:packetloss:GAUGE:600:0:1000');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (27,'Output Packets','outpackets',80,4,20,0,0,0,'DS:outpackets:COUNTER:600:0:<bandwidth>');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (28,'Drops','drops',90,4,20,0,0,0,'DS:drops:COUNTER:600:0:<bandwidth>');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (29,'Not Used','aux4',100,4,20,0,0,0,'DS:aux4:COUNTER:600:0:<bandwidth>');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (30,'Saved Input Bandwidth','bandwidthin',110,4,20,0,0,0,'DS:bandwidthin:GAUGE:600:0:<bandwidth>');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (31,'Saved Output Bandwidth','bandwidthout',120,4,20,0,0,0,'DS:bandwidthout:GAUGE:600:0:<bandwidth>');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (32,'Index','index',10,12,3,0,0,0,'');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (33,'Description','description',20,12,7,1,0,1,'System Information');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (34,'Number of Processors','cpu_num',30,12,8,1,1,0,'1');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (35,'Index','index',10,11,3,0,0,0,'');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (36,'Description','description',20,11,7,1,0,1,'System Information');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (37,'Number of CPUs','cpu_num',30,11,8,1,1,0,'1');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (43,'CPU','cpu',10,3,20,0,0,0,'DS:cpu:GAUGE:600:0:100');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (44,'Mem Used','mem_used',20,3,20,0,0,0,'DS:mem_used:GAUGE:600:0:100000000000');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (45,'Mem Free','mem_free',30,3,20,0,0,0,'DS:mem_free:GAUGE:600:0:100000000000');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (46,'Acct Packets','acct_packets',40,3,20,0,0,0,'DS:acct_packets:ABSOLUTE:600:0:100000000000');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (47,'Acct Bytes','acct_bytes',50,3,20,0,0,0,'DS:acct_bytes:GAUGE:600:0:100000000000');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (48,'Tcp Active','tcp_active',60,3,20,0,0,0,'DS:tcp_active:COUNTER:600:0:10000000');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (49,'Tcp Passive','tcp_passive',70,3,20,0,0,0,'DS:tcp_passive:COUNTER:600:0:1000000');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (50,'Tcp Established','tcp_established',80,3,20,0,0,0,'DS:tcp_established:COUNTER:600:0:1000000');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (51,'Bgpin','bgpin',10,6,20,0,0,0,'DS:bgpin:COUNTER:600:0:10000000');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (52,'Bgpout','bgpout',20,6,20,0,0,0,'DS:bgpout:COUNTER:600:0:10000000');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (53,'Bgpuptime','bgpuptime',30,6,20,0,0,0,'DS:bgpuptime:GAUGE:600:0:10000000');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (56,'Storage Block Size','storage_block_size',10,8,20,0,0,0,'DS:storage_block_size:GAUGE:600:0:<size>');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (57,'Storage Block Count','storage_block_count',20,8,20,0,0,0,'DS:storage_block_count:GAUGE:600:0:<size>');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (58,'Storage Used Blocks','storage_used_blocks',30,8,20,0,0,0,'DS:storage_used_blocks:GAUGE:600:0:<size>');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (59,'Output','output',10,9,20,0,0,0,'DS:output:COUNTER:600:0:<bandwidth>');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (60,'Hits','hits',20,9,20,0,0,0,'DS:hits:COUNTER:600:0:<bandwidth>');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (61,'Cpu User Ticks','cpu_user_ticks',10,10,20,0,0,0,'DS:cpu_user_ticks:COUNTER:600:0:86400');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (62,'Cpu Idle Ticks','cpu_idle_ticks',20,10,20,0,0,0,'DS:cpu_idle_ticks:COUNTER:600:0:86400');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (63,'Cpu Wait Ticks','cpu_wait_ticks',30,10,20,0,0,0,'DS:cpu_wait_ticks:COUNTER:600:0:86400');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (64,'Cpu Kernel Ticks','cpu_kernel_ticks',40,10,20,0,0,0,'DS:cpu_kernel_ticks:COUNTER:600:0:86400');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (65,'Swap Total','swap_total',50,10,20,0,0,0,'DS:swap_total:GAUGE:600:0:10000000000');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (66,'Swap Available','swap_available',60,10,20,0,0,0,'DS:swap_available:GAUGE:600:0:10000000000');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (67,'Mem Total','mem_total',70,10,20,0,0,0,'DS:mem_total:GAUGE:600:0:10000000000');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (68,'Mem Available','mem_available',80,10,20,0,0,0,'DS:mem_available:GAUGE:600:0:10000000000');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (69,'Load Average 1','load_average_1',90,10,20,0,0,0,'DS:load_average_1:GAUGE:600:0:1000');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (70,'Load Average 5','load_average_5',100,10,20,0,0,0,'DS:load_average_5:GAUGE:600:0:1000');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (71,'Load Average 15','load_average_15',110,10,20,0,0,0,'DS:load_average_15:GAUGE:600:0:1000');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (72,'Cpu User Ticks','cpu_user_ticks',10,11,20,0,0,0,'DS:cpu_user_ticks:COUNTER:600:0:86400');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (73,'Cpu Idle Ticks','cpu_idle_ticks',20,11,20,0,0,0,'DS:cpu_idle_ticks:COUNTER:600:0:86400');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (74,'Cpu Nice Ticks','cpu_nice_ticks',30,11,20,0,0,0,'DS:cpu_nice_ticks:COUNTER:600:0:86400');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (75,'Cpu System Ticks','cpu_system_ticks',40,11,20,0,0,0,'DS:cpu_system_ticks:COUNTER:600:0:86400');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (76,'Load Average 1','load_average_1',50,11,20,0,0,0,'DS:load_average_1:GAUGE:600:0:1000');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (77,'Load Average 5','load_average_5',60,11,20,0,0,0,'DS:load_average_5:GAUGE:600:0:1000');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (78,'Load Average 15','load_average_15',70,11,20,0,0,0,'DS:load_average_15:GAUGE:600:0:1000');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (79,'Num Users','num_users',80,11,20,0,0,0,'DS:num_users:GAUGE:600:0:100000');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (80,'Num Procs','num_procs',90,11,20,0,0,0,'DS:num_procs:GAUGE:600:0:10000000');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (81,'Tcp Active','tcp_active',100,11,20,0,0,0,'DS:tcp_active:COUNTER:600:0:10000000');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (82,'Tcp Passive','tcp_passive',110,11,20,0,0,0,'DS:tcp_passive:COUNTER:600:0:1000000');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (83,'Tcp Established','tcp_established',120,11,20,0,0,0,'DS:tcp_established:COUNTER:600:0:1000000');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (84,'CPU','cpu',10,12,20,0,0,0,'DS:cpu:GAUGE:600:0:100');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (85,'Num Users','num_users',20,12,20,0,0,0,'DS:num_users:GAUGE:600:0:100000');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (86,'Num Procs','num_procs',30,12,20,0,0,0,'DS:num_procs:GAUGE:600:0:10000000');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (87,'Tcp Active','tcp_active',40,12,20,0,0,0,'DS:tcp_active:COUNTER:600:0:10000000');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (88,'Tcp Passive','tcp_passive',50,12,20,0,0,0,'DS:tcp_passive:COUNTER:600:0:1000000');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (89,'Tcp Established','tcp_established',60,12,20,0,0,0,'DS:tcp_established:COUNTER:600:0:1000000');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (90,'Input','input',10,13,20,0,0,0,'DS:input:COUNTER:600:0:10000000000');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (91,'Output','output',20,13,20,0,0,0,'DS:output:COUNTER:600:0:10000000000');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (92,'Inputpackets','inputpackets',30,13,20,0,0,0,'DS:inputpackets:COUNTER:600:0:10000000000');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (93,'Outputpackets','outputpackets',40,13,20,0,0,0,'DS:outputpackets:COUNTER:600:0:10000000000');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (94,'RTT','rtt',10,14,20,0,0,0,'DS:rtt:GAUGE:600:0:10000');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (95,'Packetloss','packetloss',20,14,20,0,0,0,'DS:packetloss:GAUGE:600:0:1000');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (98,'Temperature','temperature',10,17,20,0,0,0,'DS:temperature:GAUGE:600:0:100');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (100,'Forward Jitter','forward_jitter',10,19,20,0,0,0,'DS:forward_jitter:GAUGE:600:0:100');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (101,'Backward Jitter','backward_jitter',20,19,20,0,0,0,'DS:backward_jitter:GAUGE:600:0:100');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (102,'Rt Latency','rt_latency',30,19,20,0,0,0,'DS:rt_latency:GAUGE:600:0:100');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (103,'Forward Packetloss','forward_packetloss',40,19,20,0,0,0,'DS:forward_packetloss:GAUGE:600:0:100');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (104,'Backward Packetloss','backward_packetloss',50,19,20,0,0,0,'DS:backward_packetloss:GAUGE:600:0:100');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (105,'RTT','rtt',10,20,20,0,0,0,'DS:rtt:GAUGE:600:0:10000');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (106,'Packetloss','packetloss',20,20,20,0,0,0,'DS:packetloss:GAUGE:600:0:1000');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (107,'Bytes','bytes',10,21,20,0,0,0,'DS:bytes:COUNTER:600:0:<ceil>');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (108,'Packets','packets',20,21,20,0,0,0,'DS:packets:COUNTER:600:0:<ceil>');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (109,'Disk Type','storage_type',10,8,7,1,0,1,'');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (110,'Size (Bytes)','size',20,8,7,1,1,1,'0');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (111,'Description','description',30,8,7,1,1,0,'');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (112,'Index','index',40,8,3,0,0,0,'');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (113,'Index','index',10,10,3,1,0,0,'');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (114,'Description','description',20,10,7,1,0,1,'System Information');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (115,'Number of Processors','cpu_num',30,10,8,1,1,0,'1');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (116,'Index','index',10,14,3,1,0,0,'');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (117,'Description','description',20,14,7,1,1,0,'');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (118,'Index','index',10,20,3,0,0,0,'');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (119,'Description','description',20,20,7,1,0,1,'');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (120,'Rate','rate',20,21,7,1,0,1,'');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (121,'Ceil','ceil',30,21,7,1,0,1,'');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (122,'Index','index',50,21,3,1,0,0,'');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (123,'Description','description',40,21,7,2,1,0,'');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (124,'Index','index',10,9,3,1,0,0,'');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (125,'Owner','owner',20,9,7,1,0,1,'');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (126,'VIP Address','address',30,9,7,1,0,1,'');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (127,'Bandwidth','bandwidth',40,9,8,1,1,0,'1');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (128,'Description','description',20,18,7,1,1,0,'');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (129,'Description','description',20,17,7,1,1,0,'');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (130,'Description','description',20,16,7,1,1,0,'');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (131,'Index','index',30,18,3,1,0,0,'');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (132,'Index','index',30,17,3,1,0,0,'');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (133,'Index','index',10,16,3,1,0,0,'');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (134,'Number of Processors','cpu_num',30,3,8,1,1,0,'1');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (135,'Index','index',10,3,3,1,0,0,'');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (136,'Description','description',20,3,7,1,0,1,'System Information');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (137,'Index','index',10,19,3,1,0,0,'');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (138,'Description','description',20,19,7,1,1,0,'');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (139,'Description','description',20,13,7,1,1,0,'');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (140,'Index','index',35,13,3,1,0,0,'');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (141,'MAC Address','mac',30,13,8,0,0,1,'');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (142,'Interface Index','ifindex',40,13,8,2,0,1,'');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (143,'IP Address','address',25,13,7,1,0,1,'');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (144,'Flip Graph In/Out','flipinout',45,13,5,2,1,0,'0');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (145,'Local IP','local',30,6,7,1,0,1,'');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (146,'Remote IP','remote',40,6,3,1,0,0,'');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (147,'Autonomous System','asn',20,6,7,1,0,1,'');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (149,'Description','description',50,6,7,2,1,0,'');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (150,'Process Name','process_name',10,15,3,2,0,0,'');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (151,'Description','description',20,15,7,1,1,0,'');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (153,'Instances at Discovery','instances',30,15,8,1,1,0,'1');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (154,'Pings to Send','pings',80,4,8,2,1,0,'50');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (155,'Pings to Send','pings',30,20,8,2,1,0,'50');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (156,'PL Threshold %','threshold',40,20,8,2,1,0,'70');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (157,'Interval (ms)','interval',50,20,8,2,1,0,'300');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (158,'Check Content RegExp','check_regexp',50,2,8,2,1,0,'');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (159,'Current Instances','current_instances',10,15,20,0,0,0,'DS:current_instances:GAUGE:600:0:99999');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (160,'Index','index',10,22,3,0,0,0,'1');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (161,'Fixed Admin Status','fixed_admin_status',99,4,5,2,1,0,'0');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (162,'Usage Threshold %','usage_threshold',40,8,8,2,1,0,'80');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (164,'Used Memory','used_memory',20,15,20,0,0,0,'DS:used_memory:GAUGE:600:0:9999999');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (165,'IP Mask','mask',32,4,8,2,1,1,'');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (167,'Show in Celcius','show_celcius',40,17,5,2,1,0,'1');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (168,'Port Number','port',10,23,3,1,1,0,'');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (169,'Port Description','description',20,23,7,1,1,0,'');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (170,'Connection Delay','conn_delay',10,23,20,0,0,0,'DS:conn_delay:GAUGE:600:0:100000');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (171,'Controller','controller',10,24,7,1,0,1,'');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (172,'Drive','drvindex',11,24,7,1,0,1,'');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (173,'Drive Model','model',15,24,8,1,0,1,'');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (174,'Index','index',5,24,3,0,0,0,'');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (175,'Index','index',5,25,3,0,0,0,'');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (176,'Chassis','chassis',10,25,8,1,0,1,'');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (177,'Fan','fanindex',11,25,8,1,0,1,'');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (178,'Location','location',20,25,7,1,0,1,'');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (179,'Index','index',5,26,3,0,1,0,'');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (180,'Chassis','chassis',10,26,7,1,0,1,'');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (181,'Sensor','tempindex',11,26,7,1,0,1,'');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (182,'Location','location',2,26,7,1,0,1,'');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (183,'Temperature','temperature',80,26,20,0,0,0,'DS:temperature:GAUGE:600:0:1000');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (184,'Index','index',10,27,3,0,0,0,'1');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (185,'Total Bytes Received','tbr',10,27,20,0,0,0,'DS:tbr:COUNTER:600:0:999999999');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (186,'Total CGI Requests','tcgir',20,27,20,0,0,0,'DS:tcgir:COUNTER:600:0:999999999');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (187,'Total Files Sent','tfs',30,27,20,0,0,0,'DS:tfs:COUNTER:600:0:999999999');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (188,'Total Gets','tg',40,27,20,0,0,0,'DS:tg:COUNTER:600:0:999999999');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (189,'Total Posts','tp',50,27,20,0,0,0,'DS:tp:COUNTER:600:0:999999999');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (190,'Serial Lines Free','pm_serial_free',10,28,20,1,0,0,'DS:pm_serial_free:GAUGE:600:0:5000');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (191,'Serial Lines Connecting','pm_serial_connecting',20,28,20,1,0,0,'DS:pm_serial_connecting:GAUGE:600:0:5000');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (192,'Serial Lines Established','pm_serial_established',30,28,20,1,0,0,'DS:pm_serial_established:GAUGE:600:0:5000');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (193,'Serial Lines Disconnecting','pm_serial_disconnecting',40,28,20,1,0,0,'DS:pm_serial_disconnecting:GAUGE:600:0:5000');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (194,'Serial Lines Command','pm_serial_command',50,28,20,1,0,0,'DS:pm_serial_command:GAUGE:600:0:5000');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (195,'Serial Lines NoService','pm_serial_noservice',60,28,20,1,0,0,'DS:pm_serial_noservice:GAUGE:600:0:5000');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (196,'Index','index',10,28,3,0,0,0,'1');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (197,'Process Threshold','proc_threshold',40,12,8,2,1,0,'100');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (199,'Description','description',20,29,7,2,1,0,'Apache Stats');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (200,'Total Accesses','tac',30,29,20,0,0,0,'DS:tac:COUNTER:600:0:100000');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (201,'IP:Port','ip_port',10,29,3,1,1,0,'');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (202,'Total KBytes','tkb',20,29,20,0,0,0,'DS:tkb:COUNTER:600:0:10000000');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (203,'CPU Load','cplo',60,29,20,0,0,0,'DS:cplo:GAUGE:600:0:1000');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (204,'Uptime','up',10,29,20,0,0,0,'DS:up:GAUGE:600:0:99999999999999');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (205,'Bytes Per Request','bpr',40,29,20,0,0,0,'DS:bpr:GAUGE:600:0:10000000');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (208,'Busy Workers','bw',90,29,20,0,0,0,'DS:bw:GAUGE:600:0:1000');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (209,'Idle Workers','iw',50,29,20,0,0,0,'DS:iw:GAUGE:600:0:1000');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (210,'Index','index',10,31,3,0,0,0,'1');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (211,'Description','description',20,31,7,1,0,1,'');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (212,'Battery Capacity','capacity',10,31,20,0,0,0,'DS:capacity:GAUGE:600:0:100');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (213,'Output Load','load',20,31,20,0,0,0,'DS:load:GAUGE:600:0:100');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (214,'Input Voltage','in_voltage',31,31,20,0,0,0,'DS:in_voltage:GAUGE:600:0:400');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (215,'Output Voltage','out_voltage',40,31,20,0,0,0,'DS:out_voltage:GAUGE:600:0:400');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (216,'Time Remaining','time_remaining',50,31,20,0,0,0,'DS:time_remaining:GAUGE:600:0:9999999999');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (217,'Temperature','temperature',60,31,20,0,0,0,'DS:temperature:GAUGE:600:0:200');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (218,'Show Temp in Celcius','show_celcius',31,31,5,2,1,0,'1');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (220,'Index','index',10,30,3,1,1,0,'1');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (221,'Description','description',20,30,7,1,1,0,'');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (222,'DSN','dsn',30,30,8,2,1,0,'');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (223,'Username','username',40,30,8,2,1,0,'');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (224,'Password','password',50,30,8,2,1,0,'');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (225,'Max Records','max_records',60,30,8,2,1,0,'');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (226,'Min Records','min_records',70,30,8,2,1,0,'');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (227,'Counter Records','records_counter',10,30,20,0,0,0,'DS:records_counter:COUNTER:600:0:9999999999');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (228,'Query','query',55,30,8,2,1,0,'');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (229,'Absolute Records','records_absolute',20,30,20,0,0,0,'DS:records_absolute:GAUGE:600:0:9999999999');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (230,'Is Absolute?','absolute',80,30,5,1,1,0,'0');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (231,'Percentile','percentile',55,4,8,2,1,0,'');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (232,'Index','index',10,32,3,0,0,0,'');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (233,'Hostname','hostname',20,32,7,1,1,0,'');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (234,'Max Connections','max_connections',30,32,8,2,1,0,'');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (235,'Total Sessions','total_sessions',50,32,20,0,0,0,'DS:total_sessions:COUNTER:600:0:100000000');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (236,'Current Sessions','current_sessions',55,32,20,0,0,0,'DS:current_sessions:GAUGE:600:0:<max_connections>');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (237,'Failures','failures',60,32,20,0,0,0,'DS:failures:COUNTER:600:0:10000');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (238,'Octets','octets',65,32,20,0,0,0,'DS:octets:COUNTER:600:0:1000000000');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (239,'Index','index',10,33,3,0,0,1,'');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (240,'Hostname','hostname',20,33,7,1,1,0,'');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (241,'Total Sessions','total_sessions',30,33,20,0,0,0,'DS:total_sessions:COUNTER:600:0:100000000');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (242,'Current Sessions','current_sessions',35,33,20,0,0,0,'DS:current_sessions:GAUGE:600:0:20000');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (243,'Octets','octets',40,33,20,0,0,0,'DS:octets:COUNTER:600:0:1000000000');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (244,'Index','index',10,34,3,0,0,0,'');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (245,'Hostname','hostname',20,34,7,1,0,0,'');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (246,'Address','address',30,34,7,1,0,0,'');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (247,'Port','port',35,34,7,1,0,0,'');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (248,'Real Server','real_server',15,34,8,0,0,1,'');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (249,'Response Time','response_time',40,34,20,0,0,0,'DS:response_time:GAUGE:600:0:10000');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (250,'Index','index',10,35,3,0,0,0,'');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (251,'Description','description',20,35,7,1,0,0,'');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (252,'Number of CPUs','cpu_num',30,35,8,0,0,0,'');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (253,'TCP Active','tcp_active',40,35,20,0,0,0,'DS:tcp_active:COUNTER:600:0:10000');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (254,'TCP Passive','tcp_passive',41,35,20,0,0,0,'DS:tcp_passive:COUNTER:600:0:10000');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (255,'TCP Established','tcp_established',42,35,20,0,0,0,'DS:tcp_established:COUNTER:600:0:10000');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (256,'Memory Total','mem_total',50,35,20,0,0,0,'DS:mem_total:GAUGE:600:0:100000000');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (257,'Memory Used','mem_used',51,35,20,0,0,0,'DS:mem_used:GAUGE:600:0:100000000');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (258,'CPU A 1 Sec','cpua_1sec',60,35,20,0,0,0,'DS:cpua_1sec:GAUGE:600:0:1000');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (259,'CPU A 4 Secs','cpua_4secs',61,35,20,0,0,0,'DS:cpua_4secs:GAUGE:600:0:1000');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (260,'CPU A 64 Secs','cpua_64secs',62,35,20,0,0,0,'DS:cpua_64secs:GAUGE:600:0:1000');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (261,'CPU B 1 Sec','cpub_1sec',65,35,20,0,0,0,'DS:cpub_1sec:GAUGE:600:0:1000');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (262,'CPU B 4 Secs','cpub_4secs',66,35,20,0,0,0,'DS:cpub_4secs:GAUGE:600:0:1000');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (263,'CPU B 64 Secs','cpub_64secs',67,35,20,0,0,0,'DS:cpub_64secs:GAUGE:600:0:1000');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (264,'Index','index',10,36,3,0,0,0,'');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (265,'Type','sensor_type',30,36,8,1,0,0,'');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (266,'Value','sensor_value',40,36,20,0,0,0,'DS:sensor_value:GAUGE:600:0:3000000000');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (267,'Index','index',10,37,3,0,0,0,'');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (268,'Description','description',20,37,7,1,0,0,'');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (269,'Physical Status','phy',40,37,8,1,0,0,'');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (270,'Tx Words','tx_words',40,37,20,0,0,0,'DS:tx_words:COUNTER:600:0:1000000000');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (271,'Rx Words','rx_words',45,37,20,0,0,0,'DS:rx_words:COUNTER:600:0:1000000000');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (272,'Tx Frames','tx_frames',50,37,20,0,0,0,'DS:tx_frames:COUNTER:600:0:100000000');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (273,'Rx Frames','rx_frames',55,37,20,0,0,0,'DS:rx_frames:COUNTER:600:0:100000000');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (274,'Index','index',10,38,3,0,0,0,'1');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (275,'Async Used','cisco_async',10,38,20,0,0,0,'DS:cisco_async:GAUGE:600:0:5000');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (276,'DSX Used','cisco_dsx',20,38,20,0,0,0,'DS:cisco_dsx:GAUGE:600:0:5000');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (277,'Free','cisco_free',30,38,20,0,0,0,'DS:cisco_free:GAUGE:600:0:5000');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (278,'Description','description',20,38,7,2,1,0,'');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (279,'System Name','name',40,11,7,1,0,1,'');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (280,'Location','location',50,11,7,1,0,1,'');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (281,'Contact','contact',60,11,7,1,0,1,'');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (282,'System Name','name',50,12,7,1,0,1,'');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (283,'Location','location',60,12,7,1,0,1,'');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (284,'Contact','contact',70,12,7,1,0,1,'');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (285,'System Name','name',40,3,7,1,0,1,'');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (286,'Location','location',50,3,7,1,0,1,'');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (287,'Contact','contact',60,3,7,1,0,1,'');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (288,'System Name','name',40,10,7,1,0,1,'');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (289,'Location','location',50,10,7,1,0,1,'');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (290,'Contact','contact',60,10,7,1,0,1,'');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (291,'System Name','name',40,35,7,1,0,1,'');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (292,'Location','location',50,35,7,1,0,1,'');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (293,'Contact','contact',60,35,7,1,0,1,'');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (294,'Index','index',10,39,3,0,0,0,'');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (295,'Description','description',20,39,7,2,1,0,'SNMP Informant Disk Stats');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (296,'lDisk % Read Time','inf_d_read_time',20,39,20,0,0,0,'DS:inf_d_read_time:GAUGE:600:0:100');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (297,'lDisk % Write Time','inf_d_write_time',10,39,20,0,0,0,'DS:inf_d_write_time:GAUGE:600:0:100');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (298,'lDisk Read Rate','inf_d_read_rate',30,39,20,0,0,0,'DS:inf_d_read_rate:GAUGE:600:0:1048576000');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (299,'lDisk Write Rate','inf_d_write_rate',25,39,20,0,0,0,'DS:inf_d_write_rate:GAUGE:600:0:1048576000');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (300,'Index','index',10,40,3,0,0,0,'1');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (301,'Identification','ident',20,40,7,1,0,1,'');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (302,'Description','description',30,40,7,2,1,0,'');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (303,'Battery Temperature','temperature',10,40,20,0,0,0,'DS:temperature:GAUGE:600:0:200');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (304,'Show in Celcius','show_celcius',40,40,5,2,1,0,'1');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (305,'Minutes Remaining','minutes_remaining',20,40,20,0,0,0,'DS:minutes_remaining:GAUGE:600:0:10000');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (306,'Index','index',10,41,3,0,0,0,'');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (307,'Line Type','line_type',20,41,8,0,0,0,'');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (308,'Line Index','line_index',30,41,8,0,0,0,'');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (309,'Description','description',40,41,7,2,1,0,'');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (310,'Voltage','voltage',10,41,20,0,0,0,'DS:voltage:GAUGE:600:0:500');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (311,'Current','current',20,41,20,0,0,0,'DS:current:GAUGE:600:0:100');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (312,'Load','load',30,41,20,0,0,0,'DS:load:GAUGE:600:0:100');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (313,'Charge Remaining','charge_remaining',30,40,20,0,0,0,'DS:charge_remaining:GAUGE:600:0:100');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (314,'IPTables Chain','chainnumber',10,42,3,1,1,0,'');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (315,'Default Policy','policy',30,42,7,1,1,0,'');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (316,'Number of Packets','ipt_packets',10,42,20,0,0,0,'DS:ipt_packets:COUNTER:600:0:1000000000');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (317,'Number of Bytes','ipt_bytes',20,42,20,0,0,0,'DS:ipt_bytes:COUNTER:600:0:<bandwidth>');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (318,'Description','description',20,42,7,2,1,0,'');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (319,'Accepted Routers','accepted_routes',40,6,20,0,0,0,'DS:accepted_routes:GAUGE:600:0:900000');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (320,'Advertised Routers','advertised_routes',50,6,20,0,0,0,'DS:advertised_routes:GAUGE:600:0:900000');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (322,'Estimated Bandwidth','bandwidth',40,42,8,2,1,0,'10240000');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (323,'Index','index',10,43,3,0,0,0,'');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (324,'Connections','pix_connections',10,43,20,0,0,0,'DS:pix_connections:GAUGE:600:0:1000000');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (325,'Description','description',20,43,7,1,1,0,'');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (326,'Index','index',10,44,3,0,0,0,'1');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (327,'Description','description',20,44,7,2,1,0,'');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (328,'Cisco Max Inbound NAT Bytes','NatInMax',30,44,8,2,1,0,'1000000000');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (329,'Cisco Max Outbound NAT Bytes','NatOutMax',40,44,8,2,1,0,'1000000000');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (330,'Cisco NAT Other IP Outbound','cisco_nat_other_ip_outbound',10,44,20,0,0,0,'DS:cisco_nat_other_ip_outbound:COUNTER:600:0:<NatOutMax>');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (331,'Cisco NAT Other IP Inbound','cisco_nat_other_ip_inbound',15,44,20,0,0,0,'DS:cisco_nat_other_ip_inbound:COUNTER:600:0:<NatInMax>');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (332,'Cisco NAT ICMP Outbound','cisco_nat_icmp_outbound',20,44,20,0,0,0,'DS:cisco_nat_icmp_outbound:COUNTER:600:0:<NatOutMax>');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (333,'Cisco NAT ICMP Inbound','cisco_nat_icmp_inbound',25,44,20,0,0,0,'DS:cisco_nat_icmp_inbound:COUNTER:600:0:<NatInMax>');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (334,'Cisco NAT UDP Outbound','cisco_nat_udp_outbound',30,44,20,0,0,0,'DS:cisco_nat_udp_outbound:COUNTER:600:0:<NatOutMax>');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (335,'Cisco NAT UDP Inbound','cisco_nat_udp_inbound',35,44,20,0,0,0,'DS:cisco_nat_udp_inbound:COUNTER:600:0:<NatInMax>');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (336,'Cisco NAT TCP Outbound','cisco_nat_tcp_outbound',40,44,20,0,0,0,'DS:cisco_nat_tcp_outbound:COUNTER:600:0:<NatOutMax>');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (337,'Cisco NAT TCP Inbound','cisco_nat_tcp_inbound',45,44,20,0,0,0,'DS:cisco_nat_tcp_inbound:COUNTER:600:0:<NatInMax>');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (338,'Cisco NAT Active Binds','cisco_nat_active_binds',50,44,20,0,0,0,'DS:cisco_nat_active_binds:GAUGE:600:0:100000');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (339,'Index','index',10,45,3,0,0,0,'');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (340,'Value','value',10,45,20,0,0,0,'DS:value:GAUGE:600:-100000:100000');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (341,'Description','description',20,45,7,1,1,0,'');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (342,'Show in Celcius','show_celcius',31,45,5,2,1,0,'1');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (343,'Show in Celcius','show_in_celcius',30,26,5,2,1,0,'1');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (344,'CPU Usage Threshold','cpu_threshold',90,3,8,2,1,0,'60');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (345,'Index','index',10,46,8,0,0,0,'');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (346,'Description','description',20,46,7,2,1,0,'');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (347,'CPU Usage','cpu400',10,46,20,0,0,0,'DS:cpu400:GAUGE:600:0:10000');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (348,'CPU Usage Threshold','cpu_threshold',90,11,8,2,1,0,'80');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (350,'Index','index',10,47,3,0,0,0,'');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (351,'Description','description',20,47,7,1,1,0,'System Information');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (352,'Dell OpenManage Fan RPM #2','dell_om_fan_2',51,47,20,0,0,0,'DS:dell_om_fan_2:GAUGE:600:0:100000');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (353,'Dell OpenManage Fan RPM #3','dell_om_fan_3',52,47,20,0,0,0,'DS:dell_om_fan_3:GAUGE:600:0:100000');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (354,'Dell OpenManage Fan RPM #4','dell_om_fan_4',53,47,20,0,0,0,'DS:dell_om_fan_4:GAUGE:600:0:100000');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (355,'Dell OpenManage Fan RPM #5','dell_om_fan_5',54,47,20,0,0,0,'DS:dell_om_fan_5:GAUGE:600:0:100000');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (356,'Dell OpenManage Fan RPM #6','dell_om_fan_6',55,47,20,0,0,0,'DS:dell_om_fan_6:GAUGE:600:0:100000');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (357,'Dell OpenManage Fan RPM #7','dell_om_fan_7',56,47,20,0,0,0,'DS:dell_om_fan_7:GAUGE:600:0:100000');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (367,'UPS Type','upstype',50,40,7,1,0,1,'');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (358,'UPS Type','upstype',50,41,7,1,0,1,'');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (359,'Power','power',50,41,20,0,0,0,'DS:power:GAUGE:600:0:100000');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (360,'Description','description',20,48,7,1,0,0,'');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (361,'Index','index',10,48,3,0,0,0,'');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (362,'Index','index',10,49,3,0,0,0,'');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (363,'Banks','banks',50,48,7,1,0,0,'');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (364,'Power Rating  Amps)','powerrating',40,49,7,1,0,0,'');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (365,'Load (Amps)','load',50,49,20,0,0,0,'DS:load:GAUGE:600:0:100');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (366,'Overload Threshold','threshold',50,49,7,1,0,0,'');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (394,'Index','index',5,50,3,0,0,0,'');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (368,'Index','index',10,51,3,0,0,0,'');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (369,'Health State','health_state',15,51,7,0,0,1,'');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (370,'Serial Number','serial',20,51,7,1,0,1,'');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (371,'Manuf date','manuf_date',30,51,7,1,0,1,'');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (372,'CPU1 Temperature','temperature',35,51,20,0,0,0,'DS:temperature:GAUGE:600:0:100');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (373,'CPU2 Temperature','temperature2',40,51,20,0,0,0,'DS:temperature2:GAUGE:600:0:100');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (374,'Index','index',10,52,3,0,0,0,'');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (375,'Oper Status','oper',30,52,8,1,0,0,'');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (376,'Tx Frames','tx_frames',40,52,20,0,0,0,'DS:tx_frames:COUNTER:600:0:100000000');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (377,'Rx Frames','rx_frames',50,52,20,0,0,0,'DS:rx_frames:COUNTER:600:0:100000000');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (378,'FC Port','real_index',11,52,7,1,0,1,'');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (379,'Admin Status','admin',20,52,8,1,0,0,'');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (380,'Index','index',1,53,3,0,0,0,'');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (381,'Description','description',10,53,7,1,1,0,'');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (382,'Associated','associated',20,53,20,0,0,0,'DS:associated:GAUGE:600:0:2100');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (383,'Index','index',10,54,3,0,0,0,'');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (384,'Power Module 1','module1',15,54,7,1,0,1,'');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (385,'Power Module 2','module2',20,54,7,1,0,1,'');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (386,'Power Consumption','fuelGaugePowerInUse',35,54,20,2,0,0,'DS:fuelGaugePowerInUse:GAUGE:600:0:10000');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (387,'Power Capacity','totalpower',30,54,7,1,0,0,'');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (388,'Index','index',5,55,3,0,0,0,'');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (389,'Chassis','chassis',10,55,8,1,0,1,'');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (390,'Bay','bayindex',15,55,8,1,0,1,'');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (391,'Index','index',1,56,3,0,0,0,'');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (392,'Number of arrays','nb_arrays',10,56,8,1,0,0,'');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (393,'Cpu Threshold','cpu_threshold',40,46,8,2,1,0,'90');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (406,'Index','index',5,57,3,0,0,0,'');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (395,'Description','description',10,57,7,1,1,0,'Ldisk 64');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (396,'Current Queue Length','cur_disk_q',20,57,20,0,0,0,'DS:cur_disk_q:GAUGE:600:0:10000');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (397,'Average Queue Length','avg_disk_q',25,57,20,0,0,0,'DS:avg_disk_q:GAUGE:600:0:10000');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (398,'Average Read Queue Length','avg_disk_rdq',30,57,20,0,0,0,'DS:avg_disk_rdq:GAUGE:600:0:10000');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (399,'Average Write Queue Length','avg_disk_wrq',35,57,20,0,0,0,'DS:avg_disk_wrq:GAUGE:600:0:10000');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (400,'Percent Read Time','inf_d_read_time',40,57,20,0,0,0,'DS:inf_d_read_time:GAUGE:600:0:100');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (401,'Percent Write Time','inf_d_write_time',45,57,20,0,0,0,'DS:inf_d_write_time:GAUGE:600:0:100');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (402,'Reads Per Sec','rd_ops',50,57,20,0,0,0,'DS:rd_ops:GAUGE:600:0:10000');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (403,'Writes Per Sec','wr_ops',55,57,20,0,0,0,'DS:wr_ops:GAUGE:600:0:10000');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (404,'Read Bytes Per Sec','inf_d_read_rate',55,57,20,0,0,0,'DS:inf_d_read_rate:GAUGE:600:0:10000');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (405,'Write Bytes Per Sec','inf_d_write_rate',55,57,20,0,0,0,'DS:inf_d_write_rate:GAUGE:600:0:10000');
INSERT INTO interface_types_fields (id, description, name, pos, itype, ftype, showable, overwritable, tracked, default_value) VALUES (407,'Ignore Case','ignore_case',30,15,8,1,1,0,'0');
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE interfaces (
  id int4 NOT NULL AUTO_INCREMENT,
  type int4 NOT NULL DEFAULT '1',
  interface char(30) NOT NULL DEFAULT '',
  host int4 NOT NULL DEFAULT '1',
  client int4 NOT NULL DEFAULT '1',
  sla int4 NOT NULL DEFAULT '1',
  poll int4 NOT NULL DEFAULT '1',
  make_sound int2 NOT NULL DEFAULT '1',
  show_rootmap int2 NOT NULL DEFAULT '1',
  rrd_mode int2 NOT NULL DEFAULT '2',
  creation_date int4 NOT NULL DEFAULT '0',
  modification_date int4 NOT NULL DEFAULT '0',
  last_poll_date int4 NOT NULL DEFAULT '0',
  poll_interval int4 NOT NULL DEFAULT '0',
  check_status int2 NOT NULL DEFAULT '1',
  PRIMARY KEY (id)
);
/*!40101 SET character_set_client = @saved_cs_client */;
INSERT INTO interfaces (id, type, interface, host, client, sla, poll, make_sound, show_rootmap, rrd_mode, creation_date, modification_date, last_poll_date, poll_interval, check_status) VALUES (1,1,'Unknown0',1,1,1,1,1,1,1,0,0,0,0,1);
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE interfaces_values (
  id int4 NOT NULL AUTO_INCREMENT,
  interface int4 NOT NULL DEFAULT '0',
  field int4 NOT NULL DEFAULT '0',
  value varchar(3000) NOT NULL,
  PRIMARY KEY (id)
);
/*!40101 SET character_set_client = @saved_cs_client */;
INSERT INTO interfaces_values (id, interface, field, value) VALUES (1,1,1,'');
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE journal (
  id int4 NOT NULL AUTO_INCREMENT,
  date_start datetime NOT NULL DEFAULT NULL,
  date_stop datetime NOT NULL DEFAULT NULL,
  comment text NOT NULL,
  subject varchar(40) NOT NULL DEFAULT '',
  active int2 NOT NULL DEFAULT '1',
  ticket varchar(20) NOT NULL DEFAULT '',
  PRIMARY KEY (id)
);
/*!40101 SET character_set_client = @saved_cs_client */;
INSERT INTO journal (id, date_start, date_stop, comment, subject, active, ticket) VALUES (1,'2002-01-20 19:09:07',NULL,'Internally used ID','',0,'');
INSERT INTO journal (id, date_start, date_stop, comment, subject, active, ticket) VALUES (2,'2002-01-20 19:09:07',NULL,'Internally used ID','',0,'');
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE logfiles (
  id int4 NOT NULL AUTO_INCREMENT,
  filename varchar(60) NOT NULL,
  last_poll_date int4 NOT NULL DEFAULT '0',
  file_offset int4 DEFAULT NULL,
  description varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (id)
);
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE logfiles_match_groups (
  id int4 NOT NULL AUTO_INCREMENT,
  logfile int4 NOT NULL,
  pos int4 NOT NULL DEFAULT '10',
  match_item int4 NOT NULL,
  PRIMARY KEY (id)
);
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE logfiles_match_items (
  id int4 NOT NULL AUTO_INCREMENT,
  description varchar(30) NOT NULL,
  match_text varchar(255) NOT NULL,
  interface varchar(20) NOT NULL,
  username varchar(20) NOT NULL,
  state varchar(20) NOT NULL,
  info varchar(20) NOT NULL,
  type int4 NOT NULL,
  host varchar(20) DEFAULT NULL,
  logfile_id int4 NOT NULL DEFAULT '0',
  pos int4 NOT NULL DEFAULT '10',
  PRIMARY KEY (id)
);
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE maps (
  id int4 NOT NULL AUTO_INCREMENT,
  parent int4 NOT NULL DEFAULT '0',
  name char(60) NOT NULL DEFAULT '',
  color char(6) NOT NULL DEFAULT '00A348',
  PRIMARY KEY (id)
);
/*!40101 SET character_set_client = @saved_cs_client */;
INSERT INTO maps (id, parent, name, color) VALUES (1,1,'Root Map','00A348');
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE maps_interfaces (
  id int4 NOT NULL AUTO_INCREMENT,
  map int4 NOT NULL DEFAULT '0',
  interface int4 NOT NULL DEFAULT '0',
  x int4 NOT NULL DEFAULT '1',
  y int4 NOT NULL DEFAULT '1',
  PRIMARY KEY (id)
);
/*!40101 SET character_set_client = @saved_cs_client */;
INSERT INTO maps_interfaces (id, map, interface, x, y) VALUES (1,1,1,1,1);
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE nad_hosts (
  id int4 NOT NULL AUTO_INCREMENT,
  snmp_name char(120) NOT NULL DEFAULT '',
  description varchar(3000) NOT NULL,
  snmp_community char(60) NOT NULL DEFAULT '',
  forwarding int2 NOT NULL DEFAULT '0',
  date_added int4 NOT NULL DEFAULT '0',
  PRIMARY KEY (id)
);
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE nad_ips (
  id int4 NOT NULL AUTO_INCREMENT,
  host int4 NOT NULL DEFAULT '1',
  ip char(20) NOT NULL DEFAULT '',
  type int4 NOT NULL DEFAULT '0',
  network int4 NOT NULL DEFAULT '1',
  dns char(120) NOT NULL DEFAULT '',
  PRIMARY KEY (id)
);
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE nad_networks (
  id int4 NOT NULL AUTO_INCREMENT,
  network char(20) NOT NULL DEFAULT '',
  deep int2 NOT NULL DEFAULT '1',
  oper_status int2 NOT NULL DEFAULT '1',
  parent int4 NOT NULL DEFAULT '1',
  seed int4 NOT NULL DEFAULT '1',
  oper_status_changed int4 NOT NULL DEFAULT '0',
  PRIMARY KEY (id)
);
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE pollers (
  id int4 NOT NULL AUTO_INCREMENT,
  name char(60) NOT NULL DEFAULT '',
  description char(60) NOT NULL DEFAULT '',
  command char(60) NOT NULL DEFAULT '',
  parameters char(100) NOT NULL DEFAULT '',
  PRIMARY KEY (id)
);
/*!40101 SET character_set_client = @saved_cs_client */;
INSERT INTO pollers (id, name, description, command, parameters) VALUES (1,'no_poller','No Poller','no_poller','');
INSERT INTO pollers (id, name, description, command, parameters) VALUES (2,'input','SNMP Input Rate','snmp_counter','.1.3.6.1.2.1.2.2.1.10.<interfacenumber>');
INSERT INTO pollers (id, name, description, command, parameters) VALUES (3,'verify_interface_number','Cisco Verify Interface Number','verify_interface_number','');
INSERT INTO pollers (id, name, description, command, parameters) VALUES (5,'cisco_snmp_ping_start','Cisco SNMP Ping Start','cisco_snmp_ping_start','');
INSERT INTO pollers (id, name, description, command, parameters) VALUES (6,'cisco_snmp_ping_wait','Cisco SNMP Ping Wait','cisco_snmp_ping_wait','');
INSERT INTO pollers (id, name, description, command, parameters) VALUES (7,'packetloss','Cisco SNMP Ping Get PL','cisco_snmp_ping_get_pl','');
INSERT INTO pollers (id, name, description, command, parameters) VALUES (8,'rtt','Cisco SNMP Ping Get RTT','cisco_snmp_ping_get_rtt','');
INSERT INTO pollers (id, name, description, command, parameters) VALUES (9,'cisco_snmp_ping_end','Cisco SNMP Ping End','cisco_snmp_ping_end','');
INSERT INTO pollers (id, name, description, command, parameters) VALUES (10,'output','SNMP Output Rate','snmp_counter','.1.3.6.1.2.1.2.2.1.16.<interfacenumber>');
INSERT INTO pollers (id, name, description, command, parameters) VALUES (11,'outputerrors','SNMP Output Errors','snmp_counter','.1.3.6.1.2.1.2.2.1.20.<interfacenumber>');
INSERT INTO pollers (id, name, description, command, parameters) VALUES (12,'inputerrors','SNMP Input Errors','snmp_counter','.1.3.6.1.2.1.2.2.1.14.<interfacenumber>');
INSERT INTO pollers (id, name, description, command, parameters) VALUES (13,'interface_oper_status','SNMP Interface Operational Status','snmp_interface_status_all','8');
INSERT INTO pollers (id, name, description, command, parameters) VALUES (14,'interface_admin_status','SNMP Interface Administrative Status','snmp_interface_status_all','7');
INSERT INTO pollers (id, name, description, command, parameters) VALUES (16,'cpu','Cisco CPU Utilization','snmp_counter','.1.3.6.1.4.1.9.9.109.1.1.1.1.5.1');
INSERT INTO pollers (id, name, description, command, parameters) VALUES (21,'inpackets','SNMP Input Packets','snmp_counter','.1.3.6.1.2.1.2.2.1.11.<interfacenumber>');
INSERT INTO pollers (id, name, description, command, parameters) VALUES (22,'outpackets','SNMP Output Packets','snmp_counter','.1.3.6.1.2.1.2.2.1.17.<interfacenumber>');
INSERT INTO pollers (id, name, description, command, parameters) VALUES (23,'tcp_status,tcp_content,conn_delay','TCP Port Check & Delay','tcp_status','');
INSERT INTO pollers (id, name, description, command, parameters) VALUES (24,'mem_used','Cisco Used Memory','snmp_counter','.1.3.6.1.4.1.9.9.48.1.1.1.5.1');
INSERT INTO pollers (id, name, description, command, parameters) VALUES (25,'mem_free','Cisco Free Memory','snmp_counter','.1.3.6.1.4.1.9.9.48.1.1.1.6.1');
INSERT INTO pollers (id, name, description, command, parameters) VALUES (26,'drops','SNMP Drops','snmp_counter','.1.3.6.1.2.1.2.2.1.19.<interfacenumber>');
INSERT INTO pollers (id, name, description, command, parameters) VALUES (30,'cpu','Cisco 2500 Series CPU Utilization','snmp_counter','.1.3.6.1.4.1.9.2.1.56.0');
INSERT INTO pollers (id, name, description, command, parameters) VALUES (31,'bgpin','BGP Inbound Updates','snmp_counter','.1.3.6.1.2.1.15.3.1.10.<remote>');
INSERT INTO pollers (id, name, description, command, parameters) VALUES (32,'bgpout','BGP Outbound Updates','snmp_counter','.1.3.6.1.2.1.15.3.1.11.<remote>');
INSERT INTO pollers (id, name, description, command, parameters) VALUES (33,'bgpuptime','BGP Uptime','snmp_counter','.1.3.6.1.2.1.15.3.1.16.<remote>');
INSERT INTO pollers (id, name, description, command, parameters) VALUES (35,'storage_used_blocks','Storage Device Used Blocks','snmp_counter','.1.3.6.1.2.1.25.2.3.1.6.<index>');
INSERT INTO pollers (id, name, description, command, parameters) VALUES (36,'storage_block_count','Storage Device Total Blocks','snmp_counter','.1.3.6.1.2.1.25.2.3.1.5.<index>');
INSERT INTO pollers (id, name, description, command, parameters) VALUES (37,'storage_block_size','Storage Device Block Size','snmp_counter','.1.3.6.1.2.1.25.2.3.1.4.<index>');
INSERT INTO pollers (id, name, description, command, parameters) VALUES (38,'bgp_peer_status','BGP Peer Status','bgp_peer_status','<remote>');
INSERT INTO pollers (id, name, description, command, parameters) VALUES (40,'hits','CSS VIP Hits','snmp_counter','.1.3.6.1.4.1.2467.1.16.4.1.18.\"<owner>\".\"<interface>\"');
INSERT INTO pollers (id, name, description, command, parameters) VALUES (41,'output','CSS VIP Traffic Rate','snmp_counter','.1.3.6.1.4.1.2467.1.16.4.1.25.\"<owner>\".\"<interface>\"');
INSERT INTO pollers (id, name, description, command, parameters) VALUES (42,'cpu_kernel_ticks','CPU Kernel Time','snmp_counter','.1.3.6.1.4.1.2021.11.55.0');
INSERT INTO pollers (id, name, description, command, parameters) VALUES (43,'cpu_idle_ticks','CPU Idle Time','snmp_counter','.1.3.6.1.4.1.2021.11.53.0');
INSERT INTO pollers (id, name, description, command, parameters) VALUES (44,'cpu_wait_ticks','CPU Wait Time','snmp_counter','.1.3.6.1.4.1.2021.11.54.0');
INSERT INTO pollers (id, name, description, command, parameters) VALUES (45,'cpu_system_ticks','CPU System Time','snmp_counter','.1.3.6.1.4.1.2021.11.52.0');
INSERT INTO pollers (id, name, description, command, parameters) VALUES (46,'mem_available','Real Memory Available','snmp_counter','.1.3.6.1.4.1.2021.4.6.0');
INSERT INTO pollers (id, name, description, command, parameters) VALUES (47,'mem_total','Real Memory Total','snmp_counter','.1.3.6.1.4.1.2021.4.5.0');
INSERT INTO pollers (id, name, description, command, parameters) VALUES (48,'swap_available','Swap Memory Available','snmp_counter','.1.3.6.1.4.1.2021.4.4.0');
INSERT INTO pollers (id, name, description, command, parameters) VALUES (49,'swap_total','Swap Memory Total','snmp_counter','.1.3.6.1.4.1.2021.4.3.0');
INSERT INTO pollers (id, name, description, command, parameters) VALUES (50,'load_average_15','Load Average 15 min','snmp_counter','.1.3.6.1.4.1.2021.10.1.3.3');
INSERT INTO pollers (id, name, description, command, parameters) VALUES (51,'load_average_5','Load Average 5 min','snmp_counter','.1.3.6.1.4.1.2021.10.1.3.2');
INSERT INTO pollers (id, name, description, command, parameters) VALUES (52,'load_average_1','Load Average 1 min','snmp_counter','.1.3.6.1.4.1.2021.10.1.3.1');
INSERT INTO pollers (id, name, description, command, parameters) VALUES (53,'cpu_user_ticks','CPU User Time','snmp_counter','.1.3.6.1.4.1.2021.11.50.0');
INSERT INTO pollers (id, name, description, command, parameters) VALUES (54,'cpu_nice_ticks','CPU Nice Time','snmp_counter','.1.3.6.1.4.1.2021.11.51.0');
INSERT INTO pollers (id, name, description, command, parameters) VALUES (55,'bandwidthin','Get Bandwidth IN from DB','db','bandwidthin,to_bytes');
INSERT INTO pollers (id, name, description, command, parameters) VALUES (56,'bandwidthout','Get Bandwidth OUT from DB','db','bandwidthout,to_bytes');
INSERT INTO pollers (id, name, description, command, parameters) VALUES (57,'tcp_conn_number','TCP Connection Numbers','tcp_connection_number','');
INSERT INTO pollers (id, name, description, command, parameters) VALUES (58,'acct_bytes,acct_packets','Cisco Accounting','cisco_accounting','');
INSERT INTO pollers (id, name, description, command, parameters) VALUES (59,'cpu','Host MIB Proc Average Util','snmp_walk_average','.1.3.6.1.2.1.25.3.3.1.2');
INSERT INTO pollers (id, name, description, command, parameters) VALUES (60,'num_procs','Host MIB Number of Processes','snmp_counter','.1.3.6.1.2.1.25.1.6.0');
INSERT INTO pollers (id, name, description, command, parameters) VALUES (61,'num_users','Host MIB Number of Users','snmp_counter','.1.3.6.1.2.1.25.1.5.0');
INSERT INTO pollers (id, name, description, command, parameters) VALUES (62,'tcp_active','TCP MIB Active Opens','snmp_counter','.1.3.6.1.2.1.6.5.0');
INSERT INTO pollers (id, name, description, command, parameters) VALUES (63,'tcp_passive','TCP MIB Passive Opens','snmp_counter','.1.3.6.1.2.1.6.6.0');
INSERT INTO pollers (id, name, description, command, parameters) VALUES (64,'tcp_established','TCP MIB Established Connections','snmp_counter','.1.3.6.1.2.1.6.9.0');
INSERT INTO pollers (id, name, description, command, parameters) VALUES (65,'inputpackets','Cisco MAC Accounting Input Packets','snmp_counter','.1.3.6.1.4.1.9.9.84.1.2.1.1.3.<ifindex>.1.<mac>');
INSERT INTO pollers (id, name, description, command, parameters) VALUES (66,'outputpackets','Cisco MAC Accounting Output Packets','snmp_counter','.1.3.6.1.4.1.9.9.84.1.2.1.1.3.<ifindex>.2.<mac>');
INSERT INTO pollers (id, name, description, command, parameters) VALUES (67,'input','Cisco MAC Accounting Input Bytes','snmp_counter','.1.3.6.1.4.1.9.9.84.1.2.1.1.4.<ifindex>.1.<mac>');
INSERT INTO pollers (id, name, description, command, parameters) VALUES (68,'output','Cisco MAC Accounting Output Bytes','snmp_counter','.1.3.6.1.4.1.9.9.84.1.2.1.1.4.<ifindex>.2.<mac>');
INSERT INTO pollers (id, name, description, command, parameters) VALUES (69,'packetloss','Smokeping Loss','smokeping','loss');
INSERT INTO pollers (id, name, description, command, parameters) VALUES (70,'rtt','Smokeping RTT','smokeping','median');
INSERT INTO pollers (id, name, description, command, parameters) VALUES (71,'app_status,current_instances','Host MIB Process Verifier','hostmib_apps','<interface>');
INSERT INTO pollers (id, name, description, command, parameters) VALUES (72,'cisco_powersupply_status','Cisco Power Supply Status','cisco_envmib_status','5.1.3');
INSERT INTO pollers (id, name, description, command, parameters) VALUES (73,'cisco_temperature_status','Cisco Temperature Status','cisco_envmib_status','3.1.6');
INSERT INTO pollers (id, name, description, command, parameters) VALUES (74,'cisco_voltage_status','Cisco Voltage Status','cisco_envmib_status','2.1.7');
INSERT INTO pollers (id, name, description, command, parameters) VALUES (75,'temperature','Cisco Temperature','snmp_counter','.1.3.6.1.4.1.9.9.13.1.3.1.3.<index>');
INSERT INTO pollers (id, name, description, command, parameters) VALUES (76,'sa_agent_verify','Verify SA Agent Operation','cisco_saagent_verify','');
INSERT INTO pollers (id, name, description, command, parameters) VALUES (77,'forward_jitter','SA Agent Forward Jitter','cisco_saagent_forwardjitter','');
INSERT INTO pollers (id, name, description, command, parameters) VALUES (78,'backward_jitter','SA Agent Backward Jitter','cisco_saagent_backwardjitter','');
INSERT INTO pollers (id, name, description, command, parameters) VALUES (79,'rt_latency','SA Agent Round-Trip Latency','cisco_saagent_rtl','');
INSERT INTO pollers (id, name, description, command, parameters) VALUES (80,'forward_packetloss','SA Agent fw % PacketLoss','cisco_saagent_fwpacketloss','');
INSERT INTO pollers (id, name, description, command, parameters) VALUES (81,'backward_packetloss','SA Agent bw % PacketLoss','cisco_saagent_bwpacketloss','');
INSERT INTO pollers (id, name, description, command, parameters) VALUES (82,'verify_smokeping_number','Verify Smokeping Number','verify_smokeping_number','');
INSERT INTO pollers (id, name, description, command, parameters) VALUES (85,'tcp_content_analisis','TCP Port Response Check','tcp_port_content','tcp_content');
INSERT INTO pollers (id, name, description, command, parameters) VALUES (86,'ping','Reachability Start FPING','reachability_start','');
INSERT INTO pollers (id, name, description, command, parameters) VALUES (87,'wait','Reachability Wait until finished','reachability_wait','');
INSERT INTO pollers (id, name, description, command, parameters) VALUES (88,'rtt','Reachability RTT','reachability_values','rtt');
INSERT INTO pollers (id, name, description, command, parameters) VALUES (89,'packetloss','Reachability PL','reachability_values','pl');
INSERT INTO pollers (id, name, description, command, parameters) VALUES (90,'ping_cleanup','Reachability End','reachability_end','');
INSERT INTO pollers (id, name, description, command, parameters) VALUES (91,'status','Reachability Status','reachability_status','');
INSERT INTO pollers (id, name, description, command, parameters) VALUES (93,'bytes','Linux TC Bytes','snmp_counter','<autodiscovery_parameters>.1.6.<index>');
INSERT INTO pollers (id, name, description, command, parameters) VALUES (94,'packets','Linux TC Packets','snmp_counter','<autodiscovery_parameters>.1.7.<index>');
INSERT INTO pollers (id, name, description, command, parameters) VALUES (95,'verify_tc_number','Linux TC Verfy Interface Number','verify_tc_class_number','');
INSERT INTO pollers (id, name, description, command, parameters) VALUES (100,'tcp_status,tcp_content','TCP Port Status','buffer','');
INSERT INTO pollers (id, name, description, command, parameters) VALUES (101,'app_status','Host MIB Status','buffer','');
INSERT INTO pollers (id, name, description, command, parameters) VALUES (102,'ntp_status','NTP Status','ntp_client','');
INSERT INTO pollers (id, name, description, command, parameters) VALUES (103,'used_memory','Host MIB Process Memory Usage','hostmib_perf','2');
INSERT INTO pollers (id, name, description, command, parameters) VALUES (105,'udp_status,conn_delay','UDP Port Status & Delay','udp_status','');
INSERT INTO pollers (id, name, description, command, parameters) VALUES (106,'udp_status','UDP Port Status','buffer','');
INSERT INTO pollers (id, name, description, command, parameters) VALUES (107,'temperature','Compaq Temperature','snmp_counter','.1.3.6.1.4.1.232.6.2.6.8.1.4.<chassis>.<tempindex>');
INSERT INTO pollers (id, name, description, command, parameters) VALUES (108,'temp_status','Compaq Temperature Status','snmp_status','.1.3.6.1.4.1.232.6.2.6.8.1.6.<chassis>.<tempindex>,2=up');
INSERT INTO pollers (id, name, description, command, parameters) VALUES (109,'fan_status','Compaq Fan Condition','snmp_status','.1.3.6.1.4.1.232.6.2.6.7.1.9.<chassis>.<fanindex>,2=up');
INSERT INTO pollers (id, name, description, command, parameters) VALUES (110,'compaq_disk','Compaq Drive Condition','snmp_status','.1.3.6.1.4.1.232.3.2.5.1.1.6.<controller>.<drvindex>,2=up');
INSERT INTO pollers (id, name, description, command, parameters) VALUES (111,'tbr','IIS Total Bytes Received','snmp_counter','.1.3.6.1.4.1.311.1.7.3.1.4.0');
INSERT INTO pollers (id, name, description, command, parameters) VALUES (112,'tcgir','IIS Total CGI Requests','snmp_counter','.1.3.6.1.4.1.311.1.7.3.1.35.0');
INSERT INTO pollers (id, name, description, command, parameters) VALUES (113,'tfs','IIS Total Files Sent','snmp_counter','.1.3.6.1.4.1.311.1.7.3.1.5.0');
INSERT INTO pollers (id, name, description, command, parameters) VALUES (114,'tg','IIS Total GETs','snmp_counter','.1.3.6.1.4.1.311.1.7.3.1.18.0');
INSERT INTO pollers (id, name, description, command, parameters) VALUES (115,'tp','IIS Total Posts','snmp_counter','.1.3.6.1.4.1.311.1.7.3.1.19.0');
INSERT INTO pollers (id, name, description, command, parameters) VALUES (116,'pm_serial_free','Livingston Portmaster Free','livingston_serial_port_status','1');
INSERT INTO pollers (id, name, description, command, parameters) VALUES (117,'pm_serial_established','Livingston Portmaster Established','livingston_serial_port_status','3');
INSERT INTO pollers (id, name, description, command, parameters) VALUES (118,'pm_serial_disconnecting','Livingston Portmaster Disconnecting','livingston_serial_port_status','4');
INSERT INTO pollers (id, name, description, command, parameters) VALUES (119,'pm_serial_command','Livingston Portmaster Command','livingston_serial_port_status','5');
INSERT INTO pollers (id, name, description, command, parameters) VALUES (120,'pm_serial_connecting','Livingston Portmaster Connecting','livingston_serial_port_status','2');
INSERT INTO pollers (id, name, description, command, parameters) VALUES (121,'pm_serial_noservice','Livingston Portmaster No Service','livingston_serial_port_status','6');
INSERT INTO pollers (id, name, description, command, parameters) VALUES (122,'tac,tkb,cplo,up,bpr,bw,iw','Apache Status','apache','');
INSERT INTO pollers (id, name, description, command, parameters) VALUES (123,'capacity','a APC Battery Capacity','snmp_counter','.1.3.6.1.4.1.318.1.1.1.2.2.1.0');
INSERT INTO pollers (id, name, description, command, parameters) VALUES (124,'load','a APC Output Load','snmp_counter','.1.3.6.1.4.1.318.1.1.1.4.2.3.0');
INSERT INTO pollers (id, name, description, command, parameters) VALUES (125,'in_voltage','a APC Input Voltage','snmp_counter','.1.3.6.1.4.1.318.1.1.1.3.2.1.0');
INSERT INTO pollers (id, name, description, command, parameters) VALUES (126,'out_voltage','a APC Output Voltage','snmp_counter','.1.3.6.1.4.1.318.1.1.1.4.2.1.0');
INSERT INTO pollers (id, name, description, command, parameters) VALUES (127,'time_remaining','a APC Time Remaining','snmp_counter','.1.3.6.1.4.1.318.1.1.1.2.2.3.0');
INSERT INTO pollers (id, name, description, command, parameters) VALUES (128,'status','a APC Battery Status','snmp_status','.1.3.6.1.4.1.318.1.1.1.2.1.1.0,2=battery normal|1=battery unknown|3=battery low');
INSERT INTO pollers (id, name, description, command, parameters) VALUES (129,'temperature','a APC Temperature','snmp_counter','.1.3.6.1.4.1.318.1.1.1.2.2.2.0');
INSERT INTO pollers (id, name, description, command, parameters) VALUES (130,'output_status','a APC Output Status','snmp_status','.1.3.6.1.4.1.318.1.1.1.4.1.1.0,2=on line|3=on battery');
INSERT INTO pollers (id, name, description, command, parameters) VALUES (131,'records_counter,records_absolute','ODBC Query','odbc_query','');
INSERT INTO pollers (id, name, description, command, parameters) VALUES (132,'sql_status','SQL Query Status','sql_status','');
INSERT INTO pollers (id, name, description, command, parameters) VALUES (133,'admin_state','Alteon RServer Admin','snmp_counter','.1.3.6.1.4.1.1872.2.1.5.2.1.10.<index>');
INSERT INTO pollers (id, name, description, command, parameters) VALUES (134,'oper_state','Alteon RServer Oper','snmp_status','.1.3.6.1.4.1.1872.2.1.9.2.2.1.7.<index>,2=up');
INSERT INTO pollers (id, name, description, command, parameters) VALUES (135,'current_sessions','Alteon RServer Current Sessions','snmp_counter','.1.3.6.1.4.1.1872.2.1.8.2.5.1.2.<index>');
INSERT INTO pollers (id, name, description, command, parameters) VALUES (136,'failures','Alteon RServer Failures','snmp_counter','.1.3.6.1.4.1.1872.2.1.8.2.5.1.4.<index>');
INSERT INTO pollers (id, name, description, command, parameters) VALUES (137,'octets','Alteon RServer Octets','snmp_counter','.1.3.6.1.4.1.1872.2.1.8.2.5.1.7.<index>');
INSERT INTO pollers (id, name, description, command, parameters) VALUES (138,'total_sessions','Alteon RServer Total Sessions','snmp_counter','.1.3.6.1.4.1.1872.2.1.8.2.5.1.3.<index>');
INSERT INTO pollers (id, name, description, command, parameters) VALUES (139,'admin_state','Alteon VServer Admin State','snmp_counter','.1.3.6.1.4.1.1872.2.1.5.5.1.4.<index>');
INSERT INTO pollers (id, name, description, command, parameters) VALUES (140,'current_sessions','Alteon VServer Current Sessions','snmp_counter','.1.3.6.1.4.1.1872.2.1.8.2.7.1.2.<index>');
INSERT INTO pollers (id, name, description, command, parameters) VALUES (141,'total_sessions','Alteon VServer Total Sessions','snmp_counter','.1.3.6.1.4.1.1872.2.1.8.2.7.1.3.<index>');
INSERT INTO pollers (id, name, description, command, parameters) VALUES (142,'octets','Alteon VServer Octets','snmp_counter','.1.3.6.1.4.1.1872.2.1.8.2.7.1.6.<index>');
INSERT INTO pollers (id, name, description, command, parameters) VALUES (143,'admin_state','Alteon RService Admin State','snmp_counter','.1.3.6.1.4.1.1872.2.1.5.2.1.10.<real_server>');
INSERT INTO pollers (id, name, description, command, parameters) VALUES (144,'oper_state','Alteon RService Oper State','snmp_status','.1.3.6.1.4.1.1872.2.1.9.2.4.1.6.<index>,2=up');
INSERT INTO pollers (id, name, description, command, parameters) VALUES (145,'response_time','Alteon RService Response Time','snmp_counter','.1.3.6.1.4.1.1872.2.1.9.2.4.1.7.<index>');
INSERT INTO pollers (id, name, description, command, parameters) VALUES (146,'cpua_1sec','Alteon CPU A 1Sec','snmp_counter','.1.3.6.1.4.1.1872.2.1.8.16.1.0');
INSERT INTO pollers (id, name, description, command, parameters) VALUES (147,'cpua_4secs','Alteon CPU A 4Secs','snmp_counter','.1.3.6.1.4.1.1872.2.1.8.16.3.0');
INSERT INTO pollers (id, name, description, command, parameters) VALUES (148,'cpua_64secs','Alteon CPU A 64Secs','snmp_counter','.1.3.6.1.4.1.1872.2.1.8.16.5.0');
INSERT INTO pollers (id, name, description, command, parameters) VALUES (149,'cpub_1sec','Alteon CPU B 1Sec','snmp_counter','.1.3.6.1.4.1.1872.2.1.8.1.16.2.0');
INSERT INTO pollers (id, name, description, command, parameters) VALUES (150,'cpub_4secs','Alteon CPU B 4 Secs','snmp_counter','.1.3.6.1.4.1.1872.2.1.8.16.4.0');
INSERT INTO pollers (id, name, description, command, parameters) VALUES (151,'cpub_64secs','Alteon CPU B 64Secs','snmp_counter','.1.3.6.1.4.1.1872.2.1.8.16.6.0');
INSERT INTO pollers (id, name, description, command, parameters) VALUES (152,'mem_total','Alteon Memory Total','snmp_counter','.1.3.6.1.4.1.1872.2.1.8.12.6.0');
INSERT INTO pollers (id, name, description, command, parameters) VALUES (153,'mem_used','Alteon Memory Used','snmp_counter','.1.3.6.1.4.1.1872.2.1.8.12.4.0');
INSERT INTO pollers (id, name, description, command, parameters) VALUES (154,'sensor_value','Brocade Sensor Value','snmp_counter','1.3.6.1.4.1.1588.2.1.1.1.1.22.1.4.<index>');
INSERT INTO pollers (id, name, description, command, parameters) VALUES (155,'oper_status','Brocade Sensor Oper','snmp_status','1.3.6.1.4.1.1588.2.1.1.1.1.22.1.3.<index>,4=ok|3=alert|5=alert');
INSERT INTO pollers (id, name, description, command, parameters) VALUES (156,'tx_words','Brocade FCPort TxWords','snmp_counter','1.3.6.1.4.1.1588.2.1.1.1.6.2.1.11.<index>');
INSERT INTO pollers (id, name, description, command, parameters) VALUES (157,'rx_words','Brocade FCPort RxWords','snmp_counter','1.3.6.1.4.1.1588.2.1.1.1.6.2.1.12.<index>');
INSERT INTO pollers (id, name, description, command, parameters) VALUES (158,'tx_frames','Brocade FCPort TxFrames','snmp_counter','1.3.6.1.4.1.1588.2.1.1.1.6.2.1.13.<index>');
INSERT INTO pollers (id, name, description, command, parameters) VALUES (159,'rx_frames','Brocade FCPort RxFrames','snmp_counter','1.3.6.1.4.1.1588.2.1.1.1.6.2.1.14.<index>');
INSERT INTO pollers (id, name, description, command, parameters) VALUES (160,'admin_state','Brocade Fc Port Admin State','snmp_counter','1.3.6.1.4.1.1588.2.1.1.1.6.2.1.5.<index>');
INSERT INTO pollers (id, name, description, command, parameters) VALUES (161,'oper_status','Brocade fC Ports Oper Status','snmp_status','1.3.6.1.4.1.1588.2.1.1.1.6.2.1.4.<index>,1=up|3=testing');
INSERT INTO pollers (id, name, description, command, parameters) VALUES (162,'phy_state','Brocade FC Port Phy State','brocade_fcport_phystate','<index>');
INSERT INTO pollers (id, name, description, command, parameters) VALUES (163,'cisco_async','Cisco Async Utilisation','cisco_serial_port_status','1');
INSERT INTO pollers (id, name, description, command, parameters) VALUES (164,'cisco_dsx','Cisco DSX Utilisation','cisco_serial_port_status','2');
INSERT INTO pollers (id, name, description, command, parameters) VALUES (165,'cisco_free','Cisco Port Free','cisco_serial_port_status','3');
INSERT INTO pollers (id, name, description, command, parameters) VALUES (166,'inf_d_read_time','Informant Disk Read Time','snmp_counter','.1.3.6.1.4.1.9600.1.1.1.1.2.<index>');
INSERT INTO pollers (id, name, description, command, parameters) VALUES (167,'inf_d_write_time','Informant Disk Write Time','snmp_counter','.1.3.6.1.4.1.9600.1.1.1.1.4.<index>');
INSERT INTO pollers (id, name, description, command, parameters) VALUES (168,'inf_d_read_rate','Informant Disk Read Rate','snmp_counter','.1.3.6.1.4.1.9600.1.1.1.1.15.<index>');
INSERT INTO pollers (id, name, description, command, parameters) VALUES (169,'inf_d_write_rate','Informant Disk Write Rate','snmp_counter','.1.3.6.1.4.1.9600.1.1.1.1.18.<index>');
INSERT INTO pollers (id, name, description, command, parameters) VALUES (170,'status','UPS Battery Status','snmp_status','.1.3.6.1.2.1.33.1.2.1.0,2=battery normal|1=battery unknown|3=battery low|3=battery depleted');
INSERT INTO pollers (id, name, description, command, parameters) VALUES (171,'temperature','UPS Battery Temperature','snmp_counter','.1.3.6.1.2.1.33.1.2.7.0');
INSERT INTO pollers (id, name, description, command, parameters) VALUES (172,'minutes_remaining','UPS Battery Minutes Remaining','snmp_counter','.1.3.6.1.2.1.33.1.2.3.0');
INSERT INTO pollers (id, name, description, command, parameters) VALUES (173,'charge_remaining','UPS Battery Charge Remaining','snmp_counter','.1.3.6.1.2.1.33.1.2.4.0');
INSERT INTO pollers (id, name, description, command, parameters) VALUES (174,'voltage','UPS Lines Voltage','ups_line','');
INSERT INTO pollers (id, name, description, command, parameters) VALUES (175,'current','UPS Lines Current','ups_line','');
INSERT INTO pollers (id, name, description, command, parameters) VALUES (176,'load','UPS Lines Load','ups_line','');
INSERT INTO pollers (id, name, description, command, parameters) VALUES (177,'ipt_packets','IPTables Chain Packets','snmp_counter','.1.3.6.1.4.1.2021.5002.1.4.<chainnumber>');
INSERT INTO pollers (id, name, description, command, parameters) VALUES (178,'ipt_bytes','IPTables Chainl Bytes','snmp_counter','.1.3.6.1.4.1.2021.5002.1.5.<chainnumber>');
INSERT INTO pollers (id, name, description, command, parameters) VALUES (179,'accepted_routes','BGP Accepted Routes','snmp_counter','.1.3.6.1.4.1.9.9.187.1.2.4.1.1.<remote>.1.1');
INSERT INTO pollers (id, name, description, command, parameters) VALUES (180,'advertised_routes','BGP Advertised Routes','snmp_counter','.1.3.6.1.4.1.9.9.187.1.2.4.1.6.<remote>.1.1');
INSERT INTO pollers (id, name, description, command, parameters) VALUES (181,'pix_connections','Pix Connections Poller','snmp_counter','.1.3.6.1.4.1.9.9.147.1.2.2.2.1.5.<index>');
INSERT INTO pollers (id, name, description, command, parameters) VALUES (182,'cisco_nat_other_ip_inbound','Cisco NAT Other IP Inbound','snmp_counter','.1.3.6.1.4.1.9.10.77.1.3.1.1.2.1');
INSERT INTO pollers (id, name, description, command, parameters) VALUES (183,'cisco_nat_icmp_inbound','Cisco NAT ICMP Inbound','snmp_counter','.1.3.6.1.4.1.9.10.77.1.3.1.1.2.2');
INSERT INTO pollers (id, name, description, command, parameters) VALUES (184,'cisco_nat_udp_inbound','Cisco NAT UDP Inbound','snmp_counter','.1.3.6.1.4.1.9.10.77.1.3.1.1.2.3');
INSERT INTO pollers (id, name, description, command, parameters) VALUES (185,'cisco_nat_tcp_inbound','Cisco NAT TCP Inbound','snmp_counter','.1.3.6.1.4.1.9.10.77.1.3.1.1.2.4');
INSERT INTO pollers (id, name, description, command, parameters) VALUES (186,'cisco_nat_other_ip_outbound','Cisco NAT Other IP Outbound','snmp_counter','.1.3.6.1.4.1.9.10.77.1.3.1.1.3.1');
INSERT INTO pollers (id, name, description, command, parameters) VALUES (187,'cisco_nat_icmp_outbound','Cisco NAT ICMP Outbound','snmp_counter','.1.3.6.1.4.1.9.10.77.1.3.1.1.3.2');
INSERT INTO pollers (id, name, description, command, parameters) VALUES (188,'cisco_nat_udp_outbound','Cisco NAT UDP Outbound','snmp_counter','.1.3.6.1.4.1.9.10.77.1.3.1.1.3.3');
INSERT INTO pollers (id, name, description, command, parameters) VALUES (189,'cisco_nat_tcp_outbound','Cisco NAT TCP Outbound','snmp_counter','.1.3.6.1.4.1.9.10.77.1.3.1.1.3.4');
INSERT INTO pollers (id, name, description, command, parameters) VALUES (190,'cisco_nat_active_binds','Cisco NAT Active Binds','snmp_counter','.1.3.6.1.4.1.9.10.77.1.2.1.0');
INSERT INTO pollers (id, name, description, command, parameters) VALUES (191,'value','Sensor Value','snmp_counter','.1.3.6.1.2.1.25.8.1.5.<index>');
INSERT INTO pollers (id, name, description, command, parameters) VALUES (192,'storage_verify','Verify Storage Index','verify_storage_index','');
INSERT INTO pollers (id, name, description, command, parameters) VALUES (193,'cpu400','OS 400 System Load','snmp_counter','.1.3.6.1.4.1.2.6.4.5.1.0');
INSERT INTO pollers (id, name, description, command, parameters) VALUES (194,'dell_om_chassis','Dell OpenManage Chassis','snmp_status','1.3.6.1.4.1.674.10892.1.200.10.1.2.1,1=other|2=unknown|3=ok|4=noncritical|5=critical|6=nonrecoverabl');
INSERT INTO pollers (id, name, description, command, parameters) VALUES (195,'dell_om_temp','Dell OpenManage Ambient Temp','snmp_counter','1.3.6.1.4.1.674.10892.1.700.20.1.6.1.1');
INSERT INTO pollers (id, name, description, command, parameters) VALUES (196,'dell_om_fan_1','Dell OpenManage Fan RPM #1','snmp_counter','1.3.6.1.4.1.674.10892.1.700.12.1.6.1.1');
INSERT INTO pollers (id, name, description, command, parameters) VALUES (197,'dell_om_fan_2','Dell OpenManage Fan RPM #2','snmp_counter','1.3.6.1.4.1.674.10892.1.700.12.1.6.1.2');
INSERT INTO pollers (id, name, description, command, parameters) VALUES (198,'dell_om_fan_3','Dell OpenManage Fan RPM #3','snmp_counter','1.3.6.1.4.1.674.10892.1.700.12.1.6.1.3');
INSERT INTO pollers (id, name, description, command, parameters) VALUES (199,'dell_om_fan_4','Dell OpenManage Fan RPM #4','snmp_counter','1.3.6.1.4.1.674.10892.1.700.12.1.6.1.4');
INSERT INTO pollers (id, name, description, command, parameters) VALUES (200,'dell_om_fan_5','Dell OpenManage Fan RPM #5','snmp_counter','1.3.6.1.4.1.674.10892.1.700.12.1.6.1.5');
INSERT INTO pollers (id, name, description, command, parameters) VALUES (201,'dell_om_fan_6','Dell OpenManage Fan RPM #6','snmp_counter','1.3.6.1.4.1.674.10892.1.700.12.1.6.1.6');
INSERT INTO pollers (id, name, description, command, parameters) VALUES (202,'dell_om_fan_7','Dell OpenManage Fan RPM #7','snmp_counter','1.3.6.1.4.1.674.10892.1.700.12.1.6.1.7');
INSERT INTO pollers (id, name, description, command, parameters) VALUES (203,'power','UPS Lines Power','ups_line','');
INSERT INTO pollers (id, name, description, command, parameters) VALUES (204,'status','PDU Load Status','snmp_status','.1.3.6.1.4.1.318.1.1.12.2.3.1.1.3.<index>,1=load normal|2=load low|3=load near overload|4=load ove');
INSERT INTO pollers (id, name, description, command, parameters) VALUES (205,'load','PDU Banks Load','pdu_banks','');
INSERT INTO pollers (id, name, description, command, parameters) VALUES (206,'ibm_component_health','IBM Component Health Status','snmp_status','1.3.6.1.4.1.2.6.159.1.1.30.3.1.2.<index>,0=up|1=warning|2=down');
INSERT INTO pollers (id, name, description, command, parameters) VALUES (207,'status','IBM Blade Server Health Status','snmp_status_ibm','.1.3.6.1.4.1.2.3.51.2.22.1.5.1.1.5.<index>,1=up|2=warning|3=down');
INSERT INTO pollers (id, name, description, command, parameters) VALUES (208,'temperature','IBM Blade Server CPU1 Temp','snmp_ibm_temperature','.1.3.6.1.4.1.2.3.51.2.22.1.5.3.1.13.<index>');
INSERT INTO pollers (id, name, description, command, parameters) VALUES (209,'status','IBM Blade Power Status','snmp_status_ibm','.1.3.6.1.4.1.2.3.51.2.2.10.1.1.1.3.index>,1=up|2=warning|3=down');
INSERT INTO pollers (id, name, description, command, parameters) VALUES (210,'fuelGaugePowerInUse','IBM Blade Power Gauge','snmp_ibm_power','.1.3.6.1.4.1.2.3.51.2.2.10.1.1.1.10.<index>');
INSERT INTO pollers (id, name, description, command, parameters) VALUES (211,'temperature2','IBM Blade Server CPU2 Temp','snmp_ibm_temperature','.1.3.6.1.4.1.2.3.51.2.22.1.5.3.1.14.<index>');
INSERT INTO pollers (id, name, description, command, parameters) VALUES (212,'status','FC Oper Status','snmp_status','.1.3.6.1.2.1.75.1.2.2.1.2.<real_index>,1=up|2=offline|4=linkFailure');
INSERT INTO pollers (id, name, description, command, parameters) VALUES (213,'rx_frames','FCPort RxFrames','snmp_counter','.1.3.6.1.2.1.75.1.4.3.1.1.<real_index>');
INSERT INTO pollers (id, name, description, command, parameters) VALUES (214,'tx_frames','FCPort TxFrames','snmp_counter','.1.3.6.1.2.1.75.1.4.3.1.2.<real_index>');
INSERT INTO pollers (id, name, description, command, parameters) VALUES (215,'associated','Client Associated','snmp_counter','.1.3.6.1.4.1.9.9.273.1.1.2.1.1.1');
INSERT INTO pollers (id, name, description, command, parameters) VALUES (216,'power_status','Compaq Power Condition','snmp_status','1.3.6.1.4.1.232.6.2.9.3.1.4.<chassis>.<bayindex>,2=up');
INSERT INTO pollers (id, name, description, command, parameters) VALUES (217,'status','IBM Storage Controller status','ibm_ds_storage','controler');
INSERT INTO pollers (id, name, description, command, parameters) VALUES (218,'arraystatus','AS 400 System','snmp_array_null','.1.3.6.1.2.1.25.2.3.1.6');
INSERT INTO pollers (id, name, description, command, parameters) VALUES (219,'cur_disk_q','Inf-64 Disk CurrentDiskQueue','snmp_counter','.1.3.6.1.4.1.9600.1.2.44.1.16.<index>');
INSERT INTO pollers (id, name, description, command, parameters) VALUES (220,'avg_disk_q','Inf-64 Disk AvgDiskQueu','snmp_counter','.1.3.6.1.4.1.9600.1.2.44.1.10.<index>');
INSERT INTO pollers (id, name, description, command, parameters) VALUES (221,'avg_disk_rdq','Inf-64 Disk avg Read DiskQueue','snmp_counter','.1.3.6.1.4.1.9600.1.2.44.1.11.<index>');
INSERT INTO pollers (id, name, description, command, parameters) VALUES (222,'avg_disk_wrq','Inf-64 Disk avg Write DiskQueue','snmp_counter','.1.3.6.1.4.1.9600.1.2.44.1.12.<index>');
INSERT INTO pollers (id, name, description, command, parameters) VALUES (223,'inf_d_read_time','Inf-64 Disk Read Time','snmp_counter','.1.3.6.1.4.1.9600.1.2.44.1.2.<index>');
INSERT INTO pollers (id, name, description, command, parameters) VALUES (224,'inf_d_write_time','Inf-64 Disk Write Time','snmp_counter','.1.3.6.1.4.1.9600.1.2.44.1.4.<index>');
INSERT INTO pollers (id, name, description, command, parameters) VALUES (225,'rd_ops','Inf-64 Disk Read rate','snmp_counter','.1.3.6.1.4.1.9600.1.2.44.1.19.<index>');
INSERT INTO pollers (id, name, description, command, parameters) VALUES (226,'wr_ops','Inf-64 Disk Write rate','snmp_counter','.1.3.6.1.4.1.9600.1.2.44.1.22.<index>');
INSERT INTO pollers (id, name, description, command, parameters) VALUES (227,'inf_d_read_rate','Inf-64 Disk Read Bytes','snmp_counter','.1.3.6.1.4.1.9600.1.2.44.1.18.<index>');
INSERT INTO pollers (id, name, description, command, parameters) VALUES (228,'inf_d_write_rate','Inf-64 Disk Write Bytes','snmp_counter','.1.3.6.1.4.1.9600.1.2.44.1.21.<index>');
INSERT INTO pollers (id, name, description, command, parameters) VALUES (229,'input','SNMP Input Rate HC','snmp_counter','.1.3.6.1.2.1.2.2.1.10.<interfacenumber>,.1.3.6.1.2.1.31.1.1.1.6.<interfacenumber>');
INSERT INTO pollers (id, name, description, command, parameters) VALUES (230,'output','SNMP Output Rate HC','snmp_counter','.1.3.6.1.2.1.2.2.1.16.<interfacenumber>,.1.3.6.1.2.1.31.1.1.1.10.<interfacenumber>');
INSERT INTO pollers (id, name, description, command, parameters) VALUES (231,'inpackets','SNMP Input Packets HC','snmp_counter','.1.3.6.1.2.1.2.2.1.11.<interfacenumber>,.1.3.6.1.2.1.31.1.1.1.7.<interfacenumber>');
INSERT INTO pollers (id, name, description, command, parameters) VALUES (232,'outpackets','SNMP Output Packets HC','snmp_counter','.1.3.6.1.2.1.2.2.1.17.<interfacenumber>,.1.3.6.1.2.1.31.1.1.1.11.<interfacenumber>');
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE pollers_backend (
  id int4 NOT NULL AUTO_INCREMENT,
  description char(60) NOT NULL DEFAULT '',
  command char(60) NOT NULL DEFAULT '',
  parameters char(60) NOT NULL DEFAULT '',
  type int2 NOT NULL DEFAULT '0',
  PRIMARY KEY (id)
);
/*!40101 SET character_set_client = @saved_cs_client */;
INSERT INTO pollers_backend (id, description, command, parameters, type) VALUES (1,'No Backend','no_backend','',0);
INSERT INTO pollers_backend (id, description, command, parameters, type) VALUES (2,'Unknown Event','event','1',0);
INSERT INTO pollers_backend (id, description, command, parameters, type) VALUES (9,'Temporal Buffer','buffer','',0);
INSERT INTO pollers_backend (id, description, command, parameters, type) VALUES (10,'RRDTool All DSs','rrd','*',0);
INSERT INTO pollers_backend (id, description, command, parameters, type) VALUES (12,'Alarm Verify Operational','alarm','3,,180',1);
INSERT INTO pollers_backend (id, description, command, parameters, type) VALUES (14,'Change Interface Number','verify_interface_number','',0);
INSERT INTO pollers_backend (id, description, command, parameters, type) VALUES (19,'Alarm TCP Port','alarm','22',1);
INSERT INTO pollers_backend (id, description, command, parameters, type) VALUES (20,'Alarm Environmental','alarm','26',1);
INSERT INTO pollers_backend (id, description, command, parameters, type) VALUES (24,'Alarm BGP Peer','alarm','6,,180',1);
INSERT INTO pollers_backend (id, description, command, parameters, type) VALUES (25,'Application Alarm','alarm','38',1);
INSERT INTO pollers_backend (id, description, command, parameters, type) VALUES (27,'Alarm TCP Content','alarm','39',1);
INSERT INTO pollers_backend (id, description, command, parameters, type) VALUES (28,'Alarm Reachability','alarm','40',1);
INSERT INTO pollers_backend (id, description, command, parameters, type) VALUES (29,'Admin Status Change View','db','show_rootmap,down=2|up=1,0',1);
INSERT INTO pollers_backend (id, description, command, parameters, type) VALUES (30,'Multiple Temporal Buffer','multi_buffer','',0);
INSERT INTO pollers_backend (id, description, command, parameters, type) VALUES (31,'Alarm NTP','alarm','41,nothing',1);
INSERT INTO pollers_backend (id, description, command, parameters, type) VALUES (32,'RRD Individual Value','rrd','',0);
INSERT INTO pollers_backend (id, description, command, parameters, type) VALUES (33,'Alarm APC','alarm','60',1);
INSERT INTO pollers_backend (id, description, command, parameters, type) VALUES (34,'Alarm SQL Records','alarm','50',1);
INSERT INTO pollers_backend (id, description, command, parameters, type) VALUES (35,'Alteon Admin Status View','db','show_rootmap,down=0|up=2,2',1);
INSERT INTO pollers_backend (id, description, command, parameters, type) VALUES (36,'Alarm Alteon RServer','alarm','68',1);
INSERT INTO pollers_backend (id, description, command, parameters, type) VALUES (37,'Alarm Alteon Service','alarm','69',1);
INSERT INTO pollers_backend (id, description, command, parameters, type) VALUES (38,'Alarm Alteon VServer','alarm','70',1);
INSERT INTO pollers_backend (id, description, command, parameters, type) VALUES (39,'Brocace FC Admin View','db','show_rootmap,down=2|up=1,0',0);
INSERT INTO pollers_backend (id, description, command, parameters, type) VALUES (40,'Alarm Brocade FC Port','alarm','71',1);
INSERT INTO pollers_backend (id, description, command, parameters, type) VALUES (41,'Alarm IBM','alarm','75',1);
INSERT INTO pollers_backend (id, description, command, parameters, type) VALUES (42,'IBM San Trap','event','77',0);
INSERT INTO pollers_backend (id, description, command, parameters, type) VALUES (43,'Alarm OS/400','alarm','78',1);
INSERT INTO pollers_backend (id, description, command, parameters, type) VALUES (44,'Alarm IBM Storage Controller','alarm','80',1);
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE pollers_groups (
  id int4 NOT NULL AUTO_INCREMENT,
  description char(60) NOT NULL DEFAULT '',
  interface_type int4 NOT NULL DEFAULT '1',
  PRIMARY KEY (id)
);
/*!40101 SET character_set_client = @saved_cs_client */;
INSERT INTO pollers_groups (id, description, interface_type) VALUES (1,'No Polling',1);
INSERT INTO pollers_groups (id, description, interface_type) VALUES (2,'Cisco Interface',4);
INSERT INTO pollers_groups (id, description, interface_type) VALUES (3,'Cisco Router',3);
INSERT INTO pollers_groups (id, description, interface_type) VALUES (5,'TCP/IP Port',2);
INSERT INTO pollers_groups (id, description, interface_type) VALUES (8,'BGP Neighbor',6);
INSERT INTO pollers_groups (id, description, interface_type) VALUES (9,'Storage Device',8);
INSERT INTO pollers_groups (id, description, interface_type) VALUES (10,'CSS VIP',9);
INSERT INTO pollers_groups (id, description, interface_type) VALUES (11,'Linux/Unix Host',11);
INSERT INTO pollers_groups (id, description, interface_type) VALUES (12,'Solaris Host',10);
INSERT INTO pollers_groups (id, description, interface_type) VALUES (13,'Windows Host',12);
INSERT INTO pollers_groups (id, description, interface_type) VALUES (14,'Cisco Accounting',13);
INSERT INTO pollers_groups (id, description, interface_type) VALUES (15,'Smokeping Host',14);
INSERT INTO pollers_groups (id, description, interface_type) VALUES (16,'HostMIB Application',15);
INSERT INTO pollers_groups (id, description, interface_type) VALUES (17,'Cisco Power Supply',16);
INSERT INTO pollers_groups (id, description, interface_type) VALUES (18,'Cisco Tempererature',17);
INSERT INTO pollers_groups (id, description, interface_type) VALUES (19,'Cisco Voltage',18);
INSERT INTO pollers_groups (id, description, interface_type) VALUES (20,'Cisco SA Agent',19);
INSERT INTO pollers_groups (id, description, interface_type) VALUES (21,'Reachability',20);
INSERT INTO pollers_groups (id, description, interface_type) VALUES (22,'TC Class',21);
INSERT INTO pollers_groups (id, description, interface_type) VALUES (23,'NTP',22);
INSERT INTO pollers_groups (id, description, interface_type) VALUES (24,'UDP/IP Port',23);
INSERT INTO pollers_groups (id, description, interface_type) VALUES (25,'Compaq Physical Drive',24);
INSERT INTO pollers_groups (id, description, interface_type) VALUES (26,'Compaq Fan',25);
INSERT INTO pollers_groups (id, description, interface_type) VALUES (27,'Compaq Temperature',26);
INSERT INTO pollers_groups (id, description, interface_type) VALUES (28,'IIS Info',27);
INSERT INTO pollers_groups (id, description, interface_type) VALUES (29,'Livingston Portmaster',28);
INSERT INTO pollers_groups (id, description, interface_type) VALUES (30,'Apache',29);
INSERT INTO pollers_groups (id, description, interface_type) VALUES (31,'APC',31);
INSERT INTO pollers_groups (id, description, interface_type) VALUES (32,'ODBC',30);
INSERT INTO pollers_groups (id, description, interface_type) VALUES (33,'Alteon Real Server',32);
INSERT INTO pollers_groups (id, description, interface_type) VALUES (34,'Alteon Virtual Server',33);
INSERT INTO pollers_groups (id, description, interface_type) VALUES (35,'Alteon Real Services',34);
INSERT INTO pollers_groups (id, description, interface_type) VALUES (36,'Alteon System Info',35);
INSERT INTO pollers_groups (id, description, interface_type) VALUES (37,'Brocade Sensors',36);
INSERT INTO pollers_groups (id, description, interface_type) VALUES (38,'Brocade FC Ports',37);
INSERT INTO pollers_groups (id, description, interface_type) VALUES (39,'Cisco Dialup',38);
INSERT INTO pollers_groups (id, description, interface_type) VALUES (40,'Windows Informant Disks',39);
INSERT INTO pollers_groups (id, description, interface_type) VALUES (41,'UPS',40);
INSERT INTO pollers_groups (id, description, interface_type) VALUES (42,'UPS Lines',41);
INSERT INTO pollers_groups (id, description, interface_type) VALUES (43,'IPTable Chain',42);
INSERT INTO pollers_groups (id, description, interface_type) VALUES (44,'PIX Connection Stat',43);
INSERT INTO pollers_groups (id, description, interface_type) VALUES (45,'Cisco NAT',44);
INSERT INTO pollers_groups (id, description, interface_type) VALUES (46,'Sensors',45);
INSERT INTO pollers_groups (id, description, interface_type) VALUES (47,'OS/400 Host',46);
INSERT INTO pollers_groups (id, description, interface_type) VALUES (48,'Dell Chassis',47);
INSERT INTO pollers_groups (id, description, interface_type) VALUES (49,'PDU',48);
INSERT INTO pollers_groups (id, description, interface_type) VALUES (50,'PDU Banks',49);
INSERT INTO pollers_groups (id, description, interface_type) VALUES (57,'IBM Component Health',50);
INSERT INTO pollers_groups (id, description, interface_type) VALUES (51,'IBM Blade Servers',51);
INSERT INTO pollers_groups (id, description, interface_type) VALUES (52,'Fibre Channel Interface',52);
INSERT INTO pollers_groups (id, description, interface_type) VALUES (53,'Cisco 802.11X Device',53);
INSERT INTO pollers_groups (id, description, interface_type) VALUES (54,'IBM Blade Power Module',54);
INSERT INTO pollers_groups (id, description, interface_type) VALUES (55,'Compaq Power Supply',55);
INSERT INTO pollers_groups (id, description, interface_type) VALUES (56,'IBM Storage Controller',56);
INSERT INTO pollers_groups (id, description, interface_type) VALUES (58,'Informant Disks 64',57);
INSERT INTO pollers_groups (id, description, interface_type) VALUES (59,'Cisco Interface HC',4);
INSERT INTO pollers_groups (id, description, interface_type) VALUES (60,'SNMP Interface',4);
INSERT INTO pollers_groups (id, description, interface_type) VALUES (61,'SNMP Interface HC',4);
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE pollers_poller_groups (
  id int4 NOT NULL AUTO_INCREMENT,
  poller_group int4 NOT NULL DEFAULT '1',
  pos int2 NOT NULL DEFAULT '1',
  poller int4 NOT NULL DEFAULT '1',
  backend int4 NOT NULL DEFAULT '1',
  PRIMARY KEY (id)
);
/*!40101 SET character_set_client = @saved_cs_client */;
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (1,1,1,1,1);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (2,2,20,2,9);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (3,2,10,3,14);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (4,2,15,5,9);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (5,2,50,6,1);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (6,2,55,7,9);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (7,2,60,8,9);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (8,2,65,9,1);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (9,2,30,10,9);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (10,2,40,11,9);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (11,2,45,12,9);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (12,2,80,1,10);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (14,2,16,13,12);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (16,3,10,16,9);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (20,3,2,20,19);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (21,2,25,21,9);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (22,2,35,22,9);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (23,5,10,23,30);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (24,3,20,24,9);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (25,3,50,1,10);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (26,3,30,25,9);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (27,2,46,26,9);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (30,8,10,31,32);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (31,8,40,32,32);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (32,8,50,33,32);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (35,9,10,37,9);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (36,9,20,36,9);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (37,9,30,35,9);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (38,9,60,1,10);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (39,8,5,38,24);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (41,10,10,41,9);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (42,10,20,40,9);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (43,10,60,1,10);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (44,11,10,54,9);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (45,11,20,53,9);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (46,11,30,43,9);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (47,11,40,45,9);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (48,11,50,52,9);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (49,11,60,51,9);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (50,11,70,50,9);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (51,11,80,1,10);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (52,12,10,45,9);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (53,12,20,43,9);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (54,12,30,42,9);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (55,12,40,44,9);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (56,12,50,52,9);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (57,12,60,51,9);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (58,12,70,50,9);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (59,12,80,46,9);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (60,12,90,47,9);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (61,12,100,48,9);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (62,12,110,49,9);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (63,12,120,1,10);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (64,2,47,55,9);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (65,2,48,56,9);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (66,5,20,57,9);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (67,5,60,1,10);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (68,3,40,58,30);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (69,13,10,59,9);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (70,13,20,60,9);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (71,13,30,61,9);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (72,13,40,62,9);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (73,13,50,64,9);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (74,13,60,63,9);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (75,13,100,1,10);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (76,11,15,60,9);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (77,11,25,61,9);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (78,11,35,64,9);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (79,11,45,62,9);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (80,11,55,63,9);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (81,3,15,64,9);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (82,3,25,62,9);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (83,3,35,63,9);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (84,14,10,65,9);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (85,14,20,67,9);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (86,14,30,68,9);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (87,14,40,66,9);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (88,14,50,1,10);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (89,15,30,70,9);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (90,15,20,69,9);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (91,15,90,1,10);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (92,16,10,71,30);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (93,17,20,72,20);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (94,18,10,73,20);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (95,19,20,74,20);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (96,18,20,75,9);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (97,18,50,1,10);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (98,20,10,76,11);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (99,20,20,77,9);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (100,20,30,81,9);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (101,20,40,80,9);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (102,20,50,79,9);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (103,20,60,78,9);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (104,20,90,1,10);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (105,15,10,82,14);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (106,5,30,85,27);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (107,21,1,86,9);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (108,21,122,87,1);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (109,21,123,88,9);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (110,21,124,89,9);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (111,21,126,90,1);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (112,21,125,91,28);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (113,21,127,1,10);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (114,22,92,93,9);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (115,22,93,94,9);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (116,22,91,95,14);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (117,22,94,1,10);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (122,2,17,14,29);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (123,5,15,100,19);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (124,16,20,101,25);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (125,16,90,1,10);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (126,23,50,102,31);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (127,16,30,103,9);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (129,24,10,105,30);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (130,24,20,106,19);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (131,24,30,1,10);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (132,27,10,108,20);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (133,27,20,107,9);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (134,27,90,1,10);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (135,26,10,109,20);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (136,25,10,110,20);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (137,28,10,111,9);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (138,28,20,112,9);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (139,28,30,113,9);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (140,28,40,114,9);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (141,28,50,115,9);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (142,28,90,1,10);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (143,29,90,1,10);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (144,29,10,116,9);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (145,29,20,120,9);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (146,29,30,117,9);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (147,29,50,119,9);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (148,29,60,121,9);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (149,29,40,118,9);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (150,30,20,1,10);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (151,30,10,122,30);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (152,31,10,128,20);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (153,31,20,123,9);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (154,31,31,124,9);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (155,31,40,125,9);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (156,31,50,126,9);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (157,31,60,127,9);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (158,31,90,1,10);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (159,31,70,129,9);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (160,31,15,130,33);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (161,32,10,131,30);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (162,32,20,132,34);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (163,32,90,1,10);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (164,33,10,133,35);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (165,33,15,134,12);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (166,33,20,135,9);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (167,33,25,136,9);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (168,33,30,137,9);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (169,33,35,138,9);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (170,33,90,1,10);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (171,34,10,139,29);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (172,34,20,140,9);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (173,34,30,142,9);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (174,34,40,141,9);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (175,34,90,1,10);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (176,35,10,143,29);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (177,35,30,144,37);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (178,35,20,145,9);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (179,35,90,1,10);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (180,36,10,146,9);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (181,36,11,147,9);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (182,36,12,148,9);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (183,36,15,149,9);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (184,36,16,150,9);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (185,36,17,151,9);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (186,36,20,62,9);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (187,36,21,63,9);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (188,36,22,64,9);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (189,36,30,153,9);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (190,36,35,152,9);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (191,36,90,1,10);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (192,37,10,155,12);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (193,37,20,154,9);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (194,37,90,1,10);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (195,38,40,156,9);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (196,38,45,157,9);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (197,38,50,158,9);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (198,38,55,159,9);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (199,38,90,1,10);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (200,38,10,160,39);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (201,38,20,161,12);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (202,38,30,162,40);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (203,39,10,165,9);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (204,39,20,164,9);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (205,39,30,163,9);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (206,39,90,1,10);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (207,40,10,168,32);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (208,40,20,169,32);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (209,40,30,166,32);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (210,40,40,167,32);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (211,41,10,170,20);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (212,41,20,173,32);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (213,41,30,172,32);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (214,41,40,171,32);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (215,42,10,174,32);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (216,42,20,175,32);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (217,42,30,176,32);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (218,43,10,178,32);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (219,43,20,177,32);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (220,8,20,179,32);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (221,8,30,180,32);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (222,44,10,181,32);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (223,45,50,190,32);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (224,45,10,186,32);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (225,45,20,187,32);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (226,45,30,188,32);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (227,45,40,189,32);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (228,45,15,182,32);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (229,45,25,183,32);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (230,45,35,184,32);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (231,45,45,185,32);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (232,46,20,191,32);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (233,9,5,192,14);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (234,47,1,193,32);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (235,48,20,194,29);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (236,48,10,194,12);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (237,48,30,195,32);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (238,48,40,196,9);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (239,48,41,197,9);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (240,48,42,198,9);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (241,48,43,199,9);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (242,48,44,200,9);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (243,48,45,201,9);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (244,48,46,202,9);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (245,48,50,1,10);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (246,42,50,203,32);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (247,49,10,204,32);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (248,50,10,205,32);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (249,57,1,206,41);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (250,51,1,207,20);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (251,51,5,208,9);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (252,52,1,212,20);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (253,52,5,213,9);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (254,52,10,214,9);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (255,52,20,1,10);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (256,51,10,211,9);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (257,51,15,1,10);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (258,53,1,215,32);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (259,54,1,210,32);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (260,54,2,209,41);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (261,55,10,216,20);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (262,56,1,217,44);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (263,47,5,218,43);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (264,58,10,219,32);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (265,58,20,220,32);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (266,58,30,221,32);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (267,58,40,222,32);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (268,58,50,223,32);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (269,58,60,224,32);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (270,58,70,225,32);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (271,58,80,226,32);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (272,58,90,227,32);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (273,58,100,228,32);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (274,59,10,3,14);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (275,59,15,5,9);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (276,59,16,13,12);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (277,59,17,14,29);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (278,59,20,229,9);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (279,59,25,231,9);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (280,59,30,230,9);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (281,59,35,232,9);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (282,59,40,11,9);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (283,59,45,12,9);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (284,59,46,26,9);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (285,59,47,55,9);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (286,59,48,56,9);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (287,59,50,6,1);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (288,59,55,7,9);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (289,59,60,8,9);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (290,59,65,9,1);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (291,59,80,1,10);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (292,60,10,3,14);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (293,60,16,13,12);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (294,60,17,14,29);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (295,60,20,2,9);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (296,60,25,21,9);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (297,60,30,10,9);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (298,60,35,22,9);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (299,60,40,11,9);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (300,60,45,12,9);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (301,60,46,26,9);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (302,60,47,55,9);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (303,60,48,56,9);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (304,60,80,1,10);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (305,61,10,3,14);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (306,61,16,13,12);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (307,61,17,14,29);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (308,61,20,229,9);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (309,61,25,231,9);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (310,61,30,230,9);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (311,61,35,232,9);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (312,61,40,11,9);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (313,61,45,12,9);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (314,61,46,26,9);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (315,61,47,55,9);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (316,61,48,56,9);
INSERT INTO pollers_poller_groups (id, poller_group, pos, poller, backend) VALUES (317,61,80,1,10);
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE profiles (
  id int4 NOT NULL AUTO_INCREMENT,
  userid int4 NOT NULL DEFAULT '1',
  profile_option int4 DEFAULT '1',
  value int4 DEFAULT '1',
  PRIMARY KEY (id)
);
/*!40101 SET character_set_client = @saved_cs_client */;
INSERT INTO profiles (id, userid, profile_option, value) VALUES (1,1,1,1);
INSERT INTO profiles (id, userid, profile_option, value) VALUES (2,2,9,12);
INSERT INTO profiles (id, userid, profile_option, value) VALUES (3,2,11,300);
INSERT INTO profiles (id, userid, profile_option, value) VALUES (4,2,13,30);
INSERT INTO profiles (id, userid, profile_option, value) VALUES (5,2,16,36);
INSERT INTO profiles (id, userid, profile_option, value) VALUES (6,2,20,46);
INSERT INTO profiles (id, userid, profile_option, value) VALUES (7,2,25,50);
INSERT INTO profiles (id, userid, profile_option, value) VALUES (8,2,2,8);
INSERT INTO profiles (id, userid, profile_option, value) VALUES (9,2,8,6);
INSERT INTO profiles (id, userid, profile_option, value) VALUES (10,2,14,32);
INSERT INTO profiles (id, userid, profile_option, value) VALUES (11,2,15,34);
INSERT INTO profiles (id, userid, profile_option, value) VALUES (12,2,6,20);
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE profiles_options (
  id int4 NOT NULL AUTO_INCREMENT,
  tag char(30) NOT NULL DEFAULT '',
  description char(60) NOT NULL DEFAULT '',
  editable int2 NOT NULL DEFAULT '0',
  show_in_profile int2 NOT NULL DEFAULT '1',
  use_default int2 NOT NULL DEFAULT '0',
  default_value char(60) NOT NULL DEFAULT '',
  type char(10) NOT NULL DEFAULT '',
  PRIMARY KEY (id)
);
/*!40101 SET character_set_client = @saved_cs_client */;
INSERT INTO profiles_options (id, tag, description, editable, show_in_profile, use_default, default_value, type) VALUES (1,'NO_TAG','No Option',1,1,0,'','select');
INSERT INTO profiles_options (id, tag, description, editable, show_in_profile, use_default, default_value, type) VALUES (2,'ADMIN_ACCESS','Administration Access',0,1,0,'1','select');
INSERT INTO profiles_options (id, tag, description, editable, show_in_profile, use_default, default_value, type) VALUES (6,'REPORTS_VIEW_ALL_INTERFACES','View All Interfaces',0,1,0,'1','select');
INSERT INTO profiles_options (id, tag, description, editable, show_in_profile, use_default, default_value, type) VALUES (8,'ADMIN_USERS','User Administration',0,1,0,'1','select');
INSERT INTO profiles_options (id, tag, description, editable, show_in_profile, use_default, default_value, type) VALUES (9,'MAP_SOUND','Map Sound',1,1,1,'1','select');
INSERT INTO profiles_options (id, tag, description, editable, show_in_profile, use_default, default_value, type) VALUES (11,'EMAIL','eMail',1,1,1,'','text');
INSERT INTO profiles_options (id, tag, description, editable, show_in_profile, use_default, default_value, type) VALUES (12,'MAP','Base Map',0,0,0,'1','text');
INSERT INTO profiles_options (id, tag, description, editable, show_in_profile, use_default, default_value, type) VALUES (13,'EVENTS_SOUND','Events Sound',1,1,1,'1','select');
INSERT INTO profiles_options (id, tag, description, editable, show_in_profile, use_default, default_value, type) VALUES (14,'ADMIN_SYSTEM','System Administration',0,1,0,'1','select');
INSERT INTO profiles_options (id, tag, description, editable, show_in_profile, use_default, default_value, type) VALUES (15,'ADMIN_HOSTS','Host Administration',0,1,0,'1','select');
INSERT INTO profiles_options (id, tag, description, editable, show_in_profile, use_default, default_value, type) VALUES (16,'VIEW_REPORTS','Reports Access',0,0,1,'1','select');
INSERT INTO profiles_options (id, tag, description, editable, show_in_profile, use_default, default_value, type) VALUES (19,'POPUPS_DISABLED','Disable Popups',0,1,0,'1','select');
INSERT INTO profiles_options (id, tag, description, editable, show_in_profile, use_default, default_value, type) VALUES (20,'VIEW_STARTPAGE_STATS','View Start Page Stats',1,1,1,'1','select');
INSERT INTO profiles_options (id, tag, description, editable, show_in_profile, use_default, default_value, type) VALUES (21,'EVENTS_DEFAULT_FILTER','Events Default Filter',1,1,0,'0','text');
INSERT INTO profiles_options (id, tag, description, editable, show_in_profile, use_default, default_value, type) VALUES (22,'EVENTS_REFRESH','Events Refresh Interval (secs)',1,1,0,'20','text');
INSERT INTO profiles_options (id, tag, description, editable, show_in_profile, use_default, default_value, type) VALUES (23,'MAP_REFRESH','Map Refresh Interval (secs)',1,1,0,'20','text');
INSERT INTO profiles_options (id, tag, description, editable, show_in_profile, use_default, default_value, type) VALUES (24,'SMSALIAS','SMS Pager Alias',1,1,0,'','text');
INSERT INTO profiles_options (id, tag, description, editable, show_in_profile, use_default, default_value, type) VALUES (25,'VIEW_TYPE_DEFAULT','Default View Type',1,1,1,'dhtml','select');
INSERT INTO profiles_options (id, tag, description, editable, show_in_profile, use_default, default_value, type) VALUES (26,'VIEW_DEFAULT','Default View',1,1,0,'start','select');
INSERT INTO profiles_options (id, tag, description, editable, show_in_profile, use_default, default_value, type) VALUES (27,'CUSTOMER','Customer Filter',0,1,0,'','text');
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE profiles_values (
  id int4 NOT NULL AUTO_INCREMENT,
  profile_option int4 NOT NULL DEFAULT '1',
  description char(30) NOT NULL DEFAULT '',
  value char(250) NOT NULL DEFAULT '',
  PRIMARY KEY (id)
);
/*!40101 SET character_set_client = @saved_cs_client */;
INSERT INTO profiles_values (id, profile_option, description, value) VALUES (1,1,'No Value','1');
INSERT INTO profiles_values (id, profile_option, description, value) VALUES (5,9,'Disable','0');
INSERT INTO profiles_values (id, profile_option, description, value) VALUES (6,8,'Yes','1');
INSERT INTO profiles_values (id, profile_option, description, value) VALUES (7,8,'No','0');
INSERT INTO profiles_values (id, profile_option, description, value) VALUES (8,2,'Yes','1');
INSERT INTO profiles_values (id, profile_option, description, value) VALUES (12,9,'Enable','1');
INSERT INTO profiles_values (id, profile_option, description, value) VALUES (20,6,'Yes','1');
INSERT INTO profiles_values (id, profile_option, description, value) VALUES (21,6,'No','0');
INSERT INTO profiles_values (id, profile_option, description, value) VALUES (30,13,'Yes','1');
INSERT INTO profiles_values (id, profile_option, description, value) VALUES (31,13,'No','0');
INSERT INTO profiles_values (id, profile_option, description, value) VALUES (32,14,'Yes','1');
INSERT INTO profiles_values (id, profile_option, description, value) VALUES (33,14,'No','0');
INSERT INTO profiles_values (id, profile_option, description, value) VALUES (34,15,'Yes','1');
INSERT INTO profiles_values (id, profile_option, description, value) VALUES (35,15,'No','0');
INSERT INTO profiles_values (id, profile_option, description, value) VALUES (36,16,'Yes','1');
INSERT INTO profiles_values (id, profile_option, description, value) VALUES (37,16,'No','0');
INSERT INTO profiles_values (id, profile_option, description, value) VALUES (43,19,'Yes','1');
INSERT INTO profiles_values (id, profile_option, description, value) VALUES (44,19,'No','0');
INSERT INTO profiles_values (id, profile_option, description, value) VALUES (46,20,'Yes','1');
INSERT INTO profiles_values (id, profile_option, description, value) VALUES (47,20,'No','0');
INSERT INTO profiles_values (id, profile_option, description, value) VALUES (48,25,'Normal','normal');
INSERT INTO profiles_values (id, profile_option, description, value) VALUES (49,25,'Text Only','text');
INSERT INTO profiles_values (id, profile_option, description, value) VALUES (50,25,'DHTML','dhtml');
INSERT INTO profiles_values (id, profile_option, description, value) VALUES (52,25,'Normal Big','normal-big');
INSERT INTO profiles_values (id, profile_option, description, value) VALUES (53,25,'DHTML Big','dhtml-big');
INSERT INTO profiles_values (id, profile_option, description, value) VALUES (55,26,'Start Page','start');
INSERT INTO profiles_values (id, profile_option, description, value) VALUES (56,26,'Hosts & Events','hosts-events');
INSERT INTO profiles_values (id, profile_option, description, value) VALUES (57,26,'Interfaces & Events','interfaces-events');
INSERT INTO profiles_values (id, profile_option, description, value) VALUES (58,26,'Maps & Events','maps-events');
INSERT INTO profiles_values (id, profile_option, description, value) VALUES (59,26,'Alarmed Interfaces & Events','alarmed-events');
INSERT INTO profiles_values (id, profile_option, description, value) VALUES (60,26,'Alarmed Interfaces','alarmed');
INSERT INTO profiles_values (id, profile_option, description, value) VALUES (61,26,'Interfaces','interfaces');
INSERT INTO profiles_values (id, profile_option, description, value) VALUES (62,26,'Hosts','hosts');
INSERT INTO profiles_values (id, profile_option, description, value) VALUES (63,26,'Maps','maps');
INSERT INTO profiles_values (id, profile_option, description, value) VALUES (64,26,'Hosts All Interfaces','hosts-all-int');
INSERT INTO profiles_values (id, profile_option, description, value) VALUES (65,26,'Events','events');
INSERT INTO profiles_values (id, profile_option, description, value) VALUES (66,26,'Alarmed Hosts & Events','alarmed-hosts-events');
INSERT INTO profiles_values (id, profile_option, description, value) VALUES (300,11,'','');
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE satellites (
  id int4 NOT NULL AUTO_INCREMENT,
  description char(40) NOT NULL DEFAULT '',
  parent int4 NOT NULL DEFAULT '1',
  url char(150) NOT NULL DEFAULT '',
  sat_group int4 NOT NULL DEFAULT '1',
  sat_type int2 NOT NULL DEFAULT '0',
  PRIMARY KEY (id)
);
/*!40101 SET character_set_client = @saved_cs_client */;
INSERT INTO satellites (id, description, parent, url, sat_group, sat_type) VALUES (1,'Local',1,'',1,1);
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE severity (
  id int2 NOT NULL AUTO_INCREMENT,
  level int2 NOT NULL DEFAULT '0',
  severity char(20) NOT NULL DEFAULT '',
  bgcolor char(15) NOT NULL DEFAULT '',
  fgcolor char(15) NOT NULL DEFAULT '',
  PRIMARY KEY (id)
);
/*!40101 SET character_set_client = @saved_cs_client */;
INSERT INTO severity (id, level, severity, bgcolor, fgcolor) VALUES (1,127,'Unknown','000000','FFFFFF');
INSERT INTO severity (id, level, severity, bgcolor, fgcolor) VALUES (2,30,'Warning','00AA00','FFFFFF');
INSERT INTO severity (id, level, severity, bgcolor, fgcolor) VALUES (3,40,'Fault','F51D30','EEEEEE');
INSERT INTO severity (id, level, severity, bgcolor, fgcolor) VALUES (4,50,'Big Fault','DA4725','FFFFFF');
INSERT INTO severity (id, level, severity, bgcolor, fgcolor) VALUES (5,60,'Critical','FF0000','FFFFFF');
INSERT INTO severity (id, level, severity, bgcolor, fgcolor) VALUES (13,10,'Administrative','8D00BA','FFFFFF');
INSERT INTO severity (id, level, severity, bgcolor, fgcolor) VALUES (14,20,'Information','F9FD5F','000000');
INSERT INTO severity (id, level, severity, bgcolor, fgcolor) VALUES (18,35,'Service','0090F0','FFFFFF');
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE slas (
  id int4 NOT NULL AUTO_INCREMENT,
  description char(60) NOT NULL DEFAULT '',
  state int4 NOT NULL DEFAULT '3',
  info char(60) NOT NULL DEFAULT '',
  event_type int4 NOT NULL DEFAULT '12',
  threshold int2 NOT NULL DEFAULT '100',
  interface_type int4 NOT NULL DEFAULT '1',
  PRIMARY KEY (id)
);
/*!40101 SET character_set_client = @saved_cs_client */;
INSERT INTO slas (id, description, state, info, event_type, threshold, interface_type) VALUES (1,'No SLA',3,'No SLA',12,100,1);
INSERT INTO slas (id, description, state, info, event_type, threshold, interface_type) VALUES (4,'Customer Satellite Link',3,'Customer Sat Link:',12,75,4);
INSERT INTO slas (id, description, state, info, event_type, threshold, interface_type) VALUES (5,'Main Fiber Link',3,'Main Link:',12,100,4);
INSERT INTO slas (id, description, state, info, event_type, threshold, interface_type) VALUES (6,'Main Satellite Link',3,'Main Sat Link:',12,100,4);
INSERT INTO slas (id, description, state, info, event_type, threshold, interface_type) VALUES (7,'Cisco Router',3,'Router:',12,100,3);
INSERT INTO slas (id, description, state, info, event_type, threshold, interface_type) VALUES (8,'Smokeping Host',3,'Smokeping:',12,100,14);
INSERT INTO slas (id, description, state, info, event_type, threshold, interface_type) VALUES (9,'Storage',3,'Storage',12,100,8);
INSERT INTO slas (id, description, state, info, event_type, threshold, interface_type) VALUES (10,'Linux/Unix CPU',3,'',12,100,11);
INSERT INTO slas (id, description, state, info, event_type, threshold, interface_type) VALUES (11,'Windows CPU',3,'',12,100,12);
INSERT INTO slas (id, description, state, info, event_type, threshold, interface_type) VALUES (12,'APC UPS',3,'APC UPS',12,100,31);
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE slas_cond (
  id int4 NOT NULL AUTO_INCREMENT,
  cond char(250) NOT NULL,
  description char(60) NOT NULL DEFAULT '',
  event char(60) NOT NULL DEFAULT '',
  variable_show char(250) NOT NULL DEFAULT '',
  variable_show_info char(60) NOT NULL DEFAULT '',
  PRIMARY KEY (id)
);
/*!40101 SET character_set_client = @saved_cs_client */;
INSERT INTO slas_cond (id, cond, description, event, variable_show, variable_show_info) VALUES (1,'1=2','Unknown','Unknown','','');
INSERT INTO slas_cond (id, cond, description, event, variable_show, variable_show_info) VALUES (2,'(<rtt> > 60)','RoundTrip Time > 60ms','RTT > 60','<rtt>','ms');
INSERT INTO slas_cond (id, cond, description, event, variable_show, variable_show_info) VALUES (3,'( ((<packetloss> * 100) / <pings>) > 20)','Packet Loss > 20%','PL > 20%','((<packetloss> * 100) / <pings>)','%');
INSERT INTO slas_cond (id, cond, description, event, variable_show, variable_show_info) VALUES (4,'(<in> < ((<bandwidthin>*95)/100))','Input Traffic < 95%','IN < 95%','(<in> / 1000)','Kbps');
INSERT INTO slas_cond (id, cond, description, event, variable_show, variable_show_info) VALUES (5,'AND','AND','','','');
INSERT INTO slas_cond (id, cond, description, event, variable_show, variable_show_info) VALUES (6,'OR','OR','','','');
INSERT INTO slas_cond (id, cond, description, event, variable_show, variable_show_info) VALUES (7,'(<rtt> > 700)','RoundTrip Time > 700ms','RTT > 700','<rtt>','ms');
INSERT INTO slas_cond (id, cond, description, event, variable_show, variable_show_info) VALUES (8,'(<rtt> > 900)','RoundTrip Time > 900ms','RTT > 900','<rtt>','ms');
INSERT INTO slas_cond (id, cond, description, event, variable_show, variable_show_info) VALUES (9,'(((<packetloss> * 100) / <pings>) > 50)','Packet Loss > 50%','PL > 50%','((<packetloss> * 100) / <pings>)','%');
INSERT INTO slas_cond (id, cond, description, event, variable_show, variable_show_info) VALUES (11,'(<in> > ((<bandwidthin>*90)/100))','Input Traffic > 90%','IN > 90%','(<in> / 1000)','Kbps');
INSERT INTO slas_cond (id, cond, description, event, variable_show, variable_show_info) VALUES (12,'(<in> < ((<bandwidthin>*1)/100))','Input Traffic < 1%','IN < 1 %','','');
INSERT INTO slas_cond (id, cond, description, event, variable_show, variable_show_info) VALUES (13,' (<out> > ((<bandwidthout>*90)/100))','Output Traffic > 90%','OUT > 90%','(<out> / 1000 )','kbps');
INSERT INTO slas_cond (id, cond, description, event, variable_show, variable_show_info) VALUES (14,'(<out> < ((<bandwidthout>*95)/100))','Output Traffic < 95%','OUT < 95%','(<out> / 1000 )','kbps');
INSERT INTO slas_cond (id, cond, description, event, variable_show, variable_show_info) VALUES (15,'( ( (<inerrors> / (<inpackets> + <inerrors> + 1) )*100) > 20)','Input Error Rate > 20%','IN ERR > 20%','( (<inerrors> / (<inpackets> + <inerrors> + 1) )*100)','% = <inerrors> eps');
INSERT INTO slas_cond (id, cond, description, event, variable_show, variable_show_info) VALUES (16,'( ( (<inerrors> / (<inpackets> + <inerrors> + 1) )*100) > 10)','Input Error Rate > 10%','IN ERR > 10%','( (<inerrors> / (<inpackets> + <inerrors> + 1) )*100)','% = <inerrors> eps');
INSERT INTO slas_cond (id, cond, description, event, variable_show, variable_show_info) VALUES (18,'( ( (<drops> / (<outpackets> + 1) )*100) > 1)','Drops > 1%','Drops > 1%','( (<drops> / (<outpackets> + 1) )*100)','% = <drops> dps');
INSERT INTO slas_cond (id, cond, description, event, variable_show, variable_show_info) VALUES (19,' ( ( (<drops> / (<outpackets> + 1) )*100) > 2)','Drops > 2%','Drops > 2%','( (<drops> / (<outpackets> + 1) )*100)','% = <drops> dps');
INSERT INTO slas_cond (id, cond, description, event, variable_show, variable_show_info) VALUES (20,'(((<packetloss> * 100) / <pings>) > 10)','Packet Loss > 10%','PL > 10%','((<packetloss> * 100) / <pings>)','%');
INSERT INTO slas_cond (id, cond, description, event, variable_show, variable_show_info) VALUES (21,'( ( (<drops> / (<outpackets> +<drops> + 1) )*100) > 10)','Drops > 10%','Drops > 10%','( (<drops> / (<outpackets> +<drops> + 1) )*100)','%');
INSERT INTO slas_cond (id, cond, description, event, variable_show, variable_show_info) VALUES (22,' (<in> < ((<bandwidthin>*99)/100))','Input Traffic < 99%','IN < 99%',' (<in> / 1000 )','Kbps');
INSERT INTO slas_cond (id, cond, description, event, variable_show, variable_show_info) VALUES (23,' (<out> < ((<bandwidthout>*99)/100))',' Output Traffic < 99%',' OUT < 99%',' (<out> / 1000 )','Kbps');
INSERT INTO slas_cond (id, cond, description, event, variable_show, variable_show_info) VALUES (24,'(<cpu> > <cpu_threshold>)','High CPU Utilization','Usage > <cpu_threshold>%','<cpu>','%');
INSERT INTO slas_cond (id, cond, description, event, variable_show, variable_show_info) VALUES (25,'(<packetloss> > 10)','SP Packet Loss > 10%','Packet Loss > 10%','<packetloss>','%');
INSERT INTO slas_cond (id, cond, description, event, variable_show, variable_show_info) VALUES (26,'( <storage_used_blocks> > ((<storage_block_count>*<usage_threshold>)/100))','Used Storage','Used > <usage_threshold>%','((<storage_used_blocks> * 100)/<storage_block_count>)','%');
INSERT INTO slas_cond (id, cond, description, event, variable_show, variable_show_info) VALUES (27,'( <load_average_5> > 5 )','Load Average > 5','Load Average > 5','<load_average_5>','');
INSERT INTO slas_cond (id, cond, description, event, variable_show, variable_show_info) VALUES (28,'(((( <cpu_user_ticks> + <cpu_nice_ticks> + <cpu_system_ticks> ) * 100 ) / ( <cpu_user_ticks> + <cpu_idle_ticks> + <cpu_nice_ticks> + <cpu_system_ticks> )) > <cpu_threshold>)','High CPU Utilization','Usage > <cpu_threshold>%','((( <cpu_user_ticks> + <cpu_nice_ticks> + <cpu_system_ticks> ) * 100 ) / ( <cpu_user_ticks> + <cpu_idle_ticks> + <cpu_nice_ticks> + <cpu_system_ticks> ))','%');
INSERT INTO slas_cond (id, cond, description, event, variable_show, variable_show_info) VALUES (29,'( ((<mem_used> * 100) / (<mem_used> + <mem_free>)) > 80)','Memory Usage > 80%','Memory Usage > 80%','((<mem_used> * 100) / (<mem_used> + <mem_free>))','%');
INSERT INTO slas_cond (id, cond, description, event, variable_show, variable_show_info) VALUES (30,'(<cpu> > 90)','CPU Utilization > 90%','CPU > 90%','<cpu>','%');
INSERT INTO slas_cond (id, cond, description, event, variable_show, variable_show_info) VALUES (31,'(<num_procs> > <proc_threshold>)','Too Many Processes','Processes > <proc_threshold>','<num_procs>','Processes');
INSERT INTO slas_cond (id, cond, description, event, variable_show, variable_show_info) VALUES (32,'(<temperature> > 55)','APC temp > 55','APC temp > 55','<temperature>','C');
INSERT INTO slas_cond (id, cond, description, event, variable_show, variable_show_info) VALUES (33,'(<time_remaining> < 300000)','APC time < 50 minutes','APC time < 50 minutes','<time_remaining>','min');
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE slas_sla_cond (
  id int4 NOT NULL AUTO_INCREMENT,
  pos int2 NOT NULL DEFAULT '1',
  sla int4 NOT NULL DEFAULT '1',
  cond int4 NOT NULL DEFAULT '1',
  show_in_result int2 NOT NULL DEFAULT '0',
  PRIMARY KEY (id)
);
/*!40101 SET character_set_client = @saved_cs_client */;
INSERT INTO slas_sla_cond (id, pos, sla, cond, show_in_result) VALUES (1,1,1,1,0);
INSERT INTO slas_sla_cond (id, pos, sla, cond, show_in_result) VALUES (9,30,4,4,0);
INSERT INTO slas_sla_cond (id, pos, sla, cond, show_in_result) VALUES (10,40,4,7,1);
INSERT INTO slas_sla_cond (id, pos, sla, cond, show_in_result) VALUES (11,50,4,20,1);
INSERT INTO slas_sla_cond (id, pos, sla, cond, show_in_result) VALUES (12,70,4,6,0);
INSERT INTO slas_sla_cond (id, pos, sla, cond, show_in_result) VALUES (13,74,4,5,0);
INSERT INTO slas_sla_cond (id, pos, sla, cond, show_in_result) VALUES (14,10,5,11,1);
INSERT INTO slas_sla_cond (id, pos, sla, cond, show_in_result) VALUES (15,20,5,2,1);
INSERT INTO slas_sla_cond (id, pos, sla, cond, show_in_result) VALUES (16,40,5,20,1);
INSERT INTO slas_sla_cond (id, pos, sla, cond, show_in_result) VALUES (17,50,5,6,0);
INSERT INTO slas_sla_cond (id, pos, sla, cond, show_in_result) VALUES (18,60,5,6,0);
INSERT INTO slas_sla_cond (id, pos, sla, cond, show_in_result) VALUES (19,10,6,11,1);
INSERT INTO slas_sla_cond (id, pos, sla, cond, show_in_result) VALUES (20,30,6,7,1);
INSERT INTO slas_sla_cond (id, pos, sla, cond, show_in_result) VALUES (21,60,6,6,0);
INSERT INTO slas_sla_cond (id, pos, sla, cond, show_in_result) VALUES (22,40,6,3,1);
INSERT INTO slas_sla_cond (id, pos, sla, cond, show_in_result) VALUES (23,50,6,6,0);
INSERT INTO slas_sla_cond (id, pos, sla, cond, show_in_result) VALUES (33,20,4,14,0);
INSERT INTO slas_sla_cond (id, pos, sla, cond, show_in_result) VALUES (35,75,4,6,0);
INSERT INTO slas_sla_cond (id, pos, sla, cond, show_in_result) VALUES (36,15,5,13,1);
INSERT INTO slas_sla_cond (id, pos, sla, cond, show_in_result) VALUES (37,70,5,6,0);
INSERT INTO slas_sla_cond (id, pos, sla, cond, show_in_result) VALUES (38,20,6,13,1);
INSERT INTO slas_sla_cond (id, pos, sla, cond, show_in_result) VALUES (39,70,6,6,0);
INSERT INTO slas_sla_cond (id, pos, sla, cond, show_in_result) VALUES (40,10,4,16,1);
INSERT INTO slas_sla_cond (id, pos, sla, cond, show_in_result) VALUES (41,73,4,5,0);
INSERT INTO slas_sla_cond (id, pos, sla, cond, show_in_result) VALUES (42,45,6,16,1);
INSERT INTO slas_sla_cond (id, pos, sla, cond, show_in_result) VALUES (43,55,6,6,0);
INSERT INTO slas_sla_cond (id, pos, sla, cond, show_in_result) VALUES (44,30,5,16,1);
INSERT INTO slas_sla_cond (id, pos, sla, cond, show_in_result) VALUES (45,45,5,6,0);
INSERT INTO slas_sla_cond (id, pos, sla, cond, show_in_result) VALUES (47,60,4,18,1);
INSERT INTO slas_sla_cond (id, pos, sla, cond, show_in_result) VALUES (48,72,4,6,0);
INSERT INTO slas_sla_cond (id, pos, sla, cond, show_in_result) VALUES (49,10,7,24,1);
INSERT INTO slas_sla_cond (id, pos, sla, cond, show_in_result) VALUES (50,10,8,25,1);
INSERT INTO slas_sla_cond (id, pos, sla, cond, show_in_result) VALUES (51,30,8,6,0);
INSERT INTO slas_sla_cond (id, pos, sla, cond, show_in_result) VALUES (52,20,8,2,1);
INSERT INTO slas_sla_cond (id, pos, sla, cond, show_in_result) VALUES (53,1,9,26,1);
INSERT INTO slas_sla_cond (id, pos, sla, cond, show_in_result) VALUES (54,10,10,27,1);
INSERT INTO slas_sla_cond (id, pos, sla, cond, show_in_result) VALUES (55,20,10,28,1);
INSERT INTO slas_sla_cond (id, pos, sla, cond, show_in_result) VALUES (56,30,10,6,0);
INSERT INTO slas_sla_cond (id, pos, sla, cond, show_in_result) VALUES (57,20,7,29,1);
INSERT INTO slas_sla_cond (id, pos, sla, cond, show_in_result) VALUES (58,30,7,6,0);
INSERT INTO slas_sla_cond (id, pos, sla, cond, show_in_result) VALUES (59,10,11,30,1);
INSERT INTO slas_sla_cond (id, pos, sla, cond, show_in_result) VALUES (60,20,11,31,1);
INSERT INTO slas_sla_cond (id, pos, sla, cond, show_in_result) VALUES (61,30,11,6,0);
INSERT INTO slas_sla_cond (id, pos, sla, cond, show_in_result) VALUES (62,5,12,32,1);
INSERT INTO slas_sla_cond (id, pos, sla, cond, show_in_result) VALUES (63,10,12,6,0);
INSERT INTO slas_sla_cond (id, pos, sla, cond, show_in_result) VALUES (64,1,12,33,1);
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE syslog (
  date datetime NOT NULL DEFAULT NULL,
  host varchar(128) DEFAULT NULL,
  date_logged datetime NOT NULL DEFAULT NULL,
  message text,
  id int4 NOT NULL AUTO_INCREMENT,
  analized int2 NOT NULL DEFAULT '0',
  PRIMARY KEY (id)
);
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE syslog_types (
  id int4 NOT NULL AUTO_INCREMENT,
  match_text char(255) NOT NULL DEFAULT '',
  interface char(10) NOT NULL DEFAULT '',
  username char(20) NOT NULL DEFAULT '',
  state char(10) NOT NULL DEFAULT '',
  info char(10) NOT NULL DEFAULT '',
  type int4 NOT NULL DEFAULT '1',
  pos int2 NOT NULL DEFAULT '1',
  PRIMARY KEY (id)
);
/*!40101 SET character_set_client = @saved_cs_client */;
INSERT INTO syslog_types (id, match_text, interface, username, state, info, type, pos) VALUES (1,'UNKNOWN','0','','','*',1,1);
INSERT INTO syslog_types (id, match_text, interface, username, state, info, type, pos) VALUES (2,'%SYS-5-CONFIG_I:.+|%SYS-5-CONFIG:','7','5','','3',2,1);
INSERT INTO syslog_types (id, match_text, interface, username, state, info, type, pos) VALUES (3,'%LINEPROTO-5-UPDOWN:','5','','9','',3,1);
INSERT INTO syslog_types (id, match_text, interface, username, state, info, type, pos) VALUES (4,'%LINK-3-UPDOWN:','2','','6','',4,1);
INSERT INTO syslog_types (id, match_text, interface, username, state, info, type, pos) VALUES (5,'%CONTROLLER-5-UPDOWN:','3','','7','2',5,1);
INSERT INTO syslog_types (id, match_text, interface, username, state, info, type, pos) VALUES (6,'%BGP-5-ADJCHANGE:','2','','3','6',6,1);
INSERT INTO syslog_types (id, match_text, interface, username, state, info, type, pos) VALUES (7,'%LINK-5-CHANGED:','2','','7','6',7,1);
INSERT INTO syslog_types (id, match_text, interface, username, state, info, type, pos) VALUES (9,'%RCMD-4-RSHPORTATTEMPT:','5','','','7',9,1);
INSERT INTO syslog_types (id, match_text, interface, username, state, info, type, pos) VALUES (17,'%CLEAR-5-COUNTERS:','5','7','','10',17,1);
INSERT INTO syslog_types (id, match_text, interface, username, state, info, type, pos) VALUES (20,'%PIX-2-106006:','7','5','1','3',29,1);
INSERT INTO syslog_types (id, match_text, interface, username, state, info, type, pos) VALUES (21,'%PIX-2-106007:','7','5','1','3',29,1);
INSERT INTO syslog_types (id, match_text, interface, username, state, info, type, pos) VALUES (22,'%PIX-2-106001:','8','6','4','2',29,1);
INSERT INTO syslog_types (id, match_text, interface, username, state, info, type, pos) VALUES (25,'%PIX-3-106010:','7','5','1','3',29,1);
INSERT INTO syslog_types (id, match_text, interface, username, state, info, type, pos) VALUES (26,'%PIX-3-106014:','7','5','1','3',29,1);
INSERT INTO syslog_types (id, match_text, interface, username, state, info, type, pos) VALUES (29,'%PIX-3-305006:.+|%PIX-2-106012:.+|%PIX-3-305005:.+|%PIX-3-307001:.+','','','','D',28,1);
INSERT INTO syslog_types (id, match_text, interface, username, state, info, type, pos) VALUES (30,'%CDP-4-DUPLEX_MISMATCH:','5','10','','11',34,1);
INSERT INTO syslog_types (id, match_text, interface, username, state, info, type, pos) VALUES (31,'%SEC-6-IPACCESSLOGS:','2','4','3','5',35,1);
INSERT INTO syslog_types (id, match_text, interface, username, state, info, type, pos) VALUES (32,'%SEC-6-IPACCESSLOGP:.+|%SEC-6-IPACCESSLOGNP:','2','7','3','8',35,1);
INSERT INTO syslog_types (id, match_text, interface, username, state, info, type, pos) VALUES (33,'%BGP-3-NOTIFICATION: (\\S+ \\S+) neighbor (\\S+) \\S+ (\\S+ \\S+ \\S+) \\S+ \\S+','2','','1','3',36,1);
INSERT INTO syslog_types (id, match_text, interface, username, state, info, type, pos) VALUES (34,'%SYS-5-RESTART:','','','','D',26,1);
INSERT INTO syslog_types (id, match_text, interface, username, state, info, type, pos) VALUES (35,'%SYS-5-RELOAD:','','','','D',26,1);
INSERT INTO syslog_types (id, match_text, interface, username, state, info, type, pos) VALUES (36,'%SEC-6-IPACCESSLOGDP:','2','5','3','9',35,1);
INSERT INTO syslog_types (id, match_text, interface, username, state, info, type, pos) VALUES (37,'EXCESSCOLL:','1','','','',37,1);
INSERT INTO syslog_types (id, match_text, interface, username, state, info, type, pos) VALUES (38,'^([^[]+)(?:\\[\\d+\\])?:\\s+(.+)$','1','','','2',44,100);
INSERT INTO syslog_types (id, match_text, interface, username, state, info, type, pos) VALUES (39,'CRON\\[\\d+\\]: \\((\\S+)\\) CMD (.*)','cron','1','','2',45,1);
INSERT INTO syslog_types (id, match_text, interface, username, state, info, type, pos) VALUES (40,'^(\\S.*)\\[info\\]\\s*(\\S+)\\s*(\\S.*)','1','','2','3',46,10);
INSERT INTO syslog_types (id, match_text, interface, username, state, info, type, pos) VALUES (41,'^(\\S.*)\\[error\\]\\s*(\\S+)\\s*(\\S.*)','1','','2','3',48,10);
INSERT INTO syslog_types (id, match_text, interface, username, state, info, type, pos) VALUES (42,'^(\\S.*)\\[warning\\]\\s*(\\S+)\\s*(\\S.*)','1','','2','3',47,10);
INSERT INTO syslog_types (id, match_text, interface, username, state, info, type, pos) VALUES (43,'^security\\[failure\\] (\\d*) (.*)','','','1','2',49,10);
INSERT INTO syslog_types (id, match_text, interface, username, state, info, type, pos) VALUES (44,'%PIX-1-(\\d*): (.*)','1','','','2',67,2);
INSERT INTO syslog_types (id, match_text, interface, username, state, info, type, pos) VALUES (45,'%PIX-2-(\\d*): (.*)','1','','','2',66,2);
INSERT INTO syslog_types (id, match_text, interface, username, state, info, type, pos) VALUES (46,'%PIX-3-(\\d*): (.*)','1','','','2',65,2);
INSERT INTO syslog_types (id, match_text, interface, username, state, info, type, pos) VALUES (47,'%PIX-4-(\\d*): (.*)','1','','','2',64,2);
INSERT INTO syslog_types (id, match_text, interface, username, state, info, type, pos) VALUES (48,'%PIX-6-(\\d*): (.*)','1','','','2',62,2);
INSERT INTO syslog_types (id, match_text, interface, username, state, info, type, pos) VALUES (49,'%PIX-5-(\\d*): (.*)','1','','','2',63,2);
INSERT INTO syslog_types (id, match_text, interface, username, state, info, type, pos) VALUES (50,'%PIX-7-(\\d*): (.*)','1','','','2',61,2);
INSERT INTO syslog_types (id, match_text, interface, username, state, info, type, pos) VALUES (51,'%PIX-4-106023:','6','4','1','2',29,1);
INSERT INTO syslog_types (id, match_text, interface, username, state, info, type, pos) VALUES (52,'^UPS: (.*)\\. (.*)$','UPS','','','1',26,1);
INSERT INTO syslog_types (id, match_text, interface, username, state, info, type, pos) VALUES (53,'WebOS <slb>: real server (\\S+) operational','1','','up','',68,1);
INSERT INTO syslog_types (id, match_text, interface, username, state, info, type, pos) VALUES (54,'WebOS <slb>: cannot contact real server (\\S+)','1','','down','',68,1);
INSERT INTO syslog_types (id, match_text, interface, username, state, info, type, pos) VALUES (55,'WebOS <slb>: No services are available for Virtual Server\\d+:(\\S+)','1','','down','',70,1);
INSERT INTO syslog_types (id, match_text, interface, username, state, info, type, pos) VALUES (56,'WebOS <slb>: Services are available for Virtual Server\\d+:(\\S+)','1','','up','',70,1);
INSERT INTO syslog_types (id, match_text, interface, username, state, info, type, pos) VALUES (57,'WebOS <slb>: real service (\\S+) operational','1','','up','',69,1);
INSERT INTO syslog_types (id, match_text, interface, username, state, info, type, pos) VALUES (58,'WebOS <slb>: cannot contact real service (\\S+)','1','','closed','',69,1);
INSERT INTO syslog_types (id, match_text, interface, username, state, info, type, pos) VALUES (59,'%ISDN-6-CONNECT: Interface (\\S+) is now (\\S+) (.+)$','1','','2','3',72,1);
INSERT INTO syslog_types (id, match_text, interface, username, state, info, type, pos) VALUES (60,'%ISDN-6-DISCONNECT: Interface (\\S+) (\\S+) (.+)$','1','','2','3',72,1);
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE tools (
  id int4 NOT NULL AUTO_INCREMENT,
  description char(60) NOT NULL DEFAULT '',
  name char(30) NOT NULL DEFAULT '',
  file_group char(30) NOT NULL DEFAULT '',
  itype int4 NOT NULL DEFAULT '1',
  pos int4 NOT NULL DEFAULT '1',
  allow_set int2 NOT NULL DEFAULT '0',
  allow_get int2 NOT NULL DEFAULT '1',
  PRIMARY KEY (id)
);
/*!40101 SET character_set_client = @saved_cs_client */;
INSERT INTO tools (id, description, name, file_group, itype, pos, allow_set, allow_get) VALUES (1,'Nothing','none','none',1,1,0,1);
INSERT INTO tools (id, description, name, file_group, itype, pos, allow_set, allow_get) VALUES (3,'Description','if_alias','',4,2,1,1);
INSERT INTO tools (id, description, name, file_group, itype, pos, allow_set, allow_get) VALUES (4,'Change Admin Status','if_admin','',4,3,1,1);
INSERT INTO tools (id, description, name, file_group, itype, pos, allow_set, allow_get) VALUES (5,'Connections List','tcp_cnx','',2,1,1,1);
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE trap_receivers (
  id int4 NOT NULL AUTO_INCREMENT,
  position int4 NOT NULL DEFAULT '0',
  match_oid char(100) NOT NULL DEFAULT '',
  description char(60) NOT NULL DEFAULT '',
  command char(60) NOT NULL DEFAULT '',
  parameters char(250) NOT NULL DEFAULT '',
  backend int4 NOT NULL DEFAULT '1',
  interface_type int4 NOT NULL DEFAULT '1',
  stop_if_matches int2 NOT NULL DEFAULT '1',
  PRIMARY KEY (id)
);
/*!40101 SET character_set_client = @saved_cs_client */;
INSERT INTO trap_receivers (id, position, match_oid, description, command, parameters, backend, interface_type, stop_if_matches) VALUES (1,99,'.*','Default Trap Receiver','unknown','',2,1,1);
INSERT INTO trap_receivers (id, position, match_oid, description, command, parameters, backend, interface_type, stop_if_matches) VALUES (2,10,'.1.3.6.1.6.3.1.1.5.4','Link Up','static','up,interfacenumber,1',12,4,1);
INSERT INTO trap_receivers (id, position, match_oid, description, command, parameters, backend, interface_type, stop_if_matches) VALUES (3,10,'.1.3.6.1.6.3.1.1.5.3','Link Down','static','down,interfacenumber,1',12,4,1);
INSERT INTO trap_receivers (id, position, match_oid, description, command, parameters, backend, interface_type, stop_if_matches) VALUES (4,10,'enterprises.1123.4.300.0.1','IBM DS Event','static','warning,state,2',12,1,1);
INSERT INTO trap_receivers (id, position, match_oid, description, command, parameters, backend, interface_type, stop_if_matches) VALUES (10001,10,'enterprises.9.0.1','blah','static','warning,state,2',2,4,1);
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE traps (
  id int4 NOT NULL AUTO_INCREMENT,
  ip char(20) NOT NULL DEFAULT '',
  trap_oid char(250) NOT NULL DEFAULT '',
  analized int2 NOT NULL DEFAULT '0',
  date int4 NOT NULL DEFAULT '0',
  PRIMARY KEY (id)
);
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE traps_varbinds (
  id int4 NOT NULL AUTO_INCREMENT,
  trapid int4 NOT NULL DEFAULT '0',
  trap_oid varchar(250) DEFAULT NULL,
  value varchar(250) NOT NULL DEFAULT '',
  oidid int4 NOT NULL DEFAULT '0',
  PRIMARY KEY (id)
);
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE triggers (
  id int4 NOT NULL AUTO_INCREMENT,
  description char(40) NOT NULL DEFAULT '',
  type char(20) NOT NULL DEFAULT 'alarm',
  PRIMARY KEY (id)
);
/*!40101 SET character_set_client = @saved_cs_client */;
INSERT INTO triggers (id, description, type) VALUES (1,'No Trigger','alarm');
INSERT INTO triggers (id, description, type) VALUES (2,'Interface Status Change','alarm');
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE triggers_rules (
  id int4 NOT NULL AUTO_INCREMENT,
  trigger_id int4 NOT NULL DEFAULT '1',
  pos int4 NOT NULL DEFAULT '10',
  field char(40) NOT NULL DEFAULT '',
  operator char(20) NOT NULL DEFAULT '',
  value char(100) NOT NULL DEFAULT '',
  action_id int4 NOT NULL DEFAULT '1',
  action_parameters char(250) NOT NULL DEFAULT '',
  stop int2 NOT NULL DEFAULT '1',
  and_or int2 NOT NULL DEFAULT '1',
  PRIMARY KEY (id)
);
/*!40101 SET character_set_client = @saved_cs_client */;
INSERT INTO triggers_rules (id, trigger_id, pos, field, operator, value, action_id, action_parameters, stop, and_or) VALUES (1,1,10,'none','=','',1,'',1,1);
INSERT INTO triggers_rules (id, trigger_id, pos, field, operator, value, action_id, action_parameters, stop, and_or) VALUES (2,2,10,'type','!IN','12,25',2,'from:,subject:<interface-client_shortname> <interface-interface> <interface-description> <alarm-type_description> <alarm-state_description>,comment:Default Trigger',0,1);
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE triggers_users (
  id int4 NOT NULL AUTO_INCREMENT,
  user_id int4 NOT NULL DEFAULT '1',
  trigger_id int4 NOT NULL DEFAULT '1',
  active int2 NOT NULL DEFAULT '0',
  PRIMARY KEY (id)
);
/*!40101 SET character_set_client = @saved_cs_client */;
INSERT INTO triggers_users (id, user_id, trigger_id, active) VALUES (1,1,1,0);
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE types (
  id int4 NOT NULL AUTO_INCREMENT,
  description char(30) NOT NULL DEFAULT '',
  severity int4 NOT NULL DEFAULT '1',
  text char(250) NOT NULL DEFAULT '',
  generate_alarm int2 NOT NULL DEFAULT '0',
  alarm_up int4 NOT NULL DEFAULT '1',
  alarm_duration int4 NOT NULL DEFAULT '0',
  show_default int2 NOT NULL DEFAULT '1',
  show_host int2 NOT NULL DEFAULT '1',
  PRIMARY KEY (id)
);
/*!40101 SET character_set_client = @saved_cs_client */;
INSERT INTO types (id, description, severity, text, generate_alarm, alarm_up, alarm_duration, show_default, show_host) VALUES (1,'Unknown',2,'<interface> <user> <state> <info>',0,1,0,1,1);
INSERT INTO types (id, description, severity, text, generate_alarm, alarm_up, alarm_duration, show_default, show_host) VALUES (2,'Configuration',2,'<user>: Changed Configuration from <info> <interface>',0,1,0,1,1);
INSERT INTO types (id, description, severity, text, generate_alarm, alarm_up, alarm_duration, show_default, show_host) VALUES (3,'Interface Protocol',3,'Interface <interface> Protocol <state> <info> (<client> <interface-description>)',1,1,0,1,1);
INSERT INTO types (id, description, severity, text, generate_alarm, alarm_up, alarm_duration, show_default, show_host) VALUES (4,'Interface Link',4,'Interface <interface> Link <state> <info> (<client> <interface-description>)',0,1,0,1,1);
INSERT INTO types (id, description, severity, text, generate_alarm, alarm_up, alarm_duration, show_default, show_host) VALUES (5,'Controller Status',4,'Controller  <info> <interface> <state>',0,1,0,1,1);
INSERT INTO types (id, description, severity, text, generate_alarm, alarm_up, alarm_duration, show_default, show_host) VALUES (6,'BGP Status',5,'BGP Neighbor <interface> <state> <info> (<client> <interface-description>)',1,1,0,1,1);
INSERT INTO types (id, description, severity, text, generate_alarm, alarm_up, alarm_duration, show_default, show_host) VALUES (7,'Interface Shutdown',4,'Interface <interface> <info> <state> (<client> <interface-description>)',0,4,0,1,1);
INSERT INTO types (id, description, severity, text, generate_alarm, alarm_up, alarm_duration, show_default, show_host) VALUES (8,'Command',2,'<user>: <info>',0,1,0,1,1);
INSERT INTO types (id, description, severity, text, generate_alarm, alarm_up, alarm_duration, show_default, show_host) VALUES (9,'RShell Attempt',14,'RShell attempt from <info> <state>',0,1,0,1,1);
INSERT INTO types (id, description, severity, text, generate_alarm, alarm_up, alarm_duration, show_default, show_host) VALUES (12,'SLA',14,'<interface> <info> (<client> <interface-description>)',1,1,1800,1,1);
INSERT INTO types (id, description, severity, text, generate_alarm, alarm_up, alarm_duration, show_default, show_host) VALUES (17,'Clear Counters',14,'<user> Cleared Counters of <interface>  (<client> <interface-description>)',0,1,0,1,1);
INSERT INTO types (id, description, severity, text, generate_alarm, alarm_up, alarm_duration, show_default, show_host) VALUES (22,'TCP/UDP Service',18,'TCP/UDP Service <interface> <state> (<client> <interface-description>) <info>',1,1,0,1,1);
INSERT INTO types (id, description, severity, text, generate_alarm, alarm_up, alarm_duration, show_default, show_host) VALUES (25,'Administrative',13,'<interface> <info>',1,1,1800,1,1);
INSERT INTO types (id, description, severity, text, generate_alarm, alarm_up, alarm_duration, show_default, show_host) VALUES (26,'Environmental',5,'<interface> <state> <info>',1,1,0,1,1);
INSERT INTO types (id, description, severity, text, generate_alarm, alarm_up, alarm_duration, show_default, show_host) VALUES (28,'PIX Event',14,'<info>',0,1,0,1,1);
INSERT INTO types (id, description, severity, text, generate_alarm, alarm_up, alarm_duration, show_default, show_host) VALUES (29,'PIX Port',2,'<state> <info> packet from <user> to <interface>',0,1,0,1,1);
INSERT INTO types (id, description, severity, text, generate_alarm, alarm_up, alarm_duration, show_default, show_host) VALUES (34,'Duplex Mismatch',2,'Duplex Mismatch, <interface> is not full duplex and <user> <info> is full duplex',0,1,0,1,1);
INSERT INTO types (id, description, severity, text, generate_alarm, alarm_up, alarm_duration, show_default, show_host) VALUES (35,'ACL',14,'ACL <interface> <state> <info> packets from <user>',0,1,0,1,1);
INSERT INTO types (id, description, severity, text, generate_alarm, alarm_up, alarm_duration, show_default, show_host) VALUES (36,'BGP Notification',14,'Notification <state> <interface> <info>',0,1,0,1,1);
INSERT INTO types (id, description, severity, text, generate_alarm, alarm_up, alarm_duration, show_default, show_host) VALUES (37,'Excess Collitions',2,'Excess Collitions on Interface <interface>',0,1,0,1,1);
INSERT INTO types (id, description, severity, text, generate_alarm, alarm_up, alarm_duration, show_default, show_host) VALUES (38,'Application',5,'Application <interface> is <state> <info> (<client> <interface-description>)',1,1,0,1,1);
INSERT INTO types (id, description, severity, text, generate_alarm, alarm_up, alarm_duration, show_default, show_host) VALUES (39,'TCP Content',18,'Content Response on <interface> is <state> (<client> <interface-description>) <info>',1,1,0,1,1);
INSERT INTO types (id, description, severity, text, generate_alarm, alarm_up, alarm_duration, show_default, show_host) VALUES (40,'Reachability',5,'Host is <state> with <info>',1,1,0,1,1);
INSERT INTO types (id, description, severity, text, generate_alarm, alarm_up, alarm_duration, show_default, show_host) VALUES (41,'NTP',14,'<interface> is <state> <info>',1,1,0,1,1);
INSERT INTO types (id, description, severity, text, generate_alarm, alarm_up, alarm_duration, show_default, show_host) VALUES (42,'Tool Action',5,'<interface> <info> changed to <state> by <user> (<client> <interface-description>)',0,1,0,1,1);
INSERT INTO types (id, description, severity, text, generate_alarm, alarm_up, alarm_duration, show_default, show_host) VALUES (43,'Internal',14,'<user> <interface> <state> <info>',0,1,0,1,0);
INSERT INTO types (id, description, severity, text, generate_alarm, alarm_up, alarm_duration, show_default, show_host) VALUES (44,'Syslog',14,'<interface>: <info>',0,1,0,1,1);
INSERT INTO types (id, description, severity, text, generate_alarm, alarm_up, alarm_duration, show_default, show_host) VALUES (45,'Hide this Event',14,'<interface> <user> <state> <info>',0,1,0,0,1);
INSERT INTO types (id, description, severity, text, generate_alarm, alarm_up, alarm_duration, show_default, show_host) VALUES (46,'Win Info',14,'<interface>: <info> (ID:<state>)',0,1,0,2,1);
INSERT INTO types (id, description, severity, text, generate_alarm, alarm_up, alarm_duration, show_default, show_host) VALUES (47,'Win Warning',2,'<interface>: <info> (ID:<state>)',0,1,0,1,1);
INSERT INTO types (id, description, severity, text, generate_alarm, alarm_up, alarm_duration, show_default, show_host) VALUES (48,'Win Error',3,'<interface>: <info> (ID:<state>)',0,1,0,1,1);
INSERT INTO types (id, description, severity, text, generate_alarm, alarm_up, alarm_duration, show_default, show_host) VALUES (49,'Win Security',3,'<info> (ID:<state>)',0,1,0,1,1);
INSERT INTO types (id, description, severity, text, generate_alarm, alarm_up, alarm_duration, show_default, show_host) VALUES (50,'SQL',3,'SQL <interface> is <state> <info>',1,1,0,1,1);
INSERT INTO types (id, description, severity, text, generate_alarm, alarm_up, alarm_duration, show_default, show_host) VALUES (60,'APC Status',5,'<interface> is <state> <info>',1,1,0,1,1);
INSERT INTO types (id, description, severity, text, generate_alarm, alarm_up, alarm_duration, show_default, show_host) VALUES (61,'PIX Debug',13,'<info> (ID:<interface>)',0,1,0,2,1);
INSERT INTO types (id, description, severity, text, generate_alarm, alarm_up, alarm_duration, show_default, show_host) VALUES (62,'PIX Info',14,'<info> (ID:<interface>)',0,1,0,2,1);
INSERT INTO types (id, description, severity, text, generate_alarm, alarm_up, alarm_duration, show_default, show_host) VALUES (63,'PIX Notif',2,'<info> (ID:<interface>)',0,1,0,2,1);
INSERT INTO types (id, description, severity, text, generate_alarm, alarm_up, alarm_duration, show_default, show_host) VALUES (64,'PIX Warn',18,'<info> (ID:<interface>)',0,1,0,2,1);
INSERT INTO types (id, description, severity, text, generate_alarm, alarm_up, alarm_duration, show_default, show_host) VALUES (65,'PIX Error',3,'<info> (ID:<interface>)',0,1,0,2,1);
INSERT INTO types (id, description, severity, text, generate_alarm, alarm_up, alarm_duration, show_default, show_host) VALUES (66,'PIX Crit',4,'<info> (ID:<interface>)',1,1,0,1,1);
INSERT INTO types (id, description, severity, text, generate_alarm, alarm_up, alarm_duration, show_default, show_host) VALUES (67,'PIX Alert',5,'<info> (ID:<interface>)',1,1,0,1,1);
INSERT INTO types (id, description, severity, text, generate_alarm, alarm_up, alarm_duration, show_default, show_host) VALUES (68,'Alteon RServer',3,'Real Server <interface> is <state>',1,1,0,1,1);
INSERT INTO types (id, description, severity, text, generate_alarm, alarm_up, alarm_duration, show_default, show_host) VALUES (69,'Alteon Service',3,'Real Service <interface> is <state> <info>',1,1,0,1,1);
INSERT INTO types (id, description, severity, text, generate_alarm, alarm_up, alarm_duration, show_default, show_host) VALUES (70,'Alteon VServer',3,'Virtual Server <interface> is <state> <info>',0,1,0,1,1);
INSERT INTO types (id, description, severity, text, generate_alarm, alarm_up, alarm_duration, show_default, show_host) VALUES (71,'Brocade FC Port',3,'<interface> <state> (<info>)',1,1,0,1,1);
INSERT INTO types (id, description, severity, text, generate_alarm, alarm_up, alarm_duration, show_default, show_host) VALUES (72,'ISDN',14,'<interface> <state> <info>',1,1,0,1,1);
INSERT INTO types (id, description, severity, text, generate_alarm, alarm_up, alarm_duration, show_default, show_host) VALUES (73,'To delete',13,'<info>  <state>  <interface> <user>',0,1,0,2,1);
INSERT INTO types (id, description, severity, text, generate_alarm, alarm_up, alarm_duration, show_default, show_host) VALUES (74,'IBM error',5,'<info> (id:<interface>)',1,1,0,1,1);
INSERT INTO types (id, description, severity, text, generate_alarm, alarm_up, alarm_duration, show_default, show_host) VALUES (75,'IBM Warning',127,'<interface> is in <state> state',1,1,0,1,1);
INSERT INTO types (id, description, severity, text, generate_alarm, alarm_up, alarm_duration, show_default, show_host) VALUES (76,'Disable polling',13,'Polling for host <interface> is disabled (enabling time: <info>)',0,1,0,1,0);
INSERT INTO types (id, description, severity, text, generate_alarm, alarm_up, alarm_duration, show_default, show_host) VALUES (77,'IBM San Trap',5,'<user>: <state> <info> (<interface>)',1,1,0,1,1);
INSERT INTO types (id, description, severity, text, generate_alarm, alarm_up, alarm_duration, show_default, show_host) VALUES (78,'OS/400 Error',5,'A subsystem is <state> on the OS/400',1,1,0,1,1);
INSERT INTO types (id, description, severity, text, generate_alarm, alarm_up, alarm_duration, show_default, show_host) VALUES (79,'Enable polling',13,'Polling for host <interface> is enabled',0,1,0,1,0);
INSERT INTO types (id, description, severity, text, generate_alarm, alarm_up, alarm_duration, show_default, show_host) VALUES (80,'Storage Controller',4,'<info>',1,1,0,1,1);
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE zones (
  id int4 NOT NULL AUTO_INCREMENT,
  zone char(60) NOT NULL DEFAULT '',
  shortname char(10) NOT NULL DEFAULT '',
  image char(30) NOT NULL DEFAULT '',
  seeds char(250) NOT NULL DEFAULT '',
  max_deep int2 NOT NULL DEFAULT '2',
  communities char(250) NOT NULL DEFAULT '',
  refresh int4 NOT NULL DEFAULT '86400',
  admin_status int2 NOT NULL DEFAULT '0',
  show_zone int2 NOT NULL DEFAULT '1',
  allow_private int2 NOT NULL DEFAULT '0',
  PRIMARY KEY (id)
);
/*!40101 SET character_set_client = @saved_cs_client */;
INSERT INTO zones (id, zone, shortname, image, seeds, max_deep, communities, refresh, admin_status, show_zone, allow_private) VALUES (1,'Unknown','UNK','unknown.png','',1,'',86400,0,1,0);
INSERT INTO zones (id, zone, shortname, image, seeds, max_deep, communities, refresh, admin_status, show_zone, allow_private) VALUES (2,'New Zone','NewZone','unknown.png','',1,'',86400,0,1,1);

SELECT SETVAL('interface_types_id_seq',(select case when max(id)>10000 then max(id) else 10000 end from interface_types));
SELECT SETVAL('interface_types_fields_id_seq',(select case when max(id)>10000 then max(id) else 10000 end from interface_types_fields));
SELECT SETVAL('interface_types_field_types_id_seq',(select case when max(id)>10000 then max(id) else 10000 end from interface_types_field_types));
SELECT SETVAL('graph_types_id_seq',(select case when max(id)>10000 then max(id) else 10000 end from graph_types));
SELECT SETVAL('alarm_states_id_seq',(select case when max(id)>10000 then max(id) else 10000 end from alarm_states));
SELECT SETVAL('severity_id_seq',(select case when max(id)>10000 then max(id) else 10000 end from severity));
SELECT SETVAL('syslog_types_id_seq',(select case when max(id)>10000 then max(id) else 10000 end from syslog_types));
SELECT SETVAL('trap_receivers_id_seq',(select case when max(id)>10000 then max(id) else 10000 end from trap_receivers));
SELECT SETVAL('types_id_seq',(select case when max(id)>10000 then max(id) else 10000 end from types));
SELECT SETVAL('slas_id_seq',(select case when max(id)>10000 then max(id) else 10000 end from slas));
SELECT SETVAL('slas_cond_id_seq',(select case when max(id)>10000 then max(id) else 10000 end from slas_cond));
SELECT SETVAL('slas_sla_cond_id_seq',(select case when max(id)>10000 then max(id) else 10000 end from slas_sla_cond));
SELECT SETVAL('filters_id_seq',(select case when max(id)>10000 then max(id) else 10000 end from filters));
SELECT SETVAL('filters_fields_id_seq',(select case when max(id)>10000 then max(id) else 10000 end from filters_fields));
SELECT SETVAL('filters_cond_id_seq',(select case when max(id)>10000 then max(id) else 10000 end from filters_cond));
SELECT SETVAL('pollers_id_seq',(select case when max(id)>10000 then max(id) else 10000 end from pollers));
SELECT SETVAL('pollers_groups_id_seq',(select case when max(id)>10000 then max(id) else 10000 end from pollers_groups));
SELECT SETVAL('pollers_backend_id_seq',(select case when max(id)>10000 then max(id) else 10000 end from pollers_backend));
SELECT SETVAL('pollers_poller_groups_id_seq',(select case when max(id)>10000 then max(id) else 10000 end from pollers_poller_groups));
SELECT SETVAL('autodiscovery_id_seq',(select case when max(id)>10000 then max(id) else 10000 end from autodiscovery));
SELECT SETVAL('hosts_config_types_id_seq',(select case when max(id)>10000 then max(id) else 10000 end from hosts_config_types));
SELECT SETVAL('tools_id_seq',(select case when max(id)>10000 then max(id) else 10000 end from tools));
SELECT SETVAL('profiles_options_id_seq',(select case when max(id)>10000 then max(id) else 10000 end from profiles_options));
SELECT SETVAL('actions_id_seq',(select case when max(id)>10000 then max(id) else 10000 end from actions));
SELECT SETVAL('profiles_values_id_seq',(select case when max(id)>299 then max(id) else 299 end from profiles_values));
