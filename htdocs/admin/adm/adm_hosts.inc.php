<?php
/* This file is part of JFFNMS
 * Copyright (C) <2002-2005> Javier Szyszlican <javier@szysz.com>
 * This program is licensed under the GNU GPL, full terms in the LICENSE file
 */
jffnms_load_api('iad');

function hosts_action_view_config()
{
  adm_frame_menu_split('hosts_config');
}

function hosts_action_view_interfaces()
{
  adm_frame_menu_split('interfaces');
}

function hosts_action_discovery($api, $id)
{
  global $st;

  $st['after_record_function'] = 'hosts_action_real_discovery';
  $st['after_record_id'] = $id;
  $st['md_type'] = 1;
  $st['fields'] = array_slice($st['fields'],0,5);
  $GLOBALS['filter'] = $id;
  $GLOBALS['init']=0;
}

function hosts_action_fast_discovery($api, $id)
{
  global $st;

  $st['after_record_function'] = 'hosts_action_real_discovery';
  $st['after_record_id'] = $id;
  $st['md_type'] = 2;
  $st['fields'] = array_slice($st['fields'],0,5);
  $GLOBALS["filter"] = $id;
  $GLOBALS["init"]=0;
}

//main GET Interfaces manual discovery code
function host_interfaces($host_ip,$rocommunity,$hostid, $md_type = 0)
{
  global $Config;

  //JS for Checkboxes
  echo 
      script("
  function check(field_aux) {
      field=document.forms[0].elements[field_aux];
      for (i = 0; i < field.length; i++) { 
    if (field[i].checked==true) field[i].checked = false; 
    else  
        field[i].checked = true; 
      }
      return true;
  }");

  echo table('','manual_discovery');

  $GLOBALS["SCRIPT_NAME"]="adm_interfaces.php";
  $GLOBALS["init"]=0;
  adm_form('bulkadd','POST');

  $max_fields = 15;
  $found_interfaces = 0;
  
  $types_obj = new JffnmsInterface_Types();
  $types_fields_obj = new JffnmsInterface_Types_Fields();
  $Interfaces = new JffnmsInterfaces();

  //get host data
  $Hosts = new JffnmsHosts();
  $host_db_data = current($Hosts->get_all($hostid));
  unset($Hosts);
  
  $types_obj->get(); //get pointer to interface types list
  
  $bulk_add = array();
  $bulk_add_id = 0;

  while ($itype_row = $types_obj->fetch()) 
    if (($itype_row['id'] > 1) &&               //avoid Unknown interface type
       (($md_type!=2) || (($itype_row['id']!=2) && ($itype_row['id']!=23))))  // if MDtype=2 No PortScan (TCP/UDP)
    {
      //get interfaces from the database
      $db = iad_interfaces_from_db($Interfaces, $hostid, $itype_row['id']);

      //do a discovery and get the interface list
      $host = iad_interfaces_from_discovery($itype_row['autodiscovery_function'],$host_ip,$rocommunity,$hostid,$itype_row['autodiscovery_parameters']);

      if (!is_array($db)) $db = array();
      if (!is_array($host)) $host = array();
      if (!isset($fields) || !is_array($fields)) $fields = array();
      //debug ($db);
      //debug ($host);

      //merge all keys
      $interface_ids_list = array_unique(array_merge(array_keys($db),array_keys($host)));
      asort($interface_ids_list);
      reset($interface_ids_list);

      if ((count($interface_ids_list) > 0) || ($itype_row['allow_manual_add']==1))  //key is valid 
      {
        $fields = $types_fields_obj->get_all(NULL,array('itype'=>$itype_row['id'],'exclude_types'=>20));
        //debug ($fields);
        $internal_fields = array('host','type','client','poll','sla','show_rootmap','make_sound','interface');
        foreach ($internal_fields as $fname)
          $fields[]=array('name'=>$fname,'showable'=>0,'overwritable'=>1);
        unset ($internal_fields);
        $showable_fields = 4;

        echo 
          table_row($itype_row['description'].
            (($itype_row['allow_manual_add']==1)?"&nbsp; &nbsp;".
              linktext('[Manual Add]','adm_interfaces.php?action=bulkadd'.
              '&bulk_add_ids[]=0'.
              '&bulk_add[0][host]='.$hostid.
              '&bulk_add[0][type]='.$itype_row['id'].
              '&bulk_add[0][client]='.$host_db_data['autodiscovery_default_customer'].
              '&bulk_add[0][poll]='.$itype_row['autodiscovery_default_poller'].
              '&bulk_add[0][sla]='.$itype_row['sla_default'],'','manual_add'):''),
              'discovery_title',$max_fields,'', false);
        if (count($interface_ids_list) > 0)
        { 
          echo
            tr_open('','discovery_header').
            td ('Add/Edit', 'field_action').
            td ('Index', 'field_index').
            td ('Name', 'field_interface').
            td ('Status', 'field_status');
          foreach ($fields as $key=>$fdata)
            if (array_key_exists('ftype', $fdata) && $fdata['ftype']==3)
            {
              $index_field = $fdata['name'];
              $fields[$key]['showable']=0;
            } else
              if (($fdata['showable']==1) && ($fdata['ftype_handler']!='bool')) 
              {
                echo td($fdata['description'], 'field_'.$fdata['name']);
                $showable_fields++;
              }
          $fields_left = $max_fields - $showable_fields;
          if ($fields_left > 0) 
            echo td ('&nbsp;','empty_fields','',$fields_left);
          echo tag_close('tr');
        }
        foreach ($interface_ids_list as $key) //key is valid 
        {
          $found_interfaces++; //number of interfaces found on all  the host
          if (array_key_exists($key, $db) && is_array($db[$key]))
            echo tr_open('', 'discovery_interface_old');
          else
            echo tr_open('', 'discovery_interface_new');
          if (array_key_exists($key, $host) and is_array($host[$key]))  //interface is in the host
          {
            $value = $host[$key];     //get the value from the host returned data
            $value[$index_field]=$key;   //put the KEY as the Index Field (for adding)
          } else {
            $value = $db[$key]; //get the value from the interfaces table in the DB
            $value['admin'] = '(Not Found in Host)';
          }
          ad_set_default ($value['host'],$hostid);
          ad_set_default ($value['type'],$itype_row['id']);
          ad_set_default ($value['client'],$host_db_data['autodiscovery_default_customer']);
          ad_set_default ($value['poll'],$itype_row['autodiscovery_default_poller']);
          ad_set_default ($value['sla'],$itype_row['sla_default']);
          foreach ($value as $fname=>$data)
            $value[$fname] = str_replace("\"","",$value[$fname]);

          //Update Handler
          $update_function = $itype_row['update_handler'];
          $real_function = "handler_".$update_function;
          $function_file = $Config->get('jffnms_real_path')."/engine/handlers/".$update_function.".inc.php";
  
          if (in_array($function_file,get_included_files()) || (file_exists($function_file) &&  (include_once($function_file))))
          {
            if (function_exists($real_function))
              if (array_key_exists($key, $db))
                call_user_func_array($real_function,array(false, &$value,$db[$key]));
              else
                call_user_func_array($real_function,array(false, &$value,array()));
            else
              echo html("span", "ERROR: Calling Function \"$real_function\" doesn't exist.", "error");
          } else
            echo html("span", "ERROR Loading file \"$function_file\"", "error");

          //Show Field Values
        
          if (array_key_exists($key, $db) && is_array($db[$key])) //if the interface is in the DB
            echo td (linktext("Edit","adm_interfaces.php?action=edit&interface_id=".$db[$key]["id"]),"field_action"); //put a edit link
          else { //if its not, put an ADD link
            $bulk_add_id++;
            foreach ($fields as $fdata) //for each field
              if (array_key_exists($fdata['name'], $value)
                && (!array_key_exists('default_value',$fdata) ||($fdata['default_value']!=$value[$fdata['name']])) //is not the default
                && isset($value[$fdata['name']])
                && ($value[$fdata['name']]!==''))         //if the value is ok
                $bulk_add[$bulk_add_id][$fdata['name']]=$value[$fdata['name']];     //store value for multiple add
            echo td(checkbox_value("bulk_add_ids[]",$bulk_add_id,0),"bulk_add_checkbox");
          }//add link
          if ($value[$index_field]==$value['interface'])   //if Index and Inteface name are equal.
            echo td ($value['interface'],'field_interface','',2);  //show only one
          else {
            echo td($value[$index_field],"field_index");
            echo td($value["interface"],"field_interface");
          }
          //Status
          unset ($aux);
          if (isset($value['admin'])) $aux[]=$value['admin'];
          if (isset($value['oper']))  $aux[]=$value['oper'];
          echo td(join(' / ',array_unique($aux)),'field_status');
          //other fields
          foreach ($fields as $fdata) 
          if (($fdata['showable']==1) && ($fdata['ftype_handler']!='bool'))
            echo td(htmlspecialchars(substr($value[$fdata['name']],0,30)),'field_'.$fdata['name']);
          if ($fields_left > 0) 
            echo td('&nbsp;','empty_fields','',$fields_left);
          echo tag_close('tr');
          flush();
        } //foreach found interface
        table_row("&nbsp;","separator","",$max_fields);
      } //count(host) > 0
  } //interface types while
  
  if ($found_interfaces==0)
    table_row('No Interfaces Found','no_interfaces_found',$max_fields);
  else   
    if (is_array($bulk_add) && (count($bulk_add)>0))
    {
      $html = '';
      while ( list($key,$data) = each ($bulk_add))
      {
        while ( list($field,$value) = each ($data)) 
          $html.=hidden("bulk_add[".$key."][".$field."]",htmlspecialchars($value),0);
        $html.="\n";
      }
      echo $html;
      echo 
        tr_open('add_marked_interfaces').
        td(adm_form_submit('Add Marked Interfaces'),'','',2).
        td(
          tag("input","",""," type=checkbox onClick=\"this.value=check('bulk_add_ids[]');\"").
          "Mark All Interfaces").
        tag_close('tr');
    }
  echo 
    form_close().
    table_close();
}

function hosts_action_real_discovery($host)
{
  global $st;
  if ($host['id'] == $st['after_record_id'])
  {
    echo
      tr_open('','manual_discovery'). 
      tag('td','','','colspan=21');
      host_interfaces($host['ip'], $host['rocommunity'], $host['id'], $st['md_type']);
      
    echo 
      tag_close('td').
      tag_close('tr');

      unset ($st['after_record_function']);
  }
}

?>
