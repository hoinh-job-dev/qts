/*****************************************
 * 入力値のValidate
 *****************************************/
function isValid_text(obj) {
    var isValid = true;
    obj.value = obj.value.trim();
    if(""==obj.value) {
        isValid=false;
        obj.classList.add('outline-red');
    } else {
        obj.classList.remove('outline-red');
    }
    return isValid;
}


/*****************************************
 * データ送信
 *****************************************/
// 発注する
function submit() {
    if (!isValid_text(document.getElementById('token_id'))) {
        return false;
    }
    document.getElementById("order").submit();
}
