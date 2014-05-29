#!/bin/bash
SCRIPT=`readlink -f $0` 
SCRIPTPATH=`dirname $SCRIPT`
PHP=`which php`
#PHP=/bin/php
nohup $PHP $SCRIPTPATH/Upload.php  >> /tmp/upload.log &   