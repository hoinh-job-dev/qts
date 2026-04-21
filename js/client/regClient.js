// step1の入力値チェック
function isValid_Step1() {
    var isValid = true;
    // 個人/企業を選択
    // 姓
    if (!isValid_familyname()) {
        isValid=false;
    }
    // 名
    if (!isValid_text(document.getElementById('name'))) {
        isValid=false;
    }
    // 生年月日
    if (!isValid_birthday()) {
        isValid=false;
    }
    // 郵便番号
    if (!isValid_text_hyphen(document.getElementById('zip'))) {
        isValid=false;
    }
    // 都道府県
    if (!isValid_text(document.getElementById('add'))) {
        isValid=false;
    }
    // 市区町村
    if (!isValid_text(document.getElementById('add02'))) {
        isValid=false;
    }
    // それ以降の住所
    // 国籍
    if (!isValid_text(document.getElementById('country'))) {
        isValid=false;
    }
    // 電話番号
    if (!isValid_text_hyphen(document.getElementById('tel'))) {
        isValid=false;
    }
    // メールアドレス
    if (!isValid_mail()) {
        isValid=false;
    }
    // メールアドレス(確認)
    if (!isValid_mailconfirm()) {
        isValid=false;
    }
    // 支払い方法
    // 予定金額
    if (!isValid_number_lessthan(document.getElementById('money'), 1000)) {
        isValid=false;
    }
    // 方針への同意
    if (!isValid_checkbox(document.getElementById('personal_agreement'))) {
        isValid=false;
    }
    return isValid;
}

// step2の入力値チェック
function isValid_Step2() {
    var isValid = true;
    // 規約への同意
    if (!isValid_checkbox(document.getElementById('rule_agreement'))) {
        isValid=false;
    }
    // リスクへの同意
    if (!isValid_checkbox(document.getElementById('risk_agreement'))) {
        isValid=false;
    }
    return isValid;
}

// step3の入力値チェック
function isValid_Step3() {
    var isValid = true;
    // セルフィ
    if (!isValid_photo(document.getElementById('FileAttachment'))) {
        isValid=false;
    }
    return isValid;
}
