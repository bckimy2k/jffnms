<?php
/* This file is part of JFFNMS
 * Copyright (C) <2002-2005> Javier Szyszlican <javier@szysz.com>
 * This program is licensed under the GNU GPL, full terms in the LICENSE file
 */
    //Temp store the results so other backend or poller can use them (probably the rrd backend)

function backend_multi_buffer($options,$result)
{
  global $poller_buffer;
  
  $var_names = explode (',',$options['poller_name']);
  $values = explode ('|',$result);
  
  foreach ($var_names as $key=>$name)
  {
    $buffer_name = $name.'-'.$options['interface_id'];
    if (!array_key_exists($buffer_name, $poller_buffer) && !empty($values[$key]))
      $poller_buffer[$buffer_name] = $values[$key];
  }
  return count($poller_buffer);
}

?>
