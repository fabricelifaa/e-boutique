

jQuery(document).ready(function(){
	jQuery('#new_delivery_cities_type' ).change(function () {
	if (jQuery(this).val()== '') {
		jQuery(this).hide();
		jQuery('#new_delivery_cities_parent').show();
	}
	
});
	jQuery('#update_delivery_cities_type' ).change(function () {
	if (jQuery(this).val()== '') {
		jQuery(this).hide();
		jQuery('#update_delivery_cities_parent').show();
	}
	
});

});
