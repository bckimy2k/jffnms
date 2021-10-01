<?php
/* This file is part of JFFNMS
 * Copyright (C) <2002-2011> JFFNMS authors
 * This program is licensed under the GNU GPL, full terms in the LICENSE file
 */

function poller_udp_status ($options)
{
  global $Config;

  if ( ($host_info = resolve_host($options['host_ip'])) === FALSE)
    return 'closed|0';
  list ($af, $ip, $port) = $host_info;

  if ($af == 6)
    $ipv6_flag = '-6';
  else
    $ipv6_flag = '';

  $temp_path = $Config->get('engine_temp_path');
  $nmap = $Config->get('nmap_executable');

  $i = 0;
  if (preg_match('/^(\d+)/', $options['port'], $regs))
    $port = $regs[1];
  else
    return 'error|0';

  $filename = $temp_path.'/'.uniqid('').'.log';
  $command = "$nmap -sU -p$port $ipv6_flag -n -oG $filename $ip";
  exec($command,$a,$b);

  if (file_exists($filename)==true)
  {
    $data = file($filename);
    unlink($filename);
  }

  if (count($data)==3)
  {     
    $pos1 = strpos($data[1],"Ports")+6;
    if ($pos1 > 6) {
      $data_line = substr($data[1],$pos1,strlen($data[1])-$pos1);
      $data_ports = explode(",",$data_line);
    }
    $time = current(array_slice(explode(" ",$data[2]),-2));
  }

  if (is_array($data_ports) && (count($data_ports) > 0))
    foreach ($data_ports as $port) 
      list ($udp_port, $status) = explode("/",trim($port));
  return "$status|$time";
}
?>
