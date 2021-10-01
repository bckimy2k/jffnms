<?php
/* Cisco 80211.X associated Graph. This file is part of JFFNMS
 * Copyright (C) <2007> David LIMA <dlima@fr.scc.com>
 * This program is licensed under the GNU GPL, full terms in the LICENSE file
 */

function graph_cisco_80211X_associated($data) {

    $opts_DEF = rrdtool_get_def($data,array("associated"));

    $opts_GRAPH = array(
        "AREA:associated#0033FF:'Associated clients '",
        "GPRINT:associated:MAX:'Max\: %3.0lf '",
        "GPRINT:associated:AVERAGE:'Average\: %3.0lf '",
        "GPRINT:associated:LAST:'Last\: %3.0lf \\n'",
    );

    $opts_header[] = "--vertical-label='Cisco 802.11X clients'";
    $opts_header[] = "--rigid";

    return array ($opts_header, @array_merge($opts_DEF,$opts_GRAPH));    
}

?>
