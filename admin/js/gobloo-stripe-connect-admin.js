(function( $ ) {
	'use strict';
	setTimeout(function(){
		$('.gobloo-admin-selector').on('change',function(e){
			e.preventDefault();
			var url = $(this).find('option:selected').attr('data-href');
			$(this).find('option:first-child').prop('selected',true);
			$(this).parent().find('.hidden-qoute-link').attr('href',url).get(0).click();
			// var win = window.open(url, '_blank');
			// if (win) {
			// 	//Browser has allowed it to be opened
			// 	win.focus();
			// } 
		});
	},3000);
	

})( jQuery );
