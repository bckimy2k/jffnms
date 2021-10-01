<?php
/* Dell OpenManage Temperature. This file is part of JFFNMS
 * Copyright (C) 2008  blentz at users.sourceforge.net
 * This program is licensed under the GNU GPL, full terms in the LICENSE file
 */
function graph_dell_om_temp ($data) { 
    
    $opts_DEF = rrdtool_get_def($data,array("dell_om_temp"));
   
    $far = (isset($data["show_celcius"]) && ($data["show_celcius"]==0))?1:0;

    $opts_GRAPH = array(
        "CDEF:temperature=dell_om_temp,".(($far==1)?"0.18,*,32,+":"10,/"),
        "AREA:temperature#FF0000:'Temperature in degrees ".(($far==1)?"Fahrenheit":"Celcius")."\:'",
        "GPRINT:temperature:MAX:'Max\:%5.0lf'",
        "GPRINT:temperature:AVERAGE:'Average\:%5.0lf'",
        "GPRINT:temperature:LAST:'Last\:%5.0lf \\n'"
    );

    $opts_header[] = "--vertical-label='Temperature'";

    return array ($opts_header, @array_merge($opts_DEF,$opts_GRAPH));    
}

?>
