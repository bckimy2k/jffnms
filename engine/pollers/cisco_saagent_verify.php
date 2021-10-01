<?php
/* This file is part of JFFNMS
 * Copyright (C) <2002-2005> Javier Szyszlican <javier@szysz.com>
 * This program is licensed under the GNU GPL, full terms in the LICENSE file
 */
function poller_cisco_saagent_verify($options)
{
    if ($options['ro_community'] == '')
        return FALSE;

    $numrtt = snmpget($options['host_ip'], $options['ro_community'],
        '.1.3.6.1.4.1.9.9.42.1.5.2.1.1.'.$options['index']);
    if ($numrtt === FALSE)
        return FALSE;
    return "UP|$numrtt";
}
?>
