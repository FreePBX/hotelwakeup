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

	$('#btn_operator_add_number').on("click", addNumberOperator);
	$('#operator_add_number').keypress(function(event)
	{
		var keycode = (event.keyCode ? event.keyCode : event.which);
		if(keycode == '13')
		{
			addNumberOperator();
			event.stopPropagation();
		}
	});

	$(".ExtensionList").on('click', 'li', function (e) {
		if (e.ctrlKey || e.metaKey) {
			$(this).toggleClass("selected");
		} else {
			$(this).addClass("selected").siblings().removeClass('selected');
		}
	}).sortable({
		connectWith: ".ExtensionList",
		delay: 150, //Needed to prevent accidental drag when trying to select
		revert: 0,
		helper: function (e, item) {
			//Basically, if you grab an unhighlighted item to drag, it will deselect (unhighlight) everything else
			if (!item.hasClass('selected')) {
				item.addClass('selected').siblings().removeClass('selected');
			}
			
			//////////////////////////////////////////////////////////////////////
			//HERE'S HOW TO PASS THE SELECTED ITEMS TO THE `stop()` FUNCTION:
			
			//Clone the selected items into an array
			var elements = item.parent().children('.selected').clone();
			
			//Add a property to `item` called 'multidrag` that contains the 
			//  selected items, then remove the selected items from the source list
			item.data('multidrag', elements).siblings('.selected').remove();
					
			//Now the selected items exist in memory, attached to the `item`,
			//  so we can access them later when we get to the `stop()` callback
			
			//Create the helper
			var helper = $('<li/>');
			return helper.append(elements);
		},
		stop: function (e, ui) {
			//Now we access those items that we stored in `item`s data!
			var elements = ui.item.data('multidrag');
			
			//`elements` now contains the originally selected items from the source list (the dragged items)!!
			
			//Finally we insert the selected items after the `item`, then remove the `item`, since 
			//  item is a duplicate of one of the selected items.
			ui.item.after(elements).remove();
			elements.removeClass('selected');
			updateExtensions();
		}
	
	});

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

	var ls_available = $("#available_extensions");
	var ls_selected  = $("#selected_extensions");

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

			// Clean list of extensions
			ls_available.find("li").remove();
			ls_selected.find("li").remove();

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

			// $(form).find("[name='" + k + "']").val(v.toString()).trigger('change');
			$.each(data.extensions, function(k_ext, v_ext)
			{
				if (! data.config.operator_extensions.includes(k_ext))
				{
					ls_available.append(sprintf('<li class="list-group-item" data-extension="%s">%s (%s)</li>',k_ext, v_ext, k_ext));
				}
			});

			$.each(data.config.operator_extensions, function(k_ext, v_ext)
			{
				if (data.extensions[v_ext] !== undefined)
				{
					ls_selected.append(sprintf('<li class="list-group-item" data-extension="%s">%s (%s)</li>', v_ext, data.extensions[v_ext], v_ext));
				}
				else
				{
					ls_selected.append(sprintf('<li class="list-group-item" data-extension="%s">%s</li>', v_ext, v_ext));
				}
			});


			
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

//Update the Extensions field
function updateExtensions(){
    var optionTexts = [];
	$("#selected_extensions li").each(function() {
		optionTexts.push($(this).data("extension"))
	});
    $('#operator_extensions').val(optionTexts);
}

function addNumberOperator(e) {
	if (e != undefined)
	{
		e.preventDefault();
	}

	var input 		 = $("#operator_add_number");
	var ls_available = $("#available_extensions");
	var ls_selected  = $("#selected_extensions");

	var new_input  = undefined;
	var new_number = input.val().trim();

	if (new_number == "")
	{
		fpbxToast(_("No number specified!"), '', 'error');
		input.focus();
	}
	else
	{
		var isSelectedExtension = false;
		ls_selected.each(function()
		{
			$(this).find('li').each(function()
			{
				var current   = $(this);
				var extension = current.data('extension');
				if (extension == new_number)
				{
					isSelectedExtension = true;
					fpbxToast(_("The number is already in the operator list."), '', 'error');
					input.focus();
					return true;
				}
			});
		});

		if (isSelectedExtension == false)
		{
			ls_available.each(function()
			{
				$(this).find('li').each(function()
				{
					var current 	= $(this);
					var extension 	= current.data('extension');

					if (extension !== undefined)
					{
						if (extension == new_number)
						{
							new_input = current.clone();
							current.remove();
							return true;
						}
					}
				});
			});

			if (new_input == undefined) 
			{
				new_input = sprintf('<li class="list-group-item" data-extension="%s" data-manual="yes">%s</li>', new_number, new_number);
			}

			ls_selected.append(new_input);
			updateExtensions();
		}
	}
}