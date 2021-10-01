<?php
/* This file is part of JFFNMS
 * Copyright (C) <2002-2005> Javier Szyszlican <javier@szysz.com>
 * This program is licensed under the GNU GPL, full terms in the LICENSE file
 */
function poller_verify_tc_class_number ($options)
{
    static $static_buffer = array();
    static $buffer_age = 0;
    $buffer_max_len = 20;
    $buffer_max_age = 180;

    $linux_tc_oid = $options['autodiscovery_parameters'];
    
    if (empty($options['ro_community']))
        return -1;

    // Check freshness of buffer
    if ($buffer_age + $buffer_max_age < time()) {
        $static_buffer = array();
        $buffer_age = time();
    }

    if (!array_key_exists($options['host_id'], $static_buffer)) {
        // Check max buffer size
        if (sizeof($static_buffer) > $buffer_max_len)
            array_shift($static_buffer);

        $static_buffer[$options['host_id']] = snmp_walk($options['host_ip'],$options['ro_community'], $linux_tc_oid.'.1.2',1);
    }

    if (is_array($static_buffer[$options['host_id']])) {
	    $class_name = substr($options['interface'],strpos($options['interface'],'/')+1,strlen($options['interface']));
	    $oid_key = array_search($class_name,$static_buffer[$options['host_id']]);
	    return current(array_reverse(explode ('.',$oid_key)));
    }
    return -1;
}
?>
