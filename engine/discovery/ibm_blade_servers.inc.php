<?php
/* IBM Blade Servers. This file is part of JFFNMS
 * Copyright (C) <2005> David LIMA <dlima@fr.scc.com>
 * This program is licensed under the GNU GPL, full terms in the LICENSE file
 */

// Some used OID for this interface type
define('BladeServerIndex', '.1.3.6.1.4.1.2.3.51.2.22.1.5.1.1.1');
define('BladeServerSerial', '.1.3.6.1.4.1.2.3.51.2.2.21.4.1.1.11');
define('BladeServerState', '.1.3.6.1.4.1.2.3.51.2.22.1.5.1.1.3');
define('BladeServerHealthState', '.1.3.6.1.4.1.2.3.51.2.22.1.5.1.1.5');
define('BladeServerName', '.1.3.6.1.4.1.2.3.51.2.22.1.5.1.1.6');
define('BladeManufDate', '.1.3.6.1.4.1.2.3.51.2.2.21.4.1.1.9');

function discovery_ibm_blade_servers($ip, $community, $hostid, $param)
{
  $interfaces = array();

  if ($ip && $community && $hostid)
  {
    $indexes = snmp_walk($ip, $community, BladeServerIndex);
    if ($indexes === FALSE) return FALSE;
    $bladeserverserial = snmp_walk($ip, $community, BladeServerSerial);
    $bladeserverstate = snmp_walk($ip, $community, BladeServerState);
    $bladeservername = snmp_walk($ip, $community, BladeServerName);
    $bladeserverhealth = snmp_walk($ip,$community, BladeServerHealthState);
    $blademanufdate = snmp_walk($ip,$community, BladeManufDate);

    if ($indexes !== FALSE)
    {
      foreach($indexes as $index)
      {
        $idx = $index-1;
        if (!isset($bladeserverserial["$idx"])) 
          $bladeserverserial="N/A";
        else
          $bladeserverfru= $bladeserverserial["$idx"];
        if (!isset($bladeservername["$idx"])) $bladeservername["$idx"] = 'N/A';
        if (!isset($blademanufdate["$idx"])) $blademanufdate["$idx"] = 'N/A';
        if (isset($bladeserverstate["$idx"]) && $bladeserverstate["$idx"] == '1')
        {
          $admin = 'Up';
        } else {
          $admin = 'No blade present';
        }
                
        switch ($bladeserverhealth["$idx"])
        {
        case "0": $bladeserverhealth["$idx"] = "N/A"; break;
        case "1": $bladeserverhealth["$idx"] = "good"; break;
        case "2": $bladeserverhealth["$idx"] = "warning"; break;
        case "3": $bladeserverhealth["$idx"] = "bad"; break;
        }
             
        $interfaces["$index"] = array (
          //'interface' => $index,
          'serial' => $bladeserverfru,
          'admin' => $admin,
          'interface' => $bladeservername["$idx"],
          'manuf_date' => $blademanufdate["$idx"],
          'health_state' => $bladeserverhealth["$idx"],
        );
      }
    }
  }
  return $interfaces;
}
?>
