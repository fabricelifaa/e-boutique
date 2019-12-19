<?php 
/*
Plugin Name: E-BOUTIQUE Delivery
Description: Gestion de système de livraison 
Author: Kamgoko
Author URI:  https://kamgoko.tech
Version: 0.1
*/

require_once('class-cities-districts-tables.php');
require_once('class-companies-delivery-tables.php');
require_once('class-price-cities-districts-table.php');
require_once('class-price-per-cities-districts-table.php');
require_once('class-price-per-company-table.php');

register_activation_hook( __FILE__, 'create_e_boutique_tables');
add_action('admin_menu', 'add_e_boutique_admin_page');
add_action('admin_enqueue_scripts', 'add_custom_file');
add_action( 'admin_notices', 'display_flash_notices', 12 );
register_deactivation_hook( __FILE__, 'delete_e_boutique_tables' );

// Delete tables
function delete_e_boutique_tables() {
	global $wpdb;
	$table_delivery_cities = $wpdb->prefix . 'delivery_cities';
	$table_delivery_companies = $wpdb->prefix . 'delivery_companies';
	$table_delivery_pricing = $wpdb->prefix . 'delivery_pricing';
	$table_delivery_delay = $wpdb->prefix . 'delivery_delay';
	$sqls = array(
		  	'a'=> "DROP TABLE IF EXISTS ".$table_delivery_delay.";",
		  	'b'=> "DROP TABLE IF EXISTS ".$table_delivery_pricing.";",
		  	'c' => "DROP TABLE IF EXISTS ".$table_delivery_companies.";",
		  	'd'=> "DROP TABLE IF EXISTS ".$table_delivery_cities.";",
		  );
	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
	foreach ($sqls as $key => $sql) {
		$wpdb->query($sql);
	}
	delete_option("my_plugin_db_version");
}

function create_e_boutique_tables(){
    	global $wpdb;
	  $charset_collate = $wpdb->get_charset_collate();
	  $table_delivery_cities = $wpdb->prefix . 'delivery_cities';
	  $table_delivery_companies = $wpdb->prefix . 'delivery_companies';
	  $table_delivery_pricing = $wpdb->prefix . 'delivery_pricing';
	  $table_delivery_delay = $wpdb->prefix . 'delivery_delay';
	  $sqls= array(
	  	$table_delivery_cities => "CREATE TABLE IF NOT EXISTS `". $table_delivery_cities ."` (
  		`id` int(11) NOT NULL AUTO_INCREMENT,
  		`Parent` int(11) NOT NULL,
  		`label` varchar(45) DEFAULT NULL,
 		 PRIMARY KEY (`id`)
		) ENGINE=InnoDB $charset_collate;",
		$table_delivery_companies => "
			CREATE TABLE IF NOT EXISTS `".$table_delivery_companies."` (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `company_name` varchar(45) DEFAULT NULL,
			  `address` varchar(255) DEFAULT NULL,
			  `phone` varchar(45) DEFAULT NULL,
			  `logo` varchar(255) DEFAULT NULL,
			  `email` varchar(45) DEFAULT NULL,
			  `commercial_register` varchar(45) DEFAULT NULL,
			  `ifu` varchar(45) DEFAULT NULL,
			  PRIMARY KEY (`id`)
			) ENGINE=InnoDB $charset_collate;",
		$table_delivery_pricing => "CREATE TABLE IF NOT EXISTS `".$table_delivery_pricing."` (
		  `id` int(11) NOT NULL AUTO_INCREMENT,
		  `delivery_companies_id` int(11) NOT NULL,
		  `delivery_cities_id` int(11) NOT NULL,
		  `value` varchar(45) DEFAULT NULL,
		  PRIMARY KEY (`id`,`delivery_companies_id`,`delivery_cities_id`),
		  KEY `fk_delivery_companies_has_delivery_cities_delivery_cities1_idx` (`delivery_cities_id`),
		  KEY `fk_delivery_companies_has_delivery_cities_delivery_companie_idx` (`delivery_companies_id`),
		  CONSTRAINT `fk_delivery_companies_has_delivery_cities_delivery_cities1` FOREIGN KEY (`delivery_cities_id`) REFERENCES `".$table_delivery_cities."` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
		  CONSTRAINT `fk_delivery_companies_has_delivery_cities_delivery_companies` FOREIGN KEY (`delivery_companies_id`) REFERENCES `".$table_delivery_companies."` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;",
		$table_delivery_delay => "CREATE TABLE IF NOT EXISTS `".$table_delivery_delay."` (
		  `id` int(11) NOT NULL AUTO_INCREMENT,
		  `delay` varchar(45) DEFAULT NULL,
		  `pricing_id` int(11) NOT NULL,
		  `pricing_delivery_companies_id` int(11) NOT NULL,
		  `pricing_delivery_cities_id` int(11) NOT NULL,
		  PRIMARY KEY (`id`,`pricing_id`,`pricing_delivery_companies_id`,`pricing_delivery_cities_id`),
		  KEY `fk_delivery_delay_pricing1_idx` (`pricing_id`,`pricing_delivery_companies_id`,`pricing_delivery_cities_id`),
		  CONSTRAINT `fk_delivery_delay_pricing1` FOREIGN KEY (`pricing_id`, `pricing_delivery_companies_id`, `pricing_delivery_cities_id`) REFERENCES `".$table_delivery_pricing."` (`id`, `delivery_companies_id`, `delivery_cities_id`) ON DELETE CASCADE ON UPDATE NO ACTION
		) ENGINE=InnoDB DEFAULT CHARSET=utf8;",
	  );

	  
	  	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		foreach ($sqls as $key => $sql) {
	  		if ($wpdb->get_var("SHOW TABLES LIKE '$key'") != $key) {
				
		    	dbDelta($sql);
			}
		}
		  	  
}

function add_flash_notice( $notice = "", $type = "warning", $dismissible = true ) {
    // Here we return the notices saved on our option, if there are not notices, then an empty array is returned
    $notices = get_option( "my_flash_notices", array() );
 
    $dismissible_text = ( $dismissible ) ? "is-dismissible" : "";
 
    // We add our new notice.
    array_push( $notices, array( 
            "notice" => $notice, 
            "type" => $type, 
            "dismissible" => $dismissible_text
        ) );
 
    // Then we update the option with our notices array
    update_option("my_flash_notices", $notices );
}

function display_flash_notices() {
    $notices = get_option( "my_flash_notices", array() );
     
    // Iterate through our notices to be displayed and print them.
    foreach ( $notices as $notice ) {
        printf('<div class="notice notice-%1$s %2$s"><p>%3$s</p></div>',
            $notice['type'],
            $notice['dismissible'],
            $notice['notice']
        );
    }
 
    // Now we reset our options to prevent notices being displayed forever.
    if( ! empty( $notices ) ) {
        delete_option( "my_flash_notices", array() );
    }
}

function add_e_boutique_admin_page(){
	add_menu_page('e-boutique', 'E-BOUTIQUE', 'manage_options' ,'e-boutique', 'e_boutique_admin_page', 'dashicons-wordpress');
	add_submenu_page('e-boutique', 'delivery_companies', 'Sociétés', 'manage_options', 'list_companies', 'companies_delivery_list_table');    
	add_submenu_page('e-boutique', 'cities_districts', 'Villes et quartiers', 'manage_options', 'list_cities_districts', 'cities_districts_list_table');
	add_submenu_page('e-boutique', 'add_cities_districts', 'Ajouter villes/quartiers', 'manage_options', 'add_cities_districts', 'add_cities_districts_page');
	add_submenu_page('e-boutique', 'add_delivery_companies', 'Ajouter société', 'manage_options', 'add_delivery_companies', 'add_delivery_companies_page');
	add_submenu_page(NULL, NULL, NULL, 'manage_options', 'edit_delivery_companies', 'edit_delivery_companies_page');
	add_submenu_page(NULL, NULL, NULL, 'manage_options', 'edit_cities_districts', 'edit_cities_districts_page');
	add_submenu_page(NULL, NULL, NULL, 'manage_options', 'price_per_cities_districts', 'price_per_cities_districts_page');
	add_submenu_page(NULL, NULL, NULL, 'manage_options', 'price_per_company', 'price_per_company_page');
	add_submenu_page('e-boutique', 'add_delivery_princing', 'Ajouter un prix', 'manage_options', 'add_delivery_princing', 'add_delivery_princing_page');
	add_submenu_page('e-boutique', 'price_cities_districts', 'Prix par villes/quartiers', 'manage_options', 'price_cities_districts', 'price_cities_districts_page');
}

function add_custom_file(){
    wp_enqueue_script( 'e-boutique-custom-js', plugins_url( 'public/js/custom.js', __FILE__ ), array('jquery'));
}

function e_boutique_admin_page(){
	?>
		<div class="wrap">
			<H2>Bienvenu dans le plugin E-boutique</H2>
			<br>
			<br>
			<table>
				<tr>
					<td><a  href="admin.php?page=add_cities_districts" >Ajouter villes/quartiers</a></td>
					<td><a  href="admin.php?page=list_cities_districts" >Listes des Villes/Quartiers</a></td>
					<td><a href="admin.php?page=add_delivery_companies">Ajouter une société</a></td>
					<td><a href="admin.php?page=list_companies">Listes des sociétés</a></td>
					<td><a href="admin.php?page=price_cities_districts">Prix par villes et quartiers</a></td>

				</tr>
			</table>
		</div>
	<?php 
}

function cities_districts_list_table(){
	global $wpdb;
    $table_delivery_cities = $wpdb->prefix . 'delivery_cities';
	if (isset($_GET['action']) and isset($_GET['city_district'])) {
		if ($_GET['action'] == 'delete') {
			$city_district=$_GET['city_district'];
			$wpdb->query("DELETE FROM $table_delivery_cities  WHERE id='$city_district'");
	    	echo "<script>location.replace('admin.php?page=list_cities_districts&upd=delete');</script>";
	    	if ( isset( $_GET['upd'] ) && $_GET['upd']=='delete' ){
		       add_flash_notice( __("Suppression effectué avec succès!"), "warning", true );
		    }
		}
	}
	

    /*if (isset($_POST['new_delivery_cities_submit']) && empty($_POST['new_delivery_cities_type'])) {
		$label = $_POST['new_delivery_cities_label'];
	    $parent = $_POST['new_delivery_cities_parent'];
	    $wpdb->query("INSERT INTO $table_delivery_cities(label,Parent) VALUES('$label','$parent')");
	    echo "<script>location.replace('admin.php?page=e-boutique/e-boutique.php');</script>";
	} elseif (isset($_POST['new_delivery_cities_submit']) && $_POST['new_delivery_cities_type'] == 0) {
		$label = $_POST['new_delivery_cities_label'];
		$parent = $_POST['new_delivery_cities_type'];
		 $wpdb->query("INSERT INTO $table_delivery_cities(label,Parent) VALUES('$label','$parent')");
	    echo "<script>location.replace('admin.php?page=e-boutique/e-boutique.php');</script>";
	}

	if (isset($_GET['del_delivery_cities'])) {
	    $del_id = $_GET['del_delivery_cities'];
	    $wpdb->query("DELETE FROM $table_delivery_cities  WHERE id='$del_id'");
	    echo "<script>location.replace('admin.php?page=e-boutique/e-boutique.php');</script>";
	}*/

	
	$Cities_districts_table = new Cities_districts_table();
	
	if (isset($_POST['action'])) {
		if ($_POST['action'] == 'bulk-delete' && isset($_POST['bulk-delete'])) {
			$bulk_deletes=$_POST['bulk-delete'];
			foreach ($bulk_deletes as $key => $bulk_delete) {
				$wpdb->query("DELETE FROM $table_delivery_cities  WHERE id='$bulk_delete'");
			}
			echo "<script>location.replace('admin.php?page=list_cities_districts&upd=delete');</script>";
			if ( isset( $_GET['upd'] ) && $_GET['upd']=='delete' ){
		       add_flash_notice( __("Suppression effectué avec succès!"), "warning", true );
		    }
		}
		
	}
	?>
	<div class="wrap">
		
		<h1>Villes et quartiers</h1>

		<form method="post" id='cities_disctricts_table'>
			<?php 
				$Cities_districts_table->prepare_items(); 
				$Cities_districts_table->search_box('Recherche', 'cities_districts_search');
  				$Cities_districts_table->display();
			 ?>
		</form>
	
	<br>
	<br>
	<br>
		
	</div>
<?php 

}

function add_cities_districts_page(){
	global $wpdb;
	$table_delivery_cities = $wpdb->prefix . 'delivery_cities';
	

	if (isset($_POST['new_delivery_cities_submit']) && $_POST['new_delivery_cities_type']== '') {
		$label = ($_POST['new_delivery_cities_label']== '' ? 'vide': $_POST['new_delivery_cities_label']);
	    $parent = $_POST['new_delivery_cities_parent'];
	    $wpdb->query("INSERT INTO $table_delivery_cities(label,Parent) VALUES('$label','$parent')");
	    echo "<script>location.replace('admin.php?page=add_cities_districts&upd=add');</script>";
	    if ( isset( $_GET['upd'] ) && $_GET['upd']=='add' ){
	       add_flash_notice( __("Ajouter avec succès!"), "warning", true );
	    }
	} elseif (isset($_POST['new_delivery_cities_submit']) && $_POST['new_delivery_cities_type'] == 0) {
		$label = ($_POST['new_delivery_cities_label']== '' ? 'vide': $_POST['new_delivery_cities_label']);
		$parent = $_POST['new_delivery_cities_type'];
		 $wpdb->query("INSERT INTO $table_delivery_cities(label,Parent) VALUES('$label','$parent')");
	    echo "<script>location.replace('admin.php?page=add_cities_districts&upd=add');</script>";
	    if ( isset( $_GET['upd'] ) && $_GET['upd']=='add' ){
	       add_flash_notice( __("Ajouter avec succès!"), "warning", true );
	    }
	}

	?>
		<div class="wrap">
			<h2>Ajouter une villes ou quartiers</h2>
			<form method="post">
				<table class="form-table">
					<tr>
						<td><label for="new_delivery_cities_label"><b>Nom</b> :</label></td>
						<td><input type="text" name="new_delivery_cities_label" /></td>
					</tr>
					<tr>
						<td><label for=""><b>Types</b> :</label></td>
						<td>
							<select id="new_delivery_cities_type" name="new_delivery_cities_type">
								<option value="0">Villes</option>
								<option value="" >Quartiers</option>
							</select>
							<select id="new_delivery_cities_parent" name="new_delivery_cities_parent" style="display: none;">
								<?php 
									$results = $wpdb->get_results("SELECT * FROM $table_delivery_cities WHERE Parent = 0;");
									
				            			foreach ($results as $key => $result) {
				            				echo "
				            					<option value='$result->id' >$result->label</option>
				            				";
				            			}
						            $results=NULL;
								 ?>
							</select>
						</td>
					</tr>
				</table>
				<?php submit_button('Ajouter', 'primary', 'new_delivery_cities_submit'); ?>
			</form>

		</div>
	<?php 
	
}

function edit_cities_districts_page(){
	global $wpdb;
	$table_delivery_cities = $wpdb->prefix . 'delivery_cities';

	if (isset($_POST['update_delivery_cities_submit']) && $_POST['update_delivery_cities_type']== '') {
		$label = ($_POST['update_delivery_cities_label']== '' ? 'vide': $_POST['update_delivery_cities_label']);
	    $parent = $_POST['update_delivery_cities_parent'];
		$item= $_POST['id'];
		 $wpdb->query("UPDATE $table_delivery_cities SET label='$label',Parent='$parent' WHERE id='$item' ");
	    echo "<script>location.replace('admin.php?page=list_cities_districts&upd=edit');</script>";
	    if ( isset( $_GET['upd'] ) && $_GET['upd']=='edit' ){
	       add_flash_notice( __("Ajouter avec succès!"), "warning", true );
	    }

	} elseif (isset($_POST['update_delivery_cities_submit']) && $_POST['update_delivery_cities_type'] == 0) {
		$label = ($_POST['update_delivery_cities_label']== '' ? 'vide': $_POST['update_delivery_cities_label']);
		$parent = $_POST['update_delivery_cities_type'];
		$item= $_POST['id'];
		 $wpdb->query("UPDATE $table_delivery_cities SET label='$label',Parent='$parent' WHERE id='$item' ");		 
	    echo "<script>location.replace('admin.php?page=list_cities_districts&upd=edit');</script>";
	    if ( isset( $_GET['upd'] ) && $_GET['upd']=='edit' ){
	       add_flash_notice( __("Ajouter avec succès!"), "warning", true );
	    }
	}elseif (isset($_POST['update_delivery_cities_submit']) && $_POST['update_delivery_cities_type'] == 1) {
		$label = ($_POST['update_delivery_cities_label']== '' ? 'vide': $_POST['update_delivery_cities_label']);
		$parent = $_POST['type_hide_id'];
		$item= $_POST['id'];
		 $wpdb->query("UPDATE $table_delivery_cities SET label='$label',Parent='$parent' WHERE id='$item' ");
	    echo "<script>location.replace('admin.php?page=list_cities_districts&upd=edit');</script>";
	    if ( isset( $_GET['upd'] ) && $_GET['upd']=='edit' ){
	       add_flash_notice( __("Ajouter avec succès!"), "warning", true );
	    }
	}

	if (isset($_GET['action']) && $_GET['action'] == 'edit') {
		
		$item = $_GET['ci'];
		$cities = $wpdb->get_results("SELECT * FROM $table_delivery_cities WHERE id= $item");
	}
	?>
		<div class="wrap">
			<h2>Modifier une ville ou quartier</h2>
			<form method="post">
				<table class="form-table">
					<tr>
						<input type="hidden" name="id" value="<?php echo $cities[0]->id; ?>">
					</tr>
					<tr>
						<td><label for="update_delivery_cities_label"><b>Nom</b> :</label></td>
						<td><input type="text" name="update_delivery_cities_label" value="<?php echo $cities[0]->label; ?>" /></td>
					</tr>
					<tr>
						<td><label for=""><b>Types</b> :</label></td>
						<td>
							<input type="hidden" name="type_hide_id" value="<?php echo $cities[0]->Parent; ?>">
							<select id="update_delivery_cities_type" name="update_delivery_cities_type">
								<option value="1">--types--</option>
								<option value="0">Villes</option>
								<option value="" id="quartiers">Quartiers</option>
							</select>
							<select id="update_delivery_cities_parent" name="update_delivery_cities_parent" style="display: none;">
								<?php 
									$results = $wpdb->get_results("SELECT * FROM $table_delivery_cities WHERE Parent = 0");
									
				            			foreach ($results as $key => $result) {
				            				echo "
				            					<option value= $result->id >$result->label</option>
				            				";
				            			}
						            $results=NULL;
								 ?>
							</select>
						</td>
					</tr>
				</table>
				<?php submit_button('Modifier', 'primary', 'update_delivery_cities_submit'); ?>
			</form>

		</div>
	<?php 
	
}

function price_cities_districts_page(){
	global $wpdb;
    $table_delivery_cities = $wpdb->prefix . 'delivery_cities';

    $Price_cities_districts_table= new Price_cities_districts_table();
    ?>
    	<div class="wrap">
		<h2>Listes</h2>
		<p>Recherché la ville ou quartier de votre choix.</p>

		<form method="post" id='price_cities_disctricts_table'>
			<?php 
				$Price_cities_districts_table->prepare_items(); 
				$Price_cities_districts_table->search_box('Recherche', 'price_cities_districts_search');
  				$Price_cities_districts_table->display();
			 ?>
		</form>
	
	<br>
	<br>
	<br>
		
	</div>
    <?php 
}

function price_per_cities_districts_page(){
	global $wpdb;
    $table_delivery_cities = $wpdb->prefix . 'delivery_cities';

    $Price_per_cities_districts_table= new Price_per_cities_districts_table();
    if (isset($_GET['ci'])) {
    	$item= $_GET['ci'];
    	$row= $wpdb->get_results("SELECT * FROM $table_delivery_cities WHERE id= $item");
    	
    }

    ?>
    	<div class="wrap">
		<h2><?php echo $row[0]->label; ?></h2>
		

		<form method="post" id='price_cities_disctricts_table'>
			<?php 
				$Price_per_cities_districts_table->prepare_items(); 
				//$Price_per_cities_districts_table->search_box('Recherche', 'price_per_cities_districts_search');
  				$Price_per_cities_districts_table->display();
			 ?>
		</form>
	
	<br>
	<br>
	<br>
		
	</div>
    <?php 
}

function price_per_company_page(){
	global $wpdb;
    $table_delivery_companies = $wpdb->prefix . 'delivery_companies';

    $Price_per_company_table= new Price_per_company_table();
    if (isset($_GET['ci'])) {
    	$item= $_GET['ci'];
    	$row= $wpdb->get_results("SELECT * FROM $table_delivery_companies WHERE id= $item");
    	
    }

    ?>
    	<div class="wrap">
		<h2><?php echo $row[0]->company_name; ?></h2>
		

		<form method="post" id='price_per_company_table'>
			<?php 
				$Price_per_company_table->prepare_items(); 
				//$Price_per_cities_districts_table->search_box('Recherche', 'price_per_cities_districts_search');
  				$Price_per_company_table->display();
			 ?>
		</form>
	
	<br>
	<br>
	<br>
		
	</div>
    <?php 
}

function companies_delivery_list_table(){
	global $wpdb;
    $table_delivery_companies = $wpdb->prefix . 'delivery_companies';
    
	if (isset($_GET['action']) and isset($_GET['companies_delivery'])) {
		if ($_GET['action'] == 'delete') {
			$company_delivery=$_GET['companies_delivery'];
			$wpdb->query("DELETE FROM $table_delivery_companies  WHERE id='$company_delivery'");
	    	echo "<script>location.replace('admin.php?page=list_companies&upd=delete');</script>";
	    	if ( isset( $_GET['upd'] ) && $_GET['upd']=='delete' ){
			       add_flash_notice( __("Suppression effectué avec succès!"), "warning", true );
			    }
		}
	}

	$delivery_companies_table= new Companies_delivery_tables();
	
	if (isset($_POST['action'])) {
		if ($_POST['action'] == 'companies-delete' && isset($_POST['companies-delete'])) {
			$companies_deletes=$_POST['companies-delete'];
			foreach ($companies_deletes as $key => $companies_delete) {
				$wpdb->query("DELETE FROM $table_delivery_companies  WHERE id='$companies_delete'");
	    		echo "<script>location.replace('admin.php?page=list_companies&upd=delete');</script>";
				if ( isset( $_GET['upd'] ) && $_GET['upd']=='delete' ){
			       add_flash_notice( __("Suppression effectué avec succès!"), "warning", true );
			    }
			}
		}
		
	}
	?>
		<div class="wrap">
			<h1>Société de livraisons</h1>

			<form method="post" id="delivery_companies_table">
				<?php 
					$delivery_companies_table->prepare_items();
					$delivery_companies_table->search_box('Recherche', 'delivery_companies_search');
					$delivery_companies_table->display();
				 ?>
			</form>
		</div>
	<?php


}


function add_delivery_companies_page(){
	global $wpdb;
	$table_delivery_companies = $wpdb->prefix . 'delivery_companies';

	if (isset($_POST['new_delivery_companies_submit'])) {
		$name = (!empty($_POST['new_delivery_companies_name']) ? strip_tags($_POST['new_delivery_companies_name']) : 'vide');
		$address = (!empty($_POST['new_delivery_companies_address']) ? strip_tags($_POST['new_delivery_companies_address']) : 'vide');
		$phone = (!empty($_POST['new_delivery_companies_phone']) ? strip_tags($_POST['new_delivery_companies_address']) : 'vide');
		$email = (!empty($_POST['new_delivery_companies_email']) ? strip_tags($_POST['new_delivery_companies_email']) : 'vide');
		$commercial_register = (!empty($_POST['new_delivery_companies_commercial_register']) ? strip_tags($_POST['new_delivery_companies_commercial_register']) : 'vide');
		$ifu = (!empty($_POST['new_delivery_companies_ifu']) ? strip_tags($_POST['new_delivery_companies_ifu']) : 'vide');
		if (isset($_FILES['new_delivery_companies_logo'])) {
			$upload_overrides = array( 'test_form' => false );
			$file= $_FILES['new_delivery_companies_logo'];
			$logo= wp_handle_upload($file, $upload_overrides);
			$logo = $logo['url'];
		}else {
			$logo = '';
		}
		$wpdb->query("INSERT INTO $table_delivery_companies(company_name, address, phone, logo, email, commercial_register, ifu) VALUES('$name','$address', '$phone', '$logo', '$email', '$commercial_register', '$ifu')");
		echo "<script>location.replace('admin.php?page=add_delivery_companies&upd=add');</script>";
		if ( isset( $_GET['upd'] ) && $_GET['upd']=='add' ){
			add_flash_notice( __("Ajouter avec succès!"), "success", true );
		}
	}

	?>
		<div class="wrap">
			<h2>Ajouter une société</h2>
			<form method="post" enctype="multipart/form-data">
				<table class="form-table">
					<tr>
						<td><label for="new_delivery_companies_name"><b>Nom de la société</b> :</label></td>
						<td><input type="text" name="new_delivery_companies_name" required="" /></td>
					</tr>
					<tr>
						<td><label for="new_delivery_companies_address"><b>Adresse</b> :</label></td>
						<td>
							<input type="text" name="new_delivery_companies_address">
						</td>
					</tr>
					<tr>
						<td><label for="new_delivery_companies_phone"><b>Téléphone</b> :</label></td>
						<td>
							<input type="text" name="new_delivery_companies_phone">
						</td>
					</tr>
					<tr>
						<td><label for="new_delivery_companies_logo"><b>Logo</b> :</label></td>
						<td>
							<input type="file" name="new_delivery_companies_logo" id="new_delivery_companies_logo">
						</td>
					</tr>
					<tr>
						<td><label for="new_delivery_companies_email"><b>Email</b> :</label></td>
						<td>
							<input type="email" name="new_delivery_companies_email">
						</td>
					</tr>
					<tr>
						<td><label for="new_delivery_companies_commercial_register"><b>Regitre de commerce</b> :</label></td>
						<td>
							<input type="text" name="new_delivery_companies_commercial_register">
						</td>
					</tr>
					<tr>
						<td><label for="new_delivery_companies_ifu"><b>IFU</b> :</label></td>
						<td>
							<input type="text" name="new_delivery_companies_ifu">
						</td>
					</tr>
				</table>
				<?php submit_button('Ajouter', 'primary', 'new_delivery_companies_submit'); ?>
			</form>

		</div>

	<?php 

}


function edit_delivery_companies_page(){
	global $wpdb;
	$table_delivery_companies = $wpdb->prefix . 'delivery_companies';

	if (isset($_POST['update_delivery_companies_submit'])) {
		$name = (!empty($_POST['update_delivery_companies_name']) ? strip_tags($_POST['update_delivery_companies_name']) : 'vide');
		$address = (!empty($_POST['update_delivery_companies_address']) ? strip_tags($_POST['update_delivery_companies_address']) : 'vide');
		$phone = (!empty($_POST['update_delivery_companies_phone']) ? strip_tags($_POST['update_delivery_companies_address']) : 'vide');
		$email = (!empty($_POST['update_delivery_companies_email']) ? strip_tags($_POST['update_delivery_companies_email']) : 'vide');
		$commercial_register = (!empty($_POST['update_delivery_companies_commercial_register']) ? strip_tags($_POST['update_delivery_companies_commercial_register']) : 'vide');
		$ifu = (!empty($_POST['update_delivery_companies_ifu']) ? strip_tags($_POST['update_delivery_companies_ifu']) : 'vide');
		$item = $_POST['id'];
		if (isset($_FILES['update_delivery_companies_logo']) && !empty($_FILES['update_delivery_companies_logo']['name'])) {
			$upload_overrides = array( 'test_form' => false );
			$file= $_FILES['update_delivery_companies_logo'];
			$logo= wp_handle_upload($file, $upload_overrides);
			$logo = (is_array($logo) ? $logo['url'] : '');
		}else {
			$logo = $_POST['logo'];
		}
		$wpdb->query("UPDATE $table_delivery_companies SET company_name='$name', address='$address', phone='$phone', logo='$logo', email='$email', commercial_register='$commercial_register', ifu='$ifu' WHERE id = '$item'");
		echo "<script>location.replace('admin.php?page=list_companies&upd=edit');</script>";
		if ( isset( $_GET['upd'] ) && $_GET['upd']=='edit' ){
			add_flash_notice( __("Ajouter avec succès!"), "success", true );
		}
	}
	if (isset($_GET['action']) && $_GET['action'] == 'edit') {
		
		$item = $_GET['ci'];
		$results = $wpdb->get_results("SELECT * FROM $table_delivery_companies WHERE id= $item");
	}

	?>
		<div class="wrap">
			<h2>Modifier une société</h2>
			<form method="post" enctype="multipart/form-data">
				<table class="form-table">
					<tr><input type="hidden" name="id" value="<?php echo $results[0]->id; ?>"></tr>
					<tr>
						<td><label for="update_delivery_companies_name"><b>Nom de la société</b> :</label></td>
						<td><input type="text" name="update_delivery_companies_name" required=""  value="<?php echo $results[0]->company_name; ?>" /></td>
					</tr>
					<tr>
						<td><label for="update_delivery_companies_address"><b>Adresse</b> :</label></td>
						<td>
							<input type="text" name="update_delivery_companies_address" value="<?php echo $results[0]->address; ?>">
						</td>
					</tr>
					<tr>
						<td><label for="update_delivery_companies_phone"><b>Téléphone</b> :</label></td>
						<td>
							<input type="text" name="update_delivery_companies_phone" value="<?php echo $results[0]->phone; ?>">
						</td>
					</tr>
					<tr>
						<td><label for="update_delivery_companies_logo"><b>Logo</b> :</label></td>
						<td>
							<input type="hidden" name="logo" value="<?php echo $results[0]->logo; ?>">
							<input type="file" name="update_delivery_companies_logo" id="update_delivery_companies_logo">
						</td>
					</tr>
					<tr>
						<td><label for="update_delivery_companies_email"><b>Email</b> :</label></td>
						<td>
							<input type="email" name="update_delivery_companies_email" value="<?php echo $results[0]->email; ?>">
						</td>
					</tr>
					<tr>
						<td><label for="update_delivery_companies_commercial_register"><b>Regitre de commerce</b> :</label></td>
						<td>
							<input type="text" name="update_delivery_companies_commercial_register" value="<?php echo $results[0]->commercial_register; ?>">
						</td>
					</tr>
					<tr>
						<td><label for="update_delivery_companies_ifu"><b>IFU</b> :</label></td>
						<td>
							<input type="text" name="update_delivery_companies_ifu" value="<?php echo $results[0]->ifu; ?>">
						</td>
					</tr>
				</table>
				<?php submit_button('Modifier', 'primary', 'update_delivery_companies_submit'); ?>
			</form>

		</div>

	<?php 

}

function add_delivery_princing_page(){
	global $wpdb;
	$table_delivery_cities = $wpdb->prefix . 'delivery_cities';
  	$table_delivery_companies = $wpdb->prefix . 'delivery_companies';
  	$table_delivery_pricing = $wpdb->prefix . 'delivery_pricing';
  	$table_delivery_delay = $wpdb->prefix . 'delivery_delay';

  	if (isset($_POST['new_delivery_pricing_submit'])) {

  		$company= $_POST['new_delivery_pricing_companies'];
  		$city_district = $_POST['new_delivery_pricing_cities'];
  		$delay= $_POST['new_delivery_pricing_delay'];
  		$price = (int) $_POST['new_delivery_price'];



  		$wpdb->query("INSERT INTO $table_delivery_pricing(delivery_companies_id, delivery_cities_id, value) VALUES('$company','$city_district', '$price')");
  		$lastid = $wpdb->insert_id;
  		$wpdb->query("INSERT INTO $table_delivery_delay(delay, pricing_id, pricing_delivery_companies_id, pricing_delivery_cities_id) VALUES('$delay', '$lastid', '$company','$city_district')");
  		echo "<script>location.replace('admin.php?page=add_delivery_princing&upd=add');</script>";
		if ( isset( $_GET['upd'] ) && $_GET['upd']=='add' ){
			add_flash_notice( __("Ajouter avec succès!"), "success", true );
		}
  		
  	}

	?>
		<div class="wrap">
			<h2>Ajouter un prix de livraison</h2>
			<form method="post">
				<table class="form-table">
					<tr>
						<td><label for="new_delivery_pricing_companies"><b>Nom de la société</b> :</label></td>
						<td>
							<select name="new_delivery_pricing_companies">
								<?php 
									$results = $wpdb->get_results("SELECT * FROM $table_delivery_companies");
									
				            			foreach ($results as $key => $result) {
				            				echo "
				            					<option value= $result->id >$result->company_name</option>
				            				";
				            			}
						            $results=NULL;
								 ?>
							</select>
						</td>
					</tr>
					<tr>
						<td><label for="new_delivery_pricing_cities"><b>Ville/quartiers</b> :</label></td>
						<td>
							<select name="new_delivery_pricing_cities">
								<?php 
									$results = $wpdb->get_results("SELECT * FROM $table_delivery_cities");
									
				            			foreach ($results as $key => $result) {
				            				echo "
				            					<option value= $result->id >$result->label</option>
				            				";
				            			}
						            $results=NULL;
								 ?>
							</select>
						</td>
					</tr>
					<tr>
						<td><label for="new_delivery_pricing_delay"><b>Délais de livraison</b> :</label></td>
						<td>
							<select name="new_delivery_pricing_delay">
								<option value="1H">1H</option>
								<option value="3H">3H</option>
								<option value="J+1">J+1</option>
							</select>
						</td>
					</tr>
					<tr>
						<td><label for="new_delivery_price"><b>Prix</b> :</label></td>
						<td>
							<input type="text" name="new_delivery_price">
						</td>
					</tr>
					
				</table>
				<?php submit_button('Ajouter', 'primary', 'new_delivery_pricing_submit'); ?>
			</form>
		</div>
	<?php 
}

require_once('fonctions-helper.php');