function send() {
    // validation
    var number = document.getElementById("order_number");
    number.value = number.value.trim();
    if(""==number.value) {
        isValid=false;
        number.style.backgroundColor = '#ff6666';
        return false;
    } else {
        number.style.backgroundColor = '#ffffff';
    }
    //submit
    var form = document.getElementById("ordernumber");
    form.submit();
}

function confirm() {
    // validation
    var number = document.getElementById("order_number");
    number.value = number.value.trim();
    if(""==number.value) {
        number.style.backgroundColor = '#ff6666';
        return false;
    } else {
        number.style.backgroundColor = '#ffffff';
    }
    var order = document.getElementById("order");
    order.value = order.value.trim();
    if(""==order.value) {
        number.style.backgroundColor = '#ff6666';
        return false;
    } else {
        number.style.backgroundColor = '#ffffff';
    }
    //submit
    var form = document.getElementById("confirmBtc");
    form.submit();
}