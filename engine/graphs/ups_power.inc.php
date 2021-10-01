<?php
/* UPS Voltage Graph. This file is part of JFFNMS
 * Copyright (C) <2005> Javier Szyszlican <javier@szysz.com>
 * This program is licensed under the GNU GPL, full terms in the LICENSE file
 */

function graph_ups_power ($data) {

    $opts_DEF = rrdtool_get_def($data,array("power"));

    $opts_GRAPH = array(
        "AREA:power#0033AA:'Watts '",
        "GPRINT:power:MAX:'Max\: %3.0lf %sWatts'",
        "GPRINT:power:AVERAGE:'Average\: %3.0lf %sWatts'",
        "GPRINT:power:LAST:'Last\: %3.0lf %sWatts\\n'",
    );

    $opts_header[] = "--vertical-label='Watts'";
    return array ($opts_header, @array_merge($opts_DEF,$opts_GRAPH));    
}

?>
