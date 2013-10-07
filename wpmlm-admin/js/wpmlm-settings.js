function validtebonusform()
{
	var units = jQuery('#setunit').val(); 
	if(units =='')
	{
		jQuery('#js-msg').html('<div class="notibar msgerror"><a class="close"></a><p>Please enter the Units</p></div>');	
		jQuery('#setunit').focus();
		return false; 
	}
	
	if(isNaN(units))
	{
		jQuery('#js-msg').html('<div class="notibar msgerror"><a class="close"></a><p>Please enter Number only</p></div>');	
		jQuery('#setunit').focus();
		return false; 	
	}
	
	var amounts = jQuery('#setamount').val(); 
	if(amounts =='')
	{
		jQuery('#js-msg').html('<div class="notibar msgerror"><a class="close"></a><p>Please enter the Amount</p></div>');	
		jQuery('#setamount').focus();
		return false; 
	}
	
	if(isNaN(amounts))
	{
		jQuery('#js-msg').html('<div class="notibar msgerror"><a class="close"></a><p>Please enter Number only</p></div>');	
		jQuery('#setamount').focus();
		return false; 
	}
	
	

	document.frm.submit();
}
function deleteBonus(frmaction)
{
	if(confirm('are you sure you want the delete this bonus.'))
	{
		document.frm.action = frmaction;
		document.frm.submit();	
	}else{
		return false
	}
}
function editBonus(frmaction)
{
	document.frm.action = frmaction;
	document.frm.submit();	
}

function updateBonus()
{
	var units = jQuery('#setunit').val(); 
	if(units =='')
	{
		jQuery('#js-msg').html('<div class="notibar msgerror"><a class="close"></a><p>Please enter the Units</p></div>');	
		jQuery('#setunit').focus();
		return false; 
	}
	
	if(isNaN(units))
	{
		jQuery('#js-msg').html('<div class="notibar msgerror"><a class="close"></a><p>Please enter Number only</p></div>');	
		jQuery('#setunit').focus();
		return false; 	
	}
	
	var amounts = jQuery('#setamount').val(); 
	if(amounts =='')
	{
		jQuery('#js-msg').html('<div class="notibar msgerror"><a class="close"></a><p>Please enter the Amount</p></div>');	
		jQuery('#setamount').focus();
		return false; 
	}
	
	if(isNaN(amounts))
	{
		jQuery('#js-msg').html('<div class="notibar msgerror"><a class="close"></a><p>Please enter Number only</p></div>');	
		jQuery('#setamount').focus();
		return false; 
	}
	document.frm.submit();	
}

function cancelBonus(frmaction)
{ 
	document.frm.action = frmaction;
	document.frm.submit();		
}

///// NOTIFICATION CLOSE BUTTON /////
jQuery(document).ready(function(){	
	jQuery('.notibar .close').click(function(){
		jQuery(this).parent().fadeOut(function(){
			jQuery(this).remove();
		});
	});
});