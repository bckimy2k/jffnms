<?php
/* This file is part of JFFNMS
 * Copyright (C) <2002-2011> JFFNMS Authors
 * This program is licensed under the GNU GPL, full terms in the LICENSE file
 */

//Read the tcp.tcpConnTable.tcpConnEntry.tcpConnState table to find out
//which ports are used in the host

function poller_tcp_connection_number ($options)
{
    static $static_buffer = array();
    static $buffer_age = 0;
    $buffer_max_len=20; // 20 hosts of details
    $buffer_max_age=180; // 3 minutes of buffer
    $port = $options['port'];
    $hid = $options['host_id'];

    if ($options['ro_community'] =='')
       return FALSE; 

    if ($buffer_age + $buffer_max_age < time()) {
        $static_buffer = array();
        $buffer_age = time();
    }
    if (!array_key_exists($hid, $static_buffer) or count($static_buffer[$hid])==0) {
        if (sizeof($static_buffer) > $buffer_max_len)
            array_shift($static_buffer);

        // Fill the static buffer with port counts
        $snmp_table = snmp_walk ($options['host_ip'],$options['ro_community'],'.1.3.6.1.2.1.6.13.1.1',1);
        if (!is_array($snmp_table))
            return 0;
        reset($snmp_table);
        $static_buffer[$hid]=array();
        foreach ($tcpConnEntry as $key => $state)
            if (strpos($state,'5')!==FALSE) { //only established
                $entry = explode('.',$key);
	            $entry = array_slice ($entry, count($entry)-10,10); //get only the last 10 items (SRC-IP(4) + srcport + DEST-IP(4) + destport)
	            $entry_port = $entry[4]; //srcport (local)

                if (array_key_exists($entry_port,$static_buffer[$hid]))
                    $static_buffer[$hid][$entry_port]++;
                else
                    $static_buffer[$hid][$entry_port]=1;
            }//state==5
    }//buffer doesnt have hid
    if (array_key_exists($hid, $static_buffer) and array_key_exists($port, $static_buffer[$hid]))
        return $static_buffer[$hid][$port];
    return 0;
}
?>
