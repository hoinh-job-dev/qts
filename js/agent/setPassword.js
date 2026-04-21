/*****************************************
 * 入力値のValidate
 *****************************************/
function isValid_passconfirm() {
    var isValid = true;
    var password =  document.getElementById('password');
    var password_confirm =  document.getElementById('password2');
    password.value = password.value.trim();
    password_confirm.value = password_confirm.value.trim();
    if(null==password.value || password.value!=password_confirm.value) {
        isValid=false;
        password_confirm.style.backgroundColor = '#ff6666';
    } else {
        password_confirm.style.backgroundColor = '#ffffff';
    }
    return isValid;
}

function isValid_pass() {
    var isValid = true;
    var password =  document.getElementById('password');
    password.value = password.value.trim();
    pattern=/(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{6,}/;
    if(false==pattern.test(password.value)) {
        isValid=false;
        password.style.backgroundColor = '#ff6666';
    } else {
        password.style.backgroundColor = '#ffffff';
    }
    isValid_passconfirm();
    return isValid;
}

/*****************************************
 * データ送信
 *****************************************/
// 発注する
function submitPass() {
    if (!(isValid_pass() && isValid_passconfirm())) {
        return false;
    }
    document.getElementById("register").submit(); // 送信
}
