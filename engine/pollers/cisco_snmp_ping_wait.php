<?php
/* This file is part of JFFNMS
 * Copyright (C) <2002-2005> Javier Szyszlican <javier@szysz.com>
 * This program is licensed under the GNU GPL, full terms in the LICENSE file
 */
function poller_cisco_snmp_ping_wait ($options) {
  global $poller_buffer;

  $ping_count = $options['pings'];
  $oid = '.1.3.6.1.4.1.9.9.16.1.1.1';
  $cant = 1;
  
  $buffer_name = "cisco_snmp_ping_start-$options[interface_id]";
  if (!array_key_exists($buffer_name, $poller_buffer) ||
    $poller_buffer[$buffer_name] != 2)
    return -1;

  //Esperar a que terminen los pings o que pasen mas de ((.25*4)*8) == 8 seg = 32 == 30
  $timeout = 2; //sec per ping 
  $wait = 4;
  $max_wait = ($ping_count*$timeout*$wait)*80/100; //cant de usleeps max
  while (( ($pings_sent = trim(get_snmp_counter($options['host_ip'],$options['rw_community'],"$oid.9.$options[random]$options[interface_id]"))) < $ping_count ) && ($cant < $max_wait))
  {
    usleep((1000000/$wait));
    $cant++;
    echo ".";
  }
  echo "\n";
  //logger("end wait\n");
  if ($cant < $max_wait)
    return $pings_sent;
  return -1;
}
?>
