/*****************************************
 * 入力値のValidate
 *****************************************/
function check(address) {
    var result = false;

    if(address.length>34 || address.length<27) { /* not valid string length */
        return result;
    }

    if(/[0OIl]/.test(address)) {  /* this character are invalid in base58 encoding */
        return result;
    }

    $.ajax({
        url : "https://blockchain.info/it/q/addressbalance/" + address,   /* return balance in satoshi */
        async : false
    }).done(function(data) {
        var isnum = /^\d+$/.test(data);   
        if (isnum) {                         /* if the returned data are all digits, it's valid */  
            console.log("data is integer");
            result = true;
        }
    });
    return result;
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
    if(isValid) {
        obj.style.backgroundColor = '#ffffff';
    } else {
        obj.style.backgroundColor = '#ff6666';
    }
    return isValid;
}

/*****************************************
 * データ送信
 *****************************************/
// 発注する
function submitAddr() {
    var error = $('#register .error');
    var btc_address = $('#register #btc_address').val();
    if(btc_address == ''){
        error.html(('Bitcoin address is required.').fontcolor("red"));
        return false;
    }

    var isValid = validateBtcAddress(btc_address);
    if (!isValid) {
        error.html(('Bitcoin address is invalid.').fontcolor("red"));
        return false;
    }
    document.getElementById("register").submit(); // 送信
}

$( document ).ready(function() {

    $('#btc_address').keypress(function(event){

        var keycode = (event.keyCode ? event.keyCode : event.which);
        if(keycode == '13'){//You pressed a "enter" key in text btc_address
            isValid_btcAddress(this);
        }
    });
    $('#ConfirmRegister').keypress(function(event){

        var keycode = (event.keyCode ? event.keyCode : event.which);
        if(keycode == '13'){
            //You pressed a "enter" key in button
        }
        event.stopPropagation();
    });

    $(document).keypress(function(event){

        var keycode = (event.keyCode ? event.keyCode : event.which);
        if(keycode == '13'){//You pressed a "enter" key in somewhere
            return submitAddr();
        }
    });
});