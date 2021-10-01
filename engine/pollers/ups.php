<?php
/* This file is part of JFFNMS
 * Copyright (C) <2002-2005> Javier Szyszlican <javier@szysz.com>
 * This program is licensed under the GNU GPL, full terms in the LICENSE file
 */

function poller_ups ($options) {
	
	$upstype = $options["upstype"];

	switch($upstype) {

	case "ups":
	    $UPSMIB = ".1.3.6.1.2.1.33"; //SNMPv2-SMI::mib-2.33
	    $chargeRemaining = $UPSMIB.".1.2.4.0";
	    $minutesRemaining = $UPSMIB.".1.2.3.0";
	    $status = $UPSMIB.".1.2.1.0";
	    $temperature = $UPSMIB.".1.2.7.0";
	    break;

	case "ups_mitsu":
	    $UPSMIB = ".1.3.6.1.4.1.13891.101";
	    $chargeRemaining = $UPSMIB.".2.4.0";
	    $minutesRemaining = $UPSMIB.".2.3.0";
	    $status = $UPSMIB.".2.1.0";
	    $temperature = $UPSMIB.".2.7.0";
	    break;
	}

        switch ($options["poller_name"]) {
	    case "charge_remaining":  $value = poller_ups_getvalue($options, $chargeRemaining); break;
	    case "minutes_remaining":  $value = poller_ups_getvalue($options, $minutesRemaining); break;
	    case "temperature":  $value = poller_ups_getvalue($options, $temperature); break;
	    case "status":  $value = poller_ups_batterystatus($options, $status); break;
	}

	return $value;
}

function poller_ups_getvalue ($options, $oid) {

	if (!empty($oid)) { 
	    $value = snmp_get($options["host_ip"], $options["ro_community"], $oid);
	} 
	return $value;
}

function poller_ups_batterystatus ($options, $oid) {

	if (!empty($oid)) { 
	    $value = snmp_get($options["host_ip"], $options["ro_community"], $oid);
	} 
	$battery_status = array (1=>"battery unknown", 2=>"battery normal", 3=>"battery low", 4=>"battery depleted");
	return $battery_status[$value];
}


?>
