<?php
/* This file is part of JFFNMS
 * Copyright (C) <2002-2005> Javier Szyszlican <javier@szysz.com>
 * This program is licensed under the GNU GPL, full terms in the LICENSE file
 */
function graph_ibm_blade_cpu_temp($data) { 

    $opts_DEF = rrdtool_get_def($data,array("temperature2","temperature"));

    $opts_GRAPH = array( 		    
		    
        "LINE2:temperature2#00CC00:'Cpu2 Temperature'",
        "GPRINT:temperature2:MAX:'Max\:%5.0lf%s°'",
        "GPRINT:temperature2:AVERAGE:'Average\:%5.0lf%s°'",
        "GPRINT:temperature2:LAST:'Last\:%5.0lf%s°\\n'",

        "LINE2:temperature#0000FF:'Cpu1 Temperature'",
        "GPRINT:temperature:MAX:'Max\:%5.0lf%s°'",
        "GPRINT:temperature:AVERAGE:'Average\:%5.0lf%s°'",
        "GPRINT:temperature:LAST:'Last\:%5.0lf%s°'"
    );

    $opts_header[] = "--vertical-label='Degres Centigrade'";

    return array ($opts_header, @array_merge($opts_DEF,$opts_GRAPH));    
}

?>
