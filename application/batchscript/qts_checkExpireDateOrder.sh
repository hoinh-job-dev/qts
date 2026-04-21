#!/bin/bash
#Quanta Token System
#synchronize block

# To successfully write a shell script, you have to do three things:
# - Write a script
# - Give the shell permission to execute it
# - Put it somewhere the shell can find it
#
# ◆ cronの設定方法
# 手順1. コマンド
#   > crontab -u ubuntu -e
# 手順2. 下記を追記してください。
#   0 0 * * * /var/www/html/QT/application/qts_checkExpireDateOrder.sh

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

#----------------------------------------
# 処理
#----------------------------------------
PATH="$PATH:${vapp_dir}:${vmysqldump_dir}"
cd ${vapp_dir}

date "+%Y/%m/%d %H:%M:%S | checkExpiredBtcOrder cron job " >> "${vlog_dir}/${vlog_file}"
php index.php Operator checkExpiredBtcOrder