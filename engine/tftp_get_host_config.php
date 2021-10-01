<?php
/* Host Config Downloader (via plugins). This file is part of JFFNMS
 * Copyright (C) <2002-2003> Javier Szyszlican <javier@szysz.com>
 * This program is licensed under the GNU GPL, full terms in the LICENSE file
 */

require_once('../conf/config.php');
$Config = new JffnmsConfig();
config_load_libs('engine', 0);

$opt_host=0;
// main
{ 
  $Hosts = new JffnmsHosts();
  $HostsConfig = new JffnmsHosts_configs();

  $time_total = time_msec();
  parse_commandline();

  if ($opt_host)
    $hostid_filter = "= $opt_host";
  else
    $hostid_filter = "> 0";
        
  $query_config= "
    SELECT
      hosts.id as host_id, hosts.ip as host_ip, hosts.rwcommunity,
      hosts.tftp as tftp_server, hosts_config_types.command
    FROM
      hosts, hosts_config_types
    WHERE
      hosts.id $hostid_filter AND
      hosts.config_type = hosts_config_types.id AND
      hosts.config_type > 1
    ORDER BY hosts.id";
    
  $result_config = db_query ($query_config) or die ('Query failed - T2 - '.db_error());
    
  $config_included_files = array();
  while ($config_row = db_fetch_array($result_config))
  {
    $config_time = time_msec();
    $config_file = $Config->get('jffnms_real_path').'/engine/configs/'.
      $config_row["command"].'.inc.php';
    $config_get_command = 'config_'.$config_row['command'].'_get';
    $config_wait_command = 'config_'.$config_row['command'].'_wait';

    $now = date("Y-m-d H:i:s",time());
    if (array_key_exists($config_file, $config_included_files))
    {
      if ($config_included_files[$config_file] === FALSE) // already failed
        continue;
    } else {
      if (!is_readable($config_file))
      {
        host_config_logger($config_row, $config_time,
          "ERROR: Unable to load config plugin file '$config_file'.\n");
        $config_included_files[$config_file] = FALSE;
        continue;
      }
      require_once($config_file);
      if (!function_exists($config_get_command))
      {
        host_config_logger($config_row, $config_time,
          "ERROR: Config get function '$config_get_command' does not exist in config file '$config_file'.\n");
        $config_included_files[$config_file] = FALSE;
        continue;
      }
      if (!function_exists($config_wait_command))
      {
        host_config_logger($config_row, $config_time,
          "ERROR: Config wait function '$config_wait_command' does not exist in config file '$config_file'.\n");
        $config_included_files[$config_file] = FALSE;
        continue;
      }
      $config_included_files[$config_file] = TRUE;
    }

    $tftp_filename = uniqid('').'.dat'; //generate random filename
    $real_tftp_filename = $Config->get('tftp_real_path').'/'.$tftp_filename;
    if (!touch($real_tftp_filename))
    {
      host_config_logger($config_row, $config_time,
        "ERROR: Unable to create temporary TFTP filename '$real_tftp_filename'\n");
      continue;
    }
    if (!chmod($real_tftp_filename, 0666))
    {
      host_config_logger($config_row, $config_time, 
        "Error: Unable to chmod temporary TFTP filename '$real_tftp_filename'.\n");
      unlink($real_tftp_filename);
      continue;
    }
      
    $function_data = array($config_row['host_ip'],$config_row['rwcommunity'],
        $config_row['tftp_server'],$tftp_filename);
    
    if (call_user_func_array($config_get_command, $function_data) === FALSE)
    {
      host_config_logger($config_row, $config_time,
        'WARNING: Failed to get config file from host');
      unlink($real_tftp_filename);
      continue;
    }
    if (call_user_func_array($config_wait_command, $function_data) === FALSE)
    {
      host_config_logger($config_row, $config_time,
        'WARNING: Failed waiting for file transfer');
      unlink($real_tftp_filename);
      continue;
    }
    clearstatcache();
    if (!file_exists($real_tftp_filename) || filesize($real_tftp_filename) == 0)
    {
      host_config_logger($config_row, $config_time,
        'WARNING: file did not transfer');
      unlink($real_tftp_filename);
      continue;
    }
    $config_data_new = file_get_contents($real_tftp_filename);
    unlink($real_tftp_filename);
    $db_host_configs =  $HostsConfig->get_all(NULL, $config_row['host_id'],NULL,1);
    $config_data_old = $db_host_configs[0]['config'];
    $config_id_old = $db_host_configs[0]['id'];

    //Cisco Router NTP fix
    $config_data_new = preg_replace('/ntp clock-period \S+/', '', $config_data_new);
        
    if (md5 ($config_data_new) != md5 ($config_data_old))
    {
      $config_data_new = str_replace ("'","\'",$config_data_new); //escape '
      $data = array(
        'host'=>$config_row['host_id'],
        'date'=>$now,
        'config'=>$config_data_new
      );
      $config_id = $HostsConfig->add();
      $result = $HostsConfig->update($config_id,$data); //save the config in the DB
      $info = "new config id $config_id";
    } else  
      $info = "same config as last one ($config_id_old)";
    host_config_logger($config_row, $config_time,'', $info);
  } //while row
  $time_total = time_msec_diff($time_total);
  logger( "TIMES \t: Total Time $time_total msec.\n");
  db_close();
}

function host_config_logger(&$config_row, $config_time, $error, $info='')
{
  global $Config;

  $config_time = time_msec_diff ($config_time);
  if ($error != '')
  {
    $Events = new JffnmsEvents();
    $Events->add(date("Y-m-d H:i:s",time()), $Config->get('jffnms_administrative_type'),
      $config_row['host_id'],'CPU','error','host_config',
      'Host Config Transfer: '.addslashes($error),0,0);
  } else
    $error = "OK $info";

  logger(' :  H '.str_pad($config_row['host_id'],3,' ',STR_PAD_LEFT).
    ' : '.$config_row['host_ip'].
    ' : '.$config_row['command']." : $error ($config_time msec)\n");
  flush();
}

function parse_commandline()
{
  global $opt_host;

  $longopts = array( 'help', 'version', 'host:');
  if ( ($opts = getopt('h:v?', $longopts)) === FALSE)
  {
    return;
  }
  foreach($opts as $opt => $opt_val)
  {
    switch ($opt)
    {
    case 'help':
    case '?':
      print_help();
      break;
    case 'host':
    case 'h':
      if (intval($opt_val) == 0)
        print_help("Host ID must be an integer value not \"$opt_val\"\n");
      $opt_host = $opt_val;
      break;
    case 'version':
    case 'v':
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
Usage: php -q tftp_get_host_config.php [-hv] [-t hostid]
 JFFNMS Consolidator

  -h,--help            Show this help text
  -h,--host ID         Only attempt transfer from host with id ID
  -v,--version         Show version information
";
  die();
}

?>
