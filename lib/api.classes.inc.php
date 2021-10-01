<?php
/* This file is part of JFFNMS
 * Copyright (C) <2002-2005> Javier Szyszlican <javier@szysz.com>
 * This program is licensed under the GNU GPL, full terms in the LICENSE file
 */

class JffnmsAutodiscovery extends basic
{
  public $jffnms_insert = array('description'=>'New Autodiscovery Type');
  public $jffnms_filter_record = 0;
  public $jffnms_order_field = 'description';
}

class JffnmsClients extends basic
{
  public $jffnms_insert = array('name'=>'a New Customer', 'shortname'=>'new_cust');
  public $jffnms_filter_record = 0;
  public $jffnms_order_field = 'name';
  public $jffnms_order_type = 'asc';
}
class JffnmsGraph_types  extends basic
{ 
  public $jffnms_insert = array('description'=>'New Graph Type'); 

  function get_all($ids = NULL,$fields=NULL)
  { 
    return get_db_list(  
      array('graph_types','interface_types'),  $ids, 
      array('graph_types.*','types_description'=>'interface_types.description') ,  
      array(array('graph_types.type','=','interface_types.id'),array('graph_types.id','>',0)),
      array(
        array('graph_types.type','asc'),
        array('graph_types.id','asc')
      ));
  }
} // class jffnms_graph_types


class JffnmsHosts_config_types extends basic
{ 
  public $jffnms_insert = array('description' => 'New Host Config Type'); 
  public $jffnms_filter_record = 0;
}

class JffnmsSeverity extends basic
{ 
  public $jffnms_insert = array('severity'=>'New Severity','bgcolor'=>'000000','fgcolor'=>'FFFFFF'); 
}

class JffnmsInterface_types_field_types extends basic
{
  public $jffnms_insert = array('description'=>'New','handler'=>'text');
  public $jffnms_filter_record=1;
  public $jffnms_order_type = 'asc';
}

class JffnmsSyslog_types extends basic
{ 
  public $jffnms_insert = array('match_text'=>'New Syslog Message Type','info'=>'*'); 

  function get_all($ids = NULL, $fields=NULL)
  { 
    return get_db_list(  
      array($this->jffnms_class(),'types'),  $ids, 
      array($this->jffnms_class().'.*','types_description'=>'types.description') ,  
      array(array($this->jffnms_class().'.type','=','types.id'),array($this->jffnms_class().'.id','>',1)),
      array(array($this->jffnms_class().'.pos','asc'),array($this->jffnms_class().'.id','desc'))
    ); 
  }
}

class JffnmsEvent_types extends basic
{
  public $jffnms_insert = array('description'=>'New Event Type'); 

  function jffnms_class()
  {
      return 'types';
  }

  function get_all($ids = NULL,$filters = array())
  {
    $where_special = array();
    if (array_key_exists('generate_alarm', $filters) && $filters['generate_alarm']==1)
      $where_special[]=array('types.generate_alarm','=',1);
    if (array_key_exists('show_unknown', $filters) && $filters['show_unknown']==1)
      $where_special[]=array('types.id','>',0);
    else
      $where_special[]=array('types.id','>',1);
      
    return get_db_list(  
      array('types','severity','up'=>'types'),  $ids, 
      array('types.*','severity_description'=>'severity.severity','alarm_up_description'=>'up.description'),  
      array_merge(
        array(
          array('types.severity','=','severity.id'),
          array('types.alarm_up','=','up.id')),
        $where_special),
      array(array('types.description','desc'))
    ); 
  }
}

class JffnmsFilters  extends basic
{
  public $jffnms_insert = array('description'=>'New Filter'); 
  public $jffnms_filter_record = 0;
  public $jffnms_order_type = 'asc';
  
  function generate_sql()
  {
    $params = func_get_args();
    return call_user_func_array('filters_generate_sql',$params);
  }

  function generate_where()
  {
    $params = func_get_args();
    return call_user_func_array('filters_generate_sql2',$params);
  }
}

class JffnmsFilters_fields extends basic
{ 
  public $jffnms_insert = array('description'=>'New Filter Field'); 
  public $jffnms_filter_record = 0;
  public $jffnms_order_field = 'description';
  public $jffnms_order_type = 'asc';
}

class JffnmsFilters_cond extends basic
{ 
  public $jffnms_insert = array('pos'=>1); 

  function get_all($ids = NULL, $fields=NULL)  { 
    return get_db_list(  
      array('filters',$this->jffnms_class(),'filters_fields'),  $ids, 
      array($this->jffnms_class().'.*',
        'filter_description'=>'filters.description',
        'field_description'=>'filters_fields.description',  
        'field_name'=>'filters_fields.field'),
      array(
        array($this->jffnms_class().'.field_id','=','filters_fields.id'),
        array($this->jffnms_class().'.filter_id','=','filters.id'),
      ),
      array(
        array('filters_cond.filter_id','asc'),
        array('filters_cond.pos','asc'),
        array('filters_cond.id','desc')
      )); 
  }
  function add($filter = NULL)
  {
    if ($filter==NULL) $filter=1;
    return db_insert('filters_cond', array('filter_id'=>$filter));
  }
} //class jffnms_filters_cond

class JffnmsSlas extends basic
{ 
  public $jffnms_insert = array('description'=>'New SLA'); 

  function get_all($ids = NULL,$fields=NULL)
  { 
    return get_db_list(  
      array('slas','types','interface_types','alarm_states'),  $ids, 
      array('slas.*',
        'state_description'=>'alarm_states.description',
        'types_description'=>'types.description',
        'interface_type_description'=>'interface_types.description'
      ),  
      array(  
        array('slas.event_type','=','types.id'),
        array('slas.interface_type','=','interface_types.id'),
        array('slas.state','=','alarm_states.id'),
        array('slas.id','>',1)),
        array(array('slas.id','desc')
      )
    ); 
  }
}

class JffnmsSlas_cond  extends basic
{ 
  public $jffnms_insert = array('description'=>'New Condition'); 
}

class JffnmsAlarm_states  extends basic
{ 
  public $jffnms_insert = array('description'=>'new'); 
  public $jffnms_filter_record = 0;
  public $jffnms_order_type = 'asc';
}

class JffnmsPollers_backend  extends basic
{ 
  public $jffnms_insert = array('description'=>'a New Backend','command'=>'no_backend'); 
  public $jffnms_filter_record = 0;
  public $jffnms_order_field = 'description';
  public $jffnms_order_type = 'asc';
}

class JffnmsPollers  extends basic
{ 
  public $jffnms_insert = array('description'=>'a New Poller','name'=>'new_poller','command'=>'no_poller'); 
  public $jffnms_filter_record = 0;
  public $jffnms_order_field = 'description';
  public $jffnms_order_type = 'asc';
}

class JffnmsTrap_receivers  extends basic
{ 
  public $jffnms_insert = array('description'=>'a New Trap Receiver', 'match_oid'=>'Trap OID', 'position'=>'10', 'command'=>'none');
  public $jffnms_filter_record = 0;

  function get_all($ids = NULL, $fields=NULL)  { 
    return get_db_list(  
      array('trap_receivers', 'interface_types','pollers_backend'),  $ids, 
      array(
        'trap_receivers.*',
        'interface_type_description'=>'interface_types.description', 
        'backend_description'=>'pollers_backend.description'
      ),
      array(
        array('trap_receivers.interface_type','=','interface_types.id'),
        array('trap_receivers.backend','=','pollers_backend.id')
      ),
      array(
        array('trap_receivers.position','asc'),
        array('trap_receivers.description','asc'),
        array('trap_receivers.id','desc')
      )
    ); 
  }
}

class JffnmsPollers_groups extends basic
{ 
  public $jffnms_insert = array('description'=>'New Poller Group'); 

  function get_all($ids = NULL, $fields=NULL)  { 
    return get_db_list(  
      array('pollers_groups', 'interface_types'),  $ids, 
      array('pollers_groups.*','type_description'=>'interface_types.description') ,  
      array(  
        array('pollers_groups.interface_type','=','interface_types.id'),
        array('pollers_groups.id','>',1)
      ),
      array(array('pollers_groups.id','desc')) 
    );
  }
}

class JffnmsPollers_poller_groups extends basic
{ 
  public $jffnms_insert = array(); //no error

  function add($filter=NULL)
  { 
    return db_insert('pollers_poller_groups', array('poller_group'=>$filter)); 
  }
    
  function get_all($ids = NULL, $fields=NULL)
  { 
    return get_db_list(  
      array('pollers_groups','pollers_poller_groups','pollers','pollers_backend'),  $ids, 
      array('pollers_poller_groups.*',
        'group_description'=>'pollers_groups.description',
        'poller_description'=>'pollers.description',
        'backend_description'=>'pollers_backend.description'
      ) ,  
      array(  
        array('pollers_poller_groups.poller_group','=','pollers_groups.id'),
        array('pollers_poller_groups.poller','=','pollers.id'),
        array('pollers_poller_groups.backend','=','pollers_backend.id'),
        array('pollers_poller_groups.id','>',1)
      ),
      array(
        array('pollers_poller_groups.poller_group','asc'), 
        array('pollers_poller_groups.pos','asc'), 
        array('pollers_poller_groups.id','desc')
      )
    );
  }
}

class JffnmsSlas_sla_cond extends basic
{ 
  public $jffnms_insert = array(); //no error
  
  function add($filter=NULL)
  { 
    return db_insert('slas_sla_cond',array('sla'=>$filter)); 
  }
    
  function get_all($ids = NULL, $fields=NULL)
  { 
    return get_db_list(  
      array('slas','slas_sla_cond','slas_cond'),  $ids, 
      array(  'slas_sla_cond.*',
        'sla_description'=>'slas.description',
        'cond_description'=>'slas_cond.description'
      ),  
      array(  
        array('slas_sla_cond.sla','=','slas.id'),
        array('slas_sla_cond.cond','=','slas_cond.id'),
        array('slas_sla_cond.id','>',1)
      ),
      array(
        array('slas_sla_cond.sla','asc'), 
        array('slas_sla_cond.pos','asc'), 
        array('slas_sla_cond.id','desc')
      )
    ); 
  }
}

class JffnmsLogfiles_match_items  extends basic
{ 
  public $jffnms_insert = array('description'=>'a New Item'); 
  public $jffnms_filter_record = 0;
  public $jffnms_order_field = 'description';
  public $jffnms_order_type = 'asc';

  function get_all($ids = NULL,$filters=NULL)
  { 
      $where = array();
      if (array_key_exists('logfile_id', $filters)) {
          $where[] = array('logfiles_match_items.logfile_id','=',$filters['logfile_id']);
      }
      return get_db_list(  
          array('logfiles_match_items','logfiles','types'),  $ids, 
          array(
              'logfiles_match_items.*',
              'logfile_description'=>'logfiles.description',
              'types_description'=>'types.description'
          ),  
          array_merge(
          array(
              array('logfiles_match_items.logfile_id','=','logfiles.id'),
              array('logfiles_match_items.type','=','types.id'),
              ),$where),
          array(
              array('logfiles_match_items.logfile_id','asc'),
              array('logfiles_match_items.pos','asc')
      ));
  }
}
?>
