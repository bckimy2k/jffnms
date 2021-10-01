<?php
/* This file is part of JFFNMS
 * Copyright (C) 2002-2011 JFFNMS Authors
 * This program is licensed under the GNU GPL, full terms in the LICENSE file
 */

class JffnmsInterface_types extends basic
{
  public $jffnms_insert = array('description'=>'New Interface Type',
    'autodiscovery_function'=>'none','rrd_structure_res'=>103680,
    'rrd_structure_rra'=>'RRA:AVERAGE:0.5:1:<resolution>'); 

  function get_all($ids = NULL,$fields=NULL)
  { 
    return get_db_list(  
      array('interface_types' ,'pollers_groups','graph_types','slas'),  $ids, 
      array('interface_types.*',
        'poller_default'=>'pollers_groups.description',
        'graph_types_description'=>'graph_types.description',  
        'slas_description'=>'slas.description'),  
      array(
        array('interface_types.autodiscovery_default_poller','=','pollers_groups.id'),
        array('interface_types.graph_default','=','graph_types.id'),
        array('interface_types.sla_default','=','slas.id'),
        array('interface_types.id','>',0)),
      array(array('interface_types.description','desc'))
    ); 
  }


} // class
