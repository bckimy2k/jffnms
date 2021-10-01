<?php
/* This file is part of JFFNMS
 * Copyright (C) <2002-2005> Javier Szyszlican <javier@szysz.com>
 * This program is licensed under the GNU GPL, full terms in the LICENSE file
 */
//This poller stores all the table on an array and then request its contents,
//this is faster if you are monitoring a lot of same-type interface in a host

function poller_snmp_interface_status_all ($options)
{
    // buffer->hid
    static $static_buffer = array();
    static $buffer_age = 0;
    $hid = $options['host_id'];
    $buffer_max_age = 180; // 3 minutes of buffer
    $buffer_max_size = 20; // 20 hosts worth of data
    $min_iface_buffer = 30; // If device has 30 or more int, buffer it
    $snmp_col = $options['poller_parameters'];

    if (!$options['ro_community'])
        return FALSE;

	if (($options['poller_parameters']==7) && //If we're polling the Admin Status
	    ($options['fixed_admin_status']==1))  //And the interface has specified that it does not want its admin status to be modified
        return FALSE; //Return nothing

    // Check freshnewss of buffer
    if ($buffer_age + $buffer_max_age < time()) {
        $static_buffer = array();
        $buffer_age = time();
    }

    // Fill static buffer
    if (!array_key_exists($hid, $static_buffer)) {
        // Check max size
        if (count($static_buffer) > $buffer_max_size) 
            shift($static_buffer);

        $static_buffer[$hid]=array('number_of_interfaces' => snmp_get($options['host_ip'],$options['ro_community'],'.1.3.6.1.2.1.2.1.0'));
    }
    if ($static_buffer[$hid]['number_of_interfaces'] > $min_iface_buffer) {
        if (!array_key_exists($snmp_col, $static_buffer[$hid])) {
            $rets=snmp_walk($options['host_ip'],$options['ro_community'],'.1.3.6.1.2.1.2.2.1.'.$options['poller_parameters'],1);
            if (is_array($rets)) {
                $static_buffer[$hid][$snmp_col] = array();
                foreach ($rets as $key=>$entry) {
                    $key=explode('.',$key);
	                $key=$key[count($key)-1];
	                $static_buffer[$hid][$snmp_col][$key]=$entry;
                }
            }
        }
    }
    $check_val = '';
    if ($static_buffer[$hid]['number_of_interfaces'] > $min_iface_buffer) {
        if (!array_key_exists($options['interfacenumber'], $static_buffer[$hid][$snmp_col]))
            return FALSE; // No found in buffer
        $check_val = $static_buffer[$hid][$snmp_col][$options['interfacenumber']];
    } else {
        $check_val = snmp_get($options['host_ip'],$options['ro_community'],'.1.3.6.1.2.1.2.2.1.'.$snmp_col.'.'.$options['interfacenumber']);
    }

    if ($check_val == '')
        return FALSE;

    list($check_val) = explode('(',$check_val);

	if (is_numeric($check_val)) //Process MIB Values
		switch ($check_val) {
		case '1'	:	$value = 'up'; break;
		case '2'	:	$value = 'down'; break;
		case '3'	:	$value = 'testing'; break;
		default	:	$value = 'down';
		}	
	else
	    $value=$check_val;
	return $value;
}
?>
