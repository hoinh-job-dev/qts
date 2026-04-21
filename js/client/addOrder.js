/*****************************************
 * 入力値のValidate
 *****************************************/
function isValid_mail() {
    var isValid = true;
    var email =  document.getElementById('mail');
    email.value = email.value.trim();
    if(null==email.value.match(/.+@.+\..+/)) {
        isValid=false;
        email.classList.add('outline-red');
    } else {
        email.classList.remove('outline-red');
    }
    return isValid;
}

function isValid_number_lessthan(obj, max) {
    var isValid = true;
    obj.value = obj.value.split(',').join('').trim();
    if(""==obj.value || isNaN(obj.value) || max>parseInt(obj.value)) {
        isValid=false;
        obj.classList.add('outline-red');
    } else {
        obj.classList.remove('outline-red');
    }
    return isValid;
}

function isValid_checkbox(obj) {
    var isValid = true;
    var label =  document.getElementById(obj.id+'_label');
    if(""==obj.checked) {
        isValid=false;
        label.classList.add('outline-red');
    } else {
        label.classList.remove('outline-red');
    }
    return isValid;
}

function isValid() {
    var isValid = true;
    // メールアドレス
    if (!isValid_mail()) {
        isValid=false;
    }
    // 支払い方法
    // 予定金額
    if (!isValid_number_lessthan(document.getElementById('money'),1000)) {
        isValid=false;
    }
    // 規約への同意
    if (!isValid_checkbox(document.getElementById('rule_agreement'))) {
        isValid=false;
    }
    if (!isValid_checkbox(document.getElementById('risk_agreement'))) {
        isValid=false;
    }
    return isValid;
}


/*****************************************
 * データ送信
 *****************************************/
// 発注する
function submitOrder() {
    document.getElementById('confirm').disabled = true;
    if (!isValid()) {
        console.log("submitOrder() return false");
        document.getElementById('confirm').disabled = false;
        return false;
    }
    document.getElementById("order").submit();
}
