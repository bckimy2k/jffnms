<?php
/* This file is part of JFFNMS
 * Copyright (C) <2002-2005> Javier Szyszlican <javier@szysz.com>
 * This program is licensed under the GNU GPL, full terms in the LICENSE file
 */
{
    require('../auth.php');

    // From this form
    $filter_date_start = $Sanitizer->get_string('filter_date_start');
    $date_start = $Sanitizer->get_string('date_start');
    $date_start_hour = $Sanitizer->get_string('date_start_hour');
    $filter_date_stop = $Sanitizer->get_string('filter_date_stop');
    $date_stop = $Sanitizer->get_string('date_stop');
    $date_stop_hour = $Sanitizer->get_string('date_stop_hour');
    $filter_zone = $Sanitizer->get_string('filter_zone');
    $zone_id = $Sanitizer->get_string('zone_id');
    $filter_host = $Sanitizer->get_string('filter_host');
    $host_id = $Sanitizer->get_string('host_id');
    $filter_type = $Sanitizer->get_string('filter_type');
    $type_id = $Sanitizer->get_string('type_id');
    $filter_severity = $Sanitizer->get_string('filter_severity');
    $filter_interfacename = $Sanitizer->get_string('filter_interfacename');
    $interfacename = $Sanitizer->get_string('interfacename');
    $filter_username = $Sanitizer->get_string('filter_username');
    $username= $Sanitizer->get_string('username');
    $filter_info = $Sanitizer->get_string('filter_info');
    $info = $Sanitizer->get_string('info');
    $filter_ack = $Sanitizer->get_string('filter_ack');
    $ack = $Sanitizer->get_string('ack');
    $viewtype = $Sanitizer->get_string('viewtype');

    $express_filter = $Sanitizer->get_string('express_filter');
    $filter = $Sanitizer->get_int('filter');
    $order_type = $Sanitizer->get_string('order_type');
    $view_type = $Sanitizer->get_string('view_type','same');
    $refresh=0;
    adm_header("Event Filter");

    $filtered = (profile("MAP") || profile("CUSTOMER"))?true:false;

    if ($express_filter !== FALSE) {
        $filter_array = explode('^',$express_filter);
        for ( $i=0 ; $i < count($filter_array) ; $i++ ) {
            if ($filter_array[$i]) { 
                $filter_array_items = explode (",",$filter_array[$i]);
                $filter_row = $filter_array_items[0];
                $filter_value = $filter_array_items[1];
                $filter_oper = parse_radio_id_inverse($filter_array_items[2]);
                
                //Express Filter Fields
                switch($filter_row)
                {
                case 'date':
                    $filter_date_stop = $filter_oper;
                    $date_stop = $filter_value;
                    $date_stop_hour = (60*60*24);
                    break;
                case 'date_start':
                    $filter_date_start = $filter_oper;
                    $date_start = substr($filter_value,0,10);
                    $date_start_hour = substr($filter_value,11,19);
                    break;
                case 'date_stop':
                    $filter_date_stop = $filter_oper;
                    $date_stop = substr($filter_value,0,10);
                    $date_stop_hour = substr($filter_value,11,19);
                    break;
                case 'host':
                    $filter_host = $filter_oper;
                    $host_id = $filter_value;
                    break;
                case 'zone':
                    $filter_zone = $filter_oper;
                    $zone_id = $filter_value;
                    break;
                case 'type':
                    $filter_type = $filter_oper;
                    $type_id = $filter_value;
                    break;
                case 'username':
                    $filter_username = $filter_oper;
                    $username = $filter_value;
                    break;
                case 'filter_severity':
                    $filter_severity = $filter_oper;
                    $severity_id = $filter_value;
                    break;
                case 'interface':
                    $filter_interfacename = $filter_oper;
                    $interfacename = $filter_value;
                    break;
                case 'info':
                    $filter_info = $filter_oper;
                    $info = $filter_value;
                    break;
                case 'ack':
                    $filter_ack = $filter_oper;
                    $ack = $filter_value;
                    break;
                case 'types':
                    $filter_type = $filter_oper;
                    $types_filter = explode("!",$filter_value);
                    foreach ($types_filter as $type)
                        if ($type)
                            $type_id[] = $type;
                }// switch
            }
        }
    }//express filter!=false
    $filters = array(
        'filter_date_start'=>array(
            'name'=>'Date Start',
            'values'=>select_date('date_start',$date_start,7,true,$date_start_hour,'')),

        'filter_date_stop'=>array(
            'name'=>'Date Stop',
            'values'=>select_date('date_stop',$date_stop,7,true,$date_stop_hour,'')),

        'filter_zone'=>array(
            'name'=>'Zone', 'hide_on_filter'=>true,
            'values'=>select_zones('zone_id',$zone_id)),

        'filter_host'=>array(
            'name'=>'Host', 'hide_on_filter'=>true,
            'values'=>select_hosts('host_id',$host_id)),

        'filter_type'=>array(
            'name'=>'Event Types',
            'values'=>select_event_types('type_id',$type_id,4,NULL,'',array(NULL,array('show_unknown'=>1)))),

        'filter_severity'=>array(
            'name'=>'Severity',
            'values'=>select_severity('severity_id',$severity_id,1)),

        'filter_interfacename'=>array(
            'name'=>'Interface Name',
            'values'=>textbox('interfacename',$interfacename,30)),

        'filter_username'=>array(
            'name'=>'Username', 'hide_on_filter'=>true,
            'values'=>textbox('username',$username,30)),

        'filter_info'=>array(
            'name'=>'Extra Info '.br().'(also Tacacs+ Commands)', 'hide_on_filter'=>true,
            'values'=>textbox('info',$info,30)),

        'filter_ack'=>array(
            'name'=>'Acknowledged Event',
            'values'=>hidden('ack',1).'&nbsp;')
        );
    echo 
        table("events_filter").
        form().
        table_row("Event Filter","title", 5, "", false).
        tr_open("headers").
        td("Field").
        td("Yes").td("Not").td("Don't").
        td("Values").
        tag_close("tr").
        tag("tbody","filters");
    foreach ($filters as $key=>$data) 
        if (is_array($data) && (($filtered==false) || ($data["hide_on_filter"]!==true)))
            echo
                tr_open().
                td($data["name"], "field").
                filter_radio($key,$GLOBALS[$key]).
                td($data["values"], "value").
                tag_close("tr");
    echo
        tag_close("tbody").
        table_row(    
            radiobutton("viewtype",(($viewtype=="same")?1:0),"same")." Same Window ".
            radiobutton("viewtype",(($viewtype=="same")?0:1),"new")." New Window "
                ,"view_type",5,"",false).

       hidden("filter",1).
        hidden("order_type",$order_type).
        table_row(    
            adm_form_submit("Filter Events").
            "&nbsp;&nbsp;".
            control_button("Close","", "javascript: window.close();", "logoff.png") ,"submit",5,"",false).

        form_close().
        table_close();
    adm_footer();

    if ($filter==1) {
        if ($filter_date_start>0) {
            $aux = date("Y-m-d H:i:s",strtotime($date_start)+$date_start_hour);
            $filter_url.="^date_start,$aux,".parse_radio_id($filter_date_start);
        }
        if ($filter_date_stop>0) {
            $aux = date("Y-m-d H:i:s",strtotime($date_stop)+$date_stop_hour);
            $filter_url.="^date_stop,$aux,".parse_radio_id($filter_date_stop);
        }
        if (($filter_severity>0) && ($severity_id))
            $filter_url.="^severity,$severity_id,".parse_radio_id($filter_severity);
        if (($filter_zone>0) && ($zone_id))
            $filter_url.="^zone,$zone_id,".parse_radio_id($filter_zone);
        if (($filter_host>0) && ($host_id))
            $filter_url.="^host,$host_id,".parse_radio_id($filter_host);
        if (($filter_type>0) && (is_array($type_id))) {
            $filter_url.="^types,";
            foreach ($type_id as $id) $filter_url.="$id!";
                $filter_url.=",".parse_radio_id($filter_type);
        }
        if (($filter_interfacename>0) && ($interfacename))
            $filter_url.="^interface,$interfacename,".parse_radio_id($filter_interfacename);
        if (($filter_username>0) && ($username))
            $filter_url.="^username,$username,".parse_radio_id($filter_username);
        if (($filter_info>0) && ($info))
            $filter_url.="^info,$info,".parse_radio_id($filter_info);

        if (($filter_ack>0) && ($ack))
            $filter_url.="^ack,$ack,".parse_radio_id($filter_ack);

        $url = $Config->get('jffnms_rel_path')."/events.php?span=25&refresh=0&express_filter=".$filter_url."&order_type=".$order_type;

        echo script("
            if (document.forms[0].viewtype[0].checked) 
                opener.location.href='$url'; 
            else 
                window.open('$url'); ");
    }
}//main

function filter_radio ($field,$data)
{
    if ($data==0) $data0=1;
    if ($data==1) $data1=1;
    if ($data==2) $data2=1;
    return 
         td(radiobutton($field,$data1,1,0), "radio").
         td(radiobutton($field,$data2,2,0), "radio").
         td(radiobutton($field,$data0,0,0), "radio");
}

function parse_radio_id($value) {
    if ($value==1) return "=";
    if ($value==2) return "!=";
}    

function parse_radio_id_inverse($value) {
    if ($value=="") return 0;
    if ($value=="=") return 1;
    if ($value=="!=") return 2;
}    

?>
