<?php
/* Database Cleanup. This file is part of JFFNMS
 * Copyright (C) <2003> Kovacs Andrei <kandrei@comtrust.ro>
 * Copyright (C) <2003> Javier Szyszlican <javier@jffnms.org> (Modifications)
 * This program is licensed under the GNU GPL, full terms in the LICENSE file
 */

require_once('../conf/config.php');
$Config = new JffnmsConfig();
config_load_libs('engine',0);

parse_commandline();
cleanup_raw_events();
cleanup_events();
cleanup_host_configs();
cleanup_repair_tables();

db_close();

function cleanup_raw_events()
{
  global $Config;

  $raw_days = $Config->get('raw_events_store');
  if ($raw_days <= 0)
    return;

  $period=time()-(60*60*24*$raw_days);
  $dtdelete=date("Y-m-d 00:00:00",$period);

  logger ("Cleaning up raw messages older than $dtdelete ($raw_days days)...\n");
  // Traps cleanup
  $query_traps = 'select id from traps where date <= '.$period;
  $result_traps = db_query ($query_traps) or die ('Query failed - TC1 - '.db_error());
    
  $result_del_varbinds = 0;
  while ($rows_traps = db_fetch_array($result_traps))
  {
    $id = $rows_traps['id'];

    $query='delete from traps_varbinds where trapid = '.$id;
    $result_del_varbinds = db_query ($query) or die ('Query failed: TC2 - Delete varbinds - '.db_error());
  }

  $query = "delete from traps where date <= ".$period;
  $result_del_traps = db_query ($query) or die ('Query failed: TC3 - Delete traps - '.db_error());
  logger ("Cleanup of traps/traps_varbinds tables finished... ($result_del_traps, $result_del_varbinds)\n");
  // END Traps cleanup

  // Acct cleanup
  $query = "delete from acct where date <= '$dtdelete'";
  $result_del_acct = db_query ($query) or die ('Query failed: AC1 - Delete acct - '.db_error());
  logger ("Cleanup of acct table finished... ($result_del_acct)\n");
  // END Acct cleanup

  // Syslog cleanup
  $query = "delete from syslog where date <= '$dtdelete'";
  $result_del_syslog = db_query ($query) or die ('Query failed: SC1 - Delete syslog - '.db_error());
  logger ("Cleanup of syslog table finished... ($result_del_syslog)\n");
  // END Syslog cleanup
}    

function cleanup_events()
{
  global $Config;

  $days = $Config->get('events_store');
  if ($days <= 0)
    return;

  $period=time()-(60*60*24*$days); 
  $dtdelete=date("Y-m-d 00:00:00",$period);
  logger ("Cleaning up Events/Alarms older than $dtdelete ($days days)...\n");

  // Alarms cleanup
  $query = "delete from alarms where date_stop <= '$dtdelete' and not (date_stop <= '0001-01-01 00:00:00') "; //this will just delete finished alarms which end is older than dtdelete
  $result_del_alarms = db_query ($query) or die ("Query failed: AC2 - Delete Alarms - ".db_error());
  logger ("Cleanup of alarms table finished... ($result_del_alarms)\n");
  // END Alarms cleanup

  // Events cleanup
  $query = "delete from events where date <= '$dtdelete'"; 
  $result_del_events = db_query ($query) or die ("Query failed: EC1 - Delete Events - ".db_error());
  logger ("Cleanup of events table finished... ($result_del_events)\n");
  // END Events cleanup
}

function cleanup_host_configs()
{
  global $Config;

  $config_days = $Config->get('host_configs_store');
  if ($config_days <= 0)
    return;

  $period=time()-(60*60*24*$config_days);
  $dtdelete=date("Y-m-d 00:00:00",$period);

  logger ("Cleaning up Host Configs older than $dtdelete ($config_days days)...\n");

  $query = "delete from hosts_config where date <= '$dtdelete' and id > 1";
  $result_del_config = db_query ($query) or die ("Query failed: CC1 - Delete hosts_config - ".db_error());
  logger ("Cleanup of hosts_config table finished... ($result_del_config)\n");
} //cleaup_host_configs

function cleanup_repair_tables()
{
  $tables_to_repair = array('events','alarms','traps','syslog','acct','hosts_config');

  foreach ($tables_to_repair as $table)
  {
    logger ("Repairing $table table...");
    $result = db_repair ($table);
    logger ("done, $result\n");
  }
}

function parse_commandline()
{
  $longopts = array( 'help', 'version');
  if ( ($opts = getopt('VH', $longopts)) === FALSE)
  {
    return;
  }
  if (sizeof($opts) == 0)
    return;
  foreach($opts as $opt => $opt_val)
  {
    switch ($opt)
    {
    case 'help':
    case 'H':
      print_help();
      break;
    case 'version':
    case 'V':
      print_version();
      break;
    }
  }
}

function print_help($errmsg='')
{
  if (!empty($errmsg))
    print $errmsg;
  print
"
Usage: php -q cleanup_raw_tables.php [-HV]
 JFFNMS raw table cleanup

  -H, --help            Show this help text
  -V, --version         Show version information
";
  die();
}

?>
