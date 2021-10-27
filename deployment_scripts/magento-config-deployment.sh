#!/bin/bash

function HELP {
        echo "-r|--repo: Repo Name"
        echo "-s|--scope: Scope"
	echo "-c|--code: Scope Code"
	echo "-p|--path: Path variable"
	echo "-v|--value: Value"
	echo "-f|--file: Sync file path"
	echo "-t|--type: sensitive / shared"
	echo "-h|--server: Server Name"
}

args=("$@")
idx=0
while [[ $idx -lt $# ]]
do
        case ${args[$idx]} in
                -r|--repo)
                repo="${args[$((idx+1))]}"
                idx=$((idx+2))
                ;;
                -s|--scope)
                scope="${args[$((idx+1))]}"
                idx=$((idx+2))
                ;;
                -c|--code)
                code="${args[$((idx+1))]}"
                idx=$((idx+2))
                ;;
                -p|--path)
                path="${args[$((idx+1))]}"
                idx=$((idx+2))
                ;;
                -v|--value)
                value="${args[$((idx+1))]}"
                idx=$((idx+2))
                ;;
                -f|--file)
		file="${args[$((idx+1))]}"
                idx=$((idx+2))
                ;;
                -t|--type)
		type="${args[$((idx+1))]}"
                idx=$((idx+2))
                ;;
                -h|--server)
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

### Load environment variables
. /var/www/erp.theluxuryunlimited.com/.env
hostip=`grep $server'_HOST' /var/www/erp.theluxuryunlimited.com/.env|cut -d'=' -f2`

function set_variable {
	if [ $type == "shared" ]
	then
		php bin/magento --lock-env config:set --scope=$scope --scope-code=$code $path $value
	else
		ssh -i ~/.ssh/id_rsa root@$hostip "cd /home/*/current/ ; php bin/magento config:sensitive:set --scope=$scope --scope-code=$code $path $value"
        	if [ $? -ne 0 ]
		then
	                exit 1
	        fi
	fi
}

if [ $type == "shared" ]
then
	cd /opt/magento/$repo
	git reset --hard origin/stage
	git pull origin stage
	composer install
	php -f bin/magento -- deploy:mode:set production --skip-compilation
	php bin/magento app:config:dump
fi
if [ -z $file ]
then
	set_variable
else
	while read line
	do
		scope=`echo $line|cut -d',' -f1`
		code=`echo $line|cut -d',' -f2`
		path=`echo $line|cut -d',' -f3`
		value=`echo $line|cut -d',' -f4`
		set_variable
	done < $file
fi

if [ $type == "shared" ]
then
	###### Dump changes from database and push to stage branch ###
	php bin/magento app:config:dump
	git add app/etc/config.php
	git commit -m 'Deployment config erp'
	git push origin stage

	sleep 10
	##### Create PR from stage to master ####
	pull_number=`curl -XPOST -H "Authorization: token $GITHUB_TOKEN" -H "Accept: application/vnd.github.v3+json" https://api.github.com/repos/ludxb/$repo/pulls -d '{"head":"stage","base":"master","title":"config deployment from erp"}' |grep '"number"'|awk '{print $2}'|cut -d',' -f1`

	##### Merge PR ####
	curl -XPUT -H "Authorization: token $GITHUB_TOKEN" https://api.github.com/repos/ludxb/$repo/pulls/$pull_number/merge
fi
