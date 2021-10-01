<?php
/* This file is part of JFFNMS
 * Copyright (C) <2002-2005> Javier Szyszlican <javier@szysz.com>
 * This program is licensed under the GNU GPL, full terms in the LICENSE file
 */

function poller_ups_line ($options) {

	$upstype = $options["upstype"];

	switch($upstype) {

	case "ups":
	    $UPSMIB = ".1.3.6.1.2.1.33"; //SNMPv2-SMI::mib-2.33
	    $upsInputs  = $UPSMIB.".1.3.3.1"; //UPS-MIB::upsInputEntry
	    $upsOutputs = $UPSMIB.".1.4.4.1"; //UPS-MIB::upsOutputEntry
	    $multiplier = 1;
	    break;

	case "ups_mitsu":
	    $UPSMIB = ".1.3.6.1.4.1.13891.101";
	    $upsInputs  = $UPSMIB.".3.3.1"; //Mitsubishi Diamondlink
	    $upsOutputs = $UPSMIB.".4.4.1"; //Mitsubishi Diamondlink
	    $multiplier = .1; //Mitsubishi returns voltage/current as 0.1 incriments
	    break;
	}

	$upsInputsIndex = $upsInputs.".1";
	$upsOutputsIndex = $upsOutputs.".1";

	$upsInputVoltage  = $upsInputs.".3";
	$upsOutputVoltage = $upsOutputs.".2";

	$upsInputCurrent  = $upsInputs.".4";
	$upsOutputCurrent = $upsOutputs.".3";

	$upsInputPower = $upsInputs.".5";
	$upsOutputPower = $upsOutputs.".4";

	$upsOutputLoad = $upsOutputs.".5";

	$input = ($options["line_type"]=="INPUT")?true:false;

	switch ($options["poller_name"]) {
	    case "voltage" :	$oid = ($input)?$upsInputVoltage:$upsOutputVoltage; 
				$value = poller_ups_line_multiplier($options, $multiplier, $oid); 
				break;
	    case "current" :	$oid = ($input)?$upsInputCurrent:$upsOutputCurrent;
				$value = poller_ups_line_multiplier($options, $multiplier, $oid); 
				break;
	    case "power"   :	if($upstype == "ups_mitsu") {	//only get wattage on Mitsubishi UPS
				    $oid = ($input)?$upsInputPower:$upsOutputPower;
				    $value = poller_ups_line_nomultiplier($options, $oid); 
				}
				break;
	    case "load"    :	$oid = ($input)?false:$upsOutputLoad;
				$value = poller_ups_line_nomultiplier($options, $oid); 
				break;
	}
	
	return $value;
}

function poller_ups_line_multiplier ($options, $multiplier, $oid) {

	if (!empty($oid)) { 
	    $oid .= ".".$options["line_index"];
	    $myvalue = snmp_get($options["host_ip"], $options["ro_community"], $oid);
	    $value = ($myvalue * $multiplier);
	}
	return $value;
}

function poller_ups_line_nomultiplier ($options, $oid) {

	if (!empty($oid)) { 
	    $oid .= ".".$options["line_index"];
	    $value = snmp_get($options["host_ip"], $options["ro_community"], $oid);
	} 
	return $value;
}

?>
