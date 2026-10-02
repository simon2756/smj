<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Smj_Ulm_Cal
 * @subpackage Smj_Ulm_Cal/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Smj_Ulm_Cal
 * @subpackage Smj_Ulm_Cal/public
 * @author     Your Name <email@example.com>
 */
class Smj_Ulm_Cal_Public {

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
	 * @param      string    $smj_ulm_cal       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $smj_ulm_cal, $version ) {

		$this->smj_ulm_cal = $smj_ulm_cal;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
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

		wp_enqueue_style( $this->smj_ulm_cal, plugin_dir_url( __FILE__ ) . 'css/smj-ulm-cal-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
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

		wp_enqueue_script( $this->smj_ulm_cal, plugin_dir_url( __FILE__ ) . 'js/smj-ulm-cal-public.js', array( 'jquery' ), $this->version, true );

	}

}
