<?php
/* This file is part of JFFNMS
 * Copyright (C) <2002-2005> Javier Szyszlican <javier@szysz.com>
 * This program is licensed under the GNU GPL, full terms in the LICENSE file
 */

function consolidate_alarms(&$Alarms, &$Triggers)
{
  $down_alarms = $Alarms->alarms_list(0, array('triggered'=>0, 'alarm_state'=> ALARM_DOWN));
  logger('Active Alarms for Triggers: '.count($down_alarms)."\n");
    
  foreach ( $down_alarms as $alarm )
  {
    logger( "A $alarm[id]:= @$alarm[date_start] - state: $alarm[alarm_state]".
      " ($alarm[state_description]) - int: $alarm[interface]".
      " - type: $alarm[type]\n");
    $Triggers->analyze('alarm', $alarm);
  }
}


?>
