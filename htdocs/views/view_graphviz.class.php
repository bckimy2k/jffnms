<?php
/* This file is part of JFFNMS
 * Copyright (C) <2002-2005> Javier Szyszlican <javier@szysz.com>
 * Copyright (C) 2010 Craig Small <csmall@enc.com.au>
 * This program is licensed under the GNU GPL, full terms in the LICENSE file
 */

require_once('toolbox.inc.php');
require_once('view_base.class.php');

class View_graphviz extends View_base
{
  private $graphviz_text = '';
  private $graphviz_hosts = '';
  private $graphviz_nodes = '';
  private $graphviz_cnx = '';

  function __construct()
  {
    parent::__construct();
    $this->graphviz_text = "strict graph map {
  bgcolor=\"#$this->map_color\";
  margin=-0.03;
  ratio=fill;
  size=\"10.2,5.3\";
        overlap=false;
  node [fontsize=50];
  edge [len=3];
  "; 
  }

  function break_by_host(&$item)
  {
    if ($item['host'] > 1)
      $this->graphviz_hosts .="\tH$item[host]\t[shape=egg,fillcolor=\"#000000\",fontcolor=\"#FFFFFF\",style=filled,label=\"$item[host_name] $item[zone_shortname]\"];\n";
  } //break_by_host()

  function interface_show(&$item, $bgcolor, $fgcolor, $mark_interface, $urls)
  {
    global $Config, $Source;
    if ($item['id'] <= 1)
      return FALSE;
    $this->graphviz_nodes .="\tI$item[id]\t[shape=box,fillcolor=\"#$bgcolor\",fontcolor=\"#$fgcolor\",style=filled,label=\"$item[int_sname]\",URL=\"".
      str_replace(" ","+",$Config->get('jffnms_rel_path')."/".
      $urls['events'][1])."\"];\n";
    if ($Source->source_type == 'interfaces')
      $this->graphviz_cnx .="\tH$item[host] -- I$item[id] \t;\n";
    return TRUE;
  } // interface_show()

  function finish()
  {
    global $Config;
    if (empty($this->graphviz_hosts))
      return;
    $neato_executable = $Config->get('neato_executable');
    if (!is_executable($neato_executable))
    {
      echo "<pre>Unable to execute neato program \"$neato_executable\".<pre>\n";
      return;
    }
    
    $this->graphviz_text .="\n $this->graphviz_hosts $this->graphviz_nodes \n $this->graphviz_cnx";
    $this->graphviz_text .="}\n"; 
  
    $graphviz_filename = uniqid('');
    $graphviz_name = $Config->get('images_real_path')."/$graphviz_filename";
    if ( ($graphviz_file = fopen($graphviz_name.'.dot','w+')) === FALSE)  
    {
      echo "<pre>Unable to open .dot graphviz file \"$graphviz_name\".</pre>\n";
      return;
    }
    fwrite($graphviz_file,$this->graphviz_text ,strlen($this->graphviz_text));
    fclose($graphviz_file);

    $graphviz_exec = "$neato_executable -Timap -o $graphviz_name.map $graphviz_name.dot";
    $result_exec = exec($graphviz_exec);

    //echo "<PRE>".join(" ",file("$graphviz_name.dot"))."</PRE>";
  
    $graphviz_exec = "$neato_executable -Tpng -o $graphviz_name.png $graphviz_name.dot";
    //echo $graphviz_exec;
    $result_exec = exec($graphviz_exec);
    $images_rel_path = $Config->get('images_rel_path');
    $map_html = "<td align='center'><a target=\"events\" href=\"$images_rel_path/$graphviz_filename.map\"><img src=\"$images_rel_path/$graphviz_filename.png\" border=0 ISMAP></a></td>\n";
    echo $map_html;  
  } //finish()
}
