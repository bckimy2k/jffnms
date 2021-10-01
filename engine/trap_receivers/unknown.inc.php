<?php
/* This file is part of JFFNMS
 * Copyright (C) <2005> Javier Szyszlican <javier@szysz.com>
 * This program is licensed under the GNU GPL, full terms in the LICENSE file
 */

function trap_receiver_unknown ($params)
{
  $trap_oid = $params['trap']['trap_oid'];
  $trap_oid = preg_replace('/^\.1\.3\.6\.1\.4\.1\.(\S+)/','Enterprises.$1', $trap_oid);
    
  $varbinds = array();
  foreach($params['trap_vars_oid'] as $id => $value)
    $varbinds[] = "$id=$value";
      
  $res = array(
    'info' => "Trap OID: $trap_oid Values: ".join(', ', $varbinds),
    'date' => date('Y-m-d H:i:s', $params['trap']['date']),
    'referer'   => $params['trap']['id']
  );
        
  return array(true, $res);
    }
?>
