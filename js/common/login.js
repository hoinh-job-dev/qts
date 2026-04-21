// 領域を開閉する
function box_expand() {
    var pwbox = document.getElementById('ask_password_box');
    pwbox.style.display=('none'==pwbox.style.display)?'block':'none';
}

/*****************************************
 * 入力値のValidate
 *****************************************/
function isValid_email(obj) {
    var isValid = true;
    // メールアドレス
    obj.value = obj.value.trim();
    if(null==obj.value.match(/.+@.+\..+/)) {
        isValid=false;
        obj.style.backgroundColor = '#ff6666';
    } else {
        obj.style.backgroundColor = '#ffffff';
    }
    return isValid;
}

function isValid_text(obj) {
    var isValid = true;
    // パスワード
    obj.value = obj.value.trim();
    if(""==obj.value) {
        isValid=false;
        obj.style.backgroundColor = '#ff6666';
    } else {
        obj.style.backgroundColor = '#ffffff';
    }
    return isValid;
}

/*****************************************
 * データ送信
 *****************************************/
// ログインする
function login() {
    var isValid = true;
    if (!isValid_email(document.getElementById('mail'))) {
        isValid=false;
    }
    if (!isValid_text(document.getElementById('pass'))) {
        isValid=false;
    }
    if (!isValid) {
        return isValid;
    }
    document.getElementById("login").submit(); // 送信
}

// 問い合わせる
function ask_password() {
    if (!isValid_email(document.getElementById('askmail'))) {
        return isValid;
    }
    document.getElementById('ask').submit();
}

// login for client 
function loginClient() {
    var isValid = true;

    if (!isValid_text(document.getElementById('family_name'))) {
        isValid=false;
    }
    if (!isValid_text(document.getElementById('first_name'))) {
        isValid=false;
    }
    if (!isValid_birthday()) {
        isValid=false;
    }
    if (!isValid) {
        return isValid;
    }
    document.getElementById("login").submit();
}
