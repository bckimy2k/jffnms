<?php
/* Alteon Switch Load Balancing Virt Servers. This file is part of JFFNMS
 * Copyright (C) <2005> Craig Small <csmall@enc.com.au>
 * This program is licensed under the GNU GPL, full terms in the LICENSE file
 */



function discovery_alteon_virtualservers($ip, $community, $hostid, $param)
{
  // SNMP OID
  $CfgVirtServerIndex = '.1.3.6.1.4.1.1872.2.1.5.5.1.1';
  $CfgVirtServerIpAddress = '.1.3.6.1.4.1.1872.2.1.5.5.1.2';
  $CfgVirtServerState = '.1.3.6.1.4.1.1872.2.1.5.5.1.4';
  $CfgVirtServerDname = '.1.3.6.1.4.1.1872.2.1.5.5.1.5';
  $CfgVirtServiceHname = '.1.3.6.1.4.1.1872.2.1.5.7.1.8';

  $server_type=$param;
  $interfaces = array();

  if ($ip && $community && $hostid)
  {
    $indexes = snmp_walk($ip, $community, $CfgVirtServerIndex);
    // Die Quickly if no index
    if ($indexes === FALSE) return FALSE;
    $ipaddrs = snmp_walk($ip, $community, $CfgVirtServerIpAddress);
    $adminstates = snmp_walk($ip, $community, $CfgVirtServerState);
    $serverdnames = snmp_walk($ip, $community, $CfgVirtServerDname);
    if ($indexes !== FALSE)
    {
      foreach($indexes as $key => $index)
      {
          $ipaddress = '';
          if (array_key_exists($key, $ipaddrs)) {
             if (($colpos = strpos($ipaddrs[$key],':')) !== FALSE)
                 $ipaddress = substr($ipaddrs[$key],$colpos+1);
             else
                 $ipaddress = $ipaddrs[$key];
          }
          $ipaddress = trim($ipaddress);
          if (empty($serverdnames["$key"]))
        {
          $servername = 'unknown';
        } else {
          $service_hnames = snmp_walk($ip, $community,$CfgVirtServiceHname.".$index" );
          if (!empty($service_hnames[0]))
          {
            $servername = "$service_hnames[0].$serverdnames[$key]";
          } else {
            $servername = $serverdnames["$key"];
          }
        }
        if (isset($adminstates["$key"]) && $adminstates["$key"] == '2') 
          $admin = 'up';
        else
          $admin = 'down';

        $interfaces["$index"] = array (
          'interface' => $ipaddress,
          'hostname' => $servername,
          'address' => $ipaddress,
          'admin' => $admin,
          'oper' => 'up', //Always up if switch is alive
        );
      }
    }
  }
  //var_dump($interfaces);
  return $interfaces;
}
?>
