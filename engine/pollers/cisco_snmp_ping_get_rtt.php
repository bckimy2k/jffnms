<?php
/* This file is part of JFFNMS
 * Copyright (C) <2002-2005> Javier Szyszlican <javier@szysz.com>
 * This program is licensed under the GNU GPL, full terms in the LICENSE file
 */
function poller_cisco_snmp_ping_get_rtt ($options)
{
  global $poller_buffer;

  $oid = ".1.3.6.1.4.1.9.9.16.1.1.1";
  $interface_id = $options['interface_id'];

  $buffer_name = "cisco_snmp_ping_start-$interface_id";
  if (!array_key_exists($buffer_name, $poller_buffer) ||
    $poller_buffer[$buffer_name] !=2)
    return 0;

  $rtt=round(snmp_get($options['host_ip'],$options['rw_community'],"$oid.12.$options[random]$interface_id"));
  return $rtt;
}
?>
