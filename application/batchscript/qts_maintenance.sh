
#----------------------------------------
# 変数定義
#----------------------------------------
vtoday=`date "+%Y-%m-%d"`
vtodayNoSpace=`date "+%Y%m%d"`
# 実行ファイルのディレクトリ
vapp_dir=/var/www/html/QT

# Log関連
vlog_dir=/var/www/html/QT/application/logs
vlog_file="log-${vtoday}.log"
vmain_file="log_maintenance_${vtodayNoSpace}.log"

# ERROR and INFO search
grep 'ERROR\|INFO' "${vlog_dir}/${vlog_file}" >> "${vlog_dir}/${vmain_file}" 


