<?php
/**
 * Gestaltungsblöcke des Themes: Diashow, Countdown, Brotkrümel.
 *
 * Alle drei werden auf dem Server gerendert; die Editor-Oberfläche steht in
 * assets/js/editor.js und kommt ohne Build-Schritt aus.
 *
 * @package SMJ_Ulm_Theme
 */

defined( 'ABSPATH' ) || exit;

add_action( 'init', 'smj_theme_register_bloecke' );

/**
 * Registriert Skripte und Blöcke.
 */
function smj_theme_register_bloecke() {
	wp_register_script(
		'smj-theme-editor',
		get_theme_file_uri( 'assets/js/editor.js' ),
		array( 'wp-blocks', 'wp-element', 'wp-block-editor', 'wp-components', 'wp-server-side-render' ),
		SMJ_THEME_VERSION,
		true
	);
	wp_register_script(
		'smj-theme-front',
		get_theme_file_uri( 'assets/js/front.js' ),
		array(),
		SMJ_THEME_VERSION,
		array(
			'in_footer' => true,
			'strategy'  => 'defer',
		)
	);

	register_block_type(
		'smj/diashow',
		array(
			'api_version'     => 3,
			'title'           => 'Diashow',
			'category'        => 'media',
			'editor_script'   => 'smj-theme-editor',
			'view_script'     => 'smj-theme-front',
			'render_callback' => 'smj_theme_render_diashow',
			'attributes'      => array(
				'bilder'   => array(
					'type'    => 'array',
					'default' => array(),
					'items'   => array( 'type' => 'object' ),
				),
				'sekunden' => array(
					'type'    => 'integer',
					'default' => 5,
				),
			),
		)
	);

	register_block_type(
		'smj/countdown',
		array(
			'api_version'     => 3,
			'title'           => 'Countdown',
			'category'        => 'widgets',
			'editor_script'   => 'smj-theme-editor',
			'view_script'     => 'smj-theme-front',
			'render_callback' => 'smj_theme_render_countdown',
			'supports'        => array( 'align' => array( 'wide', 'full' ) ),
			'attributes'      => array(
				'ziel'        => array(
					'type'    => 'string',
					'default' => '',
				),
				'oberzeile'   => array(
					'type'    => 'string',
					'default' => 'Zeltlager',
				),
				'titel'       => array(
					'type'    => 'string',
					'default' => 'Der Countdown läuft',
				),
				'buttonText'  => array(
					'type'    => 'string',
					'default' => '',
				),
				'buttonLink'  => array(
					'type'    => 'string',
					'default' => '',
				),
				'align'       => array( 'type' => 'string' ),
			),
		)
	);

	register_block_type(
		'smj/brotkruemel',
		array(
			'api_version'     => 3,
			'title'           => 'Brotkrümel-Navigation',
			'category'        => 'theme',
			'editor_script'   => 'smj-theme-editor',
			'render_callback' => 'smj_theme_render_brotkruemel',
			'supports'        => array( 'align' => array( 'wide', 'full' ) ),
			'attributes'      => array( 'align' => array( 'type' => 'string' ) ),
		)
	);
}

/**
 * Diashow aus ausgewählten Bildern; wechselt automatisch mit Überblendung.
 *
 * @param array $attributes Block-Attribute.
 * @return string
 */
function smj_theme_render_diashow( $attributes ) {
	$bilder = array_values(
		array_filter(
			(array) ( $attributes['bilder'] ?? array() ),
			static fn( $b ) => ! empty( $b['id'] ) && wp_attachment_is_image( (int) $b['id'] )
		)
	);
	$wrapper = get_block_wrapper_attributes(
		array(
			'class'                => 'smj-diashow',
			'data-sekunden'        => (string) max( 2, (int) ( $attributes['sekunden'] ?? 5 ) ),
			'aria-roledescription' => 'Diashow',
			'aria-label'           => 'Bilder aus der Abteilung',
		)
	);

	if ( ! $bilder ) {
		return '<div ' . $wrapper . '><div class="smj-diashow__leer">Bilder für die Diashow im Editor auswählen.</div></div>';
	}

	$slides = '';
	$punkte = '';
	foreach ( $bilder as $i => $bild ) {
		$img = wp_get_attachment_image(
			(int) $bild['id'],
			'large',
			false,
			array(
				'class'   => 'smj-diashow__bild',
				'loading' => 0 === $i ? 'eager' : 'lazy',
				'sizes'   => '(max-width: 900px) 100vw, 600px',
			)
		);
		$slides .= '<div class="smj-diashow__slide' . ( 0 === $i ? ' is-aktiv' : '' ) . '"' . ( 0 === $i ? '' : ' aria-hidden="true"' ) . '>' . $img . '</div>';
		$punkte .= '<button type="button" class="smj-diashow__punkt' . ( 0 === $i ? ' is-aktiv' : '' ) . '" data-index="' . (int) $i . '" aria-label="Bild ' . ( $i + 1 ) . ' anzeigen"' . ( 0 === $i ? ' aria-current="true"' : '' ) . '></button>';
	}

	$steuerung = count( $bilder ) > 1
		? '<div class="smj-diashow__punkte">' . $punkte . '</div>'
		. '<button type="button" class="smj-diashow__pause" aria-label="Diashow anhalten" data-label-play="Diashow abspielen" data-label-pause="Diashow anhalten"></button>'
		: '';

	return '<div ' . $wrapper . '>' . $slides . $steuerung . '</div>';
}

/**
 * Countdown bis zu einem Zeitpunkt (z. B. Zeltlager). Läuft im Browser weiter.
 *
 * @param array $attributes Block-Attribute.
 * @return string
 */
function smj_theme_render_countdown( $attributes ) {
	$ziel = $attributes['ziel'] ?? '';
	$zeit = $ziel ? DateTimeImmutable::createFromFormat( 'Y-m-d\TH:i', $ziel, wp_timezone() ) : false;
	if ( ! $zeit ) {
		return ( defined( 'REST_REQUEST' ) && REST_REQUEST )
			? '<p class="smj-countdown__leer">Datum und Uhrzeit für den Countdown in den Block-Einstellungen eintragen.</p>'
			: '';
	}

	$rest = max( 0, $zeit->getTimestamp() - time() );
	$teile = array(
		'tage'     => array( intdiv( $rest, DAY_IN_SECONDS ), 'Tage' ),
		'stunden'  => array( intdiv( $rest % DAY_IN_SECONDS, HOUR_IN_SECONDS ), 'Stunden' ),
		'minuten'  => array( intdiv( $rest % HOUR_IN_SECONDS, MINUTE_IN_SECONDS ), 'Minuten' ),
		'sekunden' => array( $rest % MINUTE_IN_SECONDS, 'Sekunden' ),
	);

	$kacheln = '';
	foreach ( $teile as $key => $teil ) {
		$wert     = 'tage' === $key ? (string) $teil[0] : str_pad( (string) $teil[0], 2, '0', STR_PAD_LEFT );
		$kacheln .= '<div class="smj-countdown__kachel"><span class="smj-countdown__zahl" data-teil="' . esc_attr( $key ) . '">' . esc_html( $wert ) . '</span><span class="smj-countdown__einheit">' . esc_html( $teil[1] ) . '</span></div>';
	}

	$button = '';
	if ( ! empty( $attributes['buttonText'] ) && ! empty( $attributes['buttonLink'] ) ) {
		$button = '<a class="smj-button smj-button--fire" href="' . esc_url( $attributes['buttonLink'] ) . '">' . esc_html( $attributes['buttonText'] ) . '</a>';
	}

	$wrapper = get_block_wrapper_attributes(
		array(
			'class'     => 'smj-countdown',
			'data-ziel' => (string) ( $zeit->getTimestamp() * 1000 ),
		)
	);

	return '<section ' . $wrapper . ' id="countdown">'
		. '<div class="smj-countdown__kopf"><div>'
		. ( ! empty( $attributes['oberzeile'] ) ? '<p class="smj-eyebrow">' . esc_html( $attributes['oberzeile'] ) . '</p>' : '' )
		. ( ! empty( $attributes['titel'] ) ? '<h2 class="smj-countdown__titel">' . esc_html( $attributes['titel'] ) . '</h2>' : '' )
		. '<p class="screen-reader-text">Noch bis ' . esc_html( wp_date( 'l, j. F Y, H:i \U\h\r', $zeit->getTimestamp() ) ) . '</p>'
		. '</div>' . $button . '</div>'
		. '<div class="smj-countdown__kacheln" aria-hidden="true">' . $kacheln . '</div>'
		. '</section>';
}

/**
 * Brotkrümel: Startseite / übergeordnete Seiten / aktuelle Seite.
 *
 * @return string
 */
function smj_theme_render_brotkruemel() {
	if ( is_front_page() ) {
		return '';
	}

	$teile = array( array( home_url( '/' ), 'Startseite' ) );

	if ( is_page() ) {
		foreach ( array_reverse( get_post_ancestors( get_queried_object_id() ) ) as $id ) {
			$teile[] = array( get_permalink( $id ), get_the_title( $id ) );
		}
		$teile[] = array( '', get_the_title( get_queried_object_id() ) );
	} elseif ( is_singular( 'post' ) ) {
		$page_for_posts = (int) get_option( 'page_for_posts' );
		if ( $page_for_posts ) {
			$teile[] = array( get_permalink( $page_for_posts ), get_the_title( $page_for_posts ) );
		}
		$teile[] = array( '', get_the_title( get_queried_object_id() ) );
	} elseif ( is_singular( 'smj_aktion' ) ) {
		$teile[] = array( '', 'Anmeldung' );
	} elseif ( is_category() || is_tag() || is_tax() ) {
		$teile[] = array( '', single_term_title( '', false ) );
	} elseif ( is_home() ) {
		$teile[] = array( '', get_the_title( (int) get_option( 'page_for_posts' ) ) ?: 'News' );
	} elseif ( is_search() ) {
		$teile[] = array( '', 'Suche' );
	} else {
		return '';
	}

	$items = '';
	$last  = count( $teile ) - 1;
	foreach ( $teile as $i => $teil ) {
		$items .= '<li>' . ( $i < $last && $teil[0]
			? '<a href="' . esc_url( $teil[0] ) . '">' . esc_html( $teil[1] ) . '</a>'
			: '<span aria-current="page">' . esc_html( $teil[1] ) . '</span>' ) . '</li>';
	}

	return '<nav ' . get_block_wrapper_attributes( array( 'class' => 'smj-brotkruemel' ) ) . ' aria-label="Brotkrümel"><ol>' . $items . '</ol></nav>';
}
