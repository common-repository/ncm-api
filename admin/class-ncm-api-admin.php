<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://www.nepalcangroup.com/
 * @since      1.0.0
 *
 * @package    Ncm_Api
 * @subpackage Ncm_Api/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Ncm_Api
 * @subpackage Ncm_Api/admin
 * @author     Nepal Can Move <it@nepalcanmove.com>
 */
class Ncm_Api_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		$valid_pages = array("ncm-api-settings", "ncm-orders-list", "non-ncm-orders-list");
		if(isset($_REQUEST['page'])){

			$page_url = sanitize_title_with_dashes($_REQUEST['page']);
	
			$page = isset($page_url) ? $page_url : "";
	
			if(in_array($page, $valid_pages)){
				//adding css file in NCM Plugin Pages
				wp_enqueue_style( "owt-bootstrap", NCM_API_PLUGIN_URI . 'assets/css/bootstrap.min.css', array(), $this->version, 'all' );
				wp_enqueue_style( "owt-datatables", NCM_API_PLUGIN_URI . 'assets/css/datatables.min.css', array(), $this->version, 'all' );
				wp_enqueue_style( "owt-sweetalert", NCM_API_PLUGIN_URI . 'assets/css/sweetalert.css', array(), $this->version, 'all' );
				wp_enqueue_style( "owt-fontawesome", NCM_API_PLUGIN_URI . 'assets/fontawesome/css/all.css', array(), $this->version, 'all' );
				wp_enqueue_style( "owt-custom", NCM_API_PLUGIN_URI . 'admin/css/ncm-api-admin.css', array(), $this->version, 'all' );
			}
		}

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		$valid_pages = array("ncm-api-settings", "ncm-orders-list", "non-ncm-orders-list");

		if(isset($_REQUEST['page'])){
			$page_url = sanitize_title_with_dashes($_REQUEST['page']);
	
			$page = isset($page_url) ? $page_url : "";
	
			if(in_array($page, $valid_pages)){
		
				wp_enqueue_script("jquery");
			
				wp_enqueue_script( "owt-bootstrap-js", NCM_API_PLUGIN_URI . 'assets/js/bootstrap.bundle.min.js', array( 'jquery' ), $this->version, false );
				wp_enqueue_script( "owt-datatables-js", NCM_API_PLUGIN_URI . 'assets/js/jquery.dataTables.min.js', array( 'jquery' ), $this->version, false );
				wp_enqueue_script( "owt-validate-js", NCM_API_PLUGIN_URI . 'assets/js/jquery.validate.min.js', array( 'jquery' ), $this->version, false );
				wp_enqueue_script( "owt-sweetalert-js", NCM_API_PLUGIN_URI . 'assets/js/sweetalert.min.js', array( 'jquery' ), $this->version, false );
			
				wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/ncm-api-admin.js', array( 'jquery' ), $this->version, false );
			}
		}
	}


	public function ncm_api_menu(){
		// Add the menu item and page
		$page_title = 'NCM API - Dashboard';
		$menu_title = 'NCM API';
		$capability = 'manage_options';
		$slug = 'ncm-api-settings';
		$callback = array( $this, 'ncm_api_dashboard' );
		$icon = 'dashicons-rest-api';
		$position = 40;
	
		add_menu_page( $page_title, $menu_title, $capability, $slug, $callback, $icon, $position );

		add_submenu_page( 'ncm-api-settings', 'NCM API - Dashboard', 'Dashboard', 'manage_options', 'ncm-api-settings', array( $this, 'ncm_api_dashboard' ));

		add_submenu_page( 'ncm-api-settings', 'NCM API - NCM Orders', 'NCM Orders', 'manage_options', 'ncm-orders-list', array( $this, 'ncm_orders' ));

		add_submenu_page( 'ncm-api-settings', 'NCM API - Non NCM Orders', 'Non NCM Orders', 'manage_options', 'non-ncm-orders-list', array( $this, 'non_ncm_orders' ));

	}

	public function ncm_api_dashboard() {
		
		if(isset($_POST['updated'])){
			$updated = wc_clean($_POST['updated']);
			if($updated === 'true'){
				$this->handle_form();
			}
		} 
		
		$url = 'https://portal.nepalcanmove.com/api/v1/announcements';
        
		//Api Authorization Key
	    $token = get_option('api_token', true);
	    $authorization = "Token ".$token;
    
        $arguments = array(
            'method' => 'GET',
            'headers'   => array(
			    'Content-Type' => 'application/json; charset=utf-8', 
			    'Authorization' => $authorization
		    ),
        );

        $response = wp_remote_get( $url, $arguments );

        $data = (array) json_decode( wp_remote_retrieve_body( $response ) );
		
		include_once(NCM_API_PLUGIN_PATH."admin/partials/ncm-api-dashboard.php");

	}

	public function handle_form() {

		$api_token_form = wc_clean($_POST['api_token_form']);
		if(
			! isset( $api_token_form ) ||
			! wp_verify_nonce( $api_token_form, 'api_update' )
		){ ?>
			
		<div class="error">
			<p>Sorry, your nonce was not correct. Please try again.</p>
		</div> 
		<?php
			exit;
		} 
		else {
				update_option( 'api_token', wc_clean($_POST['ncm_token']) );
		?>
    	<div class="updated">
        	<p>Token has been saved!</p>
    	</div> 

		<?php
			
		}
	}

	public function ncm_orders(){
		
		include_once(NCM_API_PLUGIN_PATH."admin/partials/ncm-api-order-list.php");

	} 

	public function non_ncm_orders(){		

		include_once(NCM_API_PLUGIN_PATH."admin/partials/all-order-list.php");
		
	}

}
