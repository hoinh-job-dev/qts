$(function() {

	/**
	 * ページ内スクロール
	 * #の付いたリンクを自動的にスクロール化する
	 */
	$('a[href^=#]').click(function(){
		var speed = 500;
		var href= $(this).attr("href");
		var target = $(href == "#" || href == "" ? 'html' : href);
		var position = target.offset().top;
		$("html, body").not(':animated').animate({scrollTop:position}, speed, "swing");
		return false;
	});
	
	//ユーザー代理店の判定
	var agent = navigator.userAgent;
	if(agent.search(/iPhone/) != -1){
		$("body").addClass("iphone");
	}else if(agent.search(/Android/) != -1){
		$("body").addClass("android");
	}
	
	//ウィンドウの高さを取得
	var hsize = $(window).height();
	var hhead = $('.l-header').height();
	$(".l-bg, .login-main").css("height", hsize + "px");
	$(".l-side").css("min-height", hsize - hhead + "px");
	
	//カラムの高さをそろえる
	var js_height = $('.js-height');
	if (js_height.length)
		js_height.matchHeight();

	$("#accordion_sp").click(function() { 
		$("#accordion_sp_in").slideToggle(); 
	}); 
		
});//ready

function resizeScrollableTable() {
	var $tableHead = $(document).find("div#tableHead"),
		$tableBody = $(document).find("div#tableBody");
	if($tableHead.length > 0 && $tableBody.length > 0) {
		var $tHead = $tableHead.find("table"),
			$tBody = $tableBody.find("table"),
			tableBodyWidth = $tableBody.innerWidth(),
			tBodyWidth = $tBody.innerWidth(),
			scrollWidth = tableBodyWidth - tBodyWidth - 1;
		$tableHead.css("padding-right", (scrollWidth + "px"));
	}
}

$(document).ready(function(){
	resizeScrollableTable();
});

//ウィンドウリサイズ時
$(window).resize(function () {
	
	//ウィンドウの高さを取得
	var hsize = $(window).height();
	var hhead = $('.l-header').height();
	$(".l-bg, .login-main").css("height", hsize + "px");
	$(".l-side").css("min-height", hsize - hhead + "px");
	
	resizeScrollableTable();
});//resize

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
	var isValid = true
	if (obj != null) {
		obj.value = obj.value.trim();

		isValid = validateBtcAddress(obj.value);

		if(("" === obj.value) || isValid) {
			obj.style.backgroundColor = '#ffffff';
		} else {
			obj.style.backgroundColor = '#ff6666';
		}
	}
    return isValid;
}

function zipcloudSearch() {

	function requestZipcloudApi($wrapperArr) {
		if($wrapperArr.length > 0) {
			$wrapperArr.each(function(){
				var $wrapper = $(this);
				$wrapper.find("input[name='prefecture']").val('');
				$wrapper.find("input[name='city']").val('');
				$wrapper.find("input[name='building']").val('');
				var zipcode = $wrapper.find(".selectorZipcodeValue").val(),
					zipcloudApiUrl = BASE_URL + "Operator/zipcloudSearch/" + zipcode;
				if(zipcode.length > 0) {
					$.ajax({
						type: 'GET',
						dataType: 'JSON',
						url: zipcloudApiUrl
					}).done(function(data){
						if(typeof data != "object") {
							console.log('invalid response data');
							return;
						}
						if(typeof data.status != "undefined" && data.status == 200 && typeof data.results != "undefined" && data.results != null && data.results.constructor === Array && data.results.length > 0) {
							var obj = data.results[0],
								buildingName = (data.results.length == 1 ? obj.address3 : '');
							$wrapper.find("input[name='prefecture']").val(obj.address1);
							$wrapper.find("input[name='city']").val(obj.address2);
							$wrapper.find("input[name='building']").val(buildingName);
						}
						else {
							console.log('!ERR: ' + data.message);
						}
					});
				}
			});
		}
	}

	$(document).on("click", ".selectorSearchByZipcode", function(){
		var $this = $(this),
			$wrapper = $this.closest('.selectorZipcloudSearchWrapper');
		requestZipcloudApi($wrapper);
	}).on("keypress", ".selectorZipcodeValue", function(e){
		var code = e.keyCode || e.which;
		//Enter keycode
		if(code == 13) {
			var $this = $(this),
				$wrapper = $this.closest('.selectorZipcloudSearchWrapper');
			requestZipcloudApi($wrapper);
		}
	});

}

zipcloudSearch();

function showDialog(message, callbackFunction) {
	var callback = (typeof callbackFunction === "function" ? callbackFunction : function() { });
	var dialog = ($.fn.dialog !== undefined),
		$dialog = $("#generalInfoDialog"),
		returnAction = 'CLOSE';
	if($dialog.length > 0 && dialog) {
		// set content
		$dialog.html(message);
		// init dialog
		$dialog.dialog({
			title: "警告",
            autoOpen: false,
            modal: true,
            draggable: false,
            resizable: false,
            width: "400px",
            close: function(event, ui) {
                $(this).dialog('destroy');
                callback(returnAction);
            },
            buttons: {
                'OK': function () {
                    returnAction = 'OK';
                    $(this).dialog("close");
                }
            }
        });
        // show dialog
        $dialog.dialog("open");
	}
}

function showConfirmationDialog(title, message, callbackFunction) {
	var callback = (typeof callbackFunction === "function" ? callbackFunction : function() { });
	var dialog = ($.fn.dialog !== undefined),
		$dialog = $("#generalInfoDialog"),
		returnAction = 'CLOSE';
	if($dialog.length > 0 && dialog) {
		// set content
		$dialog.html(message);
		// init dialog
		$dialog.dialog({
			title: title,
            autoOpen: false,
            modal: true,
            draggable: false,
            resizable: false,
            width: "400px",
            close: function(event, ui) {
                $(this).dialog('destroy');
                callback(returnAction);
            },
            buttons: {
                'はい': function () {
                    returnAction = 'YES';
                    $(this).dialog("close");
                },
                'キャンセル': function () {
                    returnAction = 'CANCEL';
                    $(this).dialog("close");
                }
            }
        });
        // show dialog
        $dialog.dialog("open");
	}
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