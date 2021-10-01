<?php
/* This file is part of JFFNMS
 * Copyright (C) <2002-2011> JFFNMS Authors
 * This program is licensed under the GNU GPL, full terms in the LICENSE file
 */

function discovery_tcp_ports($host,$community, $host_id, $param)
{
  global $Config;
  $ports = array();

  if ( ($host_info = resolve_host($host)) === FALSE)
    return $ports;

  $nmap = $Config->get('nmap_executable');
  list ($af, $ip, $port) = $host_info;
  if ($af == 6)
    $ipv6_flag='-6';
  else
    $ipv6_flag='';

  if ($host_id && (is_executable($nmap)===TRUE))
  { 
    $command = "$nmap $ipv6_flag $param -n -oG - $ip";
    exec($command, $lines, $retval);
    if ($retval != 0)
    {
      logger("tcp_ports(): nmap returned $retval\n");
      return $ports;
    }
    foreach($lines as $id => $line)
    {
      if (preg_match('/Host: [0-9a-f:.]+\s+\S+\s+Ports: (.+)\s+Ignored State:/', $line, $regs))
      {
        $nmap_ports = explode(',',$regs[1]);
        foreach($nmap_ports as $nmap_port)
        {
          $port_data = explode('/', $nmap_port);
          if ($port_data[1] == 'open')
          {
            $port_num = trim($port_data[0]);
            $ports[$port_num] = array(
              'interface' => 'Port '.$port_num,
              'description' => $port_data[4],
              'admin' => 'open',
              'oper' => 'open'
            );
          }
        }//foreach nmap ports
      }// preg match host
    }//foreach lines
  }
  return $ports;
}
?>
