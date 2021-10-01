<?php
/* This file is part of JFFNMS
 * Copyright (C) <2002-2005> Javier Szyszlican <javier@szysz.com>
 * This program is licensed under the GNU GPL, full terms in the LICENSE file
 */
function poller_cisco_saagent_backwardjitter($options)
{
    if (empty($options['ro_community']) or !is_numeric($options['index']))
        return FALSE;
    $sa_oid = '1.3.6.1.4.1.9.9.42.1.5.2.1';
    $ip = $options['host_ip'];
    $comm = $options['ro_community'];
    $inst = $options['index'];

    if ( ($sumposds = snmp_get($ip, $comm, "$sa_oid.19.$inst")) === FALSE)
        return FALSE;
    if ( ($sumnegds = snmp_get($ip, $comm, "$sa_oid.24.$inst")) === FALSE)
        return FALSE;
    if ( ($nrposds = snmp_get($ip, $comm, "$sa_oid.18.$inst")) === FALSE)
        return FALSE;
    if ( ($nrnegds = snmp_get($ip, $comm, "$sa_oid.23.$inst")) === FALSE)
        return FALSE;
        
    $sum = $sumposds + $sumnegds;
    $nr = $nrposds + $nrnegds;

    $jitter=0;
    if ($nr > 0)
        $jitter = round($sum / $nr,2);
    return $jitter;
}
?>
