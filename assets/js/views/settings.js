var global_module_hotelwakeup_i18n = i18nGet('settings');

function is_Numeric(num)
{
	return !isNaN(parseFloat(num)) && isFinite(num);
}

$(document).ready(function()
{
	loadSettings();
	
	$('#btn_load_settings').on("click", loadSettings);
	$('#btn_save_settings').on("click", saveSettings);
});


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
		"extensionlength": 'int',
		"waittime"		 : 'int',
		"retrytime"		 : 'int',
		"maxretries"	 : 'int',
		"callerid"		 : 'string',
	};
	for (var key in arr_options)
	{	
		let obj  = $("#" + key);
		let val  = obj.val().trim();
		let name = i18n_mod(key);
		if(val === "")
		{
			warnInvalid(obj, showmsg ? sprintf( i18n_mod("VALIDATE_ERROR_BLANK"), name ) : "");
			return false;
		}
		else if (arr_options[key] == "int" && ! is_Numeric(val))
		{
			warnInvalid(obj, showmsg ? sprintf( i18n_mod("VALIDATE_ERROR_ONLY_NUMBER"), name ) : "");
			return false;
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
}
