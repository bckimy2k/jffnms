<?php
/* IIS Discovery. This file is part of JFFNMS
 * Copyright (C) <2004> Robert St.Denis <service@iahu.ca>
 * This program is licensed under the GNU GPL, full terms in the LICENSE file
 */

function discovery_iis_info ($ip, $rocommunity, $hostid, $param)
{
  if (!$ip || !$hostid || !$rocommunity)
    return array();

  $test = snmp_get($ip, $rocommunity, '.1.3.6.1.4.1.311.1.7.3.1.1.0'); 
  if ($test === FALSE)
    return array();

  $iis_info = array(1 => array(
    'interface' => 'IIS Information',
    'admin'     => 'ok',
    'oper'      => 'up'
  ));
  return $iis_info;
}
?>
