<?php
/* This file is part of JFFNMS
 * Copyright (C) <2002-2005> Javier Szyszlican <javier@szysz.com>
 * This program is licensed under the GNU GPL, full terms in the LICENSE file
 */
function poller_reachability_start ($options)
{
  global $Config;

  $temp_path = $Config->get('engine_temp_path');
  $uniq=FALSE;

  if ( ($host_info = resolve_host($options['host_ip'])) === FALSE)
    return FALSE;
  list ($af, $ip, $port) = $host_info;
  if ($af == 6)
    $fping = $Config->get('fping6_executable');
  else
    $fping = $Config->get('fping_executable');
  
  $num_ping = $options['pings'];
  $interval = $options['interval'];

  if (file_exists($fping) && ($num_ping > 0) && ($interval > 10))
  {
    $uniq = uniqid('');
    $filename = "$temp_path/$uniq.log";
    $command = "$fping -c $num_ping -p $interval -q $ip > $filename 2>&1 &";
    exec($command); //FIXME check if it is running
  
  }
  return $uniq;
}
?>
