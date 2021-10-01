<?php
/* This file is part of JFFNMS
 * Copyright (C) <2002-2005> David LIMA <dlima@fr.scc.com> 
 * This program is licensed under the GNU GPL, full terms in the LICENSE file
 */
function poller_snmp_ibm_temperature ($options)
{
    $oid = $options['poller_parameters'];
    if (empty($options['ro_community']))
        return FALSE;

    $snmp_value =  (snmp_get($options['host_ip'],$options['ro_community'],$oid));
    if ( preg_match('{[+|-](\d+)}',$snmp_value,$matches))
        return $matches[1];
    return FALSE;
}
?>
