var global_module_hotelwakeup_i18n = i18nGet('messages');
var supportedHTML5 = "";

disabledSettings(true);

$(document).ready(function()
{
	loadSettings();
	
	$('#btn_load_settings').on("click", loadSettings);
	$('#btn_save_settings').on("click", saveSettings);

	$('.btn-clean-input').on("click", function(e) {
		e.preventDefault();
		input = $(this).closest(".form-group").find("input");
		input.val("").change();
	});

	$('.btn-copy-default').on("click", function(e) {
		e.preventDefault();
		input = $(this).closest(".form-group").find("input");
		input.val(input.attr('placeholder')).change();
	});

	$('.btn-restart-input').on("click", function(e) {
		e.preventDefault();
		input = $(this).closest(".form-group").find("input");
		loadSettings(undefined, input.attr('id'));
	});

	$("#SayUnixTime").on("change input", delay(500, function (e)
	{
		e.preventDefault();
		SayUnixTimeCheck(e);
	}));

	$(".btn-cmd-play").on("click", function(e) {
		e.preventDefault();
		playFile(e);
	});

	$(".jp-jplayer").each(function()
	{
		if (supportedHTML5 == "") { supportedHTML5 = getSupportedHTML5(); }
		$(this).jPlayer({
			ready: function(event) {
				$(this).closest(".form-group").find(".btn-cmd-play").removeClass("hidden");
			},
			swfPath: window.location.origin + "/admin/assets/js",
			supplied: supportedHTML5,
		});
	});

});


function loadSettings(e, idmsg)
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

			if (idmsg == undefined)
			{
				messageform.reset();
			}
			var messages = data.data;
			$.each(messages, function(index, item)
			{
				if (idmsg != undefined && idmsg != index)
				{
					return;
				}

				var input  = $("#" + index);
				var newval = item.join(",");
				if (input.attr('placeholder') != newval )
				{
					input.val(newval).change();
				}
			});

			if (idmsg != undefined)
			{
				fpbxToast(i18n_mod("SETTING_RELOAD_OK"), '', 'success' );
			}

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

function SayUnixTimeCheck(e) 
{
	let t 			= e.target || e.srcElement;
	let val 		= $(t).val();
	let ok 			= ! /[^AaBbhdeYIlHkMmPpQqRST]/g.test(val);
	let example_all = [];
	let date 		= new Date();

	let ex 			= $("#SayUnixTimeExample");
	let exico		= $("#SayUnixTimeExampleIcon");
	let exbox		= $("#SayUnixTimeExampleBox");
	
	for (const c of val)
	{
		example = c ;
		switch (c)
		{
			case 'R':
				// 24 Hour, Minute
				// 24 hour time, including minute (HM)
				example += ' (24 Hour, Minute)';
				break;

			case 'T':
				// 24 Hour, Minute, Second
				// 24 hour clock with minute and second (HMS)
				example += ' (24 Hour, Minute, Second)';
				break;

			case 'l': //l(lower ell)
			case 'I': //I(capital eye)
				// Hour, 12 hour clock
				// one, two, three, …, twelve
				example += ' (Hour, 12 hour clock)';
				break;

			case 'k':
				// Hour, 24 hour clock
				// ?, one, two, three, …, twenty three
				break;

			case 'H':
				// Hour, 24 hour clock
				// ?, oh one, oh two, …, oh nine, ten, eleven, …, twenty-three
				break;
			
			case 'p':
			case 'P':
				// AM or PM
				// ay em / pee em
				example += ' (AM or PM)';
				break;

			case 'M':
				// Minute
				// ?, oh one, oh two, … fifty-nine
				example += ' (Minute)';
				break;
			
			case 'S':
				example += ' (Seconds)';
				break;

			case 'a':
			case 'A':
				// Day of week
				// Saturday, Sunday, …, Friday
				example += ' (Day of week)';
				break;

			case 'd':
			case 'e':
				// numeric day of month
				// first, second, …, thirty-first
				example += ' (numeric day of month)';
				break;

			case 'h':
			case 'b':
			case 'B':
				// Month name
				// January, February, …, December
				example += ' (Month name)';
				break;
			
			case 'm': //(in CVS HEAD)
				// Month number
				// Say number of month (first – twelfth)
				example += ' (Month number)';
				break;
			
			case 'Y':
				example += ' (Year)';
				break;

			case 'Q':
				// Date
				// “today”, “yesterday” or ABdY
				example += ' (Date)';
				break;
			
			case 'q':
				// Date
				// “” (for today), “yesterday”, weekday, or ABdY
				example += ' (Date)';
				break;

			default:
				example += ' ????';
		  }
		  example_all.push(example);
	}

	// console.log("Example: " + example_all);

	exico.removeClass("fa-check fa-times fa-spinner fa-spin fa-fw");
	exbox.removeClass("label-primary label-danger label-success");
	if (ok)
	{
		
		exbox.addClass("label-success");
		// exbox.addClass("label-primary");
		ex.text( example_all.join(", ") );
		exico.addClass("fa-check");
	}
	else
	{
		exbox.addClass("label-danger");
		ex.text( "Error: Invalid Value!" );
		exico.addClass("fa-times");
	}

}

function getSupportedHTML5()
{
	let formats = "";
	$.ajax({
		async: false,
		type: 'POST',
		url: window.FreePBX.ajaxurl,
		data: {
			module	 : 'hotelwakeup',
			command	 : 'getsupportedhtml5',
		},
		// dataType: 'json',
		success: function(data)
		{
			if (data.status) {
				formats = data.data;
			}
		},
		error: function(data) {
			console.log("AJAX ERROR (getSupportedHTML5): " + data);
		},
	});
	return formats;
}

async function playFile(e)
{
	let t 	   = e.target || e.srcElement;
	let input  = $(t).closest(".form-group").find("input");
	let player = $(t).closest(".form-group").find(".jp-jplayer");
	let btn    = $(t).closest(".form-group").find(".btn-cmd-play");
	let btnIco = btn.find("i");
	let id 	   = input.attr('id');
	let files  = input.val();
	if (files == "")
	{
		//Set default value
		files = input.attr('placeholder');
	}
	files = files.split(",");

	// $(".btn-cmd-play").addClass("disabled");
	$(".btn-cmd-play").find("i").removeClass("fa-spinner fa-spin active").addClass("fa-play ");
	btnIco.toggleClass("fa-play fa-spinner fa-spin");

	let ajaxData = "";
	let post_data = {
		module	 : 'hotelwakeup',
		command	 : 'gethtml5',
		filenames: files,
		language : $("#language").val(),
	};
	$.ajax({
		async: false,
		type: 'POST',
		url: window.FreePBX.ajaxurl,
		data: post_data,
		dataType: 'json',
		timeout: 30000,
		success: function(data)
		{
			ajaxData = data;
		},
		error: function(data) {
			console.log("ERROR AJAX playFile: " + data);
		},
	});

	if(ajaxData != "" && ajaxData.status)
	{
		let files_error = "";
		for(var key in ajaxData.files)
		{
			let val = ajaxData.files[key];
			if (val.type == "file" && val.status == false) {
				files_error += "<br>" + val.filename;
			}
		}
		if ( files_error != "") { fpbxToast(i18n_mod("PLAY_FILE_NOT_FOUND") + files_error, '', 'error' ); }


		player.on($.jPlayer.event.error, function(event) {
			console.warn(event);
		});
		for(var key in ajaxData.files)
		{
			let val = ajaxData.files[key];
			let file = "";
			let status = "";

			switch(val.type) {
				case "file":
					if (val.status == false) {
						console.log("File not found: " + val.filename)
						continue;
					}
					file = { oga: val.url };
				break;

				case "option":
					if (val.key == "silence" ) {
						await sleep(val.val);
					} else {
						console.log("Key Option Unknown! " + val.key)
					}
				break;

				default:
					console.log("Type Unknown! " + val.type);
					continue;
			}
			
			if (file != "")
			{
				status = "loading";
				player.jPlayer("setMedia", file);
				
				player.one($.jPlayer.event.canplay, function(event) {
					status = "play";
					player.jPlayer("play");
				});
				player.on($.jPlayer.event.play, function(event) {
					status = "play";
					player.jPlayer("pauseOthers", 0);
					player.data("playing", true);
					// btnIco.addClass("active");
				});
				player.on($.jPlayer.event.pause, function(event) {
					status = "end";
					player.data("playing", false);
				});
				player.on($.jPlayer.event.ended, function(event) {
					status = "end";
					player.data("playing", false);
				});

				while ( status != "end" )
				{
					await sleep(10);
				};
				player.jPlayer( "clearMedia" );
			}
		}
	}

	btnIco.removeClass("fa-spinner fa-spin active");
	btnIco.addClass("fa-play");
	// $(".btn-cmd-play").removeClass("disabled");
}

