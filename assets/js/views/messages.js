disabledSettings(true);
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
		module	 : 'hotelwakeup',
		command	 : 'getmessage',
		language : $("#language").val(),
	};
	disabledSettings(true);
	$.post(window.FreePBX.ajaxurl, post_data, function(data) 
	{
		if( ! data.status)
		{
			fpbxToast(data.message, '', 'error');
		}
		else
		{
			if (e != undefined)
			{
				fpbxToast(data.message, '', 'success' );
			}

			messageform.reset();
			var messages = data.data;
			$.each(messages, function(index, item) {
				var input  = $("#" + index);
				var newval = item.join(",");
				if (input.attr('placeholder') != newval ) {
					input.val(newval);
				}
			});			
			disabledSettings(false);
		}
	});
}

function saveSettings(e)
{
	e.preventDefault();
	var post_data = {
		module	 : 'hotelwakeup',
		command	 : 'setmessage',
		language : $("#language").val(),
		messages : {},
	};
	$("form#messageform :input").each(function() {
		var input = $(this);
		post_data['messages'][input.attr('id')] = input.val();
	});
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

function disabledSettings(new_status)
{
	$("form#messageform :input").each(function() {
		$(this).prop("disabled", new_status);
	});
}
