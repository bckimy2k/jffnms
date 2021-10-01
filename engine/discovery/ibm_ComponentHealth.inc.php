<?php
/* SNMP IBM component health  Discovery. This file is part of JFFNMS.
 * Copyright (C) <2006> David LIMA <dlima@fr.scc.com>
 * This program is licensed under the GNU GPL, full terms in the LICENSE file
*/

    function discovery_ibm_ComponentHealth($ip, $community, $hostid, $param) {
	
	$interfaces = array();
	
	$ComponentHealth_MIB = ".1.3.6.1.4.1.2.6.159.1.1.30.3.1";
	$KeyIndex_oid = $ComponentHealth_MIB.".1";
	

        if ($ip && $community && $hostid) {
	
	    $ComponentEntries = snmp_walk($ip, $community, $KeyIndex_oid, true);
	    //var_dump($ComponentEntries);
	    
	     if (is_array($ComponentEntries))
		foreach ($ComponentEntries as $oid=>$interface) {

		    $index = join(".",array_slice(explode(".",$oid),10));
		    
		    
		    $interfaces[$index] = array (
			'interface' => $interface,
			'oper' => "OK"
		    );
		}
	    else
		return false;
	}

    	return $interfaces;
    }
?>
