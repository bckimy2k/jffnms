<?php
/* This file is part of JFFNMS
 * Copyright (C) <2002-2005> Javier Szyszlican <javier@szysz.com>
 * This program is licensed under the GNU GPL, full terms in the LICENSE file
 */

// Set the following to TRUE to not use a rrdtool pipe
define('RRDTOOL_NOT_USE_PIPE', FALSE);
// Set the following to the socket to use rrdcached, leave '' for no cached use
define ('RRDCACHED_SOCKET', '');
//define ('RRDCACHED_SOCKET', '/var/run/rrdcached.sock');

function rrdtool($method,$file,$opts,$force_no_module = 0)
{
  global $Config;

  $rrdtool_executable = $Config->get('rrdtool_executable');
  $os_type = $Config->get('os_type');

  $error=0;
  $ret = 1;

  if ($os_type == 'windows')
    if (is_array($opts))
      foreach ($opts as $key=>$data)
      {
        $opts[$key] = str_replace("'","\"",$opts[$key]);
        if ($key!="font")
          $opts[$key] = str_replace(":/","\:/",$opts[$key]); //font does not need this
      }
  
  if (is_array($opts))
    $aux = join($opts," ");
  else
    $aux = $opts;

  $use_pipe = FALSE;
    
  if ($os_type =='windows')
    $command = $rrdtool_executable." ".$method." \"".$file."\" ".$aux;
  else {
    $command = $rrdtool_executable." ".$method." '".$file."' ".$aux;

    //Check if we can use the new Piped/Managed RRDTool method
    if (RRDTOOL_NOT_USE_PIPE && RRDCACHED_SOCKET == '' && function_exists('proc_open') && strlen($command) < 10000)
      $use_pipe = TRUE;
  }
      
  if (RRDCACHED_SOCKET != '')
    $command .= ' --daemon '.RRDCACHED_SOCKET;

  //debug($command);
  $command .= ' 2>&1';
        
  if ($use_pipe)
    list ($ret1, $ret2) = rrd_pipe($method." '".$file."' ".$aux);
  else
    $ret3 = exec($command,$ret1,$ret2);
    
  //debug($ret1);
  //debug($ret2);
            
  if ($method=='fetch')
  {
    $start=0;
    reset ($ret1);
    $ret = false;
    
    if (count($ret1) > 1)
      foreach($ret1 as $key => $line)
        if (!empty($line))
        {
          $line1 = explode(' ',$line);
          if (strpos($line,":") > 0)
          {
            for ($i = 1; $i < count($line1); $i++)      // Part 1 is timestamp
              if ($line1[$i]!=="")           // Part 2 maybe is a space
                $value[] = ($line1[$i]=="nan")?0:str2f($line1[$i]); // Part 3 (or 2) has the data
            $date = (int)$line1[0];          // Time Stamp
            if (!isset($stop)) $stop=0;
            if ($date > $stop) $stop = $date;
            if (($date < $start) or ($start==0)) $start = $date;
          } else { 
            foreach ($line1 as $aux) 
              if (!empty($aux) && ($aux!="timestamp"))     // Avoid the timestamp as a DS (rrdtool >= 1.0.49)
                $ds_namv[]=trim($aux);
            $ds_cnt = count($ds_namv);
          }
          unset($ret1[$key]);
        }
    $ret = array("start"=>$start,"end"=>$stop,"step"=>300,"ds_cnt"=>$ds_cnt,"ds_namv"=>$ds_namv,"data"=>$value);
      //var_dump($ret);
  }
        
  $info = '';
  if (file_exists($file)==TRUE)      
    switch ($method)
    {
    case 'graph':
      if (count($ret1) < 1 )
      {
        $error=1;
        $ret = $ret1;
        $info = $command;
      } else
        $ret = $ret1;
      break;
    case 'update':
    case 'create':
    case 'fetch':
    case 'tune':
      if ($ret2 != 0 )
      {
        $error=1; $ret = 0;
        $info .+ $ret3;
      }
      break;
    case 'last':
      if ($ret2 != 0)
      {
        $error=1; $ret = 0;
      } else
        $ret = $ret1[0];
      break;
    }//switc
  else {
    $error=1; $ret = 0;
  }
  
  if ($error==1) {
      if (array_key_exists('Child', $GLOBALS)) {
          $GLOBALS['Child']->send_error("rrd_$method(): $ret3");
      } else
          echo("rrd_$method() ERROR: $info");
  }
  return $ret;
}

    function rrdtool_graph($file,$opts) {
  return rrdtool("graph",$file,$opts);
    }

function rrdtool_tune($file,$values) {
    if (file_exists($file)===TRUE) {
        if (function_exists('rrd_tune')) {
            $opts = array($values);
            if (RRDCACHED_SOCKET != '')
                $opts[] = ' --daemon '.RRDCACHED_SOCKET;
            return rrd_tune($file, $opts);
        } else {
            return (rrdtool('tune',$file,$values) == 1);
        }
    }
    return FALSE;
}

    function rrdtool_fetch($file,$opts) {
  return rrdtool("fetch",$file,$opts);
    }

function rrdtool_last($file) {
    if (function_exists('rrd_last')) {
        return rrd_last($file);
    } else {
        return (rrdtool('last',$file,NULL) == 1);
    }
}

/*
 * rrdtool_update - wrapper for rrd_update
 * Return TRUE on success, 0 on failure
 */
function rrdtool_update($file,$values) {
    $opts = array($values);
    if (function_exists('rrd_update')) {
        if (RRDCACHED_SOCKET != '')
            $opts[] = ' --daemon '.RRDCACHED_SOCKET;
        return rrd_update($file, $opts);
    }
    return (rrdtool('update',$file,$values) == 1);
}

function rrdtool_create($file,$opts)
{
  global $Config;

  if ($result = rrdtool('create', $file, $opts) && ($Config->get('os_type')=='unix')) 
    chmod ($file, 0660);
  return $result;
}

function rrdtool_dump($file,$to)
{
  return rrdtool('dump',$file," > $to");
}

function rrdtool_restore($file,$from)
{
  return rrdtool('restore',$from,$file);
}

function rrdtool_error($info=NULL)
{
    if (function_exists('rrd_error'))
        return rrd_error();
    return $info;
}

function rrd_grapher($id, $graph_function, $sizex, $sizey, $title, $graph_time_start, $graph_time_stop, $aux = '')
{
  global $Interfaces, $Config;

  if (!$graph_function || !$id)
    return FALSE;

  if (!isset($Interfaces))
      $Interfaces = new JffnmsInterfaces();

  $interface_data = $Interfaces->get_all($id);
  $rrd_path = $Config->get('rrd_real_path');
      
  foreach ($interface_data as $int_id=>$aux1) 
    $interface_data[$int_id]['filename'] = $rrd_path."/interface-$int_id.rrd";
      
  if (count($interface_data)==1)
    $interface_data=current($interface_data);
  $function_data=$interface_data;
  $function_data["id"]=$id;

  $title = str_replace("'",'',$title);  // ' fix

  $start = $graph_time_start;
  $end = $graph_time_stop;

  if ($end==0) $end -= (7*60);  //if it was 0, then substract 7 minutes to be sure there's data

  //debug ($graph_time_stop." = ".date("Y-m-d H:i:s", $graph_time_stop));
  //debug ($end." = ".date("Y-m-d H:i:s", $end));
      
  //This is only used for N Percentile calculation
  if ($aux == 'get_graph_data')      //if we were asked for the extra data (Export to CSV data points)
  $function_data['other_data'] = $aux;  //pass the request to the graph (traffic.inc.php)

  $function_data['rrd_grapher_time_start'] = $start;
  $function_data['rrd_grapher_time_stop'] = $end;
      
  $opts_header = array( 
    '--imgformat=PNG',
    '--start='.$start,
    '--end='.$end,
    '--base=1000',
    '--lower-limit=1',
    '--title=\''.$title.'\'',
    '--alt-autoscale-max', 
    '--color=GRID#CCCCCC',
    '--color=MGRID#777777',
    '--height='.$sizey,
    '--width='.$sizex
  );

  $real_function = 'graph_'.$graph_function;
  $function_file = $Config->get('jffnms_real_path').'/engine/graphs/'.$graph_function.'.inc.php';

  if (in_array($function_file,get_included_files()) || (file_exists($function_file) &&  (include_once($function_file))) )
  {
    if (!function_exists($real_function))
    {
      logger("ERROR: Calling Function '$real_function' doesn't exists in '$function_file'.<br>\n");
      return FALSE;
    } else {
      $graph_plugin_ret = call_user_func_array($real_function,array($function_data));
      $graph_header = $graph_plugin_ret[0];
      $graph_definition = $graph_plugin_ret[1];
      if (array_key_exists(2, $graph_plugin_ret))
        $other_data = $graph_plugin_ret[2];
      else
        $other_data = '';
    }
  } else {
    logger ("ERROR Loading file $function_file.<br>\n");
    return FALSE;
  }
  
  $rrdtool_font = $Config->get('rrdtool_font');
  if (is_readable($rrdtool_font))
    $opts_header['font'] = "--font=DEFAULT:7:$rrdtool_font";
  if (is_array($graph_definition))
    foreach ($graph_definition as $k=>$v) 
      if (strpos($v, "COMMENT")!==false) //RRDTool 1.2.x needs the : (colons) inside COMMENTs to be escaped with \ as \:
        $graph_definition[$k] = substr($v, 0, strpos($v, ":")+1). str_replace(":","\:",substr($v, strpos($v, ":")+1));
      
  $opts = @array_merge($opts_header,
    (($aux=="MINI") ?array("--no-legend", '') :$graph_header),
    $graph_definition);

  // Clear out empty opts array items, because it causes problems with some RRDTools
  if (is_array($opts))
    foreach ($opts as $k=>$v)
      if (empty($v))
        unset ($opts[$k]);
      
  $graph_filename = $Config->get('engine_temp_path').'/'.uniqid('').'.dat';
  $ret = rrdtool_graph($graph_filename, $opts);
      
  if (($aux!="MINI") && ($ret!=FALSE))
    rrdtool_add_legend($graph_filename, $sizey);

  if ($aux != 'get_graph_data')   //if we were not asked for the extra data (currently 95 percentile data points)
  unset ($other_data);    // do not return it.
  $other_data = FALSE;

  if (!file_exists($graph_filename))
  {
    logger ("ERROR Graph file '$graph_filename' does not exist.<br>\n");
    return FALSE;
  }
  $fp = fopen($graph_filename,"rb");
  $graph_data = fread($fp,filesize($graph_filename));
  fclose($fp);
  unlink ($graph_filename); //delete file
  $graph_data = base64_encode ($graph_data);
  return array($ret, $graph_data, $other_data);
} //rrd_grapher()
    
function rrdtool_add_legend ($file, $sizey)
{
  global $Config;

  if (! ($im = ImageCreateFromPNG($file)))
    return FALSE;

  $legend = $Config->get('jffnms_site');
  $legend1 = 'JFFNMS';
  $posy = $sizey+25;

  ImageStringUP($im,3,imagesx($im)-26,$posy,$legend ,ImageColorAllocate($im,150,150,150));
  ImageStringUP($im,3,imagesx($im)-16,$posy,$legend1,ImageColorAllocate($im,100,100,100));
      
  ImagePNG($im,$file);
  ImageDestroy($im);
}

function rrdtool_fetch_ds ($id,$dsnames,$from,$to)
{
    global $Config;
    $Interfaces = new JffnmsInterfaces();
  if (!is_array($dsnames)) $dsnames = array($dsnames);
  
  $file = $Config->get("rrd_real_path")."/interface-$id.rrd";
  $interface=current($Interfaces->get_all($id));

  $type_info = $Interfaces->get_type_info($id);
  $all_dss = $Interfaces->parse_rrd_structure($type_info["rrd_structure_def"]);
  $rra = rrdtool_parse_rra($type_info["rrd_structure_rra"]);

  $opts = array ( $rra, "--start=$from", "--end=$to" );

  if ( ($interface["rrd_mode"] == 1) && (file_exists($file)===TRUE) ) {
    $ret = rrdtool_fetch($file, $opts);
    for ($i = 0; $i < count($ret["data"]);$i+=$ret["ds_cnt"]) 
        foreach ($dsnames as $dsname)
      if (strlen($all_dss[$dsname]) > 0)
          $values[$dsname][]=round((int)($ret[data][$i+$all_dss[$dsname]]),2);
  }
  
  if ($interface["rrd_mode"]==2) {
      foreach ($dsnames as $dsname) {
    $dsn = $all_dss[$dsname]; 
    $file_ds = str_replace(".rrd","-$dsn.rrd",$file);
    if (file_exists($file_ds)===TRUE) {
        $ret = rrdtool_fetch($file_ds, $opts);
        for ($i = 0; $i < count($ret["data"])-1;$i+=$ret["ds_cnt"])
      if (isset($ret["data"][$i]))
          $values[$dsname][]=round((int)($ret["data"][$i]),2);
    }
      }      
  }
  
  if (is_array($values)) {
      $values["information"]["start"]=$ret["start"];
      $values["information"]["stop"]=$ret["end"];
  }

  return $values;
    }

    //FIXME this is too specific for Physical Interfaces
function get_rrd_rtt_pl ($id,$from,$to,$threshold,$bwin_db,$bwout_db,$flipinout)
{
    $values = rrdtool_fetch_ds($id,array('input','output','rtt','packetloss','bandwidthin','bandwidthout'),$from,$to);
    $result=array();
    
    $rtt_final_avg = 0;
    $pl_final_avg = 0;
    $cant_pings=50;
 
    if ( is_array($values) ) {
        if ($flipinout==1) { //flip in/out
            $tmp = $values['input'];
            $values['input']=$values['output'];
            $values['output']=$tmp;
            unset($tmp);
        }
        $values_count = count ($values['input']);
        for ($i = 0; $i < $values_count ; $i++) {
            $in = $values['input'][$i]*8; //convert to bits (same as bandwidth)
            $out = $values['output'][$i]*8; //convert to bits (same as bandwidth)
            $rtt = $values['rtt'][$i];
            $pl_orig = $values['packetloss'][$i]; //0 - 50
            $pl = round($pl_orig*(100/$cant_pings));
            $bwin_rrd = $values['bandwidthin'][$i]*8;
            $bwout_rrd = $values['bandwidthout'][$i]*8;
            if ($rtt > 100000)
                $rtt = 0; // if data was not valid (100sec RTT is not posible)
            //if we have bandwidth ds's then use then, otherwise take the parameters.        
            $bwin=($bwin_rrd>0?$bwin_rrd:$bwin_db);
            $bwout=($bwout_rrd>0?$bwout_rrd:$bwout_db);
        
            //if is lower than the max threshold
            if ( ($rtt > 0) and ($in >0) and ($out > 0) ) { //valid values
                $parsed++;
                if (($in < ($threshold*$bwin/100)) and ($out < ($threshold*$bwout/100))) { //in threshold
                    $result[]=Array('rtt'=>$rtt,'pl'=>$pl);
                    $counted=1;
                } else 
                    $counted = 0;
            }
            //debug("$id \t $in \t $out \t $rtt \t $pl \t ($bwin($bwin_rrd) - $bwout($bwout_rrd) - $threshold) \t $counted");
    
            unset($values['input'][$i]);
            unset($values['output'][$i]);
            unset($values['rtt'][$i]);
            unset($values['packetloss'][$i]);
        }
        if (count($result) > 0) {
            foreach($result as $key => $value) {
                $rtt_final += $value["rtt"];
                $pl_final += $value["pl"];
                unset ($result[$key]);
            }
            $rtt_final_avg = round($rtt_final/$cant);
            $pl_final_avg = round($pl_final/$cant,2);
        }
    }

    //debug ("Total in Result: $values_count");
    //debug ("Total Parsed: $parsed");
    //debug ("Total in Threshold: $cant");
    return array('id'=>$id, 'rtt'=>$rtt_final_avg, 'pl'=>$pl_final_avg);
}

function rrdtool_parse_rra($info) {
    $aux = explode (':',$info);
    return $aux[1];
}

function rrdtool_get_def($interface,$dss)
{
  global $Interfaces;

  $dss_def = array();

  if (!is_array($dss)) $dss = array($dss);
  $id = $interface['id'];
  $type_info = $Interfaces->get_type_info($id);

  $all_dss = $Interfaces->parse_rrd_structure($type_info['rrd_structure_def']);
  $rra = rrdtool_parse_rra($type_info['rrd_structure_rra']);

  foreach ($dss as $dsname => $ds)
  {
    if (is_integer($dsname)) $dsname = $ds;
    $dsn = $all_dss[$ds];
      
    if ($dsn!==NULL) //if DS exists 
    {
      $filename_ds = str_replace(".rrd","-$dsn.rrd",$interface["filename"]);

      if ($interface["rrd_mode"]==1)
        $dss_def[$dsname]="DEF:$dsname=".$interface["filename"].":$ds:$rra";
      if ($interface["rrd_mode"]==2)
      {
        if (!file_exists($filename_ds))
        {
          echo html('p', "Error: RRD file \"$filename_ds\" does not exist.", 'generic_error');
          return $dss_def;
        }
        if (!is_readable($filename_ds))
        {
          echo html('p', "Error: RRD file \"$filename_ds\" exists, but is not readable.", 'generic_error');
          return $dss_def;
        }
        $dss_def[$dsname]="DEF:$dsname=$filename_ds:data:$rra";
      }
    }
  }
  return $dss_def;
}

    function rrdtool_create_file($filename,$def,$rra,$res,$step) {

  $step = "--step $step"; 

  //replace data

  $opts = trim("$step $def $rra");
  $opts = str_replace("\n","", $opts);
  $opts = str_replace("  "," ",$opts);
        $opts = str_replace("<resolution>",$res,$opts);
  $ret = rrdtool_create($filename, explode(" ",$opts));
        return $ret;
    }

function rrd_pipe ($command)
{
  if (!is_array($GLOBALS['rrd_pipe'])) {
    $GLOBALS['rrd_pipe']['resource'] = 
      proc_open($Config->get('rrdtool_executable'). ' -', 
        array(0=> array('pipe','r'), 1=>array('pipe','w')), $GLOBALS['rrd_pipe']['pipe']);
    $GLOBALS['gc_save_vars']['rrd_pipe']=true;
  }
  
  fwrite($GLOBALS['rrd_pipe']['pipe'][0], $command."\n");
  
  stream_set_blocking($GLOBALS['rrd_pipe']['pipe'][1], false);

  while (($ret1!==false) || empty($ret))
  {
    usleep(10);
    $ret1 = fgets($GLOBALS['rrd_pipe']['pipe'][1]);

    if ($ret1!=false) $ret[] = rtrim($ret1,"\n");
  }
  
  unset ($ret[count($ret)-1]); 
  
  //echo "<PRE>RRD: ".substr($command,0)." => ".vd($ret)."</PRE>\n";
  return array($ret, 1);
}  
?>
