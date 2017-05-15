$(document).ready(function() {
    $('table tr').click(function() {
        console.log('fire');
	    $(this).parent().children().removeClass('highlight');
	    $(this).addClass('highlight');
    });
})
