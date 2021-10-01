<?php
/* NTP (Network Time Protocol) discovery, checks directy into each host, This file is part of JFFNMS
 * Copyright (C) <2004-2011> JFFNMS Authors
 * This program is licensed under the GNU GPL, full terms in the LICENSE file
 */

function discovery_ntp_client ($ip,$rocommunity,$hostid,$param)
{
  global $Config;

  $ntp_client = array();
  $ntp_command = $Config->get('ntpq_executable');

  if ( ($host_info=resolve_host($ip)) === FALSE)
    return $ntp_client;
  list ($af, $ip, $port) = $host_info;

  if (!is_executable($ntp_command))
    return $ntp_clients;

  exec($ntp_command.' -p '.$ip.' 2>/dev/null',$raw_result);
  if (count($raw_result)>0 && ($raw_result[0] != "No association ID's returned"))
  { 
    $ntp_client['1'] = array('interface' => 'Time', 'oper' => 'unsynchronised');
    foreach ($raw_result as $line)
    {
      if (preg_match('/^\s+remote/', $line) || preg_match('/^=+/', $line))
        continue;
      // scan result looking for a * as status and u as type
      if (preg_match('/^\*\S+\s+[0-9.]+\s+\d+\s+u/i', $line))
      {
        $ntp_client[1]['oper'] = 'synchronised';
        break;
      }
    }
  }
  return $ntp_client;
}
?>
