<?php
/* This file is part of JFFNMS
 * Copyright (C) <2002-2005> Javier Szyszlican <javier@szysz.com>
 * This program is licensed under the GNU GPL, full terms in the LICENSE file
 */
//Temp store the results so other backend or poller can use them (probably the rrd backend)

function backend_buffer($options,$result)
{
  global $poller_buffer;

  if (!empty($option['backend_parameters']))
    $buffer_name = $options['backend_parameters'];
  else
    $buffer_name = $options['poller_name'].'-'.$options['interface_id'];
        
  if (!array_key_exists($buffer_name, $poller_buffer))
    $poller_buffer[$buffer_name] = $result;
  return count($poller_buffer);
}

?>
