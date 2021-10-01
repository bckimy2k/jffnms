<?php
/* This file is part of JFFNMS
 * Copyright (C) 2002-2011 JFFNMS Authors
 * This program is licensed under the GNU GPL, full terms in the LICENSE file
 */
include ('auth.php');  
define('SCALE_LEGEND', 'u Micro, m Milli, k Kilo, M Mega, G Giga');

$graph_type = $Sanitizer->get_int('graph_type', 1);
$map_id = $Sanitizer->get_int('map_id', 1);
$only_aggr = $Sanitizer->get_int('only_aggr',0);
$refresh_time = $Sanitizer->get_int('refresh_time', 60*5); // 5 min default
$view_all = $Sanitizer->get_int('view_all',0);
$view_type = $Sanitizer->get_string('view_type', 'html');
$want_graph = $Sanitizer->get_int('want_graph', 0);
$graph_times = performance_graph_times();
$use_interfaces = $Sanitizer->get_string('use_interfaces',NULL);
if ($use_interfaces==NULL)
  $use_interfaces = $Sanitizer->get_int('interface_id',NULL,TRUE);
if (!is_array($use_interfaces))
    $use_interfaces = explode(',', $use_interfaces);

if ($want_graph > 0)
{
  $view_type = 'graph';
  $graph_type = $want_graph;
}
if (!is_array($graph_type)) $graph_type = array($graph_type); //select default graph


    if ($map_profile = profile("MAP")) $map_id = $map_profile; //fixed map

$Interfaces = new JffnmsInterfaces();

//get graphics array with all needed data
list ($graphics, $graph_types_show, $graph_defaults, $number_of_interfaces) = 
  performance_graphs_list ($Interfaces, $graph_type,
    reports_make_interface_filter($use_interfaces,$view_all), $use_interfaces,
    $graph_times['start_unix'], $graph_times['stop_unix']);

performance_graphs_header($view_type, $graphics, $graph_times, $graph_types_show, $graph_defaults, $number_of_interfaces, $graph_type);
    

if ($Sanitizer->get_int('only_top') != 1)    
  $aggregation = performance_graphs_show($graphics, $Interfaces, $graph_times, $view_type, $only_aggr);
//debug ($aggregation);

performance_graphs_show_aggregation($view_type, $aggregation, $Interfaces, $graph_times, $want_graph, $only_aggr);
        
performance_graph_footer($view_type, $refresh_time);

function performance_graphs_html_header (&$graph_times, $graph_type, $graph_types_show,$graph_defaults, $number_of_interfaces)
{
  global $Config, $Sanitizer;

  $jffnms_rel_path = $Config->get('jffnms_rel_path');
  $REQUEST_URI = $Sanitizer->get_url('');
  $name = $Sanitizer->get_string('name');
  $hour = (60*60);
  $day = $hour*24;
  // post/get values to copy for selecting interfaces
  $interfaces_selectors = array('use_interfaces'); 

  $graph_time_presets = array(
    0           => 'No Preset',
    -(60*10)    => 'Last 10 Minutes',
    -($hour/2)  => 'Last Half Hour',
    -$hour      => 'Last Hour',
    -$hour*6    => 'Last 6 Hours',
    -$day       => 'Last Day',
    -$day*2     => 'Last 2 Days',
    -$day*7     => 'Last Week',
    -$day*30    => 'Last Month',
    -$day*360    => 'Last Year',
  );

  $client_id=0;
  if ($client_profile = profile("CUSTOMER"))
    $client_id = $client_profile; //fixed customer

  $colspan=3;

  $refresh_url = $Sanitizer->get_url('', 'all', FALSE, array('graph_time_start', 'graph_time_stop', 'graph_time_start_hour', 'graph_time_stop_hour'));
  $presets_url = $Sanitizer->get_url('', array('name','use_interfaces','graph_type','interface_id'));

  $shortcuts = array();

  if ($_SESSION['auth_type'] == 2)
    $shortcuts[] = linktext(image('logoff.png',0,0,'Logout'),$jffnms_rel_path.'/?logout=1&OldAuth='.$GLOBALS['PHP_AUTH_USER'],'_top');
  $shortcuts[] = linktext(image('refresh.png',0,16,'Refresh Now'), $refresh_url);
  $shortcuts[] = tag("img", "", "", "src='images/refresh2.png' alt='' OnClick=\"javascript: if (self.no_refresh!=1) ".
      "{ self.no_refresh = 1; this.style.display='none'; } else document.location = '".htmlspecialchars($refresh_url)."';\"",false);

  if ($client_id == 0 && profile('ADMIN_HOSTS'))
  {
      $shortcuts[] = linktext(image('edit.png',0,0,'Edit'),
          $Sanitizer->get_url($jffnms_rel_path.'/admin/adm/adm_interfaces.php', $interfaces_selectors));
      $shortcuts[] = linktext(image('tool.png',0,0,'Tools'),
          $Sanitizer->get_url($jffnms_rel_path.'/admin/tools.php', $interfaces_selectors));
  }

  $shortcuts[] = linktext(image('text.png',0,0,'Report'),
      $Sanitizer->get_url($jffnms_rel_path.'/admin/reports/state_report.php', $interfaces_selectors));

  $shortcuts[] = linktext(image('csv.png',0,0,'Export as CSV'),
      $Sanitizer->get_url('', 'all', array('view_type'=>'csv')));

  $shortcuts[] = linktext(image('a-top.png',0,0,'Hide','','hide'),
    "javascript: hide_menu('options_header', 'options', 'hide','images/a-top.png', 'images/a1-down.png');");

  arsort($graph_types_show);

  $graph_types_marked=array();
  foreach ($graph_types_show as $graph_id=>$graph_description)
    if (
      (in_array($graph_id,$graph_type)) ||  //graph we're looking for or
      ((in_array(1,$graph_type)) && (in_array($graph_id,$graph_defaults)))) //no graph selected and default graph
      $graph_types_marked[]=$graph_id;

  $output = 
      script ("
  
      function go_preset(select) {
    var time = select.options[select.selectedIndex].value;
    location.href = '".$presets_url."'+'&graph_time=preset&graph_time_preset='+time;
    return true;
      }

      function select_all(field_aux) {
    field = document.getElementById(field_aux);
        if (field)
    for (i = 0; i < field.length; i++)
            field[i].selected = true;
      }
      
      function hide_menu(ele1_name, ele2_name, img_name, img_show, img_hide) {
    ele1 = document.getElementById(ele1_name);
    ele2 = document.getElementById(ele2_name);
    img  = document.getElementById(img_name);
    
    img.src = (ele1.style.display=='none')?img_show:img_hide;
    ele1.style.display = (ele1.style.display=='none')?'':'none';
    ele2.style.display = (ele2.style.display=='none')?'':'none';
      } 
  
      ").
  
      table("header").
      tr_open("top").
      td(linktext((!empty($name)?$name." ":"")."Performance",$REQUEST_URI),"title","",$colspan-1).
      td(
    html("div", 
        html("div",join("",$shortcuts),"shortcuts"),
    "","","align='right'")).

      tag_close("tr").
      
      tr_open("options_header").
      td("Graph Types".linktext("[ all ]","javascript: select_all('graph_type[]');","","mark_all"),"graph_types").
      td("Time Span","time_span").
      td("Time Preset","time_preset").
      tag_close("tr").

      tr_open("options").
      form("", $_SERVER["SCRIPT_NAME"], "GET").
      reports_pass_options().
      hidden("graph_time","nopreset",1).
      hidden("name", $name).
      td(
    select_custom("graph_type", $graph_types_show, $graph_types_marked, "", 3, false, "", "javascript: this.form.submit();").
    (($number_of_interfaces > 1)
        ?html("div", checkbox_value("only_aggr",1,($GLOBALS["only_aggr"]==1)?1:0).br()."Show Only".br()."Aggregate","aggr_only")
        :""), "graph_types").
      td(
    //TIME SPAN Table
    table("time_span").
    tr_open().
    td("From:" ,"labels").
    td(select_date("graph_time_start",$graph_times['start'],7,true,$graph_times['start_hour'])).
    td(adm_form_submit("View"),"","",1,2).
    tag_close("tr").
    
    tr_open().
    td("To:","labels").
    td(select_date("graph_time_stop",$graph_times['stop'],7,true,$graph_times['stop_hour'])).
    tag_close("tr").
    table_close()
    //TIME SPAN Table end
    ).
      td(
    select_custom("graph_time_preset",$graph_time_presets,$graph_times['preset'],"javascript: go_preset(this)").
    html("span",br()."Current Date".br().date("Y-m-d H:i:s"),"current_date"), "time_preset").
      form_close().
      tag_close("tr").
      table_close();
  return $output;
} //function

function performance_graphs_list (&$Interfaces, $graph_type, $filters, $use_interfaces = NULL, $start, $stop)
{  
  $graphics = array();
  $graph_types_show = array();
  $graph_defaults = array(); 
  $interfaces_ids = array();

  $filters["with_graph"] = 1; //force graph fields
  $interface_count = $Interfaces->get($use_interfaces,$filters); 

  if ($interface_count > 0)
  {
    $gd_font_size = 5;
    while ($graph = $Interfaces->fetch())
    {
      //debug ($graph);
      $graph_types_show[$graph['graph_type']]=$graph['graph_type_description'];
      $graph_defaults[$graph["graph_type_default"]]=1;
      $file = 'interface-'.$graph['id'];
      $interface_ids[$graph["id"]]=1;
      
      $graphic_aux2=array();      
      if (((in_array(1,$graph_type) and ($graph['graph_type']==$graph['graph_type_default']))) || //default graph or
        in_array($graph['graph_type'],$graph_type) || in_array($graph['graph_type_graph1'],$graph_type))  //match graph type
      {
        if ($graph['graph_type_graph1'])
        { 
          $graphic_aux['image']='i'.$graph['id'].'-'.uniqid('').'.png';
          $graphic_aux['type']=$graph['graph_type_graph1'];
          $graphic_aux['sx']=$graph['graph_type_graph1_sx'];
          $graphic_aux['sy']=$graph['graph_type_graph1_sy'];
          $graphic_aux2['graph1']=$graphic_aux;
        }

        if ($graph['graph_type_graph2'])
        { 
          $graphic_aux['image']='i'.$graph['id'].'-'.uniqid('').'.png';
          $graphic_aux['type']=$graph['graph_type_graph2'];
          $graphic_aux['sx']=$graph['graph_type_graph2_sx'];
          $graphic_aux['sy']=$graph['graph_type_graph2_sy'];
          $graphic_aux2['graph2']=$graphic_aux;
        }
        
        //FIXME Include all description fields in the title
        $last_date = ' '.date('Y-m-d H:i:s',$graph['last_poll_date']);
        $title = $graph['host_name'].' '.$graph['zone_shortname'].' '.$graph['interface'].' '.$graph['client_shortname'].' '.$graph['description'];

        $title = str_replace("\r","",$title);
        $title = str_replace("\n"," ",$title);
        $title = str_replace("  ","",$title);

        $len_aux1 = (($graph["graph_type_graph1_sx"])/$gd_font_size)-strlen($last_date)-6; //graph length/size of character - length of date - margin
        $title = substr($title,0,$len_aux1);
        $title = str_pad($title,$len_aux1," ").(($graph["last_poll_date"] > 1)?$last_date:"");
        
        $graphic_aux2["title"]=$title;
        $graphic_aux2["aggr"]=$graph["graph_type_agg"];
        $graphic_aux2["id"]=$graph["id"];
        $graphic_aux2["host"]=$graph["host"];
        $graphic_aux2["graph_type"]=$graph["graph_type"];
        $graphic_aux2["graph_description"]=$graph["graph_type_description"];
        
        if (array_key_exists('percentile', $graph))
          $graphic_aux2["percentile"] = $graph["percentile"];  // for Nth percentile
        
        // Calculate the Stop Time based on the poll interval and last_poll_date
        //$poll_interval = ($graph["poll_interval"]==0)?$graph["host_poll_interval"]:$graph["poll_interval"];
        //$graphic_aux2["stop"] = ($stop==0)
        //  ?(round($graph["last_poll_date"]/$poll_interval)*$poll_interval)-$poll_interval
        //  :$stop;

        $graphic_aux2["stop"] = $stop;
        
        $graphic_aux2["type_id"] = $graph["type"];
    
        $graphics[] = $graphic_aux2;
    } // if is used
      } //while

      $graph_defaults = array_keys($graph_defaults);
  }

  //debug ($graphics);  
  //debug ($graph_types_show);
  //debug ($graph_defaults);
    
  return array($graphics,$graph_types_show,$graph_defaults, count($interface_ids));
} //performance_graphs_list function

function performance_graphs_header($view_type, &$graphics, &$graph_times, $graph_types_show, $graph_defaults, $number_of_interfaces, $graph_type)
{
  switch ($view_type)
  {
  case 'html': 
    adm_header("Performance Trends");
      
    if (count($graphics)==0)
    {
      echo html("span","Error: No Interface Graphs were returned.", "error");
      die();
    }
    
    //show header (selector)
    echo 
      tag ("div", "performance").
      performance_graphs_html_header ($graph_times, $graph_type, $graph_types_show,$graph_defaults, $number_of_interfaces);
      
    //debug ($graphics);

    if (($graph_times['stop_unix'] >0) && (($graph_times['start_unix'] > ($graph_times['stop_unix']-500))))
    {
      echo html("span","Error: 'From' is more recent than 'To'","error");
      die();
    }
    echo table("graphs");
    break;

  case 'graph':
    header ('Content-Type: image/png');
    break;

  case 'csv': 
    if ($graph_times['start_unix'] <= 0) $graph_times['start_unix'] += time();
    if ($graph_times['stop_unix'] <= 0) $graph_times['stop_unix'] += time();
  
    header('Pragma: public');
    header('Expires: 0');
    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
    header('Content-Type: application/force-download');
    header('Content-Type: application/octet-stream');
    header('Content-Type: application/download');
    header('Content-Disposition: attachment; filename='.
    "performance_data_".date("Ymd-His",$graph_times['start_unix'])."_to_".date("Ymd-His",$graph_times['stop_unix']).".csv;");
    break;
  } // switch
}

function performance_graphs_show(&$graphics, &$Interfaces, &$graph_times, $view_type, $only_aggr)
{
  global $Config, $Sanitizer;

  $images_real_path = $Config->get('images_real_path');
  $images_rel_path = $Config->get('images_rel_path');
  $aggregation = array();

  foreach ($graphics as $graphic)
  {
    $ret1=TRUE;
    $ret2=TRUE;
    //debug($graphic);
    
    if ($only_aggr!=1 && $view_type=='html') 
      echo tr_open();

    if ($view_type=='csv')
      $graph_data = 'get_graph_data'; 
    else 
      $graph_data = '';

    if ($graphic)
    {
      $graph_type_name = $graphic['graph1']['type'];
  
      // Generate the graphs
      if ($graphic['graph1'])
        $ret1 = performance_graph($graphic['id'],
          $images_real_path.'/'.$graphic['graph1']['image'],
          $graphic['graph1']['type'],
          $graphic['graph1']['sx'],$graphic['graph1']['sy'],
          $graphic['title'],$graph_times['start_unix'],$graphic['stop'],
          $graph_data);

      if (array_key_exists('graph2', $graphic))
        $ret2 = performance_graph($graphic['id'],
          $images_real_path.'/'.$graphic['graph2']['image'],
          $graphic['graph2']['type'],
          $graphic['graph2']['sx'],$graphic['graph2']['sy'],
          $graphic['title'],$graph_times['start_unix'],$graphic['stop']);
  
      // show the graphs
      if ($only_aggr !=1 )
        switch ($view_type)
        {
        case 'html':
          $link_url = $Sanitizer->get_url('', 'all', 
            array('interface_id'=>$graphic['id'],
            'graph_type'=>$graphic['graph_type']));
          if (($graphic['graph1']) && ($ret1!==false))
          {
            $image = image($images_rel_path.'/'.$graphic['graph1']['image'],
              0, 0, current(explode('  ',$graphic['title'])).' / '.SCALE_LEGEND);
            echo td(linktext($image, $link_url),
              'graph','',(!isset($graphic['graph2'])?'2':'1'));
          }
          if (array_key_exists('graph2', $graphic) &&
            ($graphic['graph2']) && ($ret2!==false))
          {
            $image = image($images_rel_path.'/'.$graphic['graph2']['image'],
              0, 0,$graphic['title'].' / '.SCALE_LEGEND);
            echo td(linktext($image, $link_url),'graph');
          }
          break;

        case 'graph':
          if (($graphic['graph1']) && ($ret1!==false) && ($want_graph==$graphic['graph1']['type'])) 
            echo join ('',file($images_real_path.'/'.$graphic['graph1']['image']));
          break;

        case 'csv':
          if (is_array($graph_data))
          {
            list ($original_values, $selected_values, $skip) = $graph_data;
            $ids = array_keys(current($original_values));
            if (is_numeric($graphic['percentile']))
            {
              $ordered = $selected_values;
              rsort($ordered);
              $percentile = round((($ordered[$skip]*8)/1024/1024),2);
            }
            echo
              "\"Raw Data for the period starting ".date("Y-m-d H:i:s",$graph_times['start_unix']).
              " and ending ".date("Y-m-d H:i:s",$graph_times['stop_unix'])."\"\r\n".
              "\"For Interface: ".current(explode("  ",$graphic["title"]))."\"\r\n".
              (is_numeric($graphic['percentile'])
                ?"\"".$graphic["percentile"]." Percentile for this period is ".$percentile." Mbits/s.\"\r\n"
                :"").
              "Measure,\"Input Bytes\",\"Output Bytes\"".
              (is_numeric($graphic["percentile"])
                ?",\"Highest Bytes\",\"Ordered Bytes\",\"Selected\",\"Bits/sec\",\"MBits/sec\""
                :"").
              "\r\n";
            foreach ($ids as $id)
              echo $id.','.$original_values['input'][$id].','.
              $original_values['output'][$id].
              (is_numeric($graphic["percentile"])    //if we're asked to calculate percentile
                ?",".$selected_values[$id].",".$ordered[$id].
                (($id==$skip)        //if this is the percentile value
                  ?",\"<-- Selected: ".(100-$graphic["percentile"])."% of ".count($ids)." = ".$id."\",".($ordered[$id]*8).
                    ",".$percentile.",\"Mbits/sec is the ".$graphic["percentile"]."th Percentile.\""
                  :'')
                :'').
              "\r\n";
          }
          break;
        }// switch view_type
      //aggregation
      if ($graphic['aggr']==1) //allow aggregation
      {
        $aggregation[$graph_type_name]['ids'][] = $graphic['id'];
        $aggregation[$graph_type_name]["description"] = $graphic["graph_description"];
        $aggregation[$graph_type_name]["type_id"] = $graphic["type_id"];
      }
    }
    if ($view_type=='html')
    {
      if ($only_aggr!=1)
        echo tag_close('tr');
      if ((!$graphic) || ($ret1===false) || ($ret2===false)) 
        performance_graph_error($graphic, $ret1, $ret2);
    }
    flush();
  }//foreach
  return $aggregation;
} //make_performance_graphs()

function performance_graphs_show_aggregation($view_type, $aggregation, &$Interfaces, &$graph_times, $want_graph, $only_aggr)
{
  global $Config, $Sanitizer;

  $images_real_path = $Config->get('images_real_path');
  $images_rel_path = $Config->get('images_rel_path');
  foreach ($aggregation as $agg_type=>$agg_data)
  {
    if ( count($agg_data['ids']) <= 1)
      continue;

    $image_title = $agg_data['description'].' Aggregation';
    $filename = 'aggr-'.uniqid('').'.png';

    $ret3 = performance_graph($agg_data['ids'],
          $images_real_path.'/'.$filename,
          $agg_type.'_aggregation',
          500, 175, $image_title,
          $graph_times['start_unix'], $graph_times['stop_unix'],'');

    if ($ret3) 
      switch ($view_type)
      {
      case 'html':
        $link_url = $Sanitizer->get_url('',
          array('only_aggr'=>1, 'type_id' => $agg_data['type_id']));
        table_row(linktext(image($images_rel_path.'/'.$filename,0,0,SCALE_LEGEND), $link_url),'graph',2);
        break;
        
      case 'graph':
        if (($want_graph=='aggr') || ($only_aggr==1))
          echo join ("",file($images_real_path."/$filename"));
        break;
      }
      flush();
  }//foreach
} // show_aggregation
function performance_graph_footer($view_type, $refresh_time)
{
  global $Sanitizer;

  if ($view_type != 'html')
    return;

  $url = $Sanitizer->get_url('','all');
  echo 
    table_close().
    javascript_refresh("if (self.no_refresh!=1) location.href=\"$url\";",$refresh_time); 
  adm_footer();
} //footer()

function performance_graph_times()
{
  global $Sanitizer;

  if ($Sanitizer->get_string('graph_time') == 'nopreset')
  {
    $graph_times = array(
      'preset'     => 0,
      'start'      => $Sanitizer->get_string('graph_time_start'),
      'stop'       => $Sanitizer->get_string('graph_time_stop'),
      'start_hour' => $Sanitizer->get_int('graph_time_start_hour'),
      'stop_hour'  => $Sanitizer->get_int('graph_time_stop_hour'));
    $graph_times['start_unix'] = strtotime($graph_times['start'])+
      $graph_times['start_hour'];
    $graph_times['stop_unix'] = strtotime($graph_times['stop']) +
      $graph_times['stop_hour'];
  } else {
    $graph_time_preset = $Sanitizer->get_int('graph_time_preset',-24*60*60);
    $graph_times = array(
      'preset'     => $graph_time_preset,
      'start_unix' => $graph_time_preset,
      'stop_unix'  => 0,
      'start'      => date('Y-m-d', (time() + $graph_time_preset)),
      'stop'       => date('Y-m-d', time() ));
    $graph_times['start_hour'] = round ((time() + $graph_times['start_unix'] - strtotime($graph_times['start']))/60) * 60; //get 1 minutes round
    $graph_times['stop_hour'] = round ((time() - strtotime($graph_times['stop']))/60) * 60;
  }
  return $graph_times;
}

function performance_graph_error($graphic, $ret1, $ret2)
{
    table_row("The RRDTool files for Interface ID ".$graphic["id"]." (from Host ID ".$graphic["host"]."), has not been created by the Poller Process yet","error",2);
}
?>
