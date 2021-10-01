<?php
/* This file is part of JFFNMS
 * Copyright (C) <2002-2005> Javier Szyszlican <javier@szysz.com>
 * This program is licensed under the GNU GPL, full terms in the LICENSE file
 */

function poller_bgp_peer_status ($options)
{
    $oid = '.1.3.6.1.2.1.15.3.1.2.'.$options['remote'];
    $value = snmp_get($options["host_ip"],$options["ro_community"],$oid);

    if ($value == '')
        return FALSE;
    if ($value == '6')
        return 'up';
    return 'down';
}

?>
