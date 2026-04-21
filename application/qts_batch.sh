#!/bin/bash
#Quanta Token System
#daily and monthly batch

# To successfully write a shell script, you have to do three things:
# - Write a script
# - Give the shell permission to execute it
# - Put it somewhere the shell can find it
#
# ◆ cronの設定方法
# 手順1. コマンド
#   > crontab -u ubuntu -e
# 手順2. 下記を追記してください。
#   00 00 * * * /var/www/html/QT/application/qtsbacth.sh

#----------------------------------------
# 変数定義
#----------------------------------------
sudo ln -sf  /usr/share/zoneinfo/Asia/Tokyo /etc/localtime

vtoday=`date "+%Y%m%d"`
vnow=`date "+%H%M%S"`

# 実行ファイルのディレクトリ
vapp_dir=/var/www/html/QT
vmysqldump_dir=/usr/bin

# Log関連
vlog_dir=/var/www/html/QT/application/logs
vlog_file="qts${vtoday}.log"

# DBbk関連
vdb_dump_dir=/var/www/html/QT/dbdump
vdb_dump_file="qts${vtoday}.sql"

#----------------------------------------
# 処理
#----------------------------------------
echo "START" >> "${vlog_dir}/${vlog_file}"

PATH="$PATH:${vapp_dir}:${vmysqldump_dir}"
cd ${vapp_dir}

#----------------------------------------
# Daily batch
#----------------------------------------
vhost=localhost
vuser=lotte_token_user
MYSQL_PWD="lotte_token"
vdb=LotteToken

date "+%Y/%m/%d %H:%M:%S | DB Backup" >> "${vlog_dir}/${vlog_file}"
mysqldump -h${vhost} -u${vuser} -p$MYSQL_PWD --databases ${vdb} >> "${vdb_dump_dir}/${vdb_dump_file}"
if [ $? -eq 0 ]
then
  echo "[SUCCESS]" >> "${vlog_dir}/${vlog_file}"
else
  echo "[FAILURE] PLEASE CHACK THE RESULT!" >> "${vlog_dir}/${vlog_file}"
  exit
fi

#--------------------
#date "+%Y/%m/%d %H:%M:%S | File Backup" >> "${vlog_dir}/${vlog_file}"
#sudo scp -pq -i ~/.ssh/CLV_KYC.pem /var/www/html/QT/readimg/* ubuntu@52.196.100.143:~/QT/img/

#--------------------
date "+%Y/%m/%d %H:%M:%S | File Available" >> "${vlog_dir}/${vlog_file}"
sudo df -h | grep /dev/xvda1 >> "${vlog_dir}/${vlog_file}"

#-------------------- delete exprired sessions (8 days)
php index.php Operator delete_expired_session
