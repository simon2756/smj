<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Smj_Ulm_Cal
 * @subpackage Smj_Ulm_Cal/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Smj_Ulm_Cal
 * @subpackage Smj_Ulm_Cal/admin
 * @author     Your Name <email@example.com>
 */
class Smj_Ulm_Cal_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $smj_ulm_cal    The ID of this plugin.
	 */
	private $smj_ulm_cal;

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
	 * @param      string    $smj_ulm_cal       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $smj_ulm_cal, $version ) {

		$this->smj_ulm_cal = $smj_ulm_cal;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Smj_Ulm_Cal_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Smj_Ulm_Cal_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->smj_ulm_cal, plugin_dir_url( __FILE__ ) . 'css/smj-ulm-cal-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Smj_Ulm_Cal_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Smj_Ulm_Cal_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->smj_ulm_cal, plugin_dir_url( __FILE__ ) . 'js/smj-ulm-cal-admin.js', array( 'jquery' ), $this->version, false );

	}

}

 include 'partials/smj-ulm-cal-admin-pages.php';

//------------------------------------------------------------------------------
//!
//! Function: 		smj_ulm_cal_options_page_html
//!
//! Description:	returns html for the smj_ulm_cal page
//!
//! Parameter: 		None
//!
//! Return: 		None
//------------------------------------------------------------------------------


// Register the plugin settings
add_action('admin_init', 'smj_ulm_cal_admin_init');

function smj_ulm_cal_admin_init() {
    register_setting('smj_ulm_cal_options', 'smj_ulm_cal_options', 'smj_ulm_cal_options_validate');

    add_settings_section('smj_ulm_cal_master_calendar', 'Hauptkalender', 'smj_ulm_cal_section_text', 'smj_ulm_cal');


	//master calender
						//id (slug)				//title				//callback print		   //page				//sections
    add_settings_field('smj_ulm_cal__subscription_url', 	'URL zum .ics Kalender', 	'smj_ulm_cal_setting__subscription_url', 	'smj_ulm_cal', 'smj_ulm_cal_master_calendar');
    

	add_settings_section('smj_ulm_cal_sync_calendar', 'Abo Kalender erstellen', 'smj_ulm_cal_section_text', 'smj_ulm_cal');
		
	//sync calendars
    add_settings_field('smj_ulm_cal__num_output_calendars','Anzahl Kalender: ', 'smj_ulm_cal_setting__num_sync_calendars', 'smj_ulm_cal', 'smj_ulm_cal_sync_calendar');



}
//------------------------------------------------------------------------------
//!
//! Function: 		smj_ulm_cal_options_validate
//!
//! Description:	returns html for the smj_ulm_cal page
//!
//! Parameter: 		None
//!
//! Return: 		None
//------------------------------------------------------------------------------
function smj_ulm_cal_options_validate( $input ) {
	return $input; //$new_input todo
    $new_input = array();
    
    if( isset( $input['setting_1'] ) )
        $new_input['setting_1'] = sanitize_text_field( $input['setting_1'] );
    
    if( isset( $input['setting_2'] ) )
        $new_input['setting_2'] = absint( $input['setting_2'] );
    
    return $new_input;
}
//------------------------------------------------------------------------------
//!
//! Function: 		smj_ulm_cal_section_text
//!
//! Description:	smj_ulm_cal_section_text
//!
//! Parameter: 		None
//!
//! Return: 		None
//------------------------------------------------------------------------------
function smj_ulm_cal_section_text(){
	//todo
}

// Display the "smj_ulm_cal_setting__subscription_url" setting field
function smj_ulm_cal_setting__subscription_url() {
    $options = get_option('smj_ulm_cal_options');
	$subscription_url = isset($options['smj_ulm_cal__subscription_url']) ? esc_attr($options['smj_ulm_cal__subscription_url']) : '';
    echo "<input id='smj_ulm_cal__subscription_url' name='smj_ulm_cal_options[smj_ulm_cal__subscription_url]' size='100' type='text' value='{$subscription_url}' />";
}

// Display the "URL" setting field
function smj_ulm_cal_setting__master_name() {
    $options = get_option('smj_ulm_cal_options');
	$master_calender_name = isset($options['smj_ulm_cal__master_name']) ? esc_attr($options['smj_ulm_cal__master_name']) : '';
    echo "<input id='smj_ulm_cal__master_name'   name='smj_ulm_cal_options[smj_ulm_cal__master_name]' size='100' type='text' value='{$master_calender_name}' />";
 }


// Display the "num_sync_calendars" setting field
function smj_ulm_cal_setting__num_sync_calendars() {
    $options = get_option('smj_ulm_cal_options');
	$smj_ulm_cal__num_output_calendars = isset($options['smj_ulm_cal__num_output_calendars']) ? esc_attr($options['smj_ulm_cal__num_output_calendars']) : 0;
    echo "<input id='smj_ulm_cal__num_output_calendars'   name='smj_ulm_cal_options[smj_ulm_cal__num_output_calendars]' size='10' type='number' value='{$smj_ulm_cal__num_output_calendars}' />";


	$num_calendars =0;
	
	if(isset($options["url"])){
		$num_calendars = count($options["url"]);
	}


	echo "<div id='calendar-sync-list'>";
	for($i = 0; $i < $smj_ulm_cal__num_output_calendars;$i++){

		$categories = "";
		$categories_key ="categories";
		if(isset($options[$categories_key][$i])){
			$categories = $options[$categories_key][$i];
		}

		$calendar_name = "";
		$calendar_name_key ="calendar_name";
		if(isset($options[$calendar_name_key][$i])){
			$calendar_name = $options[$calendar_name_key][$i];
		}

		echo "<div class='sync-calendar'>";
			echo "<div>";
				echo "<label> Kalendername: </label>";
				echo "<input id='smj_ulm_cal_options[$calendar_name_key][]'   name='smj_ulm_cal_options[$calendar_name_key][]' type='text' value='$calendar_name' />";

				echo "<label> Kategorien: </label>";
				echo "<input id='smj_ulm_cal_options[$categories_key][]'   name='smj_ulm_cal_options[$categories_key][]' type='text' value='$categories' />";
		
			echo "</div>";
		echo "</div>";
		
	}
	echo "</div>";
 }



//------------------------------------------------------------------------------
//!
//! Function: 		smj_ulm_cal_delete_cache
//!
//! Description:	post request: delte log file
//!
//! Parameter: 		None
//!
//! Return: 		None
//------------------------------------------------------------------------------
function smj_ulm_cal_delete_cache() {
	if ( is_user_logged_in() ) {
		$dir_path = plugin_dir_path(__FILE__) ."../data/calender.ics";
		if(is_file($dir_path)) {
			unlink($dir_path);
		}

		$dir_path = plugin_dir_path(__FILE__) ."../data/statistic.txt";
		if(is_file($dir_path)) {
			unlink($dir_path);
		}

		$dir_path = plugin_dir_path(__FILE__) ."../data/out_calendars";
		if (is_dir($dir_path)) {
				$files = scandir($dir_path);
				foreach ($files as $file) {
					unlink($dir_path . DIRECTORY_SEPARATOR . $file);
				}
		}
		wp_redirect(admin_url("admin.php?page=smj_ulm_cal_options__settings"));
	} 
}
add_action('admin_post_smj_ulm_cal_delete_cache', 'smj_ulm_cal_delete_cache');


//------------------------------------------------------------------------------
//!
//! Function: 		smj_ulm_cal_delete_log
//!
//! Description:	post request: calender cache
//!
//! Parameter: 		None
//!
//! Return: 		None
//------------------------------------------------------------------------------
function smj_ulm_cal_delete_log() {
	if ( is_user_logged_in() ) {
		$dir_path = plugin_dir_path(__FILE__) ."../data/logs.txt";
		unlink($dir_path);
		wp_redirect(admin_url("admin.php?page=smj_ulm_cal_options__settings"));
	} 
}
add_action('admin_post_smj_ulm_cal_delete_log', 'smj_ulm_cal_delete_log');

//------------------------------------------------------------------------------
//!
//! Function: 		smj_ulm_cal_refresh_calender
//!
//! Description:	post request: calender cache
//!
//! Parameter: 		None
//!
//! Return: 		None
//------------------------------------------------------------------------------
function smj_ulm_cal_refresh_calender() {
	if ( is_user_logged_in() ) {
		smj_ulm_cal__get_calender();
		wp_redirect(admin_url("admin.php?page=smj_ulm_cal_options__settings"));
	} 
}
add_action('admin_post_smj_ulm_cal_refresh_calender', 'smj_ulm_cal_refresh_calender');
