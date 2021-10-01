<?php
/* This file is part of JFFNMS
 * Copyright (C) <2002-2005> Javier Szyszlican <javier@szysz.com>
 * This program is licensed under the GNU GPL, full terms in the LICENSE file
 */
function poller_cisco_envmib_status ($options)
{
    if ($options['ro_community']=='' or !is_numeric($options['index']))
        return FALSE;
    $snmp_value = snmp_get($options['host_ip'], $options['ro_community'],
        '.1.3.6.1.4.1.9.9.13.1.'.$options['poller_parameters'].'.'.
        $options['index']);
    if ($snmp_value == '1')
        return 'up';
    return 'down';
}
?>
