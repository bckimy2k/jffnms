<?php
/* IBM Blade Servers. This file is part of JFFNMS
 * Copyright (C) <2005> David LIMA <dlima@fr.scc.com>
 * This program is licensed under the GNU GPL, full terms in the LICENSE file
 */

function poller_ibm_ds_storage ($options)
{
    $smcli_bin=exec('which SMcli');
    $sudo_bin=exec('which sudo');
    $device=$options['poller_parameters'];
    $ip=$options['host_ip'];
    //debug($options);

    switch($device) {
	case 'controler':
		$smcli_cmd = "-S -e -c \"show storagesubsystem healthstatus;\"";
		$command = "$sudo_bin $smcli_bin $ip $smcli_cmd";
		//$ip=str_replace(".","\.",$ip);
		$pattern = 'Storage Subsystem health status = optimal.';
		exec($command,$output,$retval);
		//debug($retval);
		//debug($output);
		//FIXME add more checks to return status like: poller error
		$output = implode(",", $output);
		if (eregi($pattern,$output)) {
		   $status=up;
		   } else {
		   	//str_replace("The following failures have been found:,"," ",$output);
			$status="down| $output";
			}	
		return $status;
	     break;
	case 'arrays':
		//FIXME: interface with: name, 
		$smcli_cmd = "-e -c \"show storagesubsystem summary;\"";
		$command = "$smcli_bin $ip $ip2 $smcli_cmd";
		$pattern = '/Number of arrays:\s+(\d+)/';
		echo "Device type is $device\n";
		exec($command,$output);
		//print_r($output);
		$output2=join("",$output);
		//var_dump($output2);
		preg_match($pattern,join("",$output),$matches);
		$nb_arrays=$matches[1];
		echo "Number of arrays: "."$nb_arrays"."\n";
		break;

	case 'drives':
		if ($smcli_bin === FALSE) return FALSE;
		$smcli_cmd = "-e -c \"show allDrives summary;\"";
		$command = "$smcli_bin $ip $ip2 $smcli_cmd";
		$pattern = '/^\s{6}(\d+),\s+(\d+)\s+([aA-zZ]+)/';
		$keyz=0;
		exec($command,$output);
		//var_dump($output);
		foreach ($output as $drivez) {
			if (preg_match($pattern,$drivez,$drive) == TRUE) {
			$keyz++;
			$interfaces[$index]=array (
				'interfaces' => "Drive ".$keyz,
				'tray' => $drive[1],
				'slot' => $drive[2],
				'oper' => $drive[3]=="Optimal"?"up":"down",
				'drv_idx' => $keyz,
				);
			//var_dump($interfaces);
			}
		}
		break;
    case 'logdrives':
		if ($smcli_bin === FALSE) return FALSE;
		$smcli_cmd = "-e -c \"show allLogicalDrives;\"";
		$pattern = '/(\w+)\s+(\w+)\s+([0-9]*\,?[0-9]*\.?[0-9]*)\s[MKG]B\s+(\d+)\s+(\d+)$/';
		$keyz=0;
		$command = "LANG=US $smcli_bin $ip $ip2 $smcli_cmd";
	exec($command,$output);
	//print_r($output);
	foreach ($output as $line) {
	    if (preg_match($pattern,$line,$logdrive) == TRUE) {
		//var_dump($logdrive);
			//fputs($fp,$keyz.";".$logdrive[1].";".$logdrive[2].";".$logdrive[3].";".$logdrive[4]."\n");
				$interfaces[$index] = array (
					'interface' => $logdrive[1],
					'logdrive_idx' => $keyz,
					'oper' => $logdrive[2]=="Optimal"?"up":"down",
					'capacity' => $logdrive[3],
					'array' => $logdrive[4],
					);
				//$index++;
				$keyz++;
				//print_r($interfaces);
	    }
	}
		//fclose($fp);
		break;
	case 'drivesbad':
		//FIXME: interface with: name, 
		$smcli_cmd = "-e -c \"show allDrives summary;\"";
		$command = "$smcli_bin $ip $ip2 $smcli_cmd";
    	$pattern = '/(\d+),\s+(\d+)\s+(\w+)\s+/';
	    exec($command,$output);
		//print_r($output);
		foreach ($output as $lines) {
			//echo "Traitement de ligne: ".$lines;
			preg_match_all($pattern,$lines,$matches);
			foreach ($matches as $index => $value) {
				$interfaces[$index] = array (
		   		'interface' => "Drive ".$index,
		    		'tray' => $value[1],
		    		'slot' => $value[2],
		    		'oper' => $value[3],
				);
			}
		}
		break;

	}
    return $interfaces ;
}
?>
