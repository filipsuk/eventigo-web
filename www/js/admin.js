$(function() {
	$('select').select2({
		width: '100%'
	});

	$('.datetime').datetimepicker({
		locale: 'cs',
		format: "D. M. YYYY LT"
	});
});