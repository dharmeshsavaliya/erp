#!/bin/bash

function HELP {
	echo "-d|--dir: Magento Dir"
	echo "-s|--server: Server IP"
	echo "-u|--url: Admin URL"
	echo "-p|--password: Password"
}
SSHPORT="22480 2112 22"
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
	        -d|--dir)
	        ROOT_DIR="${args[$((idx+1))]}"
	        idx=$((idx+2))
	        ;;
	        -u|--url)
	        adminurl="${args[$((idx+1))]}"
	        idx=$((idx+2))
	        ;;
	        -p|--password)
	        password="${args[$((idx+1))]}"
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

for portssh in $SSHPORT
do
        ssh -p $portssh  -i ~/.ssh/id_rsa -q root@$SERVER 'exit'
        if [ $? -ne 255 ]
        then
                PORT=`echo $portssh`
        fi
done


#if [ "$function" = "userpass" ]
#then
#	if [ "$type" == "ssh" ]
#	then
ssh -p $PORT root@$server "cd $ROOT_DIR && php bin/magento setup:config:set --backend-frontname=$adminurl"
if [ $? -eq 1 ]
then
	echo "{\"status\":\"fail\",\"msg\":\"Unable to connect\"}"
else
	echo "{\"status\":\"success\",\"msg\":\"created successfully\"}"
fi

#	else
#		echo "db"
#	fi
	
#fi
