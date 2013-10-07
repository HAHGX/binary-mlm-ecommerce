
	

jQuery(document).ready(function(){	
	jQuery(function() {
		jQuery( "#from" ).datepicker({
			defaultDate: "+1w",
			changeMonth: true,
			dateFormat: 'dd-mm-yy',
			numberOfMonths: 3,
			onSelect: function( selectedDate ) {
				jQuery( "#to" ).datepicker( "option", "minDate", selectedDate );
			}
		});
		jQuery( "#to" ).datepicker({
			defaultDate: "+1w",
			changeMonth: true,
			dateFormat: 'dd-mm-yy',
			numberOfMonths: 3,
			onSelect: function( selectedDate ) {
				jQuery( "#from" ).datepicker( "option", "maxDate", selectedDate );
			}
		});
	});
	
});