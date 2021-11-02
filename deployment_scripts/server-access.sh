#!/bin/bash

function HELP {
	echo "-f|--function: userpass"
	echo "-s|--server: Server IP"
	echo "-t|--type: User Type ssh/db"
	echo "-u|--user: Username"
	echo "-p|--password: Password"
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
	        -t|--type)
	        type="${args[$((idx+1))]}"
	        idx=$((idx+2))
	        ;;
	        -u|--user)
	        user="${args[$((idx+1))]}"
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

if [ "$function" = "userpass" ]
then
	if [ "$type" == "ssh" ]
	then
		ssh root@$server "echo '$user:$password' | chpasswd"
	else
		echo "db"
	fi
	
fi
