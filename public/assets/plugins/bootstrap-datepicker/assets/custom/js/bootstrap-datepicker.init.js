$.fn.bdatepicker = $.fn.datepicker.noConflict();

$(function()
{

	/* DatePicker */
	// default
	$("#datepicker1").bdatepicker({
		format: 'dd-mm-yyyy',
		autoclose: true,
		startDate: "01-02-2015"
	});

	// component
	$('#datepicker2').bdatepicker({
		format: "dd MM yyyy",
		autoclose: true,
		startDate: "01-02-2015"
	});

	// today button
	$('#datepicker3').bdatepicker({
		format: "dd MM yyyy",
		startDate: "2013-02-14",
		autoclose: true,
		todayBtn: true
	});

	// advanced
	$('#datetimepicker4').bdatepicker({
		format: "dd MM yyyy - hh:ii",
        autoclose: true,
        todayBtn: true,
        startDate: "2013-02-14 10:00",
        minuteStep: 10
	});
	
	// meridian
	$('#datetimepicker5').bdatepicker({
		format: "dd MM yyyy - HH:ii P",
	    showMeridian: true,
	    autoclose: true,
	    startDate: "2013-02-14 10:00",
	    todayBtn: true
	});

	// other
	if ($('#datepicker').length) $("#datepicker").bdatepicker({ showOtherMonths:true });
	if ($('#datepicker-inline').length) $('#datepicker-inline').bdatepicker({ inline: true, showOtherMonths:true });

});