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
