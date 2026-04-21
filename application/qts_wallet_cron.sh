# 注文にBTCアドレスを設定
*/5 * * * * ubuntu sh /var/www/html/QT/application/batchscript/qts_autoInputBtcAddr.sh
# BTC着金の処理
*/10 * * * * ubuntu sh /var/www/html/QT/application/batchscript/qts_syncBlock.sh
# レート取得
*/3 * * * * ubuntu sh /var/www/html/QT/application/batchscript/qts_getrate.sh
# 注文完了を行います
*/10 * * * * ubuntu sh /var/www/html/QT/application/batchscript/qts_checkCompleteOrders.sh
# 注文の有効期限を切り替えます
10 00 * * * ubuntu sh /var/www/html/QT/application/batchscript/qts_checkExpireDateOrder.sh
# メールでCRONで実行
*/10 * * * * ubuntu sh /var/www/html/QT/application/batchscript/qts_send_email.sh
# 画像KYC移動
59 23 * * * ubuntu mv /var/www/html/QT/readimg/* /var/www/html/QT/readimg/moveproxy/ && scp -pq -i /home/ubuntu/.ssh/CLV_KYC.pem /var/www/html/QT/readimg/* ubuntu@HOST_IP:/var/www/html/QT/img/ && rm -f /var/www/html/QT/readimg/moveproxy/* 
# DB Backup
00 00 * * * ubuntu sh /var/www/html/QT/application/qts_batch.sh
# Maintenance log
59 23 * * * ubuntu sh /var/www/html/QT/application/batchscript/qts_maintenance.sh
