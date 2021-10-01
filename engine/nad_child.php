<?php

require_once 'common_engine.inc.php';
$nmap_pipes = array('res'=>FALSE, 'pipes'=>array());

$heartbeat = $argv[1];

if (empty($heartbeat))
  die ("No heartbeat given.\n");

nad_child_main_loop($heartbeat);

function nad_child_main_loop($heartbeat)
{
  $fp = fopen('php://stdin', 'r');
  stream_set_blocking($fp, false);
  if ( ($pid = getmypid()) === FALSE) die("getmypid() returned false");
  child_send_array('START', array('pid'=> $pid));
  $old_time = 0;

  while(TRUE)
  {
    //child_send_pong();
    $read_fds = array($fp);
    $write_fds = NULL;
    $except_fds = NULL;

    $nr = stream_select($read_fds, $write_fds, $except_fds, 1);
    if ($nr === FALSE)
    {
      child_send_error("Child process had error on select.\n");
      continue;
    }
    if ($nr == 0)
    {
      $old_time = check_heartbeat($old_time, $heartbeat);
      continue;
    }

    $line = fgets($fp,4096);
    $params = unserialize($line);
    if (!is_array($params))
    {
      logger("Received bad line from parent, not an array\n");
      continue;
    }
    if (!array_key_exists('cmd', $params))
    {
      logger("Recieved array from parent with no cmd field.\n");
      continue;
    }
    switch($params['cmd'])
    {
    case 'DIE':
      die();
      break;
    case 'DISCOVER':
      if (child_discover_network($params) === TRUE)
        child_send_array('DONE');
      else
        child_send_array('NOTDONE');
      break;
    default:
      logger("Received unknown command '".$params['cmd']."'\n");
    }//switch cmd
    $old_time = check_heartbeat($old_time, $heartbeat);
  }
}

function child_discover_network($params)
{
    return TRUE;
}

function nmap_pipe ($target, $data)
{
    global $Config;

    if (!is_resource($nmap_pipes['res'])) {
        $nmap_pipes['res'] = proc_open(
            $Config->get('nmap_executable') . ' -sP -PB --randomize_hosts -T3 -iL - -oG -',
            array(0=>array('pipe','r'), 1=>array('pipe','w'), 2=>array('pipe','r')),
            $nmap_pipes['pipes']);
        fwrite($nmap_pipes['pipes'][0], $target."\n");
	    fclose($nmap_pipes['pipes'][0]);
    }
	
	if (false !== ($ret = fgets($nmap_pipes["pipes"][1]))) 
	    if ($ret != "# Nmap run completed")
		return rtrim($ret,"\n");

    $nmap_pipes['res']=FALSE;
	return false;
}
