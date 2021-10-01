<?php
/* This file is part of JFFNMS
 * Copyright (C) <2002-2005> Javier Szyszlican <javier@szysz.com>
 * This program is licensed under the GNU GPL, full terms in the LICENSE file
 */

{
  require_once('../../auth.php');
  $action = $Sanitizer->get_string('action');
  $actionid = $Sanitizer->get_string('actionid');
  $filter = $Sanitizer->get_string('filter');
  $init = $Sanitizer->get_int('init',0);
  $span = $Sanitizer->get_int('span',20);

  if (!profile('ADMIN_SYSTEM'))
    die ('<H1> You dont have Permission to access this page.</H1></HTML>');

  $TriggersRules = new JffnmsTriggers_rules();
  adm_header('Triggers Rules');

  $editid=0;
  switch ($action)
  {
  case 'update':
      $value = $Sanitizer->get_string('value',array(),TRUE);
      if (is_array($value))
          $value = join($value,',');
      $update_data = array(
          //'trigger_id' => $Sanitizer->get_int('trigger_id'),
          'pos' => $Sanitizer->get_int('pos'),
          'field' => $Sanitizer->get_string('field'),
          'operator' => $Sanitizer->get_string('operator'),
          'value' => $value,
          'action_id' => $Sanitizer->get_int('action_id',1),
          'stop' => $Sanitizer->get_int('stop',0),
          'and_or' => $Sanitizer->get_int('and_or',0),
      );
      $action_params = $Sanitizer->get_string('action_params');
      if (is_array($action_params))
      {
          foreach ($action_params as $key=>$data)
              $aux[]="$key:$data";
	      $update_data['action_parameters']=join(",",$aux);
	  }
	  $TriggersRules->update($actionid,$update_data);
	  $action="list";
      break;

	

  case 'add':
	  $actionid=$TriggersRules->add($filter);
	  $action="edit";
    break;

  case 'delete':
	  $TriggersRules->del($actionid);
	  $action='list';
    break;

  case 'edit':
	  $editid = $actionid;
    break;
  }
  $cant = $TriggersRules->get($filter);
  echo 
	    adm_table_header("Triggers Rules", $init, $span, 13, $cant, "admin_triggers_rules", true).
      tag("tr","","header").
      td ("Action", "field", "action").
	    td ("ID", "field").
	    td ("Position", "field").
	    td ("Field", "field","",2).
	    td ("Operator", "field","",2).
	    td ("Value", "field").
	    td ("Action", "field","",2).
	    td ("Parameters", "field").
	    td ("if Match", "field").
	    td ("&nbsp;", "field").
	    tag_close("tr").
	    tag("tbody");

  $TriggersRules->slice($init,$span);

  $row=0;
  while ($rec = $TriggersRules->fetch())
  {
  	echo tr_open("row_".$rec["id"],(($editid==$rec["id"])?"editing":((($row++%2)!=0)?"odd":"")));

    if ($editid==$rec["id"])
    {
	      adm_form("update");
	      echo
		      td(adm_standard_submit_cancel("Save","Discard"), "action").
		      td($rec["id"],"field", "field_id").
	    	  td(textbox("pos",$rec["pos"],5),"field").
	    	  td("if","field").
	    	  td(select_trigger_fields("field",$rec["field"],$rec["trigger_type"]),"field").
	    	  td("is","field").
		    ((($rec["field"]!="none") && ($rec["field"]!="any"))
		     ?td(select_trigger_operator("operator",$rec["operator"]),"field").
		      td(select_trigger_fields_value("value",$rec["value"],$rec["trigger_type"],$rec["field"]),"field")
		     :td("&nbsp;","field","",2)).
		
	    	  td("then","field").
	    	  td(select_actions("action_id",$rec["action_id"]),"field").
	    	  td(($rec["action_id"]!=1)
		     ?select_action_parameters("action_params",$rec["action_parameters"],$rec["action_parameters_def"],0)
		     :"&nbsp;","field").
	    	  td(select_stop_continue("stop",$rec["stop"]),"field").
	    	  td(select_and_or("and_or",$rec["and_or"]),"field").
		      form_close();
    } else
	      echo
  	    adm_standard_edit_delete($rec["id"],$filter, false).
		      td($rec["id"],"field", "field_id").
	    	  td($rec["pos"],"field").
		     ((($rec["field"]!="none") && ($rec["field"]!="any"))
		     ?td("if","field").
	    	  td(select_trigger_fields("field",$rec["field"],$rec["trigger_type"],1),"field").
	    	  td("is","field").
		      td(select_trigger_operator("operator",$rec["operator"],1),"field").
		      td(select_trigger_fields_value("value",$rec["value"],$rec["trigger_type"],$rec["field"]),"field")

		     :(($rec["field"]=="any")
			   ?td("&nbsp;","field","",5)
			   :"")).

		    ((($rec["field"]!="none") && ($rec["action_id"]!=1))
		     ?td("then","field").
	    	  td($rec["action_description"],"field").
		      td(select_action_parameters("action_params",$rec["action_parameters"],$rec["action_parameters_def"],1),"field")
		     :td("&nbsp;","field","",3)).
	    	  td(select_stop_continue("stop",$rec["stop"],1),"field").
	    	  td(select_and_or("and_or",$rec["and_or"],1),"field");
    echo
	        tag_close("tr");
  }
  echo tag_close("tbody"). table_close();

  adm_footer();
}

?>
