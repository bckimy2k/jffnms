<?php
/* This file is part of JFFNMS
 * Copyright (C) <2005> Javier Szyszlican <javier@szysz.com>
 * This program is licensed under the GNU GPL, full terms in the LICENSE file
 */

    function discovery_ups ($ip,$rocommunity,$hostid,$param) { 
	
	$ups = array();
	$UPSMIBS= array(
	    "ups" => ".1.3.6.1.2.1.33",
	    "ups_mitsu" => ".1.3.6.1.4.1.13891.101"
	);

	foreach($UPSMIBS as $key => $UPSMIB) {

	    // any common variables between the UPS's
	    $battery_status = array (1=>"battery unknown", 2=>"battery normal", 3=>"battery low", 4=>"battery depleted");

	    switch($key) {

	    case "ups":
		$upsIdentName_oid = $UPSMIB.".1.1.5.0"; //UPS-MIB::upsIdentName.0
		$upsBatteryStatus_oid = $UPSMIB.".1.2.1.0"; //UPS-MIB::upsBatteryStatus.0
		break;
	    case "ups_mitsu":
		$upsIdentName_oid = $UPSMIB.".1.5.0";
		$upsIdentName = snmp_get($ip, $rocommunity, $upsIdentName_oid);
		$upsBatteryStatus_oid = $UPSMIB.".2.1.0";
		break;
	    }

	    if (!empty($ip) && !empty($rocommunity)) {

		$upsIdentName = snmp_get($ip, $rocommunity, $upsIdentName_oid);
	
		if ($upsIdentName!==false) {

		    $upsBatteryStatus = snmp_get($ip, $rocommunity, $upsBatteryStatus_oid);

		    if (!empty($upsIdentName) && !empty($upsBatteryStatus))
			$ups[1] = array(
				"interface"=>"UPS",
				"ident"=>$upsIdentName,
				"upstype"=>$key,
				"admin"=>"ok",
				"oper"=>$battery_status[$upsBatteryStatus]
			);
		    }
		}
	    }

	return $ups;

    } 
?>
