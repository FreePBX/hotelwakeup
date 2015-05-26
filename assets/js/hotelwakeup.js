$('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
	if(e.target.id == "settings") {
		$("#submit, #reset").removeClass("hidden");
	} else {
		$("#submit, #reset").addClass("hidden");
	}
});
$(function() {
	$("#day").datepicker();
	$('#time').timepicker();
	$("#savecall").click(function() {
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
		$.post( "ajax.php", {command: "savecall", module: "hotelwakeup", destination: $("#destination").val(), time: $("#time").val(), day: $("#day").val()}, function( data ) {
			callform.reset();
			$("#myModal").modal("hide");
			$("#savecall").prop("disabled",false);
			$('#callgrid').bootstrapTable('refresh');
		});

	});
});
