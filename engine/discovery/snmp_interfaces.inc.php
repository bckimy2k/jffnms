<?php
/* This file is part of JFFNMS
 * Copyright (C) <2002-2005> Javier Szyszlican <javier@szysz.com>
 * This program is licensed under the GNU GPL, full terms in the LICENSE file
 */
function discovery_snmp_interfaces($ip,$rocommunity,$hostid,$param)
{
  $ifAux = array();
  $snmp_interfaces = array();

  list($host_ip) = explode(":",$ip);     //remove :port from IP
  $host_ip = gethostbyname ($host_ip);  //try to resolve it, this is just to check there is an IP

  if ((ip2long($host_ip)!==-1) && !empty($hostid) && !empty($rocommunity))  
    $ifIndex = snmp_walk($ip, $rocommunity, '.1.3.6.1.2.1.2.2.1.1');

  if (is_array($ifIndex) && (count($ifIndex) > 0))
  {
    // Exceptions
    include_once(jffnms_shared('catos'));  //Load the CatOS Functions
    include_once(jffnms_shared('webos'));  //Load the WebOS Functions

    if (is_catos($ip, $rocommunity))   // Check if its CatOS
      list ($ifDescr_oid, $ifAlias) = catos_info($ip, $rocommunity, $ifIndex); //Get the CatOS Information
    elseif (is_webos($ip, $rocommunity))   // Check if its WebOS
      list($ifDescr,$ifAlias) = webos_info($ip, $rocommunity, $ifIndex);
    else           //Normal IF-MIB oids
    {
      $ifDescr_oid = '.1.3.6.1.2.1.2.2.1.2';
      $ifAlias_oid = '.1.3.6.1.2.1.31.1.1.1.18';
    }

    // Get all the data via SNMP  
    if (isset($ifDescr_oid))  $ifDescr = snmp_walk($ip,$rocommunity,$ifDescr_oid);
    if (isset($ifAlias_oid))  $ifAlias = snmp_walk($ip,$rocommunity,$ifAlias_oid);
    
    $ifAdminStatusValue   = snmp_walk($ip,$rocommunity,'.1.3.6.1.2.1.2.2.1.7');
    $ifOperStatus   = snmp_walk($ip,$rocommunity,'.1.3.6.1.2.1.2.2.1.8');
  
    $ifSpeed   = snmp_walk($ip,$rocommunity,'.1.3.6.1.2.1.2.2.1.5');
  
    $ipAddEntIP   = snmp_walk($ip,$rocommunity,'.1.3.6.1.2.1.4.20.1.1');
    $ipAddifIndex  = snmp_walk($ip,$rocommunity,'.1.3.6.1.2.1.4.20.1.2');
    $ipAddMask   = snmp_walk($ip,$rocommunity,'.1.3.6.1.2.1.4.20.1.3');
    
    if (!is_array($ipAddifIndex)) $ipAddifIndex = array();

    for ($i=0; $i < count($ifIndex) ; $i++)
      if ($ifIndex[$i])
      {
        $ipPos = array_search($ifIndex[$i],$ipAddifIndex); //Find FIRST the pos where this ifIndex has an IP
        if ($ipPos !== false)    //we Found some IPs for this interface index
        {
          $ifAddr[$i] = $ipAddEntIP[$ipPos]; //Get the IP from that Pos
          $ifAddrMask[$i] = $ipAddMask[$ipPos]; //Get the Mask from that Pos
          if (strpos($ifAddr[$i],".") > 0) //IP Address for peer
          {
            $aux = explode(".",$ifAddr[$i]);
            if ($aux[3]%2) 
              $aux[3]++; //if it's even, then peer is next one
            else 
              if ($aux[3]>0) 
                $aux[3]--; //if its not and grater than 0 peer is previous one
             $peerAddr[$i] = implode('.',$aux);
          }
        }
        if (array_key_exists($i, $ifSpeed))
          $ifspeed = round($ifSpeed[$i]/1000)*1000;
        else
          $ifspeed = 128000; // default is 128kbps

        $admin = parse_interface_status($ifAdminStatusValue[$i]);
        $oper = parse_interface_status($ifOperStatus[$i]);
        $descr = fix_interface_description($ifDescr[$i]);
        $alias = array_item_blank($ifAlias, $i);
        $alias = str_replace(array("\"", "'"),array('',''), $alias);

        $new_interface = array(
          'address' => array_item_blank($ifAddr, $i),
          'mask'    => array_item_blank($ifAddrMask, $i),
          'peer'    => array_item_blank($peerAddr, $i),
          'description' => $alias,
          'interface' => $descr,
          'bandwidthin' => $ifspeed,
          'bandwidthout' => $ifspeed,
          'admin' => $admin,
          'oper' => $oper,

          );
        // Juniper Fixes
        // Because Juniper Creates one new interface per L3 stack on each L2 interface
        // So we have to merge them
        //if the before-end char is . (like in t1-1/0/1:6.0) and the description is not set
        if (preg_match('/^(.+)\..$/', $new_interface['interface'], $regs)
          && (empty($new_interface['description'])))
        {
          $int = $regs[1];
          if (!empty($interface_names[$int])) // if it is already loaded in list
          {
            $old_id = $interface_names[$int];
            $aux2 = $snmp_interfaces[$old_id];
            $aux2['address'] = $new_interface['address'];
            $aux2['peer'] = $new_interface['peer'];
            $new_interface = $aux2;
            unset ($snmp_interfaces[$old_id]);
          }
        } else
          $interface_names[$new_interface['interface']] = $ifIndex[$i];
        //check for the interface name, if its ok, add the interface to the list
        if (!empty($new_interface['interface']))
          $snmp_interfaces[$ifIndex[$i]]= $new_interface;
      }
  }//is_array ifindex
  return $snmp_interfaces;
}


function fix_interface_description($ifdescr)
{
  global $Config;
  //FIXME This is only for cisco to discard Atm9/1/0.2-aal5 layer, and FastEthernet4/0/0.1-ISL vLAN s after the dash
  if (preg_match('/^(.+)-(?:aal5|ISL|802\.1Q)/', $ifdescr, $regs))
    $ifdescr = $regs[1];

  if ($Config->get('os_type') == 'windows')
    $ifdescr = str_replace("\"", '', $ifdescr);
  $ifdescr = substr(snmp_hex_to_string($ifdescr),0,30); 
  $ifdescr = str_replace("'", '', $ifdescr);
  return $ifdescr;
}

?>
