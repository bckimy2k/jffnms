<?php
/* This file is part of JFFNMS
 * Copyright (C) <2002-2005> Javier Szyszlican <javier@szysz.com>
 * This program is licensed under the GNU GPL, full terms in the LICENSE file
 */
function poller_reachability_values ($options)
{
  global $Config;
  global $poller_buffer;

  $fping_pattern = "/\S+ : xmt\/rcv\/%loss = (\S+)\/(\S+)\/\S+%(, min\/avg\/max = \S+\/(\S+)\/\S+|)/";
  $temp_path = $Config->get('engine_temp_path');
  $uniq = $poller_buffer['ping-'.$options['interface_id']]; //get file id from reachability_start
    
  $filename = "$temp_path/$uniq.log";
  $which_value = $options['poller_parameters']; //Poller Parameter specifies which value to return
  $num_pings = $options['pings'];
        
  if (file_exists($filename))
  {
    $data = file($filename); //get last trimmed line of the fping result
    $data = trim(end($data));
    if (!preg_match($fping_pattern,$data,$parts))
        unset($parts);
  }
  switch($which_value) {
  case 'rtt':
      if (isset($parts) && !empty($parts[3]))
          return ($parts[4]); // RTT Average
      else
          return "0";
      break;
  case 'pl':
      if (isset($parts))
          return ($parts[1] - $parts[2]); // Lost Packets = Sent Packets - Recv Packets 
      else
          return $num_pings; // If something breaks, then we lost the lot
      break;
  }
  return "0";
}
?>
