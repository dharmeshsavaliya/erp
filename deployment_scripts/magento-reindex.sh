#!/bin/bash

function HELP {
	echo "-f|--function: reindex"
	echo "-s|--server: Server Name"
}

args=("$@")
idx=0
while [[ $idx -lt $# ]]
do
        case ${args[$idx]} in
	        -f|--function)
	        function="${args[$((idx+1))]}"
	        idx=$((idx+2))
	        ;;
	        -s|--server)
	        server="${args[$((idx+1))]}"
	        idx=$((idx+2))
	        ;;
                -h|--help)
	        HELP
	        exit 1
	        ;;
	        *)
	        idx=$((idx+1))
	        ;;
	esac
done

if [ -z $server ] || [ -z $function ]
then
	HELP
	exit
fi

#################################################################################################################################################
#################################################################################################################################################
if [ "$function" = "reindex" ]
then
	hostip=`grep $server'_HOST' /var/www/erp.theluxuryunlimited.com/.env|cut -d'=' -f2`
	ssh -i ~/.ssh/id_rsa root@$hostip "cd /home/*/current/ ; php bin/magento index:reindex ; chown -R www-data.www-data *"
fi
