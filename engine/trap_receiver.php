<?php
/* PHP Database Abstrated Trap Receiver (from snmptrapd). This file is part of JFFNMS
 * Copyright (C) <2004> Aaron Daubman <daubman@ll.mit.edu>
 * Copyright (C) <2003>  Craig Small <csmall@small.dropbear.id.au>
 * Copyright (C) <2002> Javier Szyszlican <javier@szysz.com>
 * This program is licensed under the GNU GPL, full terms in the LICENSE file
 */

/* NOTES - To receive traps:
 * snmptrapd.conf:
 *   traphandle default cd /opt/jffnms/engine && php -q trap_receiver.php
 * command-line:
 * snmptrapd needs to have the -On option turned on to make numeric
 * OIDs, the rest doesn't matter too much.
 */

# Number of seconds to detect duplicate traps
define('TRAP_DUPLICATE_WINDOW', 5);

require_once('../conf/config.php');
$Config = new JffnmsConfig();
config_load_libs('basic', 0);
receive_trap();


function receive_trap()
{
  $hostname = trim(fgets(STDIN,4096)) or die('Could not get hostname');

  $line = trim(fgets(STDIN,4096)) or die('Could not get IP');
  $ipaddr = parse_ipaddr($line);


  $uptime = trim(fgets(STDIN,4096)) or die('Could not get uptime');

  $trapoid = fgets(STDIN,4096) or die("Could not get trapoid");
  $trapoid = preg_replace('/^\S+\s+(\S+)\s*$/', '$1', $trapoid);


  if (trap_duplicate($trapoid))
  {
    logger("T --:= @$uptime - ip: $ipaddr oid: $trapoid (duplicate)\n");
    return;
  }
  $varbinds = array();
  $varbind_text = '';
  $linenum=0;

  while($line=trim(fgets(STDIN,4096)))
  {
    $linenum++;
    if (preg_match ("/^(\S+)\s+=?\s?\"?([^\" \t]+)\"?$/", $line, $matches)) 
    {
      $varbinds[$matches[1]] = $matches[2];
      $varbind_text .= "     L$linenum: $matches[1] ==> $matches[2]\n";
    } else
      $varbind_text .= "     L$linenum: (no match) $line\n";
  }
  
  // Insert into database:
  $trap_id = db_insert ('traps',array('date'=>time(), 'ip'=>$ipaddr, 'trap_oid'=>$trapoid));
  $oidid = 1;
  foreach ($varbinds as $varbind_oid =>$varbind_value)
    db_insert('traps_varbinds', array(
      'trapid'=>$trap_id,
      'oidid'=>$oidid++,
      'trap_oid'=>$varbind_oid,
      'value'=>$varbind_value));
  db_close();
  logger("T $trap_id:= @$uptime - ip: $ipaddr oid: $trapoid\n$varbind_text\n");
}

function trap_duplicate ($current_oid)
{
  $trap_query = "SELECT id FROM traps WHERE trap_oid = '$current_oid' AND date > ".
    (time()-TRAP_DUPLICATE_WINDOW)." ORDER BY id desc";
  $trap_result = db_query($trap_query) or die ('Query failed - trap_duplicate() - '.db_error());

  if (db_num_rows($trap_result) > 0)
    return TRUE;
  return FALSE;
}

function parse_ipaddr($text)
{
  if (preg_match('/^UDP: \[(\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3})\]/', $text, $regs))
    return $regs[1];
  return gethostbyname($ipaddr);
}
?>
