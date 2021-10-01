<?php
/* IBM DS Storage. This file is part of JFFNMS
 * Copyright (C) <2008> David LIMA <dlima@fr.scc.com>
 * This program is licensed under the GNU GPL, full terms in the LICENSE file
 */

function discovery_ibm_ds_storage ($ip, $hostname, $rwcommunity, $param )
{
  if ( $rwcommunity == 'DS4000' )
  {
    $sudo_bin=exec('which sudo',$retval1);
    if ($retval1 == '127')
    {
      logger('Sudo is not installed, please read the documentation');
      return FALSE ;
    }
    
    $smcli_bin=exec('which SMcli',$retval1);
    if ($retval1 == '127')
    {
     logger('SMCli is not installed, please read the documentation');
     return FALSE ;
    }
    $device=$param;
    $interfaces=array();

    switch($device)
    {
    case 'storagesubsystem':
      $smcli_cmd = '-d -v';
      $command = "$sudo_bin $smcli_bin $smcli_cmd";
      //$ip=str_replace(".","\.",$ip);
      $pattern = '/(\S+).*'.$ip.'.*\s(\S+)$/';
      exec($command,$output,$retval);
      //debug($retval);
      //debug($output);
      if ($retval == '4') return FALSE;
      //FIXME add more checks to return status like: poller error
      //debug($retval);
      $i="0";
      $index = 1;
      foreach ($output as $lines)
      {
        if (preg_match($pattern,$lines,$parts) == TRUE)
        {
          $interfaces[$index] = array (
            'interface' => "IBM Storage $parts[1]",
            'oper' => $parts[2]=="Optimal"?"up":"$parts[2]",
          );
          $index++;
        }
      }
      break;
    }
    return $interfaces ;
  }
  return FALSE;
}
?>
