<?php
/* This file is part of JFFNMS
 * Copyright (C) <2002-2005> Javier Szyszlican <javier@szysz.com>
 * This program is licensed under the GNU GPL, full terms in the LICENSE file
 */

//uses CISCO-PING-MIB
//Check if your IOS version supports it in: http://tools.cisco.com/ITDIT/MIBS/servlet/com.cisco.itdit.mibs.mainServlet

function poller_cisco_snmp_ping_start ($options)
{
  $ping_count = $options['pings'];
  $peer = $options['peer'];
  $host_ip = $options['host_ip'];
  $rw_community = $options['rw_community'];
  $interface_id = $options['interface_id'];
  $random = $options['random'];

  $result = -5;
  $oid = '.1.3.6.1.4.1.9.9.16.1.1.1';

  if ($peer == '' || $host_ip == '' ||
    $rw_community == '' || $ping_count <= 0)
    return -1;

  $octects = explode('.',$peer);
  $hex = '';
  foreach ($octects as $octect)
    $hex .= dec2hex($octect);

  $set_oids = array(
    array('16', 'i', '5'),  //create
    array('15', 's', "PING-$interface_id"),  //name
    array('2',  'i', 1),  //tipo ip
    array('3',  'x', $hex),  //ip en hex
    array('4',  'i', $ping_count),  
    array('5',  'i', 64),   //size
    );

  if (snmp_set($host_ip,$rw_community,"$oid.16.$random$interface_id","i","6")) //destroy
  {
    foreach($set_oids as $set_oid)
    {
        if (snmp_set($host_ip,$rw_community, 
            $oid.'.'.$set_oid[0].$random.$interface_id,
            $set_oid[1], $set_oid[2]) === FALSE) {
                logger("ERROR: Ping not ready", 0);
                return -1;
            }
    } //foreach
    if (($result = snmp_get($host_ip,$rw_community,"$oid.16.$random$interface_id"))==2) //2 si esta listo
    {
      snmp_set($host_ip,$rw_community,"$oid.16.$random$interface_id","i",1);   //activarlo  
      return $result;
    }
  }
  return $result;
}

?>
