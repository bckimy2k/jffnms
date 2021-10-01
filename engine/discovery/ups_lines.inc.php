<?php
/* This file is part of JFFNMS
 * Copyright (C) <2005> Javier Szyszlican <javier@szysz.com>
 * This program is licensed under the GNU GPL, full terms in the LICENSE file
 */

    function discovery_ups_lines ($ip,$rocommunity,$hostid,$param) { 
	
	$ups_lines = array();
	$UPSMIBS= array(
		"ups" => ".1.3.6.1.2.1.33",
		"ups_mitsu" => ".1.3.6.1.4.1.13891.101"
	);

        foreach($UPSMIBS as $key => $UPSMIB) {

	    switch($key) {

	    case "ups":
		$upsInputs  = $UPSMIB.".1.3.3.1"; //UPS-MIB::upsInputEntry
		$upsOutputs = $UPSMIB.".1.4.4.1"; //UPS-MIB::upsOutputEntry
		$addToIndex = 0;
		break;
	    case "ups_mitsu":
		$upsInputs  = $UPSMIB.".3.3.1";
		$upsOutputs = $UPSMIB.".4.4.1";
		$addToIndex = 1;
		break;
	    }

	    $upsInputsIndex = $upsInputs.".1";
	    $upsOutputsIndex = $upsOutputs.".1";

	
	    if (!empty($ip) && !empty($rocommunity)) {

		$upsInputLines  = snmp_walk($ip, $rocommunity, $upsInputsIndex);
		$upsOutputLines = snmp_walk($ip, $rocommunity, $upsOutputsIndex);

		if (is_array($upsInputLines) && is_array($upsOutputLines)) {

		    foreach ($upsInputLines as $index) 
			$ups_lines[10+$index] = array(
				"interface"=>"Input Line ".$index,
				"line_type"=>"INPUT",
				"line_index"=>$index + $addToIndex,
				"upstype"=>$key,
				"admin"=>"up", "oper"=>"up"
			);
			
		    foreach ($upsOutputLines as $index) 
			$ups_lines[20+$index] = array(
				"interface"=>"Output Line ".$index,
				"line_type"=>"OUTPUT",
				"line_index"=>$index + $addToIndex,
				"upstype"=>$key,
				"admin"=>"up", "oper"=>"up"
			);
		}
	    }
	}
        return $ups_lines;
    } 
?>
