<?php
/* This file is part of JFFNMS
 * Copyright (C) <2002-2011> JFFNMS authors
 * This program is licensed under the GNU GPL, full terms in the LICENSE file
 */
function poller_tcp_status ($options)
{
  if ( ($host_info = resolve_host($options['host_ip'])) === FALSE)
    return 'error| |0';
  list ($af,$ip,$hport) = $host_info;
  if ($af == 6)
      $ip="tcp://[$ip]";

  //Get the Port using only the first numeric characters.
  $i = 0;
  $port = '';
  while (is_numeric(substr($options['port'],$i,1))) 
	  $port .= substr($options['port'],$i++,1);

  $time_tcp = time_msec();
  $fp = @fsockopen ($ip, $port, $errno, $errstr, 10); //try to connect
    
  $time_tcp = time_msec_diff($time_tcp); //save the delay of connection in milliseconds

  $time_tcp_secs = $time_tcp / 1000; //time tcp less the normal delay in seconds

  if (!$fp)
  {
    if (!empty($errstr))
      logger ("$errstr ($errno):");
	  $status = 'closed';
    $data = '';
  } else {
	  socket_set_blocking($fp,FALSE); //try to read for 1 second
	  sleep(3);
	  $data = preg_replace('/[^ -~]/', '', addslashes( trim (fgets ($fp,100))));
	  fclose ($fp);
	  $status = "open";
  }
  return "$status|$data|$time_tcp_secs";
}
?>
