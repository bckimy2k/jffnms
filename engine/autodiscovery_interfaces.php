<?php
/* Interface autodiscovery v2.0  This file is part of JFFNMS
 * Copyright (C) <2004-2011> JFFNMS AUTHORS
 * This program is licensed under the GNU GPL, full terms in the LICENSE file
 */
require_once 'common_parent.inc.php';
define('CHILD_FILE', 'iad_child.php');
define('MAX_IAD_TIME', 240); // Maximum autodiscovery time is 4 minutes

// Set the defaults
$opt_host = FALSE;
$opt_itype = FALSE;
$opt_children = FALSE;
$opt_force = FALSE;

parse_commandline();

if (!$opt_force && is_process_running(join(' ', $_SERVER['argv']),2))
    die("Autodiscovery interfaces already running.\n");

iad_main($opt_host, $opt_itype, $opt_children);



function parse_commandline()
{
    global $opt_host, $opt_itype, $opt_children, $opt_force;
    $num_params = $GLOBALS['argc']-1;

    $longopts = array( 'help', 'version', 'host:', 'children:', 'type::', 'force');
    if ( ($opts = getopt('c:h:t::FVH', $longopts)) === FALSE)
        return;
    foreach ($opts as $opt => $opt_val) {
        switch ($opt) {
        case 'children':
        case 'c':
            $num_params-= 2;
            $opt_children = $opt_val;
            break;
        case 'host':
        case 'h':
            $num_params-= 2;
            $opt_host = $opt_val;
            if (!is_numeric($opt_host))
                print_help('host id option must be numeric');
            break;
        case 'force':
        case 'F':
            $opt_force=TRUE;
            $num_params-= 1;
            break;
        case 'type':
        case 't':
            $num_params-= 2;
            if ($opt_val === FALSE)
                print_itypes();
            $opt_itype = $opt_val;
            if (!is_numeric($opt_itype))
                print_help('interface type option must be numeric');
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
    -h, --host <ID>        Discover all interfaces for host <ID>
    -t, --type <ID>        Discover interface type <ID> only
                           Omit the id to get a list of interface types
    -F, --force            Force running this process
    -H, --help             Print this help
    -V, --version          Print version information
    ';
die;
}

function print_itypes()
{
    $ITypes = new JffnmsInterface_types();

    $num_itypes = $ITypes->get();
    print "Known interface types ($num_itypes)\n";
    while ($row = $ITypes->fetch())
    {
        echo "$row[id]\t$row[description]\n";
    }

die;
}

function iad_main($opt_host, $opt_itype, $opt_children)
{
    $Parent = new JffnmsEngineParent($opt_children);

    $finish_loop_time = time() + MAX_IAD_TIME;

    do {
        $loop_start_time = time();

        if (!isset($starting_count)) {
            // Get all of the hosts we are going to autodiscover
            $Parent->items_new(iad_get_hosts($opt_host, $opt_itype));
            $starting_count = $Parent->waiting_count;
            logger("ITEMS     Added $starting_count items\n");
        }

        $Parent->check_children();
        $Parent->item_check_poll_time();
        $Parent->children_start();
        iad_read_children($Parent);
        $Parent->work_children();
        $Parent->print_status();

        $loop_time = time() - $loop_start_time;
        if ($loop_time < ENGINE_HEARTBEAT) {
            if ($Parent->polling_count > 0
                || ($Parent->polling_count==0 && $Parent->waiting_count==0))
                sleep(1);
            else
                sleep(ENGINE_HEARTBEAT - $loop_time);
        }
        if ($finish_loop_time < time()) {
            logger("**ERROR** Interface Autodiscovery took longer than maximum time ".MAX_IAD_TIME." seconds. Waiting/Polling: ".
                $Parent->waiting_count.'/'.$Parent->polling_count."\n");
            $Parent->stop_all_children('Autodiscovery loop exceeded.');
        }
    } while ($Parent->waiting_count > 0 || $Parent->polling_count > 0);

} // iad_main_loop

function iad_get_hosts($host_id,$itype_id)
{
  global $opt_force;
  if ($host_id === FALSE)
    $hostid_match = '> 1';
  else
    $hostid_match = "= $host_id";
  $query_hosts = "
    SELECT
      id, ip, rocommunity, autodiscovery_default_customer, autodiscovery, sysobjectid
    FROM hosts
    WHERE
    hosts.id $hostid_match";
  if (!$opt_force)
      $query_hosts .= " AND hosts.poll = 1 AND hosts.autodiscovery > 1";

  $result_hosts = db_query($query_hosts) or die ('Query failed - iad_get_hosts() '.db_error());

  $hosts = array();
  while ($host = db_fetch_array($result_hosts))
  {
    $host_id = $host['id'];
    $hosts[$host_id] = array(
      'host_id' => $host_id,
      'host_ip' => $host['ip'],
      'rocommunity' => $host['rocommunity'],
      'autodiscovery_default_customer' =>$host['autodiscovery_default_customer'],
      'autodiscovery_id' => $host['autodiscovery'],
      'sysobjectid' => $host['sysobjectid'],
      'tries' => 0,
      'itype' => $itype_id
    );
  }
  return $hosts;
}

function iad_read_children(&$Parent)
{
    $read_timeout = max(time() + ENGINE_HEARTBEAT/$Parent->max_children,2);

    while (time() < $read_timeout) {
        if ($Parent->read_children('iad_read_callback') == FALSE)
            return;
    }
}

function iad_read_callback(&$Parent, $child_id, $child_data)
{
    switch($child_data['cmd'])
    {
    case 'DONE':
        $item_id = $Parent->child_done_job($child_id);
        break;
    case 'NOTDONE':
        $Parent->child_notdone_job($child_id);
        break;
    }
} //iad_read_callback()

?>
      
