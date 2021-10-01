<?php
/* This file is part of JFFNMS
 * Copyright (C) <2002-2005> Javier Szyszlican <javier@szysz.com>
 * This program is licensed under the GNU GPL, full terms in the LICENSE file
 */
define('CATOS_IFDESCR_OID','.1.3.6.1.2.1.31.1.1.1.1');

function is_catos ($ip, $comm)
{
  return ((strpos(snmp_get($ip, $comm,'.1.3.6.1.2.1.1.1.0'),'Catalyst Operating System')!==false)?true:false);
}

function catos_info($ip, $comm, $ifIndex)
{
  $ifDescr_oid = CATOS_IFDESCR_OID;
  $ifAlias = array();

  // CatOs portName is indexted by module and port instead of ifNum..... Grrrrrrr
  // OID .1.3.6.1.4.1.9.5.1.4.1.1.4.mod.port yields the portName
  // OID .1.3.6.1.4.1.9.5.1.4.1.1.11 appears to xref .mod.port back to ifNum however

  $ifPortName  = snmp_walk($ip, $comm, '.1.3.6.1.4.1.9.5.1.4.1.1.4');
  $ifPortIfNum = snmp_walk($ip, $comm, '.1.3.6.1.4.1.9.5.1.4.1.1.11');

  if (is_array($ifPortIfNum))
    foreach ($ifPortIfNum as $PortID=>$ifNum)
      if ( ($key = array_search($ifNum, $ifIndex)) !== FALSE)
        $ifAlias[$key] = $ifPortName[$PortID];
  return array($ifDescr_oid, $ifAlias);
}

?>
