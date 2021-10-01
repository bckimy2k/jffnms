<?php
/* Consolidator. This file is part of JFFNMS
 * Copyright (C) <2002i-2011> JFFNMS Authors
 * This program is licensed under the GNU GPL, full terms in the LICENSE file
 */
{
  include_once('../conf/config.php');
  $Config = new JffnmsConfig();
  config_load_libs('engine', 0);
  $Alarms = new JffnmsAlarms();
  $Events = new JffnmsEvents();
  $Triggers = new JffnmsTriggers();
  
  $total_run_time = 60; //60 seconds
  $interval = 15; //15 seconds interval between runs

  $opt_times = 3;
  $opt_force = FALSE;
  parse_commandline();

  $i = 0;
  $date_start = time();

  if (!$opt_force && is_process_running(NULL,2) !== false)  //check if a process named as myself is already running (one instance is me)
  {
    logger ("Another instance of Consolidator is running, aborting...\n");
    db_close();
    die();
  }
  detach();
  
  $consolidators = array('alarms', 'events', 'logfiles', 'syslog', 'tacacs', 'traps');
  foreach($consolidators as $con)
    require_once("consolidate/$con.php");

  logger ("Consolidator Starting: looping $opt_times times...\n");
  do {
    $time_consolidate_loop = time_msec();
    $i++;  

    consolidate_syslog($Events);
    consolidate_tacacs($Events);
    consolidate_traps();
    consolidate_logfiles($Events);
    consolidate_events($Events, $Alarms, $Triggers);
    consolidate_alarms($Alarms, $Triggers);
  
    $time_consolidate_loop = time_msec_diff($time_consolidate_loop);
    logger ("Partial time: $time_consolidate_loop msec.\n");

    if ($i < $opt_times) sleep($interval);
    $time_elapsed = time() - $date_start;
    logger ("Elapsed time $time_elapsed sec.\n");
  } while (($i < $opt_times) && ($time_elapsed < $total_run_time)); 

  $time_total = time() - $date_start;
    logger("Total time: $time_total sec.\n");
  db_close();
} //main()

  function parse_commandline()
  {
    global $opt_times, $opt_force;

    $longopts = array( 'help', 'version', 'repeat:', 'force');
    if ( ($opts = getopt('r:FHV', $longopts)) === FALSE)
    {
      return;
    }
    if (sizeof($opts) == 0)
    {
      if (count($_SERVER['argv']) > 1)
        $opt_times = $_SERVER['argv'][1];
      if (intval($opt_times) == 0 )
        print_help("Direct number of repeats must be an integer not \"$opt_times\"\n");
      return;
    }
    foreach($opts as $opt => $opt_val)
    {
      switch ($opt)
      {
      case 'force':
      case 'F':
          $opt_force = TRUE;
          break;
      case 'help':
      case 'H':
        print_help();
        break;
      case 'repeat':
      case 'r':
        if (intval($opt_val) == 0)
          print_help("Repeat option must have integer value not \"$opt_val\"\n");
        $opt_times = $opt_val;
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
Usage: php -q consolidate.php [-hv] [-r repeat]
 JFFNMS Consolidator

  -r, --repeat num      Repeat consolidator num times
  -F, --force           Force consolidator to run
  -H, --help            Show this help text
  -V, --version         Show version information
";
  die();
  }

?>
