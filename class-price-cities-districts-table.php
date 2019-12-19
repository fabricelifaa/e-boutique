<?php 
if ( ! class_exists( 'WP_List_Table' ) ) { 
	//require_once( ABSPATH . 'wp-admin/includes/class-wp-screen.php' );//added
    //require_once( ABSPATH . 'wp-admin/includes/screen.php' );//added
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );

}

/**
 * summary
 */
class Price_cities_districts_table extends WP_List_Table
{
    /**
     * summary
     */
    public function __construct()
    {
        parent::__construct( [
			'singular' => __( 'Price_cities_districts', 'e-boutique' ), //singular name of the listed records
			'plural'   => __( 'Price_cities_districts', 'e-boutique' ), //plural name of the listed records
			'ajax'     => false //should this table support ajax?

		] );

    }

    public function get_columns(){
	  $columns = array(
	  	//'cb'      => '',
	    'label'    => 'Nom',
	    'Parent'      => 'Type'
	  );
	  return $columns;
	}
	

	/*public function column_cb( $item ) {
		return sprintf(
	    '<input type="checkbox" name="bulk-delete[]" value="%s" />', $item['id']
		);
	}	*/

	public function column_label($item) {
		global $wpdb;
		$table_delivery_cities = $wpdb->prefix . 'delivery_cities';
	  /*$actions = array(
	            'edit'      => sprintf('<a href="?page=%s&action=%s&ci=%s">Modifier</a>','edit_cities_districts','edit',$item['id']),
	            'price'      => sprintf('<a href="?page=%s&action=%s&ci=%s">Voir les prix</a>','price_cities_districts_page','view',$item['id']),
	            'delete'    => sprintf('<a href="?page=%s&action=%s&city_district=%s">Supprimé</a>',$_REQUEST['page'],'delete',$item['id']),
	        );*/
	  	$item_id = $item['Parent'];

    	$result= $wpdb->get_results("SELECT * FROM $table_delivery_cities WHERE id='$item_id'");
    	$label= (isset($result[0]->label) ? $result[0]->label.", " : '')  .$item['label'];

	  return sprintf('<a href="?page=%s&action=%s&ci=%s">%s</a>','price_per_cities_districts','view',$item['id'], $label);
	}

	public function column_Parent($item){
		return ($item['Parent'] == 0 ? 'Ville':'Quartier');
	}

	public function no_items() {
	  _e( 'Aucun villes/Quartiers disponible.', 'e-boutique');
	}

	/*public function get_bulk_actions() { 
		$actions = [ 'bulk-delete' => 'Delete' ]; 
		return $actions; 
	}*/
	public function column_default($item, $column_name) {
    	return $item[$column_name];
	}



	public function get_cities_list(){
		global $wpdb;
    	$table_delivery_cities = $wpdb->prefix . 'delivery_cities';
    	if (isset($_POST['s'])) {
    		$search= "%".strip_tags($_POST['s'])."%";
    		$results = $wpdb->get_results("SELECT * FROM $table_delivery_cities WHERE label LIKE '$search'", ARRAY_A);
    		
    	}else{
    		$results = $wpdb->get_results("SELECT * FROM $table_delivery_cities", ARRAY_A);

    	}
    	return $results;
	}

	public function get_sortable_columns() {
		$sortable_columns = array(
		    'label' => array('label',true),
		    'Parent'   => array('Parent',true)
		  );
	  return $sortable_columns;
	}

	public function usort_reorder( $a, $b ) {
		  
		
			// If no sort, default to title
			$orderby = ( ! empty( $_GET['orderby'] ) ) ? $_GET['orderby'] : 'label';
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
		$data = $this->get_cities_list();
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
