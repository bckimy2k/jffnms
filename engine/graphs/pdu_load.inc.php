<?php
/* UPS load Graph. This file is part of JFFNMS
 * Copyright (C) <2005> Javier Szyszlican <javier@szysz.com>
 * This program is licensed under the GNU GPL, full terms in the LICENSE file
 * Chris Wopat - me@falz.net Jan 2006
 */

function graph_pdu_load ($data) {

    $opts_DEF = rrdtool_get_def($data,array("load"));

    $powerrating=$data['powerrating'];
    $threshold=$data['threshold'];

    // the power rating is always 80% of the actual limit, so have the size of the graph
    // be the full limit
    $limit = $powerrating * 1.2;

    $opts_GRAPH = array(
	"HRULE:".$powerrating."#FF0000:'Maximum \: ".$powerrating." Amps\\n'",
	"HRULE:".$threshold."#FF9900:'Threshold \: ".$threshold." Amps\\n'",

	"AREA:load#0033FF:'Load '",
	"GPRINT:load:MAX:'Max\: %3.1lf %sAmps'",
	"GPRINT:load:AVERAGE:'Average\: %3.1lf %sAmps'",
	"GPRINT:load:LAST:'Last\: %3.1lf %sAmps\\n'",
    );

    $opts_header[] = "--vertical-label='Amps'";
    $opts_header[] = "--rigid";
    $opts_header[] = "--upper-limit=".$powerrating;

    return array ($opts_header, @array_merge($opts_DEF,$opts_GRAPH));    
}

?>
