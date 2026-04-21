window.onload = function() {
    var divTb = document.getElementById('tableBody');
    if (divTb && 500 < divTb.style.height) {
        divTb.style.height = 500+'px';
    }
};


/*****************************************
 * テーブルスクロール
 *****************************************/
function tableScroll() {
    document.getElementById('tableHead').scrollLeft = document.getElementById('tableBody').scrollLeft;
}

/*****************************************
 * データ送信
 *****************************************/
// 更新する
function submitStatus() {
    var orderForm = document.getElementById("update");

    if (orderForm.action.endsWith('Operator/confirmBtcAddr')) {
        var error = $('#update .error_msg');
        var rownum = document.getElementById('rownum').value;

        for (var i = 0; i < rownum; i++) {
            var btc_addr = document.getElementById('btc_address'+i).value;
            if (("" != btc_addr) && (!validateBtcAddress(btc_addr))) {
                var msgError = 'Bitcoin rows ' + (i + 1) + ' with address "' + btc_addr + '" is invalid.';
                error.html(msgError.fontcolor("red"));
                return false;
            }
        }
    }
    $("input[type=button]").attr("disabled","disabled");
    orderForm.submit(); // 送信
}

function validateUserAccount(btnSubmit) {
    var isValid = true;
    // 個人/企業を選択
    // 姓
    // if (!isValid_familyname()) {
    //     isValid=false;
    // }
    // 名
    // if (!isValid_text(document.getElementById('name'))) {
    //     isValid=false;
    // }
    // 生年月日
    // if (!isValid_birthday()) {
    //     isValid=false;
    // }
    // 郵便番号
    // if (!isValid_text_hyphen(document.getElementById('zip'))) {
    //     isValid=false;
    // }
    // 都道府県
    // if (!isValid_text(document.getElementById('add'))) {
    //     isValid=false;
    // }
    // // 市区町村
    // if (!isValid_text(document.getElementById('add02'))) {
    //     isValid=false;
    // }
    // それ以降の住所
    // 国籍
    // if (!isValid_text(document.getElementById('country'))) {
    //     isValid=false;
    // }
    // 電話番号
    // if (!isValid_text_hyphen(document.getElementById('tel'))) {
    //     isValid=false;
    // }
    // メールアドレス
    // if (!isValid_mail(true)) {
    //     isValid=false;
    // }
    // メールアドレス(確認)
    /*if (!isValid_mailconfirm()) {
        isValid=false;
    }*/
    // 方針への同意
    /*if (!isValid_checkbox(document.getElementById('personal_agreement'))) {
        isValid=false;
    }*/

    // BTC address
    // if (document.getElementById('btc_address').value != "" && !isValid_btcAddress(document.getElementById('btc_address'))) {
    //     isValid=false;
    // }
    if(isValid) {
    	btnSubmit.form.submit();
    	return true;
    }
    return false;
}

function validateBtcAddress(address) {
    if(typeof address == "undefined" || address.length == 0) {
        return false;
    }
    var isValid = false;
    if(TESTNET_MODE) {
        isValid = WAValidator.validate(address, 'bitcoin', 'testnet');
    }
    else {
        isValid = WAValidator.validate(address, 'bitcoin');
    }
    return isValid;
}

function isValid_btcAddress(obj) {
    obj.value = obj.value.trim();

    var isValid = validateBtcAddress(obj.value);

    if(("" === obj.value) || isValid) {
        obj.style.backgroundColor = '#ffffff';
    } else {
        obj.style.backgroundColor = '#ff6666';
    }
    return isValid;
}

$(document).ready(function () {
    $('.error_link').click(function(e){
        var id = "#" + $(e.target).attr('for');
        if ($(id).length && $(id).is(':visible')){
            $(id).focus();
        } else {
            var cls = "." + $(this).attr('for');
            $(cls).focus();
        }
    });

    $('.error_link').each(function(index){
        var id = "#" + $(this).attr('for');
        $(id).addClass('outline-red');
        var cls = "." + $(this).attr('for');
        $(cls).addClass('outline-red');
    });
});

function go_url(url){
        window.location = url;
}

function submitSendEmail() {   
    var listCheck = "";
    $('.selectorProcessThis').each(function(index, checkElement) {
   
    if($(checkElement).is(':checked'))
        listCheck += "," + $(checkElement).val();
    });
    $("#listEmailId").val(listCheck.substring(1));
    document.getElementById("search").submit();
}
// actionName: csv method to export CSV name
function exportCsvFromSearch(actionName) {
    var form = $('#search');
    var fcsv = form.clone()[0];

    fcsv.id = 'outputCsvSearch';
    fcsv.action = actionName;

    document.body.appendChild(fcsv);
    fcsv.submit();
    document.body.removeChild(fcsv);
}

function editTokenSubmit() {
    var TokenListCheck = "";
    $('.selectorProcessThis').each(function(index, checkElement) {
   
    if($(checkElement).is(':checked'))
        TokenListCheck += "," + $(checkElement).val();
    });
    $("#listOrderNumber").val(TokenListCheck.substring(1));
    document.getElementById("search").submit();
}