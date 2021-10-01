<?php
/* This file is part of JFFNMS
 * Copyright (C) <2002-2005> Javier Szyszlican <javier@szysz.com>
 * This program is licensed under the GNU GPL, full terms in the LICENSE file
 */
require_once('../../auth.php');

if (!profile('ADMIN_HOSTS')) die ('<H1> You dont have Permission to access this page.</H1></HTML>');
    
adm_header('Hosts Config');
$api = new JffnmsHosts_configs();

$span = $Sanitizer->get_int('span', 30);
$init = $Sanitizer->get_int('init', 0);
$filter = $Sanitizer->get_string('filter');
$action = $Sanitizer->get_string('action');
$actionid = $Sanitizer->get_int('actionid');
$diff1 = $Sanitizer->get_int('diff1');
$diff2 = $Sanitizer->get_int('diff2');
$fields = 6;    

$cant = $api->get(NULL, $filter, $init, $span);

echo 
  adm_table_header("Hosts Config (Cisco running-config) Viewer", $init, $span, $fields, $cant, "hosts_config", false);
if ($action != 'diff')
  echo form().
    hidden('action','diff').
    tag('tr','','header').
      td ('Action', 'field', 'action').
      td ('ID', 'field', 'field_id').
      td ('Date', 'field').
      td ('Host', 'field').
      td ('Config', 'field').
      td ('Size', 'field').
      tag_close('tr');
echo tag('tbody');

$row = 0;
while ($rec = $api->fetch())
{
  if ($action!='diff')
  {
    echo 
      tr_open('row_'.$rec['id'],(($row++%2)!=0)?'odd':'').
      td(
        radiobutton('diff1', ($diff1==$rec['id'])?1:0, $rec['id']).
        radiobutton('diff2', ($diff2==$rec['id'])?1:0, $rec['id']).
        linktext('Read',
          $Sanitizer->get_url('','all', 
          array('action' => 'read', 'actionid' => $rec['id'], 'init' => $init)),
          'action')).
      td($rec['id'], 'field_id').
      td($rec["date"], "field").
      td($rec['host_description'].' '.$rec['zone_description'], 'field').
      td(substr($rec['config'], 0, 50), 'field').
      td(strlen($rec['config']), 'field').
      tag_close('tr');
  }
  
  if (($actionid==$rec['id']) && ($action=='read')) 
  {
    echo 
      tr_open().
      td(memobox('', 20, 80, $rec['config']), 'field', 'field_config', $fields, '', true).
      tag_close("tr");
  }
} //while
if ($action == 'diff')
{
  $clean_url = $Sanitizer->get_url('','all', 
      array('diff1', $diff1, 'diff2', $diff2, 'filter', $filter),
      array('action'));
  echo tr(linktext('Go Back',$clean_url), 'action', $fields).
    tr('Difference Between Configurations','header',$fields).
    tr_open().
    td(memobox('', 20, 80, $api->diff($diff1, $diff2)), 'field', 'field_config', $fields, '', true);
} else
  echo tr(adm_form_submit('View Diff'), 'action', $fields).
form_close();
adm_footer();
?>
