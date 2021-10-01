<?php
/* This file is part of JFFNMS
 * Copyright (C) <2002-2005> Javier Szyszlican <javier@szysz.com>
 * This program is licensed under the GNU GPL, full terms in the LICENSE file
 */
function poller_reachability_end ($options)
{
  global $poller_buffer;
  global $Config;

  $temp_path = $Config->get('engine_temp_path');
  $uniq = $poller_buffer['ping-'.$options['interface_id']]; //get file id from reachability_start
  $filename = "$temp_path/$uniq.log";

  $result = NULL;
  if (file_exists($filename))
	  $result = unlink ($filename);
  return $result;
}
?>
