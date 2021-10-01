<?php
/* This file is part of JFFNMS
 * Copyright (C) <2002-2005> Javier Szyszlican <javier@szysz.com>
 * Copyright (C) 2012 Craig Small <csmall@enc.com.au>
 * This program is licensed under the GNU GPL, full terms in the LICENSE file
 */

function poller_verify_interface_number ($options)
{
    static $static_buffer = array();
    static $buffer_age = 0;
    $buffer_max_len=20; // 20 hosts of details
    $buffer_max_age=180; // 3 minutes of buffer
    require_once(jffnms_shared('catos'));
    require_once(jffnms_shared('webos'));
    $ifIndex_oid = '.1.3.6.1.2.1.2.2.1.1';

    $current_ifindex = $options['interfacenumber'];

    if (empty($options['ro_community']))
        return -1;
    $rocommunity = $options['ro_community'];
    $ip = $options['host_ip'];
    $hid = $options['host_id'];

    # If the interface has no IP address then we need to search by name
    if (($options['address']=='')) {
        // Check freshness of buffer
        if ($buffer_age + $buffer_max_age < time()) {
            $static_buffer = array();
            $buffer_age = time();
        }
        if (!array_key_exists($hid, $static_buffer)) {
            if (sizeof($static_buffer) > $buffer_max_len)
                array_shift($static_buffer);
            $ifIndex = snmp_walk($options['host_ip'],$options['ro_community'],$ifIndex_oid);
            include_once(jffnms_shared('catos'));
            if (is_catos($options['host_ip'], $options['ro_community']))
                $ifDescr_oid = CATOS_IFDESCR_OID;
            else
                $ifDescr_oid = '.1.3.6.1.2.1.2.2.1.2';
            $ifDescr = snmp_walk($options['host_ip'],$options['ro_community'],$ifDescr_oid);
            if (is_array($ifIndex) && count($ifIndex) > 0 && is_array($ifDescr) && count($ifDescr) > 0) {
                $static_buffer[$hid] = array('ifIndex'=>$ifIndex, 'ifDescr'=>$ifDescr);
           } else {
               return -1; //snmpwalk failed
           }

        }
        if ($current_ifindex != '') {
            $pos = array_search($current_ifindex, $static_buffer[$hid]['ifIndex']);
            if (is_numeric($pos) && array_key_exists($pos, $static_buffer[$hid]['ifDescr'])) {
                $polled_ifdescr = $static_buffer[$hid]['ifDescr'][$pos];
                if (strncmp($polled_ifdescr, $options['interface'],30)==0)
                    return $current_ifindex; // we found it
            }
        }
        // If not matched, then its changed, find new index
        $pos = array_search($options['interface'],$static_buffer[$hid]['ifDescr']);    //Find the DB interface name and return the index
        if (is_numeric($pos) and array_key_exists($pos, $static_buffer[$hid]['ifIndex']))                        //if we found something
            return $static_buffer[$hid]['ifIndex'][$pos];            //return the ifIndex at the same position we found this
    } else //Search by IP Address
        if (strpos($options['address'],'.') === FALSE)     // if the address is not an IP, use that as the IfIndex
        {
            return $options['address'];
        } else                                             //If its an IP Address
             return snmp_get($options['host_ip'],$options['ro_community'],     //Get the interface index
                '.1.3.6.1.2.1.4.20.1.2.'.$options['address']);                    //by looking up the IP
    return -1;
}


?>
