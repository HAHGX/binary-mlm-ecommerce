///// NOTIFICATION CLOSE BUTTON /////
jQuery(document).ready(function(){	
	jQuery('.notibar .close').click(function(){
		jQuery(this).parent().fadeOut(function(){
			jQuery(this).remove();
		});
	});
	
	jQuery('#dyntable2').dataTable({
		"sPaginationType": "full_numbers",
		"fnDrawCallback": function(oSettings) {
            //jQuery('input:checkbox,input:radio').uniform();
			//jQuery.uniform.update();
        }
	});
	
	///// TRANSFORM CHECKBOX AND RADIO BOX USING UNIFORM PLUGIN /////
	//jQuery('input:checkbox,input:radio').uniform();
	
	
});