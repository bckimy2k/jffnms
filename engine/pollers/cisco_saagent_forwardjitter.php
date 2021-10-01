<?php
/* This file is part of JFFNMS
 * Copyright (C) <2002-2005> Javier Szyszlican <javier@szysz.com>
 * This program is licensed under the GNU GPL, full terms in the LICENSE file
 */
function poller_cisco_saagent_forwardjitter($options)
{
    if (empty($options['ro_community']) or !is_numeric($options['index']))
        return FALSE;
    $sa_oid = '1.3.6.1.4.1.9.9.42.1.5.2.1';
    $ip = $options['host_ip'];
    $comm = $options['ro_community'];
    $inst = $options['index'];

    if ( ($sumpossd = snmp_get($ip, $comm, "$sa_oid.9.$inst")) === FALSE)
        return FALSE;
    if ( ($sumnegsd = snmp_get($ip, $comm, "$sa_oid.14.$inst")) === FALSE)
        return FALSE;
    if ( ($nrpossd = snmp_get($ip, $comm, "$sa_oid.8.$inst")) === FALSE)
        return FALSE;
    if ( ($nrnegsd = snmp_get($ip, $comm, "$sa_oid.13.$inst")) === FALSE)
        return FALSE;

    $sum = $sumpossd + $sumnegsd;
    $nr = $nrpostsd + $nrnegsd;

    $jitter=0;
    if ($nr > 0)
        $jitter = round($sum / $nr,2);
    return $jitter;
}
?>
