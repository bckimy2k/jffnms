<?php
/* This file is part of JFFNMS
 * Copyright (C) <2002-2005> Javier Szyszlican <javier@szysz.com>
 * Copyright (C) 2012 Craig Small <csmall@enc.com.au>
 * This program is licensed under the GNU GPL, full terms in the LICENSE file
 */
function poller_cisco_saagent_bwpacketloss($options)
{
    if (empty($options['ro_community']) or !is_numeric($options['index']))
        return FALSE;

    $sa_oid = '1.3.6.1.4.1.9.9.42.1.5.2.1';
    $ip = $options['host_ip'];
    $comm = $options['ro_community'];
    $inst = $options['index'];
        
    if ( ($bwpacketloss = snmp_get($ip, $comm, "$sa_oid.27.$inst")) === FALSE)
        return FALSE;
    if ( ($nr = snmp_get($ip, $comm, "$sa_oid.1.$inst")) === FALSE)
        return FALSE;

    $pktloss=0;
    if ($nr > 0)
        $pktloss = $bwpacketloss/($bwpacketloss+$nr)*100;
    return $pktloss;
}
?>
