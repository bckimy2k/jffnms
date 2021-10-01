<?php
/* This file is part of JFFNMS
 * Copyright (C) <2002-2005> Javier Szyszlican <javier@szysz.com>
 * This program is licensed under the GNU GPL, full terms in the LICENSE file
 */
function discovery_reachability ($host,$community, $host_id, $param)
{
  global $Config;
  $reach = array();
  
  if ( ($host_info = resolve_host($host)) === FALSE)
    return $reach;

  list ($af, $ip, $port)= $host_info;
  if ($af == 6)
    $fping = $Config->get ('fping6_executable');
  else
    $fping = $Config->get ('fping_executable');
  
  $perms = @fileperms($fping);


  if ($host_id && (file_exists($fping)===TRUE) && ($perms & 0x800))
  { 
    $reach['1']['description'] = 'Reachability to '.$ip;
    $reach['1']['interface'] = 'Reachability Test';
    $reach['1']['admin'] = 'Not Checked';
    $reach['1']['oper'] = 'reachable';
  }
  return $reach;
}
?>
