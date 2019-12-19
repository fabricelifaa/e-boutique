<?php 
if ( ! class_exists( 'WP_List_Table' ) ) { 
	//require_once( ABSPATH . 'wp-admin/includes/class-wp-screen.php' );//added
    //require_once( ABSPATH . 'wp-admin/includes/screen.php' );//added
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );

}

/**
 * summary
 */
class Companies_delivery_tables extends WP_List_Table
{
    /**
     * summary
     */
    public function __construct()
    {
        parent::__construct( [
			'singular' => __( 'Companies', 'e-boutique' ), //singular name of the listed records
			'plural'   => __( 'Companies', 'e-boutique' ), //plural name of the listed records
			'ajax'     => false //should this table support ajax?

		] );

    }

    public function get_columns(){
	  $columns = array(
	  	'cb'      => '',
	  	'logo'		=> 'Logo',
	    'company_name'    => 'Société',
	    'address'      => 'Adresse',
	    'phone'		=> 'Téléphone',
	    'commercial_register' => 'Registre',
	    'email' 	=> 'Email',
	    'ifu'		=> 'IFU'
	  );
	  return $columns;
	}
	

	public function column_cb( $item ) {
		return sprintf(
	    '<input type="checkbox" name="companies-delete[]" value="%s" />', $item['id']
		);
	}	

	public function column_company_name($item) {
	  $actions = array(
	            'edit'      => sprintf('<a href="?page=%s&action=%s&ci=%s">Modifier</a>','edit_delivery_companies','edit',$item['id']),
	            'view'      => sprintf('<a href="?page=%s&action=%s&ci=%s">Voir les prix</a>','price_per_company','view',$item['id']),
	            'delete'    => sprintf('<a href="?page=%s&action=%s&companies_delivery=%s">Supprimé</a>',$_REQUEST['page'],'delete',$item['id']),
	        );

	  return sprintf('%1$s %2$s', $item['company_name'], $this->row_actions($actions) );
	}

	public function column_logo($item){

		return sprintf(
	        '<img src="%s" width=45 height=45/>',
	        $item['logo']
    	);
	}

	public function no_items() {
	  _e( 'Pas de Société de livraison disponible.', 'e-boutique');
	}

	public function get_bulk_actions() { 
		$actions = [ 'companies-delete' => 'Delete' ]; 
		return $actions; 
	}
	public function column_default($item, $column_name) {
    	return $item[$column_name];
	}



	public function get_delivery_companies_list(){
		global $wpdb;
    	$table_delivery_companies = $wpdb->prefix . 'delivery_companies';
    	if (isset($_POST['s'])){
    		$search= "%".strip_tags($_POST['s'])."%";;
    		$results = $wpdb->get_results("SELECT * FROM $table_delivery_companies WHERE company_name LIKE '$search'", ARRAY_A);
    	}else{
    		$results = $wpdb->get_results("SELECT * FROM $table_delivery_companies", ARRAY_A);
    	}
    	return $results;
	}

	public function get_sortable_columns() {
		$sortable_columns = array(
		    'company_name' => array('company_name',true),
		    'address'   => array('address',true),
		    /*'phone'		=> array('phone', false),
		    'logo'		=> array('logo', false),
		    'email'		=> array('email', false),
		    'commercial_register'		=> array('commercial_register', false),
		    'ifu'		=> array('ifu', false),*/

		  );
	  return $sortable_columns;
	}

	public function usort_reorder( $a, $b ) {
		  
		if ($_GET['page']== 'list_companies') {
			// If no sort, default to title
			$orderby = ( ! empty( $_GET['orderby'] ) ) ? $_GET['orderby'] : 'id';
			  // If no order, default to asc
			$order = ( ! empty($_GET['order'] ) ) ? $_GET['order'] : 'asc';
			  // Determine sort order
			$result = strcmp( $a[$orderby], $b[$orderby] );
			  // Send final sort direction to usort
			return ( $order === 'asc' ) ? $result : -$result;
		}
	}

	public function prepare_items() {
		$columns = $this->get_columns();
		$hidden = array();
		$sortable = $this->get_sortable_columns();
		$data = $this->get_delivery_companies_list();
		$per_page = 3;
		$current_page = $this->get_pagenum();
		$total_items = count($data);

		$data_per_page = array_slice($data,(($current_page-1)*$per_page),$per_page);
		
		$this->set_pagination_args(array(
		    'total_items' => $total_items,                  //WE have to calculate the total number of items
		    'per_page'    => $per_page,                   //WE have to determine how many items to show on a page
		));
		$this->_column_headers = array($columns, $hidden, $sortable);
		usort( $data_per_page, array( &$this, 'usort_reorder' ) );
		$this->items = $data_per_page;
		
	}

}
