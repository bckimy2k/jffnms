<?php
/* This file is part of JFFNMS
 * Copyright (C) <2005> Javier Szyszlican <javier@szysz.com>
 * This program is licensed under the GNU GPL, full terms in the LICENSE file
 *
 * Chris Wopat - me@falz.net Jan 2006
 *    Nov 2007 - fixed to work with v3+ of APC firmware
 */

function discovery_pdu_banks ($ip,$rocommunity,$hostid,$param)
{ 
  $pdu_banks = array();

  $PDUMIB = '.1.3.6.1.4.1.318.1.1.12';
  $pduFirmwareVersion_oid = $PDUMIB . '.1.3.0';
  $pduBanksIndex_oid = $PDUMIB . '.2.3.1.1.1';

  $pduBankNearOverloadThresholdRoot_oid = $PDUMIB . '.2.4.1.1.3';
  $pduBankOverloadThresholdRoot_oid = $PDUMIB . '.2.4.1.1.4';

  // these two are only used in legacy (v2 firmware) to find the total thresholds  
  $pduTotalNearOverloadThresholdLegacy_oid = $PDUMIB.'.2.2.1.1.3.1';
  $pduTotalOverloadThresholdLegacy_oid = $PDUMIB.'.2.2.1.1.4.1';

  if (!empty($ip) && !empty($rocommunity))
  {
    $pduFirmwareVersion = snmp_get($ip, $rocommunity, $pduFirmwareVersion_oid);    // returns something like "v2.6.5" or "v3.3.3":
    $pduFirmwareVersionMajor = substr($pduFirmwareVersion, 1, 1);      // returns "2" or "3", etc:
    $pduNumBanks = snmp_walk($ip, $rocommunity, $pduBanksIndex_oid);      // total number of banks
    if (!is_array($pduNumBanks))
      return FALSE;

    if ($pduFirmwareVersionMajor > 2)
      $pduBankTotalID = count($pduNumBanks); // last bank = totals for v3 firmware
    else
      $pduBankTotalID = '1';   // first bank = totals for v2 firmware

    // grab last item, as it's the "special" total item
    foreach ($pduNumBanks as $index)
    {
      // special treatment if this is the "total" bank.
      // Mainly for legacy firmware support
      if ($index == $pduBankTotalID)
      {
        $interface = 'Bank Total';
        if ($pduFirmwareVersionMajor < 3)
        {
          $powerrating = snmp_get($ip, $rocommunity, $pduTotalNearOverloadThresholdLegacy_oid);
          $threshold = snmp_get($ip, $rocommunity, $pduTotalOverloadThresholdLegacy_oid);
        } else {
          $powerrating = snmp_get($ip, $rocommunity, $pduBankNearOverloadThresholdRoot_oid . "." . $index);
          $threshold = snmp_get($ip, $rocommunity, $pduBankOverloadThresholdRoot_oid . "." . $index);
        }
      } else {
        if ($pduFirmwareVersionMajor < 3)
        {
          $interface = 'Bank '. ($index -1);
          $pollindex = $index - 1;
        } else {
          $interface = 'Bank '. ($index);
          $pollindex = $index;
        }
        $powerrating = snmp_get($ip, $rocommunity, $pduBankNearOverloadThresholdRoot_oid . '.' . $pollindex);
        $threshold = snmp_get($ip, $rocommunity, $pduBankOverloadThresholdRoot_oid . '.' . $pollindex);
      }
      // create array out of information we've gathered
      $pdu_banks[$index] = array(
        'interface'=>$interface,
        'powerrating'=>$powerrating,
        'threshold'=>$threshold,
        'index'=>$index,
        'admin'=>'up',
        'oper'=>'up'
      );
    }
  }
  return $pdu_banks;
} 
?>
