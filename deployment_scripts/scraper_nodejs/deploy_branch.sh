ssh -i ~/.ssh/id_rsa root@s01.theluxuryunlimited.com "cd ~/scraper_nodejs && git checkout $1 && git pull"
ssh -i ~/.ssh/id_rsa root@s02.theluxuryunlimited.com "cd ~/scraper_nodejs && git checkout $1 && git pull"
ssh -i ~/.ssh/id_rsa root@s03.theluxuryunlimited.com "cd ~/scraper_nodejs && git checkout $1 && git pull"
ssh -i ~/.ssh/id_rsa root@s04.theluxuryunlimited.com "cd ~/scraper_nodejs && git checkout $1 && git pull"
ssh -i ~/.ssh/id_rsa root@s05.theluxuryunlimited.com "cd ~/scraper_nodejs && git checkout $1 && git pull"