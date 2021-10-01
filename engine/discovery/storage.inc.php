<?php
/* This file is part of JFFNMS
 * Copyright (C) <2002> Robert Bogdon
 * Copyright (C) <2002-2005> Modifications by Javier Szyszlican <javier@szysz.com>
 * This program is licensed under the GNU GPL, full terms in the LICENSE file
 */

function discovery_storage($ip,$rocommunity,$hostid,$param)
{
  $storage_devices = array();
  $blocked_devices = array ('/dev', '/.vol');
  
  if (!$ip || !$hostid || !$rocommunity)
    return $storage_devices;

  $deviceIndex = snmp_walk($ip, $rocommunity, '.1.3.6.1.2.1.25.2.3.1.1');
  if ($deviceIndex === FALSE)
    return $storage_devices;
    
  if (count($deviceIndex) > 0)
  {
    $deviceDescription = snmp_walk($ip, $rocommunity, '.1.3.6.1.2.1.25.2.3.1.3');
    $deviceType = snmp_walk($ip, $rocommunity, '.1.3.6.1.2.1.25.2.3.1.2');
    $deviceBlockSize = snmp_walk($ip, $rocommunity, '.1.3.6.1.2.1.25.2.3.1.4');
    $deviceBlockCount = snmp_walk($ip, $rocommunity, '.1.3.6.1.2.1.25.2.3.1.5');
    for ($i=0; $i < count($deviceIndex) ; $i++) 
    {
      if (!array_key_exists($i,$deviceIndex))
        continue;

      $devInfo = array();
      $aux1 = array();
        
      if (isset($deviceType[$i]))
      {
        $tmp_type = storage_get_device_type($deviceType[$i]);
        if ($tmp_type !== FALSE)
          $devInfo['storage_type'] = $tmp_type;
      }
        
      list($deviceBlockSize[$i], $aux) = explode(' ', $deviceBlockSize[$i]);
      if (isset($deviceBlockSize[$i]) && isset($deviceBlockCount[$i])) 
        $devInfo['size'] = $deviceBlockSize[$i] * $deviceBlockCount[$i];

      include_once(jffnms_shared('storage'));
      list ($devInfo['interface'], $devInfo['description']) = storage_interface_parse_description ($deviceDescription[$i]);
    
      foreach ($devInfo as $key=>$value) $devInfo[$key]=trim($value);

      if (in_array($devInfo['interface'], $blocked_devices)) //set blocked devices to size 0 so they don't get auto-discovered
        $devInfo['size'] = 0;
  
      if ($devInfo['size'] > 0)
      {
        $devInfo['admin'] = 'up';
        $devInfo['oper'] = 'up';
       } else {
        $devInfo['admin'] = 'down';
        $devInfo['oper'] = 'down';
      }
      $storage_devices[$deviceIndex[$i]] = $devInfo;
    }//for
  }//found indexes
  //debug($storage_devices);
  return $storage_devices;
}

function storage_get_device_type($raw_device_type)
{
  if (strpos ($raw_device_type,'.hrStorage')!==FALSE) //UCD-SNMP
  {
    $aux1 = explode('.hrStorage',$raw_device_type);
    if (isset($aux1[count($aux1) - 1]))
      return($aux1[count($aux1) - 1]);
  }
  if (strpos ($raw_device_type,'::hrStorage')!==FALSE) //NET-SNMP
  {
    $aux1 = explode ('::',$raw_device_type);
    return (str_replace('hrStorage','',$aux1[count($aux1)-1]));
  }
  //if we didnt get the name in the OID, use the RFC/MIB Values
  $aux1 = explode ('.', $raw_device_type);
  $device_type_id = current(array_reverse($aux1)); //get the last value of the OID
  switch ($device_type_id)
  {
  case '1' :  return 'Other'; break;  
  case '2' :  return 'Ram'; break;  
  case '3' :  return 'VirtualMemory'; break;  
  case '4' :  return 'FixedDisk'; break;  
  case '5' :  return 'RemovableDisk'; break;  
  case '6' :  return 'FloppyDisk'; break;  
  case '7' :  return 'CompactDisk'; break;  
  case '8' :  return 'RamDisk'; break;  
  }
  return FALSE;
}
?>
