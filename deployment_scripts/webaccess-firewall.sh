#!/bin/bash
SSHPORT="22480 2112 22"
function Add {
		
	ssh -p $PORT -i ~/.ssh/id_rsa root@$SERVER "ufw insert 1 allow proto tcp from $IP to any port '80,443' comment '$comment'"
}

function List {
	ssh -p $PORT -i ~/.ssh/id_rsa root@$SERVER "ufw status numbered|tr '][' ' '|grep 80,443|awk '{print \$1,\$5,\$7}'"
}

function Delete {
	ssh -p $PORT -i ~/.ssh/id_rsa root@$SERVER "ufw status numbered|tr '][' ' '|grep 80,443|awk '{print \$1}' |grep -w \"$IP_Numbered\""
	if [ $? -eq 0 ]
	then
		ssh -p $PORT -i ~/.ssh/id_rsa root@$SERVER "yes|ufw delete $IP_Numbered"
	else
		echo "Number is not in the list"
		exit 1
	fi
}

function HELP {
	echo " -f: Function (add - Add New Ip for web access)
		(delete - Delete Ip for web access)
		(list - List of ips who have access to erp)"
	echo "-n|--number: Number in list of ips which need to delete for web access"
	echo "-i|--ip: Ip address to add in whitelist for erp access"
	echo "-c|--comment: Comment to show ip belongs to which user/system for whitelist for erp access"
 	echo "-s|--server: server FQDN or ip address"
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
	        -n|--number)
	        IP_Numbered="${args[$((idx+1))]}"
	        idx=$((idx+2))
	        ;;
	        -i|--ip)
	        IP="${args[$((idx+1))]}"
	        idx=$((idx+2))
	        ;;
	        -c|--comment)
	        comment="${args[$((idx+1))]}"
	        idx=$((idx+2))
	        ;;
		-s|--server)
                SERVER="${args[$((idx+1))]}"
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

if [ -z "$SERVER" ]
then
	SERVER=`echo $HOSTNAME`
 fi

for portssh in $SSHPORT
do
	ssh -p $portssh  -i ~/.ssh/id_rsa -q root@$SERVER 'exit'
	if [ $? -ne 255 ]
	then
	        PORT=`echo $portssh`
	fi
done

if [ "$function" = "add" ]
then
	Add
elif [ "$function" = "delete" ]
then
	Delete
elif [ "$function" = "list" ]
then
	List
fi
