<?php 

function template_get_e_boutique_cities_list(){
	global $wpdb;
    $table_delivery_cities = $wpdb->prefix . 'delivery_cities';
    if (!is_admin()) {
    	$results = $wpdb->get_results("SELECT * FROM $table_delivery_cities");
    	return $results;
    }
    //retrun object of cities and districts list
    
}

function template_get_districts($id){
	//set id of city and get all districts of this city
	global $wpdb;
	$table_delivery_cities = $wpdb->prefix . 'delivery_cities';
	if (isset($id) && !empty($id)) {
	    if (!is_admin()) {
	    	$results = $wpdb->get_results("SELECT * FROM $table_delivery_cities WHERE Parent = '$id'");
		    //return query object
		    return $results;
	    }
	}else{
		return false;
	}
    
}

function template_get_price($id){
	//set id of city and get all districts of this city
	global $wpdb;
	$table_delivery_cities = $wpdb->prefix . 'delivery_cities';
	$table_delivery_pricing = $wpdb->prefix . 'delivery_pricing';
	$table_delivery_delay = $wpdb->prefix . 'delivery_delay';
	if (isset($id) && !empty($id)) {
	   if (!is_admin()) {
	   		$results = $wpdb->get_results("SELECT * FROM $table_delivery_cities, $table_delivery_pricing, $table_delivery_delay  WHERE $table_delivery_cities.id = $table_delivery_pricing.delivery_cities_id and $table_delivery_pricing.id= $table_delivery_delay.pricing_id and $table_delivery_pricing.delivery_companies_id='$id';");
		    //return query object
		    return $results;
	   }
	}else{
		return false;
	}
    
}