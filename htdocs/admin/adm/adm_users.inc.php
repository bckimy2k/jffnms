<?php
/* This file is part of JFFNMS
 * Copyright (C) <2002-2005> Javier Szyszlican <javier@szysz.com>
 * This program is licensed under the GNU GPL, full terms in the LICENSE file
 */
function users_action_view_profile()
{
  adm_frame_menu_split('profiles');
}

function users_action_view_triggers()
{
  adm_frame_menu_split('triggers_users',1);
}

function users_action_update($api, $actionid)
{
  global $Sanitizer;

  $usern = $Sanitizer->get_string('usern');
  $router = $Sanitizer->get_string('router', '0');
  $old_passwd = $Sanitizer->get_string('old_passwd');
  $new_passwd = $Sanitizer->get_string('new_passwd');
  $fullname = $Sanitizer->get_string('fullname');

  if (profile('ADMIN_USERS') != 1)
  {
    $router = '';
    $usern = '';
  }
  if ($actionid == 'new' && !empty($usern))
      $actionid = $api->add($usern);

  $api->modify($actionid, $usern, $old_passwd, $new_passwd, $fullname, $router);
  $GLOBALS['action']='list';
}

function users_action_delete($api, $actionid)
{
  if (profile('ADMIN_USERS'))
    $api->del($actionid);
}

?>
