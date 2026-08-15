var time = $("#servertime").data("time");
var timezone = $("#servertime").data("zone");
var wakeupTimeStep = parseInt($("#servertime").data("timestep"), 10) || 15;
var updateTime = function() {
	$("#servertime span").text(moment.unix(time).tz(timezone).format('HH:mm:ss z'));
	time = time + 1;
};
setInterval(updateTime,1000);

$(document).ready(function()
{
	$("#day").datepicker({
		// autoSize: true,
		// firstDay: 0
	});
	$("#start_date").datepicker({
		// autoSize: true,
		// firstDay: 0
	});
	$("#end_date").datepicker({
		// autoSize: true,
		// firstDay: 0
	});
	$('#time').timepicker({
		// defaultTime: 'now',
		// Force 24h display ("HH:mm", e.g. "14:30") instead of the plugin's
		// default 12h "hh:mm p" (e.g. "2:30 pm"). Note these are this
		// plugin's own format tokens, not PHP's date()/moment's -- HH/mm are
		// zero-padded 24h hour/minutes here.
		timeFormat: 'HH:mm',
		// Step between the times listed in the dropdown, configurable per
		// install on ?display=hotelwakeup_settings (defaults to 15 min).
		// Typing an exact time always works regardless of this value.
		interval: wakeupTimeStep,
		dropdown: true,
		// scrollbar: true,
		zindex: 10001
	});
	$('#savecall').on("click", saveCall);

	// Handle repeat wakeup checkbox
	$('#repeat_wakeup').on('change', function() {
		if ($(this).is(':checked')) {
			$('#single-day-container').hide();
			$('#repeat-options-container').show();
			$('#start-date-container').show();
			handleRepeatTypeChange();
		} else {
			$('#single-day-container').show();
			$('#repeat-options-container').hide();
			$('#start-date-container').hide();
			$('#consecutive-days-container').hide();
			$('#weekdays-container').hide();
			$('#end-date-container').hide();
		}
	});

	// Handle repeat type change
	$('#repeat_type').on('change', handleRepeatTypeChange);

	function handleRepeatTypeChange() {
		var repeatType = $('#repeat_type').val();
		
		// Hide all containers first
		$('#consecutive-days-container').hide();
		$('#weekdays-container').hide();
		$('#end-date-container').hide();
		
		// Show relevant containers based on repeat type
		switch (repeatType) {
			case 'consecutive':
				$('#consecutive-days-container').show();
				break;
			case 'weekdays':
				$('#weekdays-container').show();
				$('#end-date-container').show();
				break;
			case 'until_date':
				$('#end-date-container').show();
				break;
		}
	}

	$("#dlgCreateCall").on('hide.bs.modal', function () {
        $("ul.ui-timepicker-list").hide();
		$("#ui-datepicker-div").hide();
		$('#setlanguage').multiselect('select', '');
		
		// Reset repeat options
		$('#repeat_wakeup').prop('checked', false);
		$('#single-day-container').show();
		$('#repeat-options-container').hide();
		$('#start-date-container').hide();
		$('#consecutive-days-container').hide();
		$('#weekdays-container').hide();
		$('#end-date-container').hide();
		$('#repeat_type').val('consecutive');
		$('#consecutive_days').val(1);
		$('input[name="weekdays[]"]').prop('checked', false);
		
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

	var isRepeat = $("#repeat_wakeup").is(':checked');
	var post_data = {
		command: "savecall",
		module: "hotelwakeup",
		destination: $("#destination").val(),
		time: $("#time").val(),
		language: $("#setlanguage").val(),
		repeat: isRepeat ? 1 : 0
	};

	if (isRepeat) {
		// Validate repeat options
		if($("#start_date").val().trim() === "") {
			warnInvalid($("#start_date"), _("Start date can not be blank"));
			return false;
		}
		
		var repeatType = $("#repeat_type").val();
		post_data.repeat_type = repeatType;
		post_data.start_date = $("#start_date").val();
		
		switch (repeatType) {
			case 'consecutive':
				var consecutiveDays = parseInt($("#consecutive_days").val());
				if (consecutiveDays < 1 || consecutiveDays > 365) {
					warnInvalid($("#consecutive_days"), _("Number of days must be between 1 and 365"));
					return false;
				}
				post_data.consecutive_days = consecutiveDays;
				break;
				
			case 'weekdays':
				var selectedWeekdays = [];
				$('input[name="weekdays[]"]:checked').each(function() {
					selectedWeekdays.push($(this).val());
				});
				if (selectedWeekdays.length === 0) {
					warnInvalid($('input[name="weekdays[]"]:first'), _("At least one weekday must be selected"));
					return false;
				}
				if($("#end_date").val().trim() === "") {
					warnInvalid($("#end_date"), _("End date can not be blank for weekdays repeat"));
					return false;
				}
				post_data.weekdays = selectedWeekdays;
				post_data.end_date = $("#end_date").val();
				break;
				
			case 'until_date':
				if($("#end_date").val().trim() === "") {
					warnInvalid($("#end_date"), _("End date can not be blank"));
					return false;
				}
				post_data.end_date = $("#end_date").val();
				break;
		}
	} else {
		// Single day validation
		if($("#day").val().trim() === "") {
			warnInvalid($("#day"), _("Day can not be blank"));
			return false;
		}
		post_data.day = $("#day").val();
	}

	$("#savecall").prop("disabled",true);
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
