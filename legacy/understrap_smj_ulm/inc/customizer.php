<?php
/**
 * Understrap Theme Customizer
 *
 * @package Understrap
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'understrap_customize_register' ) ) {
	/**
	 * Register basic support (site title, header text color) for the Theme Customizer.
	 *
	 * @param WP_Customize_Manager $wp_customize Customizer reference.
	 */
	function understrap_customize_register( $wp_customize ) {
		$settings = array( 'blogname', 'header_textcolor' );
		foreach ( $settings as $setting ) {
			$get_setting = $wp_customize->get_setting( $setting );
			if ( $get_setting instanceof WP_Customize_Setting ) {
				$get_setting->transport = 'postMessage';
			}
		}

		// Override default partial for custom logo.
		$wp_customize->selective_refresh->add_partial(
			'custom_logo',
			array(
				'settings'            => array( 'custom_logo' ),
				'selector'            => '.custom-logo-link',
				'render_callback'     => 'understrap_customize_partial_custom_logo',
				'container_inclusive' => false,
			)
		);
	}
}
add_action( 'customize_register', 'understrap_customize_register' );

if ( ! function_exists( 'understrap_customize_partial_custom_logo' ) ) {
	/**
	 * Callback for rendering the custom logo, used in the custom_logo partial.
	 *
	 * @return string The custom logo markup or the site title.
	 */
	function understrap_customize_partial_custom_logo() {
		if ( has_custom_logo() ) {
			return get_custom_logo();
		} else {
			return get_bloginfo( 'name' );
		}
	}
}

if ( ! function_exists( 'understrap_theme_customize_register' ) ) {
	/**
	 * Register individual settings through customizer's API.
	 *
	 * @param WP_Customize_Manager $wp_customize Customizer reference.
	 */
	function understrap_theme_customize_register( $wp_customize ) {

		// Theme layout settings.
		$wp_customize->add_section(
			'understrap_theme_layout_options',
			array(
				'title'       => __( 'Theme Layout Settings', 'understrap' ),
				'capability'  => 'edit_theme_options',
				'description' => __( 'Container width and sidebar defaults', 'understrap' ),
				'priority'    => apply_filters( 'understrap_theme_layout_options_priority', 160 ),
			)
		);


		$wp_customize->add_setting(
			'understrap_container_type',
			array(
				'default'           => 'container',
				'type'              => 'theme_mod',
				'sanitize_callback' => 'understrap_customize_sanitize_select',
				'capability'        => 'edit_theme_options',
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Control(
				$wp_customize,
				'understrap_container_type',
				array(
					'label'       => __( 'Container Width', 'understrap' ),
					'description' => __( 'Choose between Bootstrap\'s container and container-fluid', 'understrap' ),
					'section'     => 'understrap_theme_layout_options',
					'type'        => 'select',
					'choices'     => array(
						'container'       => __( 'Fixed width container', 'understrap' ),
						'container-fluid' => __( 'Full width container', 'understrap' ),
					),
					'priority'    => apply_filters( 'understrap_container_type_priority', 10 ),
				)
			)
		);

		$wp_customize->add_setting(
			'understrap_navbar_type',
			array(
				'default'           => 'collapse',
				'type'              => 'theme_mod',
				'sanitize_callback' => 'understrap_customize_sanitize_select',
				'capability'        => 'edit_theme_options',
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Control(
				$wp_customize,
				'understrap_navbar_type',
				array(
					'label'       => __( 'Responsive Navigation Type', 'understrap' ),
					'description' => __(
						'Choose between an expanding and collapsing navbar or an offcanvas drawer.',
						'understrap'
					),
					'section'     => 'understrap_theme_layout_options',
					'type'        => 'select',
					'choices'     => array(
						'collapse'  => __( 'Collapse', 'understrap' ),
						'offcanvas' => __( 'Offcanvas', 'understrap' ),
					),
					'priority'    => apply_filters( 'understrap_navbar_type_priority', 20 ),
				)
			)
		);

		$wp_customize->add_setting(
			'understrap_sidebar_position',
			array(
				'default'           => 'right',
				'type'              => 'theme_mod',
				'sanitize_callback' => 'understrap_customize_sanitize_select',
				'capability'        => 'edit_theme_options',
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Control(
				$wp_customize,
				'understrap_sidebar_position',
				array(
					'label'       => __( 'Sidebar Positioning', 'understrap' ),
					'description' => __(
						'Set sidebar\'s default position. Can either be: right, left, both or none. Note: this can be overridden on individual pages.',
						'understrap'
					),
					'section'     => 'understrap_theme_layout_options',
					'type'        => 'select',
					'choices'     => array(
						'right' => __( 'Right sidebar', 'understrap' ),
						'left'  => __( 'Left sidebar', 'understrap' ),
						'both'  => __( 'Left & Right sidebars', 'understrap' ),
						'none'  => __( 'No sidebar', 'understrap' ),
					),
					'priority'    => apply_filters( 'understrap_sidebar_position_priority', 20 ),
				)
			)
		);

		$wp_customize->add_setting(
			'understrap_site_info_override',
			array(
				'default'           => '',
				'type'              => 'theme_mod',
				'sanitize_callback' => 'wp_kses_post',
				'capability'        => 'edit_theme_options',
			)
		);
		/* todoFB
		$wp_customize->add_control(
			new WP_Customize_Control(
				$wp_customize,
				'understrap_site_info_override',
				array(
					'label'       => __( 'Footer Site Info', 'understrap' ),
					'description' => __( 'Override Understrap\'s site info located at the footer of the page.', 'understrap' ),
					'section'     => 'understrap_theme_layout_options',
					'type'        => 'textarea',
					'priority'    => 20,
				)
			)
		);*/

	

		


		////////////////////////////
		// Countdown settings
		$wp_customize->add_section(
			'countdown_section',
			array(
				'title'       => __( 'Countdown', 'understrap' ),
				'capability'  => 'edit_theme_options',
				'description' => __( 'Einstellungen für den Countdown im Footer Bereich', 'understrap' ),
				'priority'    => apply_filters( 'understrap_theme_layout_options_priority', 0 ),
			)
		);


		//Countdown date
		$wp_customize->add_setting(
			'understrap_countdown',
			array(
				'default'           => '6',
				'type'              => 'theme_mod',
				'sanitize_callback' => 'wp_kses_post',
				'capability'        => 'edit_theme_options',
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Control(
				$wp_customize,
				'understrap_countdown',
				array(
					'label'       => __( 'Countdown', 'understrap' ),
					'description' => __( 'Datum des Countdown', 'understrap' ),
					'section'     => 'countdown_section',
					'type'        => 'datetime-local',
					'priority'    => 20,
				
					
				)
			)
		);

		//Countdown label
		$wp_customize->add_setting(
			'understrap_countdown_label',
			array(
				'default'           => "",
				'type'              => 'theme_mod',
				'sanitize_callback' => 'wp_kses_post',
				'capability'        => 'edit_theme_options',
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Control(
				$wp_customize,
				'understrap_countdown_label',
				array(
					'label'       => __( 'Countdown Label', 'understrap' ),
					'description' => __( 'Text vor dem Countdown', 'understrap' ),
					'section'     => 'countdown_section',
					'type'        => 'input',
					'priority'    => 20,
				
					
				)
			)
		);

		//Countdown label
		$wp_customize->add_setting(
			'understrap_countdown_enabled',
			array(
				'default'           => "",
				'type'              => 'theme_mod',
				'sanitize_callback' => 'wp_kses_post',
				'capability'        => 'edit_theme_options',
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Control(
				$wp_customize,
				'understrap_countdown_enabled',
				array(
					'label'       => __( 'Aktiv', 'understrap' ),
					'description' => __( 'Soll der Countdown angezeigt werden', 'understrap' ),
					'section'     => 'countdown_section',
					'type'        => 'checkbox',
					'priority'    => 20,
				
					
				)
			)
		);

		// Countdown settings Ende
		////////////////////////////

		////////////////////////////
		// News Startseite Start
		$wp_customize->add_section(
			'undertrap_news_home',
			array(
				'title'       => __( 'Startseite News', 'understrap' ),
				'capability'  => 'edit_theme_options',
				'description' => __( 'Einstellungen für die News auf der Startseite', 'understrap' ),
				'priority'    => apply_filters( 'understrap_theme_layout_options_priority', 0 ),
			)
		);

		//blog category
		$wp_customize->add_setting(
			'understrap_blog_category',
			array(
				'default'           => '0',
				'type'              => 'theme_mod',
				'sanitize_callback' => 'wp_kses_post',
				'capability'        => 'edit_theme_options',
			)
		);
		
		$select_categories = array();
		$categories = get_categories( );
	
		foreach( $categories as $category ) {
			$category_id = get_cat_ID( $category->name );
			$select_categories[ $category_id ] = __( $category->name);
		}


		$wp_customize->add_control(
			new WP_Customize_Control(
				$wp_customize,
				'understrap_blog_category',
				array(
					'label'       => __( 'Blog Kategorie', 'understrap' ),
					'description' => __( 'Blog Kategorie die auf der Startseite angezeigt', 'understrap' ),
					'section'     => 'undertrap_news_home',
					'type'        => 'select',
					'priority'    => 20,
					'choices' => $select_categories,
				)
			)
		);

		//num blog posts
		$wp_customize->add_setting(
			'understrap_num_blog_posts',
			array(
				'default'           => '6',
				'type'              => 'theme_mod',
				'sanitize_callback' => 'wp_kses_post',
				'capability'        => 'edit_theme_options',
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Control(
				$wp_customize,
				'understrap_num_blog_posts',
				array(
					'label'       => __( 'Anzahl Blog Beiträge', 'understrap' ),
					'description' => __( 'Anzahl der Blog Beiträge auf der Startseite.', 'understrap' ),
					'section'     => 'undertrap_news_home',
					'type'        => 'number',
					'priority'    => 20,
					'input_attrs' => array(
						'min' => 1,
						'max' => 100,
						'step' => 1,
					  ),
				)
			)
		);


		$wp_customize->add_setting(
			'understrap_expert_length',
			array(
				'default'           => '20',
				'type'              => 'theme_mod',
				'sanitize_callback' => 'wp_kses_post',
				'capability'        => 'edit_theme_options',
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Control(
				$wp_customize,
				'understrap_expert_length',
				array(
					'label'       => __( 'Länge Auzugstext Beitrag', 'understrap' ),
					'description' => __( 'Anzahl der Wörter die bei einer Beitrag als Auzug angezeigt werden .', 'understrap' ),
					'section'     => 'undertrap_news_home',
					'type'        => 'number',
					'priority'    => 20,
					'input_attrs' => array(
						'min' => 1,
						'max' => 100,
						'step' => 1,
					  ),
				)
			)
		);


		// News Startseite Ende
		////////////////////////////


		////////////////////////////
		// jumbotron settings
		$wp_customize->add_section(
			'jumbotron_section',
			array(
				'title'       => __( 'Werbung (Startseite)', 'understrap' ),
				'capability'  => 'edit_theme_options',
				'description' => __( 'Einstellunge für den Werbe - Jumbotron auf der Startseite', 'understrap' ),
				'priority'    => apply_filters( 'understrap_theme_layout_options_priority', 0 ),
			)
		);

		


		//Headline
		$wp_customize->add_setting(
			'understrap_jumbotron_headline',
			array(
				'default'           => '',
				'type'              => 'theme_mod',
				'sanitize_callback' => 'wp_kses_post',
				'capability'        => 'edit_theme_options',
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Control(
				$wp_customize,
				'understrap_jumbotron_headline',
				array(
					'label'       => __( 'Überschrift', 'understrap' ),
					
					'section'     => 'jumbotron_section',
					'type'        => 'text',
					'priority'    => 20,
				
					
				)
			)
		);

		//Paragraph
		$wp_customize->add_setting(
			'understrap_jumbotron_paragrahp',
			array(
				'default'           => '',
				'type'              => 'theme_mod',
				'sanitize_callback' => 'wp_kses_post',
				'capability'        => 'edit_theme_options',
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Control(
				$wp_customize,
				'understrap_jumbotron_paragrahp',
				array(
					'label'       => __( 'Paragraph', 'understrap' ),
					'description' => __( 'Text des Paragraphs', 'understrap' ),
					'section'     => 'jumbotron_section',
					'type'        => 'textarea',
					'priority'    => 20,
				
					
				)
			)
		);

		//Button Text
		$wp_customize->add_setting(
			'understrap_jumbotron_button_text',
			array(
				'default'           => '',
				'type'              => 'theme_mod',
				'sanitize_callback' => 'wp_kses_post',
				'capability'        => 'edit_theme_options',
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Control(
				$wp_customize,
				'understrap_jumbotron_button_text',
				array(
					'label'       => __( 'Button Text', 'understrap' ),
					'section'     => 'jumbotron_section',
					'type'        => 'text',
					'priority'    => 20,
				
					
				)
			)
		);
		//Button Link
		$wp_customize->add_setting(
			'understrap_jumbotron_button_link',
			array(
				'default'           => '',
				'type'              => 'theme_mod',
				'sanitize_callback' => 'wp_kses_post',
				'capability'        => 'edit_theme_options',
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Control(
				$wp_customize,
				'understrap_jumbotron_button_link',
				array(
					'label'       => __( 'Button Link', 'understrap' ),
					'description' => __( 'Link der beim Button Klick aufgerufen wird (z.B. /online-anmeldung', 'understrap' ),
					'section'     => 'jumbotron_section',
					'type'        => 'text',
					'priority'    => 20,
				
					
				)
			)
		);

		//active
		$wp_customize->add_setting(
			'understrap_jumbotron_isActive',
			array(
				'default'           => '1',
				'type'              => 'theme_mod',
				'sanitize_callback' => 'wp_kses_post',
				'capability'        => 'edit_theme_options',
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Control(
				$wp_customize,
				'understrap_jumbotron_isActive',
				array(
					'label'       => __( 'Aktiv', 'understrap' ),
					'description' => __( 'Soll der Werbe - Jumbotron angezeigt werden?', 'understrap' ),
					'section'     => 'jumbotron_section',
					'type'        => 'checkbox',
					'priority'    => 20,
				
				)
			)
		);


		//background-image x-offset
		$wp_customize->add_setting(
			'understrap_jumbotron_image_x_offset',
			array(
				'default'           => '50',
				'type'              => 'theme_mod',
				'sanitize_callback' => 'wp_kses_post',
				'capability'        => 'edit_theme_options',
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Control(
				$wp_customize,
				'understrap_jumbotron_image_x_offset',
				array(
					'label'       => __( 'X-Offset', 'understrap' ),
					'description' => __( 'Hintergrund Bild x-Offset (0% bis 100%)', 'understrap' ),
					'section'     => 'jumbotron_section',
					'type'        => 'number',
					'priority'    => 20,
					'input_attrs' => array(
						'min' => 0,
						'max' => 100,
						'step' => 1,
					  ),
				
				)
			)
		);
			//background-image x-offset
			$wp_customize->add_setting(
				'understrap_jumbotron_image_y_offset',
				array(
					'default'           => '50',
					'type'              => 'theme_mod',
					'sanitize_callback' => 'wp_kses_post',
					'capability'        => 'edit_theme_options',
				)
			);
	
			$wp_customize->add_control(
				new WP_Customize_Control(
					$wp_customize,
					'understrap_jumbotron_image_y_offset',
					array(
						'label'       => __( 'Y-Offset', 'understrap' ),
						'description' => __( 'Hintergrund Bild x-Offset (0% bis 100%)', 'understrap' ),
						'section'     => 'jumbotron_section',
						'type'        => 'number',
						'priority'    => 20,
						'input_attrs' => array(
							'min' => 0,
							'max' => 100,
							'step' => 1,
						  ),
					
					)
				)
			);
	
		//background-image
		$wp_customize->add_setting(
			'understrap_jumbotron_image',
			array(
				'default'           => '',
				'type'              => 'theme_mod',
				'sanitize_callback' => 'wp_kses_post',
				'capability'        => 'edit_theme_options',
			)
		);
		$wp_customize->add_control(
				new WP_Customize_Media_Control(
					$wp_customize,
					'understrap_jumbotron_image',
					array(
						'label' => __( 'Hintergrundbild', 'understrap' ),
						'description' => __( 'Hintergrundbild', 'understrap' ),
						'section' => 'jumbotron_section',
						'mime_type' => 'image',
						'priority'    => 20,
			  ) ) );

		// jumbotron settings Ende
		////////////////////////////




		$understrap_site_info = $wp_customize->get_setting( 'understrap_site_info_override' );
		if ( $understrap_site_info instanceof WP_Customize_Setting ) {
			$understrap_site_info->transport = 'postMessage';
		}
	}
} // End of if function_exists( 'understrap_theme_customize_register' ).
add_action( 'customize_register', 'understrap_theme_customize_register' );

if ( ! function_exists( 'understrap_customize_sanitize_select' ) ) {
	/**
	 * Sanitize select.
	 *
	 * @since 1.2.0 Renamed from understrap_theme_slug_sanitize_select()
	 *
	 * @param string               $input   Slug to sanitize.
	 * @param WP_Customize_Setting $setting Setting instance.
	 * @return string|bool Sanitized slug if it is a valid choice; the setting default for
	 *                     invalid choices and false in all other cases.
	 */
	function understrap_customize_sanitize_select( $input, $setting ) {

		// Ensure input is a slug (lowercase alphanumeric characters, dashes and underscores are allowed only).
		$input = sanitize_key( $input );

		$control = $setting->manager->get_control( $setting->id );
		if ( ! $control instanceof WP_Customize_Control ) {
			return false;
		}

		// Get the list of possible select options.
		$choices = $control->choices;

		// If the input is a valid key, return it; otherwise, return the default.
		return ( array_key_exists( $input, $choices ) ? $input : $setting->default );

	}
}

/**
 * Binds JS handlers to make Theme Customizer preview reload changes asynchronously.
 */
if ( ! function_exists( 'understrap_customize_preview_js' ) ) {
	/**
	 * Setup JS integration for live previewing.
	 */
	function understrap_customize_preview_js() {
		$file    = '/js/customizer.js';
		$version = filemtime( get_template_directory() . $file );
		if ( false === $version ) {
			$version = time();
		}

		wp_enqueue_script(
			'understrap_customizer',
			get_template_directory_uri() . $file,
			array( 'customize-preview' ),
			(string) $version,
			true
		);
	}
}
add_action( 'customize_preview_init', 'understrap_customize_preview_js' );

/**
 * Loads javascript for conditionally showing customizer controls.
 */
if ( ! function_exists( 'understrap_customize_controls_js' ) ) {
	/**
	 * Setup JS integration for live previewing.
	 *
	 * @since 1.1.0
	 */
	function understrap_customize_controls_js() {
		$file    = '/js/customizer-controls.js';
		$version = filemtime( get_template_directory() . $file );
		if ( false === $version ) {
			$version = time();
		}

		wp_enqueue_script(
			'understrap_customizer',
			get_template_directory_uri() . $file,
			array( 'customize-preview' ),
			(string) $version,
			true
		);
	}
}
add_action( 'customize_controls_enqueue_scripts', 'understrap_customize_controls_js' );

