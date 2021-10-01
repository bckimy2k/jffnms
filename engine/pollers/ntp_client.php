<?php
/* NTP (Network Time Protocol) poller, checks directy into each host. This file is part of JFFNMS
 * Copyright (C) <2004-2011> JFFNMS Authors
 * This program is licensed under the GNU GPL, full terms in the LICENSE file
 */

function poller_ntp_client ($data)
{
    global $Config;

    $ntp_command = $Config->get('ntpq_executable');
    if (!is_executable($ntp_command)) 
        return FALSE;

    exec($ntp_command.' -p '.$data['host_ip'].' 2>/dev/null',$raw_result);
    if (count($raw_result)<=2)
        return 'unsynchronized';

    unset($raw_result[0]);    
    unset($raw_result[1]);    
        
    $statuses = array();
                        
    foreach ($raw_result as $line) {
        if (preg_match('/^[*#]([a-z0-9.-]+)\s+\S+\s+(\d+)\s+(\S+)/',$line, $regs))
        {
            if ($regs[3] != 'l' or $regs[2] == 0) //not local clock or is 0 stratum
                return "synchronized|with $regs[1]";
        }
    }
    return 'unsynchronized';
}
?>
