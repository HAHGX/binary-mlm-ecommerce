function init() {
	tinyMCEPopup.resizeToInnerSize();
}

function getCheckedValue(radioObj) {
	if(!radioObj)
		return "";
	var radioLength = radioObj.length;
	if(radioLength == undefined)
		if(radioObj.checked)
			return radioObj.value;
		else
			return "";
	for(var i = 0; i < radioLength; i++) {
		if(radioObj[i].checked) {
			return radioObj[i].value;
		}
	}
	return "";
}

function insertWPMLMLink() {
	var tagtext;
	var select_category=document.getElementById('wpmlm_category_panel');
	var category = document.getElementById('wpmlm_category');
	var slider = document.getElementById('product_slider_panel');
	var add_product = document.getElementById('add_product_panel');
	
	// who is active ?
	if (select_category.className.indexOf('current') != -1) {
		
		items_per_page = jQuery('#wpmlm_perpage').val();
	//work out which radio button is selected and get the value
		for (var i=0; i < document.WPMLM.wpmlm_sale_shortcode.length; i++)
		   {
		   if (document.WPMLM.wpmlm_sale_shortcode[i].checked)
		      {
		      var shortcode = document.WPMLM.wpmlm_sale_shortcode[i].value;
		      }
		   }
		
		var shortcodeid = shortcode;
		var categoryid = category.value;
		var items_per_page = 0;

		if (categoryid > 0 || shortcodeid == 1) {
		
			if (shortcodeid == 1)
				tagtext = "[wpmlm_products price='sale']";
				else if (shortcodeid == 2)
					tagtext = "[wpmlm_products category_id='"+categoryid+"' price='sale']";
				else	if (items_per_page > 0)
				tagtext = "[wpmlm_products category_id='"+categoryid+"' number_per_page='"+items_per_page+"']";
			else	
				tagtext = "[wpmlm_products category_id='"+categoryid+"' ]";
		} else {
			tinyMCEPopup.close();
		}
	}
	
	if (slider.className.indexOf('current') != -1) {
		category = document.getElementById('wpmlm_slider_category');
		visi = document.getElementById('wpmlm_slider_visibles');
		var categoryid = category.value;
		var visibles = visi.value;
		
		if (categoryid > 0) {
		
			if (visibles != '') {
				tagtext = "[wpec_product_slider category_id='"+categoryid+"' visible_items='"+visibles+"']";
			} else {
				tagtext = "[wpec_product_slider category_id='"+categoryid+"']";
			}
		
		} 	
		else if(categoryid == 'all'){
			tagtext = "[wpec_product_slider]";
		}else {
			tinyMCEPopup.close();
		}
	}
	
	if (add_product.className.indexOf('current') != -1) {
		
		product = document.getElementById('wpmlm_product_name');
		
			for (var i=0; i < document.WPMLM.wpmlm_product_shortcode.length; i++)
		   {
		   if (document.WPMLM.wpmlm_product_shortcode[i].checked)
		      {
		      var shortcode = document.WPMLM.wpmlm_product_shortcode[i].value;
		      }
		   }
		var productid = product.value;
		var shortcodeid = shortcode ;
		
		if (productid > 0) {
			if (shortcodeid == 1)
				tagtext = "[buy_now_button product_id='"+productid+"']";
			
			if (shortcodeid == 2)
				tagtext = "[add_to_cart="+productid+"]";
			
			if (shortcodeid == 3)
				tagtext = "[wpmlm_products product_id='"+productid+"']";
		} else {
			tinyMCEPopup.close();
		}
	}
	
	if(window.tinyMCE) {
		window.tinyMCE.execInstanceCommand('content', 'mceInsertContent', false, tagtext);
		//Peforms a clean up of the current editor HTML. 
		//tinyMCEPopup.editor.execCommand('mceCleanup');
		//Repaints the editor. Sometimes the browser has graphic glitches. 
		tinyMCEPopup.editor.execCommand('mceRepaint');
		tinyMCEPopup.close();
	}
	return;
}
