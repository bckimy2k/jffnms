<?php
/* OS/400 CPU Utilization Graph. This file is part of JFFNMS.
 * Copyright (C) <2002-2006> David LIMA  <dlima@fr.scc.com>
 * This program is licensed under the GNU GPL, full terms in the LICENSE file
 */

function graph_cpu_os400 ($data) {

    $opts_DEF = rrdtool_get_def($data,"cpu400");
    $opts_DEF[]="CDEF:cpu400real=cpu400,100,/";

    $limit = 100;

    $opts_GRAPH = array(

        "HRULE:".$limit."#FF0000:",

        "AREA:cpu400real#00CC00:'CPU Utilization '",
        "LINE1:cpu400real#0000FF:''",

        "GPRINT:cpu400real:MAX:'Max\:%8.2lf %%'",
        "GPRINT:cpu400real:AVERAGE:'Average\:%8.2lf %%'",
        "GPRINT:cpu400real:LAST:'Last\:%8.2lf %%'"
    );

    $opts_header[] = "--vertical-label='CPU Utilization %'";
    $opts_header[] = "--rigid";
    $opts_header[] = "--upper-limit=".$limit;

    return array ($opts_header, @array_merge($opts_DEF,$opts_GRAPH));

}

?>
