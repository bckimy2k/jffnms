<?php
/* Generic FC Ports . This file is part of JFFNMS
 * Copyright (C) <2006> David LIMA <dlima@fr.scc.com>
 * This program is licensed under the GNU GPL, full terms in the LICENSE file
 */


function discovery_fc_ports($ip, $community, $hostid, $param)
{
  $fcFxPortPhysOperStatus = '1.3.6.1.2.1.75.1.2.2.1.2';
  $fcFxPortPhysAdminStatus = '1.3.6.1.2.1.75.1.2.2.1.1';

  $interfaces = array();
  if ($ip && $community && $hostid)
  {
    $oidindex = snmp_walk($ip,$community, $fcFxPortPhysOperStatus,true);
    if ($oidindex ===FALSE) return FALSE;
    foreach ($oidindex as $oid => $value)
    {
      $realindex = join('.',array_slice(explode('.',$oid),7));
      $admins = snmp_get($ip, $community, $fcFxPortPhysAdminStatus.'.'.$realindex);
      $index =  join('.',array_slice(explode('.',$realindex),1));
      $interfaces[$index] = array (
        'interface' => 'FC Port '.($index-1),
        'oper' => fcport_oper($value),
        'admin' => fcport_admin($admins),
        'real_index' => $realindex,
      );
    }      
  }
  //debug ($interfaces);
  return($interfaces);
}

function fcport_oper($status)
{
    if ($status == '1') return 'up'; #online
    if ($status == '4') return 'down'; # link-failure
    if ($status == '3') return 'testing'; # testing
 return 'down';
}

function fcport_admin($status)
{
    if ($status == '1') return 'up'; #online
    if ($status == '3') return 'testing'; # testing
 return 'down';
}



?>

