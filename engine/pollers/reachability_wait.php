<?php
/* This file is part of JFFNMS
 * Copyright (C) 2002-2011 JFFNMS Authors
 * This program is licensed under the GNU GPL, full terms in the LICENSE file
 */
function poller_reachability_wait ($options)
{
  global $Config;
  global $poller_buffer;

  $temp_path = $Config->get('engine_temp_path');
  $buffer_name ='ping-'.$options['interface_id'];
  if (!array_key_exists($buffer_name, $poller_buffer))
      return 'error';
  $uniq = $poller_buffer[$buffer_name];

  $filename = "$temp_path/$uniq.log";

  if (file_exists($filename))
  {
    for($i=0; $i < 100 ; $i++)
    {
      clearstatcache(TRUE, $filename);
      if ( ($new_filesize = filesize($filename)) === FALSE)
        return 'error';
      if ($new_filesize > 40)
        return 'ok';
      sleep(1);
    }
  }
  return 'error';
}
?>
