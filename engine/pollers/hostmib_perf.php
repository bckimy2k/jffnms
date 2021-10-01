<?php
/* This file is part of JFFNMS
 * Copyright (C) <2002-2005> Javier Szyszlican <javier@szysz.com>
 * This program is licensed under the GNU GPL, full terms in the LICENSE file
 */
    //HOSTMIB Software Performance Poller
    //By: Javier Szyszlican
    //Based on the Status Poller By: Anders Karlsson <anders.x.karlsson@songnetworks.se>

function poller_hostmib_perf ($options)
{
    global $Apps;
    $hid = $options['host_id'];

    $hrSWRunPerfEntry_oid = '.1.3.6.1.2.1.25.5.1.1';
    $perf_oid = $hrSWRunPerfEntry_oid.'.'.$options['poller_parameters'];
    
    if (!array_key_exists('Apps', $GLOBALS))
        return FALSE;

    $value = 0;
    if (array_key_exists($hid, $Apps) && is_array($Apps[$hid]['pids'][$options['interface']])) { //if we got something

        $pids = $Apps[$hid]['pids'][$options['interface']];
        
        foreach ($pids as $pid)
            $value += intval(current(explode(' ',snmp_get($options['host_ip'], $options['ro_community'], $perf_oid.".$pid"))));
    }
    return $value;
}
?>
