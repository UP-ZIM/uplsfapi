define(['jquery'], function($) {

    $(document).ready(function() {
		// console.log('orcid plugin js is working');
        $( ".icon-lsfapi-ext" ).parent().parent('.help-block').css( "display", "none" );
        $( ".icon-lsfapi-ext" ).parent().parent('.help-block').next().css( "display", "none" );
    });
});