<?php
/* This file is part of JFFNMS
 * Copyright (C) <2002-2005> Javier Szyszlican <javier@szysz.com>
 * This program is licensed under the GNU GPL, full terms in the LICENSE file
 */
    //normal traffic

function graph_traffic ($data)
{ 
  $opts_GRAPH_end = array();
  $rrd_data='';
    
  if ($data['flipinout']==1)
  {
    $opts_DEF = rrdtool_get_def($data,array('output'=>'input', 'input'=>'output'));
    $flip_legend = "(In / Out Flipped)";
  } else 
    $opts_DEF = rrdtool_get_def($data,array('input','output'));

  $opts_DEF[]='CDEF:inputbits=input,UN,input,input,IF,8,*';
  $opts_DEF[]='CDEF:outputbits=output,UN,output,output,IF,8,*';

  $opts_GRAPH_init=array(
    "HRULE:".$data["bandwidthin"]. "#FF0000:' '",
    "COMMENT:'Inbound Bandwidth: ".($data["bandwidthin"] /1000)." kbps'",     
    "HRULE:".$data["bandwidthout"]."#AA0000:' '",
    "COMMENT:'Outbound Bandwidth:".($data["bandwidthout"]/1000)." kbps".(!empty($data["address"])?"  IP: ".$data["address"]:"")."\\n'",     
  );  

  // External Data Gathering
  if (is_numeric($data['percentile']) or 
    (array_key_exists('other_data', $data) and $data['other_data']=='get_graph_data'))
  {
    $Interfaces = new JffnmsInterfaces();
    $rrd_data = $Interfaces->fetch_ds ($data['id'], array ('input', 'output'), $data['rrd_grapher_time_start'], $data['rrd_grapher_time_stop']);
  }

  // Nth Percentile Calculation
  if (is_numeric($data['percentile']))
  {
    include_once (jffnms_shared('percentile'));
    if (is_array($rrd_data))
    {
      list($result, $data_points) = calculate_percentile ($data['percentile'], $rrd_data);
      $percentile_value = $result*8;
      $opts_GRAPH_init[] =
        "HRULE:".$percentile_value."#CC00FF:'".$data["percentile"]." Percentile Usage\: ".byte_format($percentile_value,2)."bps\\n'";
    }
  } else
    $data_points = array($rrd_data);  // get Data Points for CSV without Percentile
                          
  $opts_GRAPH = array_merge($opts_GRAPH_init,array(         
    "AREA:inputbits#00CC00:'Inbound '",
      "GPRINT:inputbits:MAX:'Max\:%8.2lf %sbps'",
      "GPRINT:inputbits:AVERAGE:'Average\:%8.2lf %sbps'",
      "GPRINT:inputbits:LAST:'Last\:%8.2lf %sbps\\n'",
    "LINE2:outputbits#0000FF:Outbound",
      "GPRINT:outputbits:MAX:'Max\:%8.2lf %sbps'",
      "GPRINT:outputbits:AVERAGE:'Average\:%8.2lf %sbps'",
      "GPRINT:outputbits:LAST:'Last\:%8.2lf %sbps\\n'",
    (isset($flip_legend)?"COMMENT:'".$flip_legend."'":""),
  ),$opts_GRAPH_end);

  $opts_header[] = "--vertical-label='Bits per Second'";

  return array ($opts_header, @array_merge($opts_DEF,$opts_GRAPH), $data_points);
}
?>
