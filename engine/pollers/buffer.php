<?php
/* This file is part of JFFNMS
 * Copyright (C) <2002-2005> Javier Szyszlican <javier@szysz.com>
 * This program is licensed under the GNU GPL, full terms in the LICENSE file
 */
    // Get Values from the Buffer Temp storage

function poller_buffer ($options)
{
  global $poller_buffer;

  $buffer_names = explode (',',$options['poller_name']);
  
  $values = array();
  foreach ($buffer_names as $buffer_name)
  {
    $buffer_fullname = $buffer_name.'-'.$options['interface_id'];
    if (array_key_exists($buffer_fullname, $poller_buffer))
      $values[] = $poller_buffer[$buffer_fullname];
    else
      $values[] = '';
  }
  return join('|',$values);
}
?>
