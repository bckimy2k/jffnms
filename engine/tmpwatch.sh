#!/bin/sh

basedir=$1;
hours="24";
dirs="$basedir/../htdocs/images/temp $basedir/temp $basedir/../logs";


tmpwatch="tmpwatch" #default location
tmpwatch_locations="/usr/sbin/tmpwatch /usr/local/sbin/tmpwatch";

for AUX in $tmpwatch_locations; do
	if [ -f $AUX ]; then
		tmpwatch=$AUX
	fi
done;

tmpwatch_cmd="$tmpwatch -c -f $hours";

for DIR in $dirs
do
    echo -n "Cleaning $hours hs. old files in $DIR ...";
    if [ -d "$DIR" ]; then
    	$tmpwatch_cmd $DIR
    	touch $DIR/.check
    	echo "done.";
    else 
	echo "error.";
    fi
done
