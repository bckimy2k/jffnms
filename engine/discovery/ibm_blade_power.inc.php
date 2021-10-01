<?php
/* IBM Blade Servers. This file is part of JFFNMS
 * Copyright (C) <2005> David LIMA <dlima@fr.scc.com>
 * This program is licensed under the GNU GPL, full terms in the LICENSE file
 */

// Some used OID for this interface type
define('fuelGaugeIndex', '.1.3.6.1.4.1.2.3.51.2.2.10.1.1.1.1');
define('fuelGaugeStatus', '.1.3.6.1.4.1.2.3.51.2.2.10.1.1.1.3');
define('fuelGaugeFirstPowerModule', '.1.3.6.1.4.1.2.3.51.2.2.10.1.1.1.4');
define('fuelGaugeSecondPowerModule', '.1.3.6.1.4.1.2.3.51.2.2.10.1.1.1.5');
define('fuelGaugeTotalPower', '.1.3.6.1.4.1.2.3.51.2.2.10.1.1.1.7');

function discovery_ibm_blade_power($ip, $community, $hostid, $param)
{
  $interfaces = array();

  if ($ip && $community && $hostid)
  {
    $indexes = snmp_walk($ip, $community, fuelGaugeIndex);
    if ($indexes !== FALSE)
    {
      $fuelgaugestatus = snmp_walk($ip, $community, fuelGaugeStatus);
      $fuelgaugefirstpowermodule = snmp_walk($ip, $community, fuelGaugeFirstPowerModule);
      $fuelgaugesecondpowerpodule = snmp_walk($ip, $community, fuelGaugeSecondPowerModule );
      $fuelgaugetotalpower = snmp_walk($ip,$community, fuelGaugeTotalPower);
          
      foreach($indexes as $idx=>$index)
      {
        $interfaces[$index] = array (
          'admin'=> 'ok',
          'interface' => "Power domain $index",
          'status' => $fuelgaugestatus[$idx],
          'module1' => $fuelgaugefirstpowermodule[$idx],
          'module2' => $fuelgaugesecondpowerpodule[$idx],
          'totalpower' => $fuelgaugetotalpower[$idx],
        );
      }
    }
   }        
  return $interfaces;
}
?>
