<?php
/* This file is part of JFFNMS
 * Copyright (C) <2006> Javier Szyszlican <javier@szysz.com>
 * This program is licensed under the GNU GPL, full terms in the LICENSE file
 */

function storage_interface_parse_description ($descr) {
    $description = '';

	$label_hex = strpos($descr,"Hex");
	if ($label_hex!==FALSE)   // UCD-SNMP 4.2.4 fix
	    $descr = substr($descr,0,$label_hex-1);
		    
	$descr  = str_replace("\"","",$descr); // UCD-SNMP 4.2.4 fix
	$descr  = str_replace("\\","",$descr); // Windows Hack for C:\ breaking the DB
    // By default it is the filtered thing given to us
	$interface = $descr; 

	//Windows XP Disk Label Hack
	$label_pos = strpos($descr,"Label");
	if ($label_pos!==false) {  
	    $interface = substr($descr,0, $label_pos - 1); //strip the \ and the space
	    $description = substr($descr,$label_pos, strlen($descr) - $label_pos);
	} 

    // Juniper JUNOS hack
    if (preg_match('/mounted on: (\S+)/', $descr, $groups)) {
        $interface = $groups[1];
    }
	
    	    
	return array($interface, $description);
    }
?>
