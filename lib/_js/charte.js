$(function() {
	$("#validationSubmit").attr("disabled", "disabled");
	$("#validation").click(function() {
		if ($(this).is(":checked")) {
			$("#validationSubmit").removeAttr("disabled");
		} else {
			$("#validationSubmit").attr("disabled", "disabled");
		}
	});
});