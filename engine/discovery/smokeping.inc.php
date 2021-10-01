<?php
/* Smokeping integration. This file is part of JFFNMS
 * Copyright (C) <2003> Craig Small <csmall@enc.com.au> 
 * This program is licensed under the GNU GPL, full terms in the LICENSE file
 */

function scan_dir($parent, &$ifaces)
{
  if (!is_dir($parent))
    return;
  if ($dir = opendir($parent))
  {
    while (($file = readdir($dir)) !== false)
    {
      if ($file == '.' || $file == '..')
        continue;
      if (is_dir($parent.'/'.$file))
        scan_dir($parent.'/'.$file, $ifaces);
      elseif (preg_match('/\.rrd$/', $file))
          $ifaces[] = $parent.'/'.$file;
    }
   }
}


function discovery_smokeping ($ip, $rocommunity,$hostid,$param)
{
  $topdir = $rocommunity;
  $ifaces = array();
  scan_dir($topdir, $ifaces);

  $smokeping_interfaces = array();
  $ifnum=0;
  foreach ($ifaces as $key => $ifname)
  {
    if (preg_match('%^'.$topdir.'/(.*)\.rrd$%', $ifname, $regs))
    {
      $ifnum++;    
      $ifname = $regs[1];
      $smokeping_interfaces[$ifnum] = array(
        'interface' => trim($ifname),
        'admin' => 'up',
        'oper' => 'up'
      );
     }
   }
   return $smokeping_interfaces;
}

?>
