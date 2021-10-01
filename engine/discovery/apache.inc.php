<?php
/* This file is part of JFFNMS
 * Copyright (C) <2002-2005> Javier Szyszlican <javier@szysz.com>
 * This program is licensed under the GNU GPL, full terms in the LICENSE file
 */
    //Apache Discovery

    function discovery_apache ($ip, $rocommunity, $hostid, $param) {
        $open_timeout = 10;

	list($host_ip) = explode(":",$ip); //remove :port from IP
	$ip_port = $host_ip.":80";
	$apache_info = array();

	if ( ($skt = @fsockopen($host_ip, 80, $errno, $errstr, $open_timeout)) == FALSE) {
	    return $apache_info;
	}
	fputs($skt, "GET /server-status?auto HTTP/1.1\r\nHost: ".$host_ip."\r\n\r\n");

        $reply = '';
	while(!feof($skt) && (strlen($reply) < 100)) {
	    $reply .= fgets($skt, 1024);
	}
	fclose($skt);
	if (preg_match('/^HTTP\/1.1 200 OK/', $reply)) {
    	    $apache_info[$ip_port]["interface"] = "Apache Information";
    	    $apache_info[$ip_port]["admin"] = "ok";  //to show
    	    $apache_info[$ip_port]["oper"] = "up";   //to be added by the AD
	}
 
	return $apache_info;
    }
?>
