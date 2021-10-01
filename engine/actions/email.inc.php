<?php
/* This file is part of JFFNMS
 * Copyright (C) <2002-2005> Javier Szyszlican <javier@szysz.com>
 * This program is licensed under the GNU GPL, full terms in the LICENSE file
 */
    //Send eMail

function action_email ($data)
{
  global $Config;

  $body = array();

  if (!function_exists('mail'))
  {
    logger('Mail function does not exists.\n');
    return 0;
  }

  $from = $data['parameters']['from'];
  $to = $data['parameters']['to'];
  $subject = $data['parameters']['subject'];
  $short = FALSE;
  if (array_key_exists('short', $data['parameters']))
  {
    $short = TRUE;
    $signature = '';
  } else {
    if (array_key_exists('fullname', $data['user']))
      $fullname = $data['user']['fullname'];
    else
      $fullname = '';
    $body[] = "Hello $fullname:\n";
    $signature =  "---------------------------------------------------------------------\nJFFNMS - Just for Fun Network Management System\n";
  }

  if (array_key_exists('alarm', $data))
  {
    $alarm = $data['alarm'];
    $body[] = "Alarm Time:\t$alarm[date_start]" .
      ($alarm['alarm_state'] == ALARM_UP?" To $alarm[date_stop]":'')."\n".
      "Alarm Type:\t$alarm[type_description] $alarm[state_description] ".
      ($short?"":"\n");
  }
  
  if (array_key_exists('interface', $data))
  {
    $interface = $data['interface'];
    $body[] = "Interface:\t $interface[type_description] $interface[host_name]".
      "$interface[zone_shortname] $interface[interface] ".
      "$interface[client_name] $interface[description]\n";
  }

  if (array_key_exists('event', $data) && !$short)
  {
    $events_data = $data['event'];
    $Events = new JffnmsEvents();

    foreach ($events_data as $event)
      $body[] = "Event:\t $event[date] $event[type_description] $event[host_name] ".
         "$event[zone]\n".
         "Event:\t". $Events->replace_vars($event,$event['text'])."\n";
  }
          
  if (array_key_exists('comment', $data['parameters']) && !$short)
    $body[] = "\nComment: ".$data['parameters']['comment']."\n";

  $body_text = join($body,"\n")."\n$signature";
  if ($short)
    $body_text = str_replace("\t",' ',$body_text);
  
  $headers = 
    'From: JFFNMS@'.$Config->get('jffnms_site')." <$from>\r\n".
    'X-Mailer: JFFNMS '.JFFNMS_VERSION." ( http://www.jffnms.org )\r\n";
  
  if ((strpos($to,'@') > 1) && $subject && $body && $headers)
    return mail($to,$subject,$body_text,$headers,"-f$from");

  return 0;
}
?>
