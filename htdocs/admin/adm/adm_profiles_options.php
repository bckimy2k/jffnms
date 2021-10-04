<?php
/* This file is part of JFFNMS
 * Copyright (C) <2002-2011> JFFNMS Authors
 * This program is licensed under the GNU GPL, full terms in the LICENSE file
 */
require_once ('../../auth.php');

if (!profile('ADMIN_USERS')) die ('<H1> You dont have Permission to access this page.</H1></HTML>');

$actionid = $Sanitizer->get_int('actionid');
$action = $Sanitizer->get_string('action','list');
$filter = $Sanitizer->get_string('filter');
$init = $Sanitizer->get_int('init',0);
$span = $Sanitizer->get_int('span',10);
$editid='';

$ProfilesOptions = new JffnmsProfiles_options();
    
adm_header('Profiles Options');

switch ($action)
{
case 'view':
  adm_frame_menu_split('profiles_values',1);
  break;

case 'update':
  $options_data = array(
    'description', $Sanitizer->get_string('description'),
    'tag', $Sanitizer->get_string('tag'),
    'editable', $Sanitizer->get_int('editable', 0),
    'type' => $Sanitzer->get_string('type'),
    'use_default'  => $Sanitizer->get_int('use_default', 0),
    'default_value' => $Sanitizer->get_string('default_value'),
    'show_in_profile' => $Sanitizer->get_int('show_in_profile', 0)
  );
  $ProfilesOptions->update($actionid,$options_data);
  $action='list';
  break;

case 'add':
  $actionid=$ProfilesOptions->add_user();
  $action='edit';
  $editid = $actionid;
  break;

case 'delete':
  $ProfilesOptions->delete($actionid);
  $action='list';
  break;

case 'edit':
  $editid = $actionid;
  break;
}

$types = array('select'=>'Select', 'text'=>'Text Box');
$cant = $ProfilesOptions->get();

echo 
  adm_table_header('Profiles Options', $init, $span, 10, $cant, 'admin_profiles_options', true).
        tag('tr','','header').
        td ('Action', 'field', 'action').
  td ('ID', 'field').
  td ('Description', 'field').
  td ('Tag', 'field').
  td ('Type', 'field').
  td ('Editable?', 'field').
  td ('Show?', 'field').
  td ('is Default?', 'field').
  td ('Default Value', 'field').
  tag_close('tr').
  tag('tbody');

  $ProfilesOptions->slice($init,$span);
  $row=0;
  while ($rec = $ProfilesOptions->fetch())
  {
    echo tr_open('row_'.$rec['id'],(($editid==$rec['id'])?'editing':((($row++%2)!=0)?'odd':'')));
    if ($editid==$rec['id'])
    {
      adm_form('update');
      echo
        td(adm_standard_submit_cancel('Save','Discard'), 'action').
        td($rec['id'],'field', 'field_id').
        td(textbox('description',$rec['description'],20),'field').
        td(textbox('tag',$rec['tag'],20),'field').
        td(select_custom('type',$types,$rec['type']),'field').
        td(checkbox('editable',$rec['editable']),'field').
        td(checkbox('show_in_profile',$rec['show_in_profile']),'field').
        td(checkbox('use_default',$rec['use_default']), 'field').
        td(($rec['type']=='select')
          ?select_profiles_values('default_value',$rec['id'],$rec['default_value'])
          :textbox('default_value',$rec['default_value'],20), 'field').
       form_close();
    } else {
      $ProfilesValues = new JffnmsProfiles_values();
        echo 
          adm_standard_edit_delete('', $rec['id'], 'Values').
          td($rec['id'],'field','field_id').
          td($rec['description'],'field').
          td($rec['tag'],'field').
          td($types[$rec['type']],'field').
          td(checkbox('editable',$rec['editable'],0),'field').
          td(checkbox('show_in_profile',$rec['show_in_profile'],0),'field').
          td(checkbox('use_default',$rec['use_default'],0), 'field').
          td((($rec['type']=='select')
          ?$ProfilesValues->description($rec['id'], $rec['default_value'])
          :$rec['default_value']), 'field');
    }
    echo tag_close('tr');
  }//while fetch

  echo tag_close('tbody'). table_close();
  adm_footer();
?>
