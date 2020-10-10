var time = $("#servertime").data("time");
var timezone = $("#servertime").data("zone");
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
	$('#time').timepicker({
		// defaultTime: 'now',
		dropdown: true,
		// scrollbar: true,
		zindex: 10001
	});
	$('#savecall').on("click", saveCall);

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
