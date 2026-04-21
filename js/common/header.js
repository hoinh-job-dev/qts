/*****************************************
 *   Header
 *****************************************/

// メニュー表示非表示
function expand_menu() {
    var menu = document.getElementById('common_menu');
    if ('block' == menu.style.display) {
        menu.style.display = 'none';
    } else {
        menu.style.display = 'block';
    }
}

// ログアウトする
function logout() {
    window.location.href = BASE_URL + CONTROLLER + '/logout';
}

$(document).ready(function () {
    jQuery('.date_popup').datetimepicker({
        format:'Y/m/d',
        timepicker:false,
        scrollInput: false
    });

    $('.prevent-double-click').click(function (e){
        e.target.disabled = true;
         return true;
    });

});
