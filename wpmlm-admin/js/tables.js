jQuery(document).ready(function(){
	jQuery('#dyntable2').dataTable({
		"sPaginationType": "full_numbers",
		"aaSortingFixed": [[0,'asc']],
		"fnDrawCallback": function(oSettings) {
            jQuery('input:checkbox,input:radio').uniform();
			jQuery.uniform.update();
        }
	});
	
	///// TRANSFORM CHECKBOX AND RADIO BOX USING UNIFORM PLUGIN /////
	jQuery('input:checkbox,input:radio').uniform();
	
	
});