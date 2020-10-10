var initial_load_end = false;
var time = $("#servertime").data("time");
var timezone = $("#servertime").data("zone");
var updateTime = function() {
	$("#servertime span").text(moment.unix(time).tz(timezone).format('HH:mm:ss z'));
	time = time + 1;
};
setInterval(updateTime,1000);

function is_Numeric(num)
{
	return !isNaN(parseFloat(num)) && isFinite(num);
}

$(document).ready(function()
{
	$('a[data-toggle="tab"]').on('shown.bs.tab', function (e) 
	{
		if(e.target.id == "settings") 
		{
			if (! initial_load_end) 
			{
				initial_load_end = true;
				loadSettings();
			}
		}
	});
	$("#day").datepicker();
	$('#time').timepicker({
		// defaultTime: 'now',
		dropdown: true,
		// scrollbar: true,
		zindex: 10001
	});
	$('#savecall').on("click", saveCall);
	$('#btn_load_settings').on("click", loadSettings);
	$('#btn_save_settings').on("click", saveSettings);


	$("#dlgCreateCall").on('hide.bs.modal', function () {
        $("ul.ui-timepicker-list").hide();
		$("#ui-datepicker-div").hide();
		$('#setlanguage').multiselect('select', '');
		callform.reset();
	});
	
});



function saveCall(e)
{
	e.preventDefault();
	if($("#destination").val().trim() === "") {
		warnInvalid($("#destination"), _("Destination can not be blank"));
		return false;
	}
	if($("#time").val().trim() === "") {
		warnInvalid($("#time"), _("Time can not be blank"));
		return false;
	}
	if($("#day").val().trim() === "") {
		warnInvalid($("day"), _("Day can not be blank"));
		return false;
	}

	$("#savecall").prop("disabled",true);
	var post_data = {
		command: "savecall",
		module: "hotelwakeup",
		destination: $("#destination").val(),
		time: $("#time").val(),
		day: $("#day").val(),
		language: $("#setlanguage").val() 
	};
	$.post( window.FreePBX.ajaxurl, post_data, function( data ) {
		if(!data.status){
			fpbxToast(data.message, '', 'error');
		} else {
			$("#dlgCreateCall").modal("hide");
			$('#callgrid').bootstrapTable('refresh');
		}
		$("#savecall").prop("disabled",false);
	});
}

function removeWakeup(id, ext)
{
	var post_data = {
		command: "removecall",
		module: "hotelwakeup",
		id: id,
		ext: ext
	};
	$.post( window.FreePBX.ajaxurl, post_data, function( data ) {
		if(!data.status){
			fpbxToast(data.message, '', 'error');
		} else {
			$('#callgrid').bootstrapTable('refresh');
		}
	});
}


function loadSettings(e)
{
	if (e != undefined)
	{
		e.preventDefault();
	}
	var post_data = {
		module	: 'hotelwakeup',
		command	: 'getsettings',
	};
	disabledSettings(true);
	cleanWarnInvalid();
	$.post(window.FreePBX.ajaxurl, post_data, function(data) 
	{
		if(!data.status)
		{
			fpbxToast(data.message, '', 'error');
		}
		else
		{
			if (e != undefined)
			{
				fpbxToast(data.message, '', 'success' );
			}

			settingsform.reset();
			var config = data.config;
			var input_list = [
				"callerid",
				"extensionlength",
				"operator_extensions",
				"waittime",
				"retrytime",
				"maxretries",
			];

			$('#language').multiselect('select', config.language);
			$("#operator_mode_" + config.operator_mode).prop('checked', true);
			input_list.forEach(element => $("#" + element).val(config[element]));
			autosize.update($("#operator_extensions"));
			
			disabledSettings(false);
		}
	});
}

function saveSettings(e)
{
	e.preventDefault();
	if (! validateSettings())
	{
		return false;
	}
	var post_data = {
		module	: 'hotelwakeup',
		command	: 'setsettings',

		operator_mode		: $("input[name='operator_mode']:checked").val(),
		callerid			: $("#callerid").val(),
		extensionlength		: $("#extensionlength").val(),
		operator_extensions	: $("#operator_extensions").val(),
		waittime			: $("#waittime").val(),
		retrytime			: $("#retrytime").val(),
		maxretries			: $("#maxretries").val(),
		language			: $("#language").val(),
	};
	disabledSettings(true);
	$.post(window.FreePBX.ajaxurl, post_data, function(data) 
	{
		fpbxToast(data.message, '', (data.status ? 'success' : 'error') );
		if (data.status) 
		{
			loadSettings();
		}
		disabledSettings(false);
	});
}

function validateSettings(showmsg=true)
{
	var arr_options = {
		"extensionlength": _("Max Destination Length"),
		"waittime"		 : _("Ring Time"),
		"retrytime"		 : _("Retry Time"),
		"maxretries"	 : _("Max Retries"),
		"callerid"		 : _("Wake Up Caller ID"),
	};
	for (var key in arr_options)
	{
		let obj  = $("#" + key);
		let val  = obj.val().trim();
		let name = arr_options[key];
		if(val === "")
		{
			warnInvalid(obj, showmsg ? name + _(" can not be blank.") : "");
			return false;
		}
		if (key != "callerid")
		{
			if (! is_Numeric(val) )
			{
				warnInvalid(obj, showmsg ? name + _(" only allow numbers.") : "");
				return false;
			}
		}
	}
	return true;
}

function cleanWarnInvalid()
{
	$("#settingsform .element-container").removeClass("has-error has-warning has-success");
	$("#settingsform .element-container .input-warn").remove();
}

function disabledSettings(new_status)
{
	var input_list = [
		"input[name='operator_mode']",
		"#callerid",
		"#extensionlength",
		"#operator_extensions",
		"#waittime",
		"#retrytime",
		"#maxretries",
	];
	input_list.forEach(element => $(element).prop("disabled", new_status));
	$('#language').multiselect( new_status ? "disable" : "enable");	
}
