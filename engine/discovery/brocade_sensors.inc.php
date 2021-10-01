<?php
/* Brocade Sensors. This file is part of JFFNMS
 * Copyright (C) <2005> Craig Small <csmall@enc.com.au>
 * This program is licensed under the GNU GPL, full terms in the LICENSE file
 */
function discovery_brocade_sensors($ip, $community, $hostid, $param)
{
  $swSensorIndex = '1.3.6.1.4.1.1588.2.1.1.1.1.22.1.1';
  $swSensorType = '1.3.6.1.4.1.1588.2.1.1.1.1.22.1.2';
  $swSensorStatus = '1.3.6.1.4.1.1588.2.1.1.1.1.22.1.3';
  $swSensorValue = '1.3.6.1.4.1.1588.2.1.1.1.1.22.1.4';
  $swSensorInfo = '1.3.6.1.4.1.1588.2.1.1.1.1.22.1.5';

  $sensors = array();

  if ($ip && $community && $hostid)
  {
    $indexes = snmp_walk($ip, $community, $swSensorIndex);
    // Bomb out now if no sensors
    if ($indexes === FALSE) return FALSE;
    $types = snmp_walk($ip, $community, $swSensorType);
    $stats = snmp_walk($ip, $community, $swSensorStatus);
    $values = snmp_walk($ip, $community, $swSensorValue);
    $infos = snmp_walk($ip, $community, $swSensorInfo);
    foreach($indexes as $key => $index)
    {
      // Sensor is absent
      if ($stats["$key"] == 6) continue;
      // Magic number
      if ($values["$key"] == '-2147483648') continue;
      $type = brocade_sensor_type($types["$key"]);
      $sensors["$index"] = array(
        'interface' => $infos["$key"],
        #'description' => $infos["$key"],
        'sensor_type' => $type,
        'oper' => brocade_sensor_oper($stats["$key"]),
        'admin' => brocade_sensor_admin($stats["$key"]),
      );
    }
  }
  return $sensors;
}

function brocade_sensor_type($type)
{
  $SENSOR_TYPES = array('1'=>'temperature', '2' => 'fan', '3' => 'power');
  if (array_key_exists($type, $SENSOR_TYPES)) {
    return $SENSOR_TYPES["$type"];
  }
  return "Unknown ($type)";
}

function brocade_sensor_oper($status)
{
  if ($status == '4')  // nominal
      return 'ok';
  if ($status == '3' || $status == '5')  // nominal
      return 'alert';
  return 'down';
}

function brocade_sensor_admin($status)
{
  $istat = intval($status);
  if ($status > 1 && $status < 6)
      return 'up';
  return 'down';
}
?>
