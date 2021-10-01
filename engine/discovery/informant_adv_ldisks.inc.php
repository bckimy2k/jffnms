<?php
/* Informant Logical Disk. This file is part of JFFNMS
 * Copyright (C) <2008> Craig Small <csmall@enc.com.au>
 * Based on the work by David LIMA and Sebastian van Dijk
 */

// 
define('lDiskInstance', '.1.3.6.1.4.1.9600.1.2.44.1.1');

function discovery_informant_adv_ldisks($ip, $community, $hostid, $param)
{
  $interfaces = array();

  if ($ip && $community && $hostid) {
    $instances = snmp_walk($ip, $community, lDiskInstance, TRUE);
    if ($instances === FALSE) return FALSE;
    foreach($instances as $oid => $instance) {
      //$oid = 'enterprises.9600.1.2.44.1.1.2.67.58' i think
      $index = join('.',array_slice(explode('.',$oid),7));
      $interfaces["$index"] = array (
        'interface' => $instance.' Stats',
        'oper' => 'up'
      );
    }
  }
  return $interfaces;
}

