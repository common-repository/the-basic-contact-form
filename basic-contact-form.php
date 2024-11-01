<?php
/*
  Plugin Name: Basic Contact Form
  Plugin URI:  https://hammanitech.com/
  Description: basic contact form plugin. Simple but optional with show message length and google captcha.
  Author:      The Basic Contact Form Team 
  Version:     1.1
  Text Domain: basic-contact-form
  Domain Path: /languages/
 */

// Avoid direct calls to this file.
if ( ! function_exists( 'add_action' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit();
}
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if( !defined('BCF_VERSION')){
define( 'BCF_VERSION', '1.0' );
}

if( !defined('BCF_REQUIRED_WP_VERSION')){
define( 'BCF_REQUIRED_WP_VERSION', '5.9' );
}

if( !defined('BCF_TEXT_DOMAIN')){
define( 'BCF_TEXT_DOMAIN', 'basic-contact-form' );
}




if ( ! function_exists( 'bcf_php_mysql_versions' ) ) {

	add_action( 'rightnow_end', 'bcf_php_mysql_versions', 9 );

	/**
	 * Displays the current server's PHP and MySQL versions right below the WordPress version
	 * in the Right Now dashboard widget.
	 *
	 * @since 1.0.0
	 */
	function bcf_php_mysql_versions() {
		echo wp_kses(
			sprintf(
				/* TRANSLATORS: %1 = php version nr, %2 = mysql version nr. */
				__( '<p>You are running on <strong>PHP %1$s</strong> and <strong>MySQL %2$s</strong>.</p>', 'bcf-example-plugin' ),
				phpversion(),
				$GLOBALS['wpdb']->db_version()
			),
			array(
				'p' => array(),
				'strong' => array(),
			)
		);
	}
}

// Avoid direct calls to Plugin.
if(!defined('WPINC')){
	die;
} 


// Activation Hook database table create

if ( ! function_exists( 'create_bcf_database_table' ) ) {
register_activation_hook( __FILE__, 'create_bcf_database_table' );
function create_bcf_database_table()
{	
		global $wpdb;
		$charset_collate = $wpdb->get_charset_collate();
		$table_name =  $wpdb->prefix.'basic_contact_form';
	
		$sql = "CREATE TABLE $table_name (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
			check_count  varchar(256) NOT NULL,
			email_to  varchar(256) NOT NULL,
			site_key  varchar(256) NOT NULL,
			secret_key  varchar(256) NOT NULL,
			check_recaptcha  varchar(256) NOT NULL,
			UNIQUE KEY id (id)
		) $charset_collate;";
	
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $sql );

		$id = 1;
		$check_count = 0;
		$email_to = get_bloginfo('admin_email');
		$site_key  = '';
		$secret_key = '';
        $check_recaptcha = 0;
	  	
		if( $print-> id == ""   || $print-> check_count == ""   || $print-> email_to == "" || $print-> site_key == ""   || $print-> secret_key == ""){
			$sql = $wpdb->insert(	$table_name , array("id" => $id , "check_count" => $check_count , "email_to" => $email_to ,"site_key" => $site_key , "secret_key" => $secret_key ,"check_recaptcha" => $check_recaptcha ));
		}else {
			?>
			<h1><?php echo esc_html__( "Data is here") ?></h1>
			<?php
		}
 }

}

 // deActivation HooK drop database table

if ( ! function_exists( 'drop_bcf_database_table' ) ) {
register_deactivation_hook(__FILE__, 'drop_bcf_database_table');
function drop_bcf_database_table()
{
	global $wpdb;
	$table_name =  $wpdb->prefix.'basic_contact_form';
    $sql = "DROP TABLE IF EXISTS $table_name";
    $wpdb->query($sql);
    delete_option("devnote_plugin_db_version");
}

}
//pluign constant defined

if( !defined('BCF_PLUGIN_DIR')) {
	define('BCF_PLUGIN_DIR ' , plugin_dir_url(__FILE__)); 
}




// add relative files in plugin

if( !function_exists('bcf_plugin_scripts')) {

    function bcf_plugin_scripts(){
	  
	 wp_enqueue_script( 'jquery');
     wp_enqueue_style('bcf-css' , plugin_dir_url(__FILE__). 'assets/css/style.css' );  
	 
	 wp_enqueue_script('bcf-ajax' , plugin_dir_url(__FILE__). 'assets/js/main.js', 'jQuery' , '1.0.0' , true ); 

      wp_localize_script('bcf-ajax' , 'bcf_ajax_url' , array('ajax_url' => admin_url('admin-ajax.php')));
    }
add_action('wp_enqueue_scripts' ,'bcf_plugin_scripts'); 
 } 


//admin site file add in Plugin
if ( ! function_exists( 'bcf_load_admin_styles' ) ) {
function bcf_load_admin_styles() {
	wp_enqueue_style('bcf-admin-css' , plugin_dir_url(__FILE__). 'assets/css/admin-style.css' );   
}

add_action( 'admin_enqueue_scripts', 'bcf_load_admin_styles' );

}
 
// Add admin site menu
if ( ! function_exists( 'bcf_register_menu_page' ) ) {
 function bcf_register_menu_page() { 
 
  add_menu_page('BCF Form Templates' ,'BCF Settings' , 'manage_options' , 'basic-contact-form/includes/bcf-admin-setting-controls.php' , '' , 'dashicons-email' , 30);
  
}
add_action('admin_menu' , 'bcf_register_menu_page'); // registar main menu & sub menu

}

// add shortcode in wordpress
if ( ! function_exists( 'form_templates' ) ) {
 function form_templates($attr ) {
	$array = shortcode_atts(array(
		'width' => '500' ,
		'height' => '400'
            ) , $attr );

     require plugin_dir_path(__FILE__).'includes/bcf-settings-form-templates.php';

 }

add_shortcode("bcf_form", "form_templates"); 
}

// call ajax for form submission
if ( ! function_exists( 'bcf_form_callback' ) ) {
	
add_action('wp_ajax_bcf_form_callback','bcf_form_callback');
add_action('wp_ajax_nopriv_bcf_form_callback', 'bcf_form_callback');

function bcf_form_callback(){
	
	global $wpdb; 
	$table_name =  $wpdb->prefix.'basic_contact_form';
	 
	$results = $wpdb->get_results( "SELECT * FROM $table_name");
	 foreach ( $results as $print ) {
	 }
	 
	$email_get = $print->email_to;
	$secretkey = $print->secret_key;
	$getRecaptcha = $print->check_recaptcha;
 
    $recaptcha = sanitize_text_field($_POST['recaptcha']);
    $url = 'https://www.google.com/recaptcha/api/siteverify?secret='. $secretkey . '&response=' . $recaptcha;
   
    $data = wp_remote_get($url);
    $resp = json_decode(wp_remote_retrieve_body($data));
	 

    if($getRecaptcha == 1){
    
      if ($resp->success) {
	     
		$first_name = sanitize_text_field($_POST['name']);
	    $email = sanitize_email($_POST['email']);
	    $subject = sanitize_text_field($_POST['subject']);
	    $message = sanitize_text_field($_POST['message']);
	    $body = 'Email:' .$email ."\n". ' First Name:' . $first_name."\n".'Subject:'. $subject."\n".'Message:'. $message;
	    $to = $email_get;
     	$headers = 'From: '. $email . "\r\n" .
	               'Reply-To: ' . $email . "\r\n";
        $mailSend =wp_mail( $to, $first_name, $body , $headers );
        sleep(2);

       if($mailSend){
          echo esc_html("Email has been sent successfully.");     
         } else{
		  echo esc_html( "Failed To send Email");
	    }
 
      } else {
		echo esc_html( "Opps you are Rebot😡😡");
     }
   } else{
  
  	$first_name = sanitize_text_field($_POST['name']);
	$email = sanitize_email($_POST['email']);
	$subject = sanitize_text_field( $_POST['subject']);
	$message = sanitize_text_field( $_POST['message']);
	$body = 'Email:' .$email ."\n". ' First Name:' . $first_name."\n".'Subject:'. $subject."\n".'Message:'. $message;
	$to = $email_get;
    $headers = 'From: '. $email . "\r\n" .
	'Reply-To: ' . $email . "\r\n";
    $mailSend =wp_mail( $to, $first_name, $body , $headers );
    sleep(2);

    if($mailSend){
		echo esc_html( "Email has been sent successfully.");     
    }else {
		echo esc_html( "Email  send failed.");     
    }
 
  }

 wp_die();

}

}

?>