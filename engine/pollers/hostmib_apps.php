<?php
/* HOSTMIB Software Running Poller. This file is part of JFFNMS
 * Copyright (C) <2004> Anders Karlsson <anders.x.karlsson@songnetworks.se>
 * This program is licensed under the GNU GPL, full terms in the LICENSE file
 */


function poller_hostmib_apps ($options)
{
	global $Apps;
    static $buffer_age = 0;
    $buffer_max_len=20;
    $buffer_max_age=180;

	$oid = '.1.3.6.1.2.1.25.4.2.1.2'; //host.hrSWRun.hrSWRunTable.hrSWRunEntry.hrSWRunName
	$hid = $options['host_id'];

    if (empty($options['ro_community']))
        return FALSE;

    // Check freshness of buffer
    if (!array_key_exists('Apps', $GLOBALS) or ($buffer_age + $buffer_max_age < time())) {
        $Apps = array();
        $buffer_age = time();
    }

    if (!array_key_exists($hid, $Apps) || !array_key_exists('raw',$Apps[$hid])) 
        $Apps[$hid] = array(
            'raw'=> snmp_walk($options['host_ip'],$options['ro_community'],$oid,1),
                'pids'=> array()
        );
	
	if (is_array($Apps[$hid]['raw'])) {
		$instances=0;
		foreach ($Apps[$hid]['raw'] as $key=>$service) //go thru all 
        {
		    $interface_in = trim(str_replace(array("\"","'"),'',trim($service)));
            if (array_key_exists('ignorecase', $options) && $options['ignorecase'])
                $match = strncasecmp($interface_in,$options['interface'],30);
            else
                $match = strncmp($interface_in,$options['interface'],30);
            if ($match == 0) {
				$instances++;
			    $pid = end(explode('.',$key));
			    $Apps[$hid]['pids'][$interface_in][]=$pid;
		    }	
		}
		$value='not running';
		if ( $instances > 0 ) 
		    $value="running;$instances Instance(s)|$instances";
		return $value;
	}
}
?>
