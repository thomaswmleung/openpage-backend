$(function()
{
    /*
     * bootstrap-timepicker
     */
    $('#timepicker1').timepicker();
    $('#timepicker2').timepicker({
        minuteStep: 1,
        template: 'modal',
        showSeconds: true,
        showMeridian: false,
        modalBackdrop: true
    });
    $('#timepicker3').timepicker({
        minuteStep: 5,
        showInputs: false,
        disableFocus: true
    });
    $('#timepicker4').timepicker({
        minuteStep: 1,
        secondStep: 5,
        showInputs: false,
        showSeconds: true,
        showMeridian: false
    });
    $('#timepicker5').timepicker({
        template: false,
        showInputs: false,
        minuteStep: 5
    });
	$('#timepicker6').timepicker({
        minuteStep: 5,
        showInputs: false,
        disableFocus: true
    });
    $('.timepicker').timepicker({
        minuteStep: 5,
        showInputs: false,
        disableFocus: true
    });
    $('.duration_timepicker').timepicker({
        minuteStep: 5,
        showInputs: false,
        showMeridian: false,
        showSeconds: true,
        disableFocus: true,
        defaultTime: 'value'
    });

});