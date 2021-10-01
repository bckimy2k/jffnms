<?php
/* This file is part of JFFNMS
 * Copyright (C) <2006> Javier Szyszlican <javier@szysz.com>
 * This program is licensed under the GNU GPL, full terms in the LICENSE file
 */
function poller_verify_storage_index ($options)
{
    $hrStorageDescr_oid = '.1.3.6.1.2.1.25.2.3.1.3';
    $index_actual = -1;
    static $static_buffer = array();
    static $buffer_age=0;
    $buffer_max_len=20;
    $buffer_max_age=180;

    if (empty($options['ro_community']))
        return $index_actual;

    // Check freshness for buffer
    if ($buffer_age + $buffer_max_age < time()) {
        $static_buffer = array();
        $buffer_age = time();
    }
    if (!array_key_exists($options['host_id'], $static_buffer)) {
        // Check buffer length
        if (sizeof($static_buffer) > $buffer_max_len)
            array_shift($static_buffer);
        $storage_descs = snmp_walk($options['host_ip'],$options['ro_community'],
	    $hrStorageDescr_oid, INCLUDE_OID_1);

	    include_once(jffnms_shared('storage'));
	
        if (is_array($storage_descs)) {
            $static_buffer[$options['host_id']] = array();
            foreach ($storage_descs as $key => $value) {
	            $static_buffer[$options['host_id']][$key]=substr(current(storage_interface_parse_description ($value)),0,30);
            }
        }
	    unset ($storage_descs);
    }

    if (array_key_exists($options['host_id'], $static_buffer) and 
        sizeof($static_buffer[$options['host_id']]) > 0) {
	    $storage_name = $options['interface'];
	    if (($index_actual = array_search($storage_name, $static_buffer[$options['host_id']]))===false)
	        $index_actual = 90000+$options['interface_id'];	// if its not found in the host, change its id out of the picture for removal
    }
    return $index_actual;		    
}
?>
