<?php
/* This file is part of JFFNMS
 * Copyright (C) <2002-2005> Javier Szyszlican <javier@szysz.com>
 * This program is licensed under the GNU GPL, full terms in the LICENSE file
 */
function poller_reachability_status ($options)
{
  global $poller_buffer;

  $pl = $poller_buffer['packetloss-'.$options['interface_id']]; //get PacketLoss from reachability_pl

  $num_ping = $options['pings'];
  $interval = $options['interval'];
  $threshold = $options['threshold'];

  $pl_percent = ($pl * 100) / $num_ping;
    
  $result = 'reachable';
  if ($pl_percent > $threshold) $result = 'unreachable';

  $result .= "|$pl_percent% Packet Loss";
  return $result;
}
?>
