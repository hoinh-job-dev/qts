KYC Server Installation

Please follow this instruction step-by-step, this instruction is applied for “Ubuntu Server 14.04 LTS (HVM), SSD Volume Type - ami-a21529cc”

Login as ubuntu.

sudo adduser ubuntu
sudo usermod -aG sudo ubuntu
    1 Prepare environment
sudo apt-get update -y
sudo apt-get install curl -y
sudo apt-get install git build-essential python libtool autoconf pkg-config -y
sudo ln -s /usr/bin/libtoolize /usr/bin/libtool
Install Apache
sudo apt-get install apache2 -y
    2 Install MySQL
sudo apt-get install mysql-server libapache2-mod-auth-mysql php5-mysql -y

    3 Create KYC database
mysql -u root -p

create database Kyc;
CREATE USER 'kyc_user'@'localhost' IDENTIFIED BY 'kyc';
GRANT ALL PRIVILEGES ON * . * TO 'kyc_user'@'localhost';
FLUSH PRIVILEGES;
    5 Import KYC database
Download Kyc.sql from link below and upload to Kyc server then execute command in the box to import database:

https://github.com/hoinh-job-dev/qts/tree/main/application/config/DbUpdateScript)/Kyc.sql

mysql -ukyc_user -p Kyc < /tmp/Kyc.sql
    6 Install PHP
sudo apt-get install php5 libapache2-mod-php5 php5-mcrypt -y
sudo apt-get install php5-curl -y
sudo apt-get install php5-gd -y
    7 Install Postfix (optional)
    8 sudo apt-get install postfix -y
    9 Install Proxy server
    10 sudo apt-get install -y libapache2-mod-proxy-html
sudo apt-get install -y libxml2-dev #dependency
    11 sudo a2enmod proxy proxy_http
    12 sudo service apache2 restart
    13 Enable access from remote
edit: /etc/mysql/my.cnf, find and replace:
bind-address        = 0.0.0.0
Restart mysql service:
sudo service mysql restart

    14 Create images folder

sudo mkdir /var/www/html/PT/img -p
sudo chmod 777 /var/www/html/PT/img

    15 Get source
cd /var/www/html
sudo git clone https://github.com/hoinh-job-dev/qts.git QT

Change apache2 settings
Edit file
sudo vi /etc/apache2/sites-available/000-default.conf
And add setting below:
<Directory "/var/www/html">
AllowOverride All
</Directory>
Then enable settings:
sudo a2enmod rewrite
sudo service apache2 restart




Create database
mysql -u root -p
create database LotteToken;
CREATE USER 'lotte_token_user'@'localhost' IDENTIFIED BY 'lotte_token';
GRANT ALL PRIVILEGES ON * . * TO 'lotte_token_user'@'localhost';
FLUSH PRIVILEGES;

Import database
mysql -ulotte_token_user -p LotteToken < /var/www/html/QT/application/config/DbUpdateScript/LotteToken.sql

Change folders permission
Change apache2 user execution
sudo  vi /etc/apache2/envvars
    export APACHE_RUN_USER=ubuntu
    export APACHE_RUN_GROUP=ubuntu

Create directory logs and moveproxy
sudo mkdir /var/www/html/PT/application/logs
sudo mkdir /var/www/html/PT/moveproxy

Directory authentication setting
sudo chmod 755 /var/www/html/PT/application/logs
sudo chmod 755 /var/www/html/PT/moveproxy
sudo chmod 755 /var/www/html/PT/docs
sudo chmod 755 /var/www/html/PT/img
sudo chmod 755 /var/www/html/PT/readimg
sudo chmod 755 /var/www/html/PT/tmpimg
sudo chmod 755 /var/www/html/PT/upload


Edit configuration file
cd PT
vi application/config/config.php
In the config.php file, find and change as configuration below:
    16 $config['server_host'] = 'set your domain name here';
    17 $config['start_blockheight'] = 400000;	// change the number to be current block height from insight ui server

Edit .htaccess file
cd PT
vi .htaccess
Config to enable forward all http request to https. (just remove the “comment out”)

Change database config	
cd PT
vi application/config/database.php
Then change $db['kyc'] hostname to IP of KYC


Enable proxy server	
Edit file: 

sudo vi /etc/apache2/sites-available/000-default.conf

Then add inside <virtualhost> tag
ProxyPass /PT/readimg/ http://{KYC_IP}/QT/img/
ProxyPassReverse /PT/readimg/ http://{KYC_IP}/QT/img/

Restart apache server:

sudo service apache2 restart


Config SMTP mail	
Copy MY_Email.php into /var/www/html/QT/application/libraries/

... and then open browser chrome with address localhost/QT/Operation/login
