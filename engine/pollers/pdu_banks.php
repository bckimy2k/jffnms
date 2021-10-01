<?php
/* This file is part of JFFNMS
 * Copyright (C) <2002-2005> Javier Szyszlican <javier@szysz.com>
 * This program is licensed under the GNU GPL, full terms in the LICENSE file
 * Chris Wopat - me@falz.net Jan 2006
 */

function poller_pdu_banks ($options) {
	
	$PDUMIB = ".1.3.6.1.4.1.318.1.1.12";
	$pduLoad_oid = $PDUMIB.".2.3.1.1.2";

        switch ($options["poller_name"]) {
	// for future expansion..
	//    case "voltage":  $oid = ($input)?$pduInputVoltage:$pduOutputVoltage; break;
	//    case "current":  $oid = ($input)?$pduInputCurrent:$pduOutputCurrent; break;
	    case "load"   :  $oid = ($input)?false:$pduLoad_oid; break;
	}
	
	if (!empty($oid)) { 
	    $oid .= ".".$options["index"];
	    $tempvalue = snmp_get($options["host_ip"], $options["ro_community"], $oid);
	    // values returned are in 1/10th, so "27" is "2.7". Let's fix this up..
	    $value = $tempvalue * .1;
	} 

	return $value;
}

?>
