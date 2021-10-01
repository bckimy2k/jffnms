<?php
/* This file is part of JFFNMS
 * Copyright (C) <2005> Javier Szyszlican <javier@szysz.com>
 * This program is licensed under the GNU GPL, full terms in the LICENSE file
 * Chris Wopat - me@falz.net Jan 2006
 */

    function discovery_pdu ($ip,$rocommunity,$hostid,$param) { 

	$pdu = array();

	$PDUMIB = ".1.3.6.1.4.1.318.1.1.12";
	$pduIdentName_oid = $PDUMIB.".1.1.0";
	$pduModelNumber_oid = $PDUMIB.".1.5.0";
	$pduNumBanks_oid = $PDUMIB.".2.1.4.0";
	$pduLoadStatus_oid = $PDUMIB.".2.3.1.1.3";

	$load_status = array (1=>"load normal", 2=>"load low", 3=>"load near overload", 4=>"load overloaded");

	if (!empty($ip) && !empty($rocommunity)) {
	    
	    $pduNumBanks = snmp_get($ip, $rocommunity, $pduNumBanks_oid);
	    // total number of banks + 1 will be index for the "total" status
	    $pduTotalBanks = $pduNumBanks +1;
    	    $pduIdentName = snmp_get($ip, $rocommunity, $pduIdentName_oid);
    	    $pduModelNumber = snmp_get($ip, $rocommunity, $pduModelNumber_oid);

	    if ($pduIdentName!==false && $pduTotalBanks!==false) {

		$pduTotalStatus = $pduLoadStatus_oid . "." . $pduTotalBanks;
		$pduLoadStatus = snmp_get($ip, $rocommunity, $pduTotalStatus);

		if (!empty($pduIdentName) && !empty($pduLoadStatus))
		    $pdu[1] = array(
			    "interface"=>"PDU",
			    "ident"=>$pduIdentName,
			    "description"=>$pduIdentName." ".$pduModelNumber,
			    "banks"=>$pduNumBanks,
			    "admin"=>"ok",
			    "oper"=>$load_status[$pduLoadStatus]
			);
	    }
	}
        return $pdu;
    } 
?>
