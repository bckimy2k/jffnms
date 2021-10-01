<?php
/* NUT: Network UPS Tool Status poller 
 * This file is part of JFFNMS
 * Copyright (C) <2008,2009> Craig Small <csmall@enc.com.au>
 * This program is licensed under the GNU GPL, full terms in the LICENSE file
 */

function poller_nut_status ($options)
{
	$open_timeout = 10;

	$ip = $options['host_ip'];
	$upsname = $options['interface'];

	if ( ($skt = @fsockopen($ip, 3493, $errno, $errstr, $open_timeout)) == FALSE) {
		return -1;
	}
	$battery_status = 'battery normal';
	$output_status = 'unknown';
	fputs($skt, "LIST VAR $upsname\nLOGOUT\n");
	while(!feof($skt)) {
		$line = fgets($skt, 1024);
	if (preg_match('/^VAR \S+\s+([a-z.]+)\s+\"([^"]+)\"/', $line, $regs)) {
		switch($regs[1]) {
			case 'ups.status':
		  if (preg_match('/^(.*\s+|)(ONLINE|OL)(\s+.*|)$/', $regs[2])) {
			$output_status = 'on line';
		  }
		  if (preg_match('/^(.*\s+|)OB(\s+.*|)$/', $regs[2])) {
			$output_status = 'on battery';
		  }
		  if (preg_match('/^(LB|RB)$/', $regs[2])) {
			$battery_state = 'battery low';
		  }
		  break;
			
			case 'input.voltage':
			$in_voltage = $regs[2];
			break;
		case 'output.voltage':
			$out_voltage = $regs[2];
			break;
		case 'ups.load':
			$load = $regs[2];
			break;
		case 'ups.temperature':
			$temperature = $regs[2];
			break;
		case 'battery.charge':
			$capacity = $regs[2];
			break;
			} /* switch */
	} /*preg match */
	} /*while */
	fclose($skt);
	return "$in_voltage|$out_voltage|$load|$temperature|$capacity|$battery_status|$output_status";
}
