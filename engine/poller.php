<?php
/* Poller 3.0 This file is part of JFFNMS
 * Copyright (C) 2004-2011 JFFNMS Authors
 * This program is licensed under the GNU GPL, full terms in the LICENSE file
 */
require_once 'common_parent.inc.php';

// Set the defaults
$opt_children = FALSE;
$opt_host = FALSE;
$opt_interface = FALSE;
$opt_once = FALSE;
$opt_force = FALSE;

define('CHILD_FILE', 'poller_child.php');
parse_commandline();
if  (!$opt_force) {
    sleep(2);
    if (is_process_running(join(' ', $_SERVER['argv']),2))
        die("Process Already Running.\n");
}
poller_main_loop($opt_once, $opt_host,$opt_children);

function poller_main_loop($opt_once, $opt_host, $opt_children)
{
    $poll_interval = 60; // poll each minute
    $last_interface_get = 0;
    $polled_hosts = array();
    $Parent = new JffnmsEngineParent($opt_children);

    while(TRUE) {
        $loop_start_time = time();
        if ($Parent->waiting_count == 0 && $last_interface_get + $poll_interval < $loop_start_time) {
            $hosts_lpd_updated = FALSE;
            // Collect all the interfaces we want to poll in this loop
            list($polled_hosts, $new_interfaces) = poller_get_interfaces($opt_host, $poll_interval);
            poller_update_hosts_lpd($polled_hosts, 0);
            $Parent->items_new($new_interfaces);
            $last_interface_get = $loop_start_time;
            $start_count = $Parent->waiting_count;
            logger("ITEMS     Added $start_count items\n");
        }
        $Parent->check_children();
        $Parent->item_check_poll_time();
        $Parent->children_start();
        poller_read_children($Parent);
        $Parent->work_children();
        $Parent->print_status();
        $loop_time = time() - $loop_start_time;

        if ($loop_time > $poll_interval)
            logger("ERROR   Polling loop took $loop_time seconds, poll interval is $poll_interval\n");
        if ($Parent->waiting_count == 0 && $Parent->polling_count == 0) {
            // Done all the current polling
            if ($opt_once && count($Parent->child_procs) == 0)
                break;
        }
        if ($loop_time < ENGINE_HEARTBEAT) {
            if ($Parent->polling_count > 0
                || ($Parent->polling_count==0 && $Parent->waiting_count==0 && $opt_once)
            )
                sleep(1);
            else
                sleep(ENGINE_HEARTBEAT - $loop_time);
        }
    }// while true
}

// Read all the children's chatter
function poller_read_children(&$Parent)
{
    $read_timeout = max(time() + ENGINE_HEARTBEAT/$Parent->max_children,2);

    while (time() < $read_timeout) 
        if ($Parent->read_children('poller_read_callback') == FALSE)
            return;
}

function poller_read_callback(&$Parent, $child_id, $child_data)
{
    switch($child_data['cmd'])
    {
    case 'DONE':
        $item_id = $Parent->child_done_job($child_id);
        if ($item_id !== FALSE)
            poller_set_interface_lpd($item_id);
        break;
    case 'NOTDONE':
        $Parent->child_notdone_job($child_id);
        break;
    }
}

      
function parse_commandline()
{
  global $opt_host, $opt_interface, $opt_once, $opt_children, $opt_force;
  $num_params = $GLOBALS['argc']-1;

  $longopts = array( 'help', 'version', 'host:', 'children:', 'interface:', 'once', 'force');
  if ( ($opts = getopt('c:h:i:oFVH', $longopts)) === FALSE)
  {
    return;
  }
  foreach ($opts as $opt => $opt_val)
  {
    switch ($opt)
    {
    case 'children':
    case 'c':
      $num_params-= 2;
      $opt_children=$opt_val;
      break;
    case 'force':
    case 'F':
        $num_params-= 1;
        $opt_force = TRUE;
        $opt_once = TRUE;
        break;
    case 'host':
    case 'h':
      $num_params-= 2;
      $opt_host = $opt_val;
      if (!is_numeric($opt_host))
        print_help('host id option must be numeric');
      break;
    case 'interface':
    case 'i':
      $num_params-= 2;
      $opt_interface = $opt_val;
      if (!is_numeric($opt_interface))
        print_help('interface ID option must be numeric');
      break;
    case 'once':
    case 'o':
      $num_params-= 1;
      $opt_once = TRUE;
      break;
    case 'help':
    case 'H':
      print_help();
      break;
    case 'version':
    case 'V':
      print_version();
    }// switch
  }//for
  if ($num_params > 0) print_help('Problem with command line options.');
}

function print_help($errmsg = FALSE)
{
  if ($errmsg)
    print "$errmsg\n";
  print $_SERVER['SCRIPT_NAME'] . ' [options]
    -c, --children #       Fork with this number of child processes
    -h, --host <ID>        Poll all interfaces for host <ID>
    -i, --interface <ID>   Poll interface <ID> only
    -o, --once             Poll once and then exit
    -F, --force            Force polling, implies --once
    -H, --help             Print this help
    -V, --version          Print version information
    ';
die;
}



/*
 * Returns: list of interfaces, with dmii ones ordered first
 */
function poller_get_interfaces($host_id, $poll_interval)
{
    global $opt_interface, $opt_force;

    $Interfaces = new JffnmsInterfaces();
    $Maps = new JffnmsMaps();

    
    if ($opt_interface)
        $where_intid = " interfaces.id=$opt_interface ";
    else
        $where_intid = ' interfaces.id > 1';
    if (!$opt_force) {
        $poll_time = time() + $poll_interval;
        $where_polltime =
      ' AND ((hosts.last_poll_date + hosts.poll_interval) < \''.$poll_time.'\')
      AND (
          (interfaces.poll_interval = 0 AND (interfaces.last_poll_date + hosts.poll_interval) < '.$poll_time.')
      OR
          ( (interfaces.last_poll_date + interfaces.poll_interval) < '.$poll_time.')
      )';
    } else
        $where_polltime = '';


  
  $int_query = '
    SELECT
      interfaces.id AS interface_id, interfaces.interface,
      interfaces.poll AS poller_group,
      interfaces.check_status, interfaces.show_rootmap,
      hosts.id AS host_id, hosts.ip AS host_ip,
      hosts.rwcommunity, hosts.rocommunity,
      hosts.dmii, hosts.sysobjectid
    FROM
      interfaces, hosts
    WHERE
    '.($host_id===FALSE?'':"hosts.id=$host_id AND ").'
      interfaces.host = hosts.id
      AND hosts.id > 1 AND hosts.poll = 1
      AND '.$where_intid.' AND interfaces.poll > 1' .
      $where_polltime .
      ' ORDER BY interfaces.last_poll_date ASC';

  $int_result = db_query($int_query) or die ('Query failed - poller_get_interfaces() - '.db_error());

  $dmii_interfaces = array();
  $dmii_maps = array();
  $candidate_interfaces = array();

  while ($row = db_fetch_array($int_result))
  {
    $id = $row['interface_id'];
    $candidate_interfaces[$id] = array(
      'interface_id' => $row['interface_id'],
      'interface' => $row['interface'],
      'poller_group' => $row['poller_group'],
      'show_rootmap' => $row['show_rootmap'],
      'host_id' => $row['host_id'],
      'host_ip' => $row['host_ip'],
      'rw_community' => $row['rwcommunity'],
      'ro_community' => $row['rocommunity'],
      'sysobjectid' => $row['sysobjectid'],
      'check_status' => $row['check_status'],
      'tries' => 0,
      'is_dmii' => FALSE,
      'dmii_up' => 1
    );
    if (($row['dmii'] != '1') && preg_match('/^(MI)(\d+)$/',$row['dmii'], $regs))
    {
        if ($regs[1] == 'I')
        {
            if (!array_key_exists($regs[2], $dmii_interfaces))
                $dmii_interfaces[$regs[2]] = $Interfaces->is_up($regs[2]);
            $candidate_interfaces[$interface_id]['dmii_up'] = $dmii_interfaces[$regs[2]];
        } elseif ($regs[1] == 'M') {
            if (!array_keys_exists($regs[2], $dmii_maps))
                $dmii_maps[$regs[2]] = !($Maps->status_all_down($Interfaces, $regs[2]));
            $candidate_interfaces[$interface_id]['dmii_up'] = $dmii_maps[$regs[2]];
        }
    } 
  }//while
  $polling_interfaces = array();
  // DMII interfaces are always polled
  foreach ($dmii_interfaces as $dmii_id => $dmii_status)
      if (!array_key_exists($dmii_id, $polling_interfaces))
          $polling_interfaces[$dmii_id] = $candidate_interfaces[$dmii_id];
  // Interfaces in a DMII map are always polled
  foreach ($dmii_maps as $dmii_map_id => $dmii_status)
  {
      $dmii_map_interfaces = poller_get_map_interface_ids($dmii_map_id);
      foreach($dmii_map_interfaces as $dmii_id)
          if (!array_key_exists($dmii_id, $polling_interfaces))
              $polling_interfaces[$dmii_id] = $candidate_interfaces[$dmii_id];
  }
  // Finally, all candidate interfaces with their host dmii up are polled
  foreach ($candidate_interfaces as $cand_id => $cand_data)
      if ($cand_data['dmii_up'] == 1 
          && !array_key_exists($cand_id, $polling_interfaces))
          $polling_interfaces[$cand_id] = $cand_data;
  // Scan all interfaces to find polling hosts
  $polling_hosts = array();
  reset($polling_interfaces);
  foreach ($polling_interfaces as $int_id => $int_data)
      if (!array_key_exists($int_data['host_id'], $polling_hosts))
          $polling_hosts[$int_data['host_id']] = FALSE;
  return array($polling_hosts, $polling_interfaces);
}

function poller_set_interface_lpd($interface_id)
{
    global $opt_force;
    if (!$opt_force) 
        db_update('interfaces', $interface_id, array('last_poll_date' => time()));
}

function poller_update_hosts_lpd($hosts, $poll_time)
{
    global $opt_force;

    if ($opt_force || count($hosts) == 0)
        return;
    $host_ids = join(',', array_keys($hosts));
    $now = time();
    $lpd_query = 'UPDATE hosts '
        . "SET last_poll_date = '$now', " 
        . "last_poll_time = '$poll_time' "
        . "WHERE id IN ($host_ids)";
    db_query($lpd_query) or die ("Query failed - poller_update_hosts_lpd() - ".db_error());
    logger("LPD:   Updated hosts ($host_ids) to LPD $now\n");
}

function poller_get_map_interface_ids($dmii_map_id)
{
    $ids = array();
    $query = "SELECT interface FROM maps_interfaces WHERE map='$dmii_map_id'";
    if ( ($result = db_query($query)) === FALSE)
        return $ids;
    if (db_num_rows($result) == 0)
        return $ids;
    while ($row = db_fwtch_array($result)) 
        $ids[] = $row['interface'];
    return $ids;
}
