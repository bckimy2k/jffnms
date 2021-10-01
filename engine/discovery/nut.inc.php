<?php
/* NUT: Network UPS Tool Discovery plugin
 * This file is part of JFFNMS
 * Copyright (C) <2008,2009> Craig Small <csmall@enc.com.au>
 * This program is licensed under the GNU GPL, full terms in the LICENSE file
 */

function discovery_nut ($ip, $rocommunity, $hostid, $param) {
	$open_timeout = 10;

	$nut_info = array();

	if ( ($skt = @fsockopen($ip, 3493, $errno, $errstr, $open_timeout)) == FALSE) {
		return $nut_info;
	}
	$ups_num=1;
	fputs($skt,"LIST UPS\nLOGOUT\n");
	while(!feof($skt)) {
		$line = fgets($skt, 1024);
		if (preg_match('/^UPS (\S+) \"(.+)\"\s*$/', $line, $regs)) {
		 $nut_info[$ups_num] = array(
			'interface' => $regs[1],
			'description' => $regs[2],
			'admin' => 'up',
			'oper' => 'unknown'
			);
		}
	 }
	foreach($nut_info as $index => $fields)
	{
	  $line = '';
	  if ( ($skt = @fsockopen($ip, 3493, $errno, $errstr, $open_timeout)) == FALSE) {
	  return $nut_info;
	}
	  fputs($skt, "GET VAR $fields[interface] ups.status\nLOGOUT\n");
	  $line = fgets($skt);
	  fclose($skt);
	  if (preg_match('/^VAR \S+ ups.status \"([^"]+)\"/', $line, $regs)) {
		$nut_info["$index"]['oper'] = $regs[1];
	  }
	}
	//var_dump($nut_info);
	return $nut_info;
}
?>
