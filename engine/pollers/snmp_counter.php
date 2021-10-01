<?php
/* This file is part of JFFNMS
 * Copyright (C) <2002-2005> Javier Szyszlican <javier@szysz.com>
 * This program is licensed under the GNU GPL, full terms in the LICENSE file
 */
function poller_snmp_counter ($options)
{
    $oid = $options['poller_parameters'];
    $host_ip = $options['host_ip'];

    if (!$options['ro_community'])
        return FALSE;

    $value = trim(get_snmp_counter($options['host_ip'], 
        $options['ro_community'], $oid));
    if ($value === FALSE or $value == '')
        return $value;

    if ( ($pos = strpos($value,' ')) !==FALSE)
        $value = substr($value,0,$pos);
    return (str_replace(array('(',')'),'',$value));
}
?>
