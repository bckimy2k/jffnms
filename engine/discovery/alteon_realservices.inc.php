<?php
/* Alteon Switch Load Balancing Real Services (PORTS). This file is part of JFFNMS
 * Copyright (C) <2005> Craig Small <csmall@enc.com.au>
 * This program is licensed under the GNU GPL, full terms in the LICENSE file
 */

function discovery_alteon_realservices($ip, $community, $hostid, $param)
{
  $server_type=$param;
  $interfaces = array();

  // SNMP OIDs
  $SlbInfoVirtServ = '.1.3.6.1.4.1.1872.2.1.9.2.4.1.1';
  $SlbInfoService = '.1.3.6.1.4.1.1872.2.1.9.2.4.1.2';
  $SlbInfoRealServ = '.1.3.6.1.4.1.1872.2.1.9.2.4.1.3';
  $SlbInfoRealPort = '.1.3.6.1.4.1.1872.2.1.9.2.4.1.5';
  $SlbInfoOperState = '.1.3.6.1.4.1.1872.2.1.9.2.4.1.6';

  $CfgRealServerIpAddr = '.1.3.6.1.4.1.1872.2.1.5.2.1.2';
  $CfgRealServerState = '.1.3.6.1.4.1.1872.2.1.5.2.1.10';

  $CfgVirtServerDname = '.1.3.6.1.4.1.1872.2.1.5.5.1.5';
  $CfgVirtServiceHname = '.1.3.6.1.4.1.1872.2.1.5.7.1.8';


  if ($ip && $community && $hostid)
  {
    $rsrv_virtservs = snmp_walk($ip, $community, $SlbInfoVirtServ);
    // Die Quickly if no Virtual servers
    if ($rsrv_virtservs === FALSE) return FALSE;
    $rsrv_serviceidx = snmp_walk($ip, $community, $SlbInfoService);
    $rsrv_realservs = snmp_walk($ip, $community, $SlbInfoRealServ);
    $rsrv_realports = snmp_walk($ip, $community, $SlbInfoRealPort);
    $rsrv_oper = snmp_walk($ip, $community, $SlbInfoOperState);

    $ipaddrs = snmp_walk($ip, $community, $CfgRealServerIpAddr);
    $adminstates = snmp_walk($ip, $community, $CfgRealServerState);

    if ($rsrv_virtservs !== FALSE)
    {
        foreach($rsrv_virtservs as $key => $virt_server)
        {
          if (!isset($rsrv_serviceidx["$key"]))
            continue;
          else
            $service_idx = $rsrv_serviceidx["$key"];
          if (!isset($rsrv_realservs["$key"]))
            continue;
          else
            $real_server = $rsrv_realservs["$key"];
          $rs_name = snmp_get($ip,$community,$CfgVirtServerDname.".$virt_server.0");
          $vs_name = snmp_get($ip,$community,$CfgVirtServiceHname.".$virt_server.$service_idx.0");
          $index="$virt_server.$service_idx.$real_server";
          $rs_index = $real_server - 1;
          if (!isset($ipaddrs["$rs_index"]))
            continue;
          else
            $ipaddress = preg_replace('/^[^:]+: /','', $ipaddrs["$rs_index"]);
          // Admin state of port is bound to real server
          if (!isset($adminstates["$rs_index"]) || $adminstates["$rs_index"] != '2')
            $admin = 'down';
          else
            $admin = 'up';
          //echo "Index: $index <br> \n";
          if (!isset($rsrv_realports["$key"]))
            continue;
          else
            $real_port = $rsrv_realports["$key"];
          if (isset($rsrv_oper["$key"]) && $rsrv_oper["$key"] == '2')
            $oper = 'up';
          else
            $oper = 'down';
          $interfaces["$index"] = array (
            'interface' => "$ipaddress:$real_port",
            'hostname' => "$vs_name.$rs_name",
            'address' => $ipaddress,
            'port' => $real_port,
            'real_server' => $real_server,
            'admin' => $admin,
            'oper' => $oper,
           );
        } // foreach
    }
  }
  //var_dump($interfaces);
  return $interfaces;
}
?>
