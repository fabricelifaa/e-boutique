<?php 
if ( ! class_exists( 'WP_List_Table' ) ) { 
	//require_once( ABSPATH . 'wp-admin/includes/class-wp-screen.php' );//added
    //require_once( ABSPATH . 'wp-admin/includes/screen.php' );//added
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );

}

/**
 * summary
 */
class Price_per_cities_districts_table extends WP_List_Table
{
    /**
     * summary
     */
    public function __construct()
    {
        parent::__construct( [
			'singular' => __( 'Price_per_cities_districts', 'e-boutique' ), //singular name of the listed records
			'plural'   => __( 'Price_per_cities_districts', 'e-boutique' ), //plural name of the listed records
			'ajax'     => false //should this table support ajax?

		] );

    }

    public function get_columns(){
	  $columns = array(
	  	
	    'company_name'    => 'Société',
	    'value'      => 'Prix',
	    'delay'      => 'Délais de livraison',
	  );
	  return $columns;
	}
	

	/*public function column_cb( $item ) {
		return sprintf(
	    '<input type="checkbox" name="bulk-delete[]" value="%s" />', $item['id']
		);
	}	*/


	public function no_items() {
	  _e( 'Aucun prix disponible.', 'e-boutique');
	}

	/*public function get_bulk_actions() { 
		$actions = [ 'bulk-delete' => 'Delete' ]; 
		return $actions; 
	}*/
	public function column_default($item, $column_name) {
    	return $item[$column_name];
	}



	public function get_price_list(){
		global $wpdb;
    	$table_delivery_companies = $wpdb->prefix . 'delivery_companies';
		$table_delivery_pricing = $wpdb->prefix . 'delivery_pricing';
		$table_delivery_delay = $wpdb->prefix . 'delivery_delay';

		if (isset($_GET['ci'])) {
			$item_id = $_GET['ci'];
			$results = $wpdb->get_results("SELECT * FROM $table_delivery_companies, $table_delivery_pricing, $table_delivery_delay  WHERE $table_delivery_companies.id = $table_delivery_pricing.delivery_companies_id and $table_delivery_pricing.id= $table_delivery_delay.pricing_id and $table_delivery_pricing.delivery_cities_id='$item_id';", ARRAY_A);
		}else{
			$results= array();
		}
    	
    	return $results;
	}

	public function get_sortable_columns() {
		$sortable_columns = array(
		    'company_name' => array('company_name',true),
		    'value'   => array('value',true),
		    'delay'   => array('delay',true),
		  );
	  return $sortable_columns;
	}

	public function usort_reorder($a, $b) {
		  
		
			// If no sort, default to title
			$orderby = ( ! empty( $_GET['orderby'] ) ) ? $_GET['orderby'] : 'company_name';
			  // If no order, default to asc
			$order = ( ! empty($_GET['order'] ) ) ? $_GET['order'] : 'asc';
			  // Determine sort order
			$result = strcmp( $a[$orderby], $b[$orderby] );
			  // Send final sort direction to usort
			return ( $order === 'asc' ) ? $result : -$result;
	}

	public function prepare_items() {
		$columns = $this->get_columns();
		$hidden = array();
		$sortable = $this->get_sortable_columns();
		$data = $this->get_price_list();
		$per_page = 5;
		$current_page = $this->get_pagenum();
		$total_items = count($data);

		$data_per_page = array_slice($data,(($current_page-1)*$per_page),$per_page);
		
		$this->set_pagination_args(array(
		    'total_items' => $total_items,                  //WE have to calculate the total number of items
		    'per_page'    => $per_page,                   //WE have to determine how many items to show on a page
		));
		if (isset($_POST['s']) ) {
			
		}
		$this->_column_headers = array($columns, $hidden, $sortable);
		usort( $data_per_page, array( &$this, 'usort_reorder' ) );
		$this->items = $data_per_page;
		
	}

}
