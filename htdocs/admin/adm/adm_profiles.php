<?php
/* This file is part of JFFNMS
 * Copyright (C) <2002-2010> JFFNMS Authors
 * This program is licensed under the GNU GPL, full terms in the LICENSE file
 */
include ('../../auth.php');

{
  global $Sanitizer;
  $Profiles = new JffnmsProfiles();
  $ProfilesOptions = new JffnmsProfiles_options();
  $ProfilesValues = new JffnmsProfiles_values();

  adm_header('Profiles');
  $action = $Sanitizer->get_string('action','list');
  $actionid = $Sanitizer->get_int('actionid');
  $admin = profile('ADMIN_USERS');
  $filter = $Sanitizer->get_string('filter');
  $init = $Sanitizer->get_int('init');
  $profiles_options_id = $Sanitizer->get_int('profiles_options_id');
  $profiles_options_id_old = $Sanitizer->get_int('profiles_options_id_old');
  $profiles_values_value = $Sanitizer->get_string('profiles_values_value');
  $span = $Sanitizer->get_int('span');
  $userid = $Sanitizer->get_int('userid');
  $editid=0;
    
  if ($admin==false)
    $user_filter = array(array('auth.id','=',$_SESSION['auth_user_id']));
  else
    $user_filter = array();


  if ($action=='update') {
      $new_option_id = $Sanitizer->get_int('profiles_options_id');
      $old_option_id = $Sanitizer->get_int('profiles_options_id_old');
      $new_value = $Sanitizer->get_string('profiles_values_value');
      if (!$ProfilesOptions->get_user($userid,$new_option_id)) {
          if ($old_option_id == 1) {
              $ProfilesOptions->del($userid,$old_option_id); //delete old one
              $Profiles->add_user($userid,$new_option_id); //add it
          }
      } else  //the option already exists, modify it
          $ProfilesValues->modify($userid,$new_option_id,$new_value);
      $action='list';
  }

  if (($action=='add') && ($admin))
  {
    if (!$filter) $filter =1; 

    $editid = $Profiles->add_user($filter,1);
    $action = 'edit';
  }

  if (($action=='delete') && ($admin))
  {
    $Profiles->del($actionid);
    $action = 'list';
  }
    
  if ($action=='edit') 
    $editid = $actionid;

  $cant = $Profiles->get($filter, $user_filter);
    
  echo 
    adm_table_header('Profiles', $init, $span, 3, $cant, 'admin_profiles', true).
        tag('tr','','header').
        td ('Action', 'field', 'action').
  td ('Option', 'field').
  td ('Value', 'field').
  tag_close('tr').
  tag('tbody');

  $Profiles->slice($init,$span);

  $row=0;
  while ($rec = $Profiles->fetch())
  {
    echo tr_open("row_".$rec["id"],(($editid==$rec["id"])?"editing":((($row++%2)!=0)?"odd":"")));
  
    if (($editid==$rec['id']) && (($rec['editable']==1) || ($admin)))
    {
      adm_form('update');
      echo 
        hidden('userid',$rec['userid']).
        hidden('profiles_options_id_old',$rec['profile_option']).
        td(adm_standard_submit_cancel("Save","Discard"), "action").
        td((($rec['profile_option']==1)
        ?select_profiles_options('profiles_options_id',$rec['profile_option'])
        :hidden('profiles_options_id',$rec['profile_option']).$rec['description'])).
    
        td( (($rec['type']=='select') 
          ?select_profiles_values('profiles_values_value',$rec['profile_option'],$rec['values_value']):'').
          (($rec["type"]=="text")
          ?textbox('profiles_values_value',$rec['values_value'],20):'')).
          form_close();
      
    } else 
      if (($rec['show_in_profile']==1) || ($admin))
        echo 
          adm_standard_edit_delete($filter, $rec['id'], false).
            td ($rec['description'], 'field').
            td (
              (($rec['type']=='select')?$rec['values_description']:'').
              (($rec['type']=='text')?$rec['values_value']:''), 'field');
      echo tag_close("tr");
  }

  echo tag_close("tbody"). table_close();
  adm_footer();
}
?>
