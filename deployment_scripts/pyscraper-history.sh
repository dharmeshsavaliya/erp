while read line
do
	echo "$line"|grep Processing
	if [ $? -eq 0 ]
	then
		scraper=`echo "$line"|cut -d' ' -f1`
		server=`echo "$line"|cut -d' ' -f2`
		day=`echo "$line"|cut -d' ' -f3|cut -d'-' -f3`
		ssh -o ConnectTimeout=5 root@$server.theluxuryunlimited.com "ps -eo pid,etimes,args|grep $scraper|grep -v grep" < /dev/null
		if [ $? -ne 0 ]
		then
			endtime=`stat -c '%y' /mnt/logs/$server/$scraper-$day.log|cut -d'.' -f1|tr ' ' '-'`
			sed -i "s/Processing-$scraper-$day-$server/$endtime/" /opt/pyscrap_history
		fi
	fi
done < /opt/pyscrap_history
