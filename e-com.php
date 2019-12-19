<?php

/**
 * summary
 */

if ( ! class_exists( 'WP_List_Table' ) ) { 
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
} 

class E_com extends WP_List_Table
{
    /**
     * summary
     */
    public function __construct()
    {
    
        $this->init();
    }

    public function init(){
    	register_activation_hook( __FILE__, array($this, 'create_e_com_tables'));
    	add_action('admin_menu', array($this, 'add_e_com_admin_page'));
    	add_action('admin_enqueue_scripts', array($this, 'add_custom_file'));
    }

    public function add_custom_file(){
    	wp_enqueue_script( 'e-com-custom-js', plugins_url( 'public/js/custom.js', __FILE__ ), array('jquery'));
    }

    public function create_e_com_tables(){
    	global $wpdb;
	  $charset_collate = $wpdb->get_charset_collate();
	  $table_delivery_cities = $wpdb->prefix . 'delivery_cities';
	  $table_delivery_companies = $wpdb->prefix . 'delivery_companies';
	  $table_delevery_pricing = $wpdb->prefix . 'delevery_pricing';
	  $table_delivery_delay = $wpdb->prefix . 'delivery_delay';
	  $sqls= array(
	  	$table_delivery_cities => "CREATE TABLE IF NOT EXISTS `". $table_delivery_cities ."` (
		  `id` INT NOT NULL AUTO_INCREMENT,
		  `Parent` INT NOT NULL,
		  `label` VARCHAR(45) NULL,
		  PRIMARY KEY (`id`))
		ENGINE = InnoDB;",
		$table_delivery_companies => "CREATE TABLE IF NOT EXISTS `". $table_delivery_companies ."` (
		  `id` INT NOT NULL AUTO_INCREMENT,
		  `company_name` VARCHAR(45) NULL,
		  `address` VARCHAR(255) NULL,
		  `phone` VARCHAR(45) NULL,
		  `logo` VARCHAR(255) NULL,
		  `email` VARCHAR(45) NULL,
		  `commercial_register` VARCHAR(45) NULL,
		  `ifu` VARCHAR(45) NULL,
		  PRIMARY KEY (`id`))
		ENGINE = InnoDB;",
		$table_delevery_pricing => "CREATE TABLE IF NOT EXISTS `". $table_delevery_pricing ."` (
		  `id` INT NOT NULL AUTO_INCREMENT,
		  `delivery_companies_id` INT NOT NULL,
		  `delivery_cities_id` INT NOT NULL,
		  `value` VARCHAR(45) NULL,
		  PRIMARY KEY (`id`, `delivery_companies_id`, `delivery_cities_id`),
		  INDEX `fk_Delivery-companies_has_Cities-districts_Cities-districts_idx` (`delivery_cities_id` ASC),
		  INDEX `fk_Delivery-companies_has_Cities-districts_Delivery-compani_idx` (`delivery_companies_id` ASC),
		  CONSTRAINT `fk_Delivery-companies_has_Cities-districts_Delivery-companies`
		    FOREIGN KEY (`delivery_companies_id`)
		    REFERENCES `delivery_companies` (`id`)
		    ON DELETE CASCADE
		    ON UPDATE NO ACTION,
		  CONSTRAINT `fk_Delivery-companies_has_Cities-districts_Cities-districts1`
		    FOREIGN KEY (`delivery_cities_id`)
		    REFERENCES `e-com`.`delivery_cities` (`id`)
		    ON DELETE CASCADE
		    ON UPDATE NO ACTION)
		ENGINE = InnoDB;",
		$table_delivery_delay => "CREATE TABLE IF NOT EXISTS `". $table_delivery_delay ."` (
		  `id` INT NOT NULL,
		  `delay` VARCHAR(45) NULL,
		  `Price_id` INT NOT NULL,
		  `delivery_companies_id` INT NOT NULL,
		  `delivery_cities_id` INT NOT NULL,
		  PRIMARY KEY (`id`, `Price_id`, `delivery_companies_id`, `delivery_cities_id`),
		  INDEX `fk_Delevery-delay_Delivery-companies_has_Cities-districts1_idx` (`Price_id` ASC, `delivery_companies_id` ASC, `delivery_cities_id` ASC),
		  CONSTRAINT `fk_Delevery-delay_Delivery-companies_has_Cities-districts1`
		    FOREIGN KEY (`Price_id` , `delivery_companies_id` , `delivery_cities_id`)
		    REFERENCES `delevery_pricing` (`id` , `delivery_companies_id` , `delivery_cities_id`)
		    ON DELETE CASCADE
		    ON UPDATE NO ACTION)
		ENGINE = InnoDB;"
	  );

		foreach ($sqls as $key => $sql) {
	  		if ($wpdb->get_var("SHOW TABLES LIKE '$key'") != $key) {
				require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		    	dbDelta($sql);
			}
		}
		  	  
    }


    public function add_e_com_admin_page(){
    	add_menu_page('e-com', 'E-BOUTIQUE', 'manage_options' ,'e-com', array($this, 'e_com_admin_page'), 'dashicons-wordpress');
		//add_submenu_page('e-com', 'Villes et quartiers', 'Villes et quartiers', 'manage_options', array($this, 'cities_districts_list'));    
    }

    public function get_cities_districts(){


    }

    public function get_colums(){
    	$columns = array(
    'label' => 'Title',
    'parent'    => 'Type',
    'id'      => 'id'
  );
  return $columns;
    }

    public function cities_districts_list(){
	?>
		<div class="wrap">
			<h3>OK</h3>
		</div>
	<?php
	
	}
    

    public function e_com_admin_page(){
    	global $wpdb;
    	$table_delivery_cities = $wpdb->prefix . 'delivery_cities';
		$table_delivery_companies = $wpdb->prefix . 'delivery_companies';
		$table_delevery_pricing = $wpdb->prefix . 'delevery_pricing';
		$table_delivery_delay = $wpdb->prefix . 'delivery_delay';

		if (isset($_POST['new_delivery_cities_submit']) && empty($_POST['new_delivery_cities_type'])) {
			$label = $_POST['new_delivery_cities_label'];
		    $parent = $_POST['new_delivery_cities_parent'];
		    $wpdb->query("INSERT INTO $table_delivery_cities(label,Parent) VALUES('$label','$parent')");
		    echo "<script>location.replace('admin.php?page=e-com/e-com.php');</script>";
		} elseif (isset($_POST['new_delivery_cities_submit']) && $_POST['new_delivery_cities_type'] == 0) {
			$label = $_POST['new_delivery_cities_label'];
			$parent = $_POST['new_delivery_cities_type'];
			 $wpdb->query("INSERT INTO $table_delivery_cities(label,Parent) VALUES('$label','$parent')");
		    echo "<script>location.replace('admin.php?page=e-com/e-com.php');</script>";
		}

		if (isset($_GET['del_delivery_cities'])) {
		    $del_id = $_GET['del_delivery_cities'];
		    $wpdb->query("DELETE FROM $table_delivery_cities  WHERE id='$del_id'");
		    echo "<script>location.replace('admin.php?page=e-com/e-com.php');</script>";
		}


		?>
			<div class="wrap">
				<h2>E-BOUTIQUE Delivery</h2>
				<h5>villes et quartiers</h5>
				<table class="wp-list-table widefat striped">
					<thead>
				        <tr>
				          <th width="25%">ID</th>
				          <th width="25%">Nom</th>
				          <th width="25%">Type</th>
				          <th width="25%">Actions</th>
				        </tr>
				    </thead>
				        <tbody>
				        	<form action="" method="post" id="add_delivery_cities_form">
					        	<tr>
						            <td><input type="text" value="No" disabled></td>
						            <td><input type="text" id="new_delivery_cities_label" name="new_delivery_cities_label"></td>
						            <td><select id="new_delivery_cities_type" name="new_delivery_cities_type">
						            	<option value="0">Villes</option>
						            	<option value="" id="quartiers">Quartiers</option>
						            </select>
						            <select id="new_delivery_cities_parent" name="new_delivery_cities_parent" style="display: none;">
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
						            <td><button id="new_delivery_cities_submit" name="new_delivery_cities_submit" type="submit">INSERT</button></td>
					        	</tr>
					        </form>
					        <?php 
					        	$results_cities= $wpdb->get_results("SELECT * FROM $table_delivery_cities WHERE Parent =0 ");
					        	foreach ($results_cities as $key => $value) {
					        		
					        	
					         ?>
					        <tr>
					        	<td colspan="3"><b><?php echo $value->label; ?></b></td>
					        	<td width='25%'><a href='admin.php?page=e-com/e-com.php&del_delivery_cities=<?php echo $value->id; ?>'><button type='button'>DELETE</button></a></td>
					        </tr>
					        <?php 
					        	$results = $wpdb->get_results("SELECT * FROM $table_delivery_cities WHERE Parent = $value->id ");
					        	foreach ($results as $key => $result){

					        ?>
					        	<tr>
					        		<td width='25%'><?php echo $result->id; ?></td>
					        		<td width='25%'><?php echo $result->label; ?></td>
					        		<td width='25%'><?php echo ($result->Parent == 0 ? 'Ville' : 'Quartier'); ?></td>
					        		<td width='25%'><a href='admin.php?page=e-com/e-com.php&del_delivery_cities=<?php echo $result->id; ?>'><button type='button'>DELETE</button></a></td>
					        	</tr>
					        <?php
					        	}
					        	$results=NULL;
					        	}
					         ?>
				        </tbody>
      				
				</table>
				<br>
				<br>
				<br>
				<?php 


				 ?>
				<h5>Société de livraisons</h5>
				<table class="wp-list-table widefat striped">
					<thead>
				        <tr>
				          <th width="11.111111111%">ID</th>
				          <th width="11.111111111%">Raison sociale</th>
				          <th width="11.111111111%">Adresse</th>
				          <th width="11.111111111%">téléphone</th>
				          <th width="11.111111111%">logo</th>
				          <th width="11.111111111%">email</th>
				          <th width="11.111111111%">registre de commerce</th>
				          <th width="11.111111111%">IFU</th>
				          <th width="11.111111111%">Actions</th>
				        </tr>
				    </thead>
				    <tbody>
				    	<form action="" method="post" id="add_delivery_companies_form">
				    		<td><input type="text" value="AUTO_GENERATED" disabled></td>
				            <td><input type="text" id="new_delivery_cities_label" name="new_delivery_cities_label"></td>
				            <td><input type="text" id="new_delivery_cities_label" name="new_delivery_cities_label"></td>
				            <td><input type="text" id="new_delivery_cities_label" name="new_delivery_cities_label"></td>
				            <td><input type="text" id="new_delivery_cities_label" name="new_delivery_cities_label"></td>
				            <td><input type="text" id="new_delivery_cities_label" name="new_delivery_cities_label"></td>
				            <td><input type="text" id="new_delivery_cities_label" name="new_delivery_cities_label"></td>
				            <td><input type="text" id="new_delivery_cities_label" name="new_delivery_cities_label"></td>

				    	</form>
				    </tbody>
				</table>

			</div>
		<?php
    }
}

new E_com();


