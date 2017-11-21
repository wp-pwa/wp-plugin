<?php
/*
Plugin Name: WordPress PWA
Plugin URI: https://wordpress.org/plugins/wordpress-pwa/
Description: WordPress plugin to turn WordPress blogs into Progressive Web Apps.
Version: 1.0.0
Author: WordPress PWA
Author URI:
License: GPL v3
Copyright: Worona Labs SL
*/

if( !class_exists('wp_pwa') ):

class wp_pwa
{
	// vars
	public $plugin_version = '1.0.8';
	public $rest_api_installed 	= false;
	public $rest_api_active 	= false;
	public $rest_api_working	= false;



	/*
	*  Constructor
	*
	*  This function will construct all the neccessary actions, filters and functions for the wordpress-pwa plugin to work
	*
	*  @type	function
	*  @date	10/06/14
	*  @since	0.6.0
	*
	*  @param	N/A
	*  @return	N/A
	*/

	function __construct()
	{
		// actions
		add_action('init', array($this, 'init'), 1);
		add_action('admin_menu', array($this, 'wp_pwa_admin_actions')); //add the admin page
		add_action('admin_init', array($this,'wp_pwa_register_settings')); //register the settings
		add_action('admin_notices',array($this,'wp_pwa_admin_notices')); //Display the validation errors and update messages

		add_action('wp_ajax_sync_with_wp_pwa',array($this,'sync_with_wp_pwa'));
		add_action('wp_ajax_wp_pwa_change_siteid',array($this,'change_siteid_ajax'));
		add_action('wp_ajax_wp_pwa_change_advanced_settings',array($this,'change_advanced_settings_ajax'));

		add_action('plugins_loaded', array($this,'wp_rest_api_plugin_is_installed'));
		add_action('plugins_loaded', array($this,'wp_rest_api_plugin_is_active'));
		add_action('init', array($this,'allow_origin'));

		add_action( 'admin_enqueue_scripts', array( $this, 'register_wp_pwa_scripts' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'register_wp_pwa_styles' ) );

		add_action( 'rest_api_init', function () {
			register_rest_route( 'wp_pwa/v1', '/siteid/', array(
				'methods' => 'GET',
				'callback' => array( $this,'get_wp_pwa_site_id'))
			);
			register_rest_route( 'wp_pwa/v1', '/discover/', array(
				'methods' => 'GET',
				'callback' => array( $this,'discover_url'))
			);
			register_rest_route( 'wp_pwa/v1', '/plugin-version/', array(
				'methods' => 'GET',
				'callback' => array( $this,'get_wp_pwa_plugin_version'))
			);
		});
		// filters
	}

	/*
	*  init
	*
	*  This function is called during the 'init' action and will do things such as:
	*  create custom_post_types, register scripts, add actions / filters
	*
	*  @type	action (init)
	*  @date	10/06/14
	*  @since	0.6.0
	*
	*  @param	N/A
	*  @return	N/A
	*/

	function init()
	{
		// requires
	}

	//settings are being updated via AJAX, this validator is not used now
	function wp_pwa_settings_validator($args){

		if(isset($args['synced_with_wp_pwa']) && $args['synced_with_wp_pwa']=='true'){
			$args['synced_with_wp_pwa'] = true;
		}

    //make sure you return the args
    return $args;
	}

	function wp_pwa_admin_notices(){
		settings_errors();
	}

	function wp_pwa_register_settings() {
		register_setting(
							'wp_pwa_settings',
							'wp_pwa_settings',
							array($this,'wp_pwa_settings_validator')
		);
	}

	/**
 	* Register and enqueue style sheet.
 	*/
	public function register_wp_pwa_styles($hook) {
		wp_register_style('font-awesome', 'https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css', array(), '4.5.0');
		wp_register_style('bulma-css', 'https://cdnjs.cloudflare.com/ajax/libs/bulma/0.0.26/css/bulma.min.css',array('font-awesome'));
	}

	/**
	* Register and enqueue scripts.
	*/
	public function register_wp_pwa_scripts($hook) {
		wp_register_script('wp_pwa_admin_js',plugin_dir_url(__FILE__) . 'admin/js/wp-pwa-admin.js', array( 'jquery' ), true, true);

		wp_enqueue_script('wp_pwa_admin_js');
	}

	/*
	*  wp_pwa_admin_actions
	*
	*  This function is called during the 'admin_menu' action and will do things such as:
	*  add a wp pwa menu page to the Main Menu
	*
	*  @type	action (admin_menu)
	*  @date	18/07/14
	*  @since	0.6.0
	*
	*  @param	N/A
	*  @return	N/A
	*/
	function wp_pwa_admin_actions() {
		$icon_url	= trailingslashit(plugin_dir_url( __FILE__ )) . "assets/wp_pwa_20x20.png";
		$position	= 64.999989; //Right before the "Plugins"

		add_menu_page(
			'PWA - Admin',
			'PWA',
			1,
			'wp-pwa-admin',
			array($this, 'render_wp_pwa_admin'),
			$icon_url,
			$position
		);
	}

	/*
	*  render_wp_pwa_admin
	*
	*  This function is called by the 'wp_pwa_admin_actions' function and will do things such as:
	*  add a wp_pwa page to render the admin content
	*
	*  @type	fucntion called by 'wp_pwa_admin_actions'
	*  @date	18/07/14
	*  @since	0.6.0
	*
	*  @param	N/A
	*  @return	N/A
	*/

	function render_wp_pwa_admin() {
		wp_enqueue_style('bulma-css');
	  include( 'admin/wp-pwa-admin-page.php');
	}

	function get_wp_pwa_site_id() {
		$settings = get_option('wp_pwa_settings');

		if (isset($settings['wp_pwa_siteid'])) {
			$wp_pwa_site_id = $settings["wp_pwa_siteid"];
		} else {
			$wp_pwa_site_id = NULL;
		}

		return array('siteId'=> $wp_pwa_site_id);
	}

	function get_wp_pwa_plugin_version() {
		return array('plugin_version' => $this->plugin_version);
	}

	/*
	*	@param \WP_REST_Request $request Full details about the request
	*/
	function discover_url( $request ) {
		$first_folder = $request['first_folder'];
		$last_folder = $request['last_folder'];

		if (is_null($last_folder)) {
			return array('Error' => 'last_folder is missing');
		}

		// ----------------
		// Post
		// ----------------
		$args = array(
  		'name'        => $last_folder,
  		'numberposts' => 1,
		);
		$post = get_posts($args);
		if ( sizeof($post) > 0 ) {
			return $post[0];
		}

		// ----------------
		// Page
		// ----------------
		$args = array(
  		'name'        => $last_folder,
  		'numberposts' => 1,
			'post_type'		=> 'page',
		);
		$page = get_posts($args);
		if ( sizeof($page) > 0 ) {
			return $page[0];
		}

		// ----------------
		// Author
		// ----------------
		if($first_folder === 'author') {
			$args = array(
				'author_name'		=> $last_folder,
			);
			$author = get_posts($args);
			if ( sizeof($author) > 0 ) {
				return $author[0];
			} else {
				return( new stdClass() ); //empty object instead of null
			}
		}

		// ----------------
		// Category
		// ----------------
		$category = get_term_by('slug',$last_folder,'category');
		if( $category ) {
			return $category;
		}

		// ----------------
		// Tag
		// ----------------
		$tag = get_term_by('slug',$last_folder,'tag');
		if( $tag ) {
			return $tag;
		}

		// ----------------
		// Custom Post type
		// ----------------

		$post_types = get_post_types('','object');
		$post_type = '';

		foreach ($post_types as $p) {
			if( $p->rewrite['slug'] == $first_folder ) {
				$post_type = $p->name;
			}
		}

		if ( $post_type !== '' ) {
			$args = array(
				'name'        => $last_folder,
				'numberposts' => 1,
				'post_type'		=> $post_type,
			);
			$custom_post = get_posts($args);

			if ( sizeof($custom_post) > 0 ) {
				return $custom_post[0];
			}
		}

		// ----------------
		// Custom Taxonomy
		// ----------------
		$taxonomies = get_taxonomies('','object');
		$taxonomy = '';

		foreach ($taxonomies as $t) {
			if( $t->rewrite['slug'] === $first_folder ) {
				$taxonomy = $t->name;
			}
		}

		if ( $taxonomy === '' ) {
			return array('Error' => $first_folder . ' not supported');
		}

		$custom_taxonomy = get_term_by('slug',$last_folder,$taxonomy);

		if( $custom_taxonomy ) {
			return $custom_taxonomy;
		} else {
				return array('Error' => $first_folder . 'not supported');
		}

		// ----------------
		// first_folder not found
		// ----------------
		return array('Error' => $last_folder .' not found');
	}

	function sync_with_wp_pwa() {
		flush_rewrite_rules();

		$settings = get_option('wp_pwa_settings');
		$settings['synced_with_wp_pwa'] = true;

		update_option('wp_pwa_settings', $settings);

		$siteId = $settings['wp_pwa_siteid'];

		wp_send_json( array(
			'status' => 'ok',
			'siteId' => $siteId
		));
	}

	function change_siteid_ajax() {
		flush_rewrite_rules();

		$siteId = $_POST['siteid'];

		if(strlen($siteId)<17) {
			wp_send_json(array(
				'status' => 'error',
				'reason' => 'Site ID is not valid.'
			));
		} else {
			$settings = get_option('wp_pwa_settings');
			$settings['wp_pwa_siteid'] = $siteId;
			$settings["synced_with_wp_pwa"] = true;

			update_option('wp_pwa_settings', $settings);

			wp_send_json( array(
				'status' => 'ok',
			));
		}
	}

	function change_advanced_settings_ajax() {

		$wp_pwa_ssr = $_POST['wp_pwa_ssr'];
		$wp_pwa_static = $_POST['wp_pwa_static'];

		$settings = get_option('wp_pwa_settings');
		$settings['wp_pwa_ssr'] = $wp_pwa_ssr;
		$settings['wp_pwa_static'] = $wp_pwa_static;

		update_option('wp_pwa_settings', $settings);

		wp_send_json( array(
			'status' => 'ok',
		));
	}

	//Checks if the rest-api plugin is installed
	public function wp_rest_api_plugin_is_installed() {
		if ( ! function_exists( 'get_plugins' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}
		$plugins = get_plugins();

		$this->rest_api_installed = isset($plugins['rest-api/plugin.php']);
	}

	//Checks if the rest-api plugin is active
	public function wp_rest_api_plugin_is_active() {
		$this->rest_api_active = class_exists( 'WP_REST_Controller' );
	}

	//Generates the url to 'auto-activate' the rest-api plugin
	public function get_activate_wp_rest_api_plugin_url() {
		$plugin = 'rest-api/plugin.php';
		$plugin_escaped = str_replace('/', '%2F', $plugin);

		$activateUrl = sprintf(admin_url('plugins.php?action=activate&plugin=%s&plugin_status=all&paged=1&s'), $plugin_escaped);

  	// change the plugin request to the plugin to pass the nonce check
  	$_REQUEST['plugin'] = $plugin;
  	$activateUrl = wp_nonce_url($activateUrl, 'activate-plugin_' . $plugin);

  	return $activateUrl;
	}

	//Adds Cross origin * to the header
	function allow_origin() {
    header("Access-Control-Allow-Origin: *");
	}

	//Checks if the json posts endpoint is responding correctly
	function wp_rest_api_endpoint_works() {
		$rest_api_url = get_site_url() . '/wp-json/wp/v2/posts';
		$args = array('timeout' => 10, 'httpversion' => '1.1' );

		$response = wp_remote_get( $rest_api_url, $args );

		if( is_array($response) ) {
			$body = $response['body'];
			$code = $reponse['reponse']['code'];
			$message = $reponse['reponse']['message'];
			$json_reponse = json_decode($body);

			//CHECKS
			// $code != 200
			// json valid
			// json without error message { code: "rest_no_route" }


		} else {
			return false;
		}
	}
}

/*
*  wp_pwa
*
*  The main function responsible for returning the one true wp_pwa Instance to functions everywhere.
*  Use this function like you would a global variable, except without needing to declare the global.
*
*  Example: <?php wp_pwa = wp_pwa(); ?>
*
*  @type	function
*  @date	11/06/14
*  @since	0.6.0
*
*  @param	N/A
*  @return	(object)
*/

function wp_pwa()
{
	global $wp_pwa;

	if( !isset($wp_pwa) )
	{
		$wp_pwa = new wp_pwa();
	}

	$GLOBALS['wp_pwa_path'] = '/' . basename(plugin_dir_path( __FILE__ ));

	return $wp_pwa;
}

// initialize
wp_pwa();

function wp_pwa_activation() {

	//generates a random Site Id
	function generate_siteId() {
		$chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890';
		$chars_length = (strlen($chars) - 1);// Length of character list
		$string = $chars{rand(0, $chars_length)};// Start our string

		for ($i = 1; $i < 17; $i++) {// Generate random string
				$r = $chars{rand(0, $chars_length)};// Grab a random character from our list
				$string .= $r;// Make sure the same two characters don't appear next to each other
		}
		return $string;
	}

	$current_user = wp_get_current_user();
	$email = $current_user->user_email;

	$settings = get_option('wp_pwa_settings');

	if (isset($settings["synced_with_wp_pwa"])) {
		$synced_with_wp_pwa = $settings["synced_with_wp_pwa"];
	} else {
		$synced_with_wp_pwa = false;
	}

	if (isset($settings['wp_pwa_siteid'])) {
		$siteId = $settings['wp_pwa_siteid'];
	} else {
		$siteId = generate_siteId();
	}

	if (isset($settings['wp_pwa_ssr'])) {
		$wp_pwa_ssr = $settings['wp_pwa_ssr'];
	} else {
		$wp_pwa_ssr = 'https://ssr.wp-pwa.com';
	}

	if (isset($settings['wp_pwa_static'])) {
		$wp_pwa_static = $settings['wp_pwa_static'];
	} else {
		$wp_pwa_static = 'https://static.wp-pwa.com';
	}

	$defaults = array("synced_with_wp_pwa" => $synced_with_wp_pwa,
										"wp_pwa_siteid" => $siteId,
										"wp_pwa_ssr" => $wp_pwa_ssr,
										"wp_pwa_static" => $wp_pwa_static);

	if($settings === false){
		add_option('wp_pwa_settings',$defaults , '','yes');
	} else {
		update_option('wp_pwa_settings',$defaults);
	}
	flush_rewrite_rules();
}

register_activation_hook( __FILE__, 'wp_pwa_activation');

endif; // class_exists check
