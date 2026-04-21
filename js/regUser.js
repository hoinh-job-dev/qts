/*****************************************
 * Stepを移動する
 *****************************************/
function go(next_step_id) {
    var now_step = document.getElementById('now_step');
    var step1 = document.getElementById("step1");
    if (now_step == null && step1 == null) {
        return ;
    }
    // Stepを移動して良いか判定する
    var canGo = true;
    
    switch (next_step_id) {
        case 'step1':
            break;
        case 'step2':
            break;
        case 'step3':
            break;
        default:
            canGo = false;
    }
    
    // Stepを移動する
    if (canGo) {
        // box
        document.getElementById("step1").style.display = 'none';
        document.getElementById("step2").style.display = 'none';
        document.getElementById("step3").style.display = 'none';
        document.getElementById(next_step_id).style.display = 'block';

        // progress
        var orderflow = document.getElementById("order-flow");

        switch (next_step_id) {
            case 'step1':
                orderflow.className="order-flow order1";
                break;
            case 'step2':
                orderflow.className="order-flow order2";
                previewPhoto(document.getElementById("photo"));
                previewPhoto(document.getElementById("photo2"),2);
                previewPhoto(document.getElementById("photo3"),3);
                previewPhoto(document.getElementById("photo4"),4);
                break;
            case 'step3':
                orderflow.className="order-flow order3";
                break;
            default:
                canGo = false;
        }

        document.getElementById("step1_nav").className = '';
        document.getElementById("step2_nav").className = '';
        document.getElementById("step3_nav").className = '';
        document.getElementById(next_step_id + "_nav").className = 'active';

        now_step.value = next_step_id;
    }
    console.log("go() return "+canGo);
}


/*****************************************
 * アカウント種別切り替え
 *****************************************/
function switchType(obj) {
    var companyname = document.getElementById('company_name');
    var companynamelabel = document.getElementById(companyname.id+'_label');
    var companyname_kana = document.getElementById('company_name_kana');
    var companynamelabel_kana = document.getElementById(companyname_kana.id+'_label');
    var familynamelabel = document.getElementById('familyname_label');
    if ('01'==obj.value) {
        // 個人
		familynamelabel.innerHTML = '姓';
        companyname.value = '';
        companyname.style.display = 'none';
        companyname_kana.style.display = 'none';
        companynamelabel.style.display = 'none';
        companynamelabel_kana.style.display = 'none';
    } else {
        // 法人
		familynamelabel.innerHTML = '代表者 姓';
        companyname.style.display = 'block';
        companyname_kana.style.display = 'block';
        companynamelabel.style.display = 'block';
        companynamelabel_kana.style.display = 'block';
    }
}

/*****************************************
 * 画像選択/撮影
 *****************************************/
// 選択した画像ファイルのプレビューを表示する
function previewPhoto(obj, id) {
    if (obj == null)
        return;
    var fileList = obj.files;
    var fileurlId = "fileuploadurl";
    if (typeof(id) !="undefined" && id != null) {
        fileurlId = fileurlId + id;
    }
    var fileObj = document.getElementById(fileurlId);

    if (undefined==fileList[0]) {
        fileObj.value = '';
        document.getElementById('preview_'+obj.name).style.display = 'none';
        return;
    }

    fileObj.value = obj.value;

    //読み込み
    var reader = new FileReader();
    reader.readAsDataURL(fileList[0]);
    //読み込み後
    reader.onload = function () {
        var preview = document.getElementById('preview_'+obj.name);
        preview.src = reader.result;
        preview.style.display = 'block';
    }
    //isValid_photo(obj);
}

/*****************************************
 * 入力値のValidate
 *****************************************/
function isValid_text(obj) {
    var isValid = true;
    // パスワード
    obj.value = obj.value.trim();
    if(""==obj.value) {
        isValid=false;
        obj.classList.add('outline-red');
    } else {
        obj.classList.remove('outline-red');
    }
    return isValid;
}

function isValid_text_hyphen(obj) {
    var isValid = true;
    // パスワード
    obj.value = obj.value.trim();
    var val = obj.value.replace(/-/g,"");
    if(""==val || isNaN(val)) {
        isValid=false;
        obj.classList.add('outline-red');
    } else {
        obj.classList.remove('outline-red');
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

function isValid_familyname() {
    var isValid = true,
    	account = document.getElementById('account');
    if(account) {
    	if ('02'==document.getElementById('account').value) {
            // 法人
            return isValid;
        }
    }
    var obj = document.getElementById('familyname');
    obj.value = obj.value.trim();
    if(""==obj.value) {
        isValid=false;
        obj.classList.add('outline-red');
    } else {
        obj.classList.remove('outline-red');
    }
    return isValid;
}

function isValid_birthday() {
    var isValid = true;
    var year =  document.getElementById('year');
    var month =  document.getElementById('month');
    var day =  document.getElementById('day');
    if (!isNaN(year.value) && !isNaN(month.value) && !isNaN(day.value) 
            && isDate(year.value, month.value, day.value)) {
        document.getElementById('birthday').value = year.value+'/'+month.value+'/'+day.value;
        year.classList.remove('outline-red');
        month.classList.remove('outline-red');
        day.classList.remove('outline-red');
    } else {
        isValid = false;
        document.getElementById('birthday').value = "";
        year.classList.add('outline-red');
        month.classList.add('outline-red');
        day.classList.add('outline-red');
    }
    return isValid;
}

function isValid_mailconfirm() {
    var isValid = true;
    var email =  document.getElementById('mail');
    var email_confirm =  document.getElementById('mail2');
    email.value = email.value.trim();
    email_confirm.value = email_confirm.value.trim();
    if(null==email.value.match(/.+@.+\..+/) || email.value!=email_confirm.value) {
        isValid=false;
        email_confirm.classList.add('outline-red');
    } else {
        email_confirm.classList.remove('outline-red');
    }
    return isValid;
}

function isValid_mail(ignoreValidateConfirmEmail) {
    var isValid = true;
    var email =  document.getElementById('mail');
    email.value = email.value.trim();
    if(null==email.value.match(/.+@.+\..+/)) {
        isValid=false;
        email.classList.add('outline-red');
    } else {
        email.classList.remove('outline-red');
    }
    if(!(typeof ignoreValidateConfirmEmail == "boolean" && ignoreValidateConfirmEmail === true)) {
    	isValid_mailconfirm();
    }
    return isValid;
}

/* 
//Addition by hoinh 16.08.19. Only process image files. 
*/ 
function isImage(file){ 
     
    var ext = file.value.substr(file.value.lastIndexOf('.') + 1, 3); 
    var validFileType = "gif,jpg,jpeg,png,bmp"; 
    if (validFileType.indexOf(ext.toLowerCase()) < 0) { 
        //alert("Please select valid file type. The supported file types are .gif, .jpg , jpeg .png , .bmp"); 
        file.classList.add('outline-red'); 
        file.focus(); 
        return false; 
    }  
    return true;    
}

// 日付の有効無効チェック
function isDate(y,m,d) {
    var dt = new Date(y,m-1,d);
    return (dt.getFullYear()==y && dt.getMonth()==m-1 && dt.getDate()==d);
}


/*****************************************
 * データ送信
 *****************************************/
// 発注する
function submitOrder() { 
    document.getElementById('confirm').disabled = true;     
    document.getElementById("order").submit(); // 送信
}


$(document).ready(function () {
    $('.error_link').click(function(e){
        var id = "#" + $(e.target).attr('for');
        var cls = "." + $(e.target).attr('for');
        if (id == '#birthday'){
            id = '#year';
        }
        if(id == '#personal_agreement') {
            go('step1');
        }else if (id == '#rule_agreement' || id == '#risk_agreement'){
            go('step2');
        } else if (id == '#photo1' || id == '#photo2'){
            go('step3');
        } 

        if ($(id).is(":visible")){
            $(id).focus();
        } else if ($(cls).is(":visible")){
            $(cls)[0].focus();
        }

    });
    $('.error_link').each(function(index){
        var id = "#" + $(this).attr('for');
        var cls = "." + $(this).attr('for');
        if (id == '#birthday'){
            id = '#year';
            $('#month').addClass('outline-red');
            $('#day').addClass('outline-red');
        }
        $(id).addClass('outline-red');
        $(cls).addClass('outline-red');
    });
});
function mergeZipCode(){
    var z1 = $("#zip_code1").val();
    var z2 = $("#zip_code2").val();
    var zipcode = z1+z2;
    $("#zip_code").val(zipcode);
}
function mergePhoneCode(){
    var z1 = $("#tel1").val();
    var z2 = $("#tel2").val();
    var z3 = $("#tel3").val();
    var tel = z1+z2+z3;
    $("#tel").val(tel);
}

// process terms checkbox and confirm register agent/client button
$(document).ready(function() {
    function checkToEnableConfirmButton() {
        var $chks = $(".terms-checkbox"),
            $btnConfirm = $(".selectorConfirmRegister"),
            totalTermsChecked = 0;
        if($chks.length > 0 && $btnConfirm.length > 0) {
            $chks.each(function() {
                var $this = $(this);
                if($this.prop("checked")) {
                    totalTermsChecked++;
                }
            });
            if(totalTermsChecked >= $('.terms-checkbox').length) {
                $btnConfirm.removeClass("btn-force-disable");
                $btnConfirm.prop("disabled", false);
            }
            else {
                $btnConfirm.addClass("btn-force-disable");
                $btnConfirm.prop("disabled", true);
            }
        }
    }
    checkToEnableConfirmButton();
    $('.terms-content').on('scroll', function() {
        var $this = $(this),
            end_reached = $this.attr('data-end_reached'),
            $chk = $this.closest(".terms-container").find(".terms-checkbox");
        //if($this.scrollTop() + $this.innerHeight() >= $this[0].scrollHeight) {
		if($this.innerHeight()*1.2 >= $this[0].scrollHeight - $this.scrollTop()) {
			
            if(end_reached == 'F') {
                end_reached = 'T';
                $this.attr('data-end_reached', end_reached);
            }
        }
        if(end_reached == 'T' && $chk.length > 0 && $chk.prop("disabled") == true) {
            $chk.prop("disabled", false);
        }
    });
    $(document).on("change", ".terms-checkbox", function(){
        checkToEnableConfirmButton();
    });
    
    // check type of user
    // var types = $("#type");
    // if (types.length){
    //    switchType(types[0]);    
    // }
});

/*****************************************
 * A function check for change password *
*/ 
function submitChangePassword() {      
    var agentChangePass = document.getElementById("agentChangePasswd");
    var opeChangePass = document.getElementById("operatorChangePasswd"); 
    if(null != agentChangePass){
        agentChangePass.submit();
    }
    if(null != opeChangePass){
        opeChangePass.submit();
    }
}
