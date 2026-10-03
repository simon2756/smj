<?php
/**
 * Blöcke für den Editor: "Offene Anmeldungen" und "Termine".
 *
 * Beide werden auf dem Server gerendert; das Editor-Skript braucht keinen Build-Schritt.
 *
 * @package SMJ_Ulm
 */

defined( 'ABSPATH' ) || exit;

add_action( 'init', 'smj_ulm_register_bloecke' );

/**
 * Registriert Editor-Skript und Blöcke.
 */
function smj_ulm_register_bloecke() {
	wp_register_script(
		'smj-ulm-bloecke',
		SMJ_ULM_URL . 'assets/bloecke.js',
		array( 'wp-blocks', 'wp-element', 'wp-block-editor', 'wp-components', 'wp-server-side-render' ),
		SMJ_ULM_VERSION,
		true
	);

	register_block_type(
		'smj/aktionen',
		array(
			'api_version'     => 3,
			'title'           => 'Offene Anmeldungen',
			'category'        => 'widgets',
			'editor_script'   => 'smj-ulm-bloecke',
			'render_callback' => 'smj_ulm_render_aktionen',
			'supports'        => array( 'align' => array( 'wide', 'full' ) ),
			'attributes'      => array(
				'ueberschrift' => array(
					'type'    => 'string',
					'default' => 'Anmeldungen offen',
				),
				'align'        => array( 'type' => 'string' ),
			),
		)
	);

	register_block_type(
		'smj/termine',
		array(
			'api_version'     => 3,
			'title'           => 'Termine',
			'category'        => 'widgets',
			'editor_script'   => 'smj-ulm-bloecke',
			'render_callback' => 'smj_ulm_render_termine',
			'attributes'      => array(
				'anzahl'     => array(
					'type'    => 'integer',
					'default' => 5,
				),
				'monate'     => array(
					'type'    => 'integer',
					'default' => 12,
				),
				'kategorien' => array(
					'type'    => 'string',
					'default' => '',
				),
				'alleLink'   => array(
					'type'    => 'string',
					'default' => '',
				),
				'aboLink'    => array(
					'type'    => 'string',
					'default' => '',
				),
			),
		)
	);
}

/**
 * Karten der offenen Anmeldungen. Ohne offene Anmeldung wird nichts ausgegeben,
 * damit auf der Startseite kein leerer Abschnitt stehen bleibt.
 *
 * @param array $attributes Block-Attribute.
 * @return string
 */
function smj_ulm_render_aktionen( $attributes ) {
	$aktionen = smj_ulm_offene_aktionen();
	if ( ! $aktionen ) {
		return ( defined( 'REST_REQUEST' ) && REST_REQUEST )
			? '<p class="smj-aktionen__leer">Gerade ist keine Anmeldung offen. Dieser Abschnitt ist auf der Website dann ausgeblendet.</p>'
			: '';
	}

	$karten = '';
	foreach ( $aktionen as $aktion ) {
		$schluss = smj_ulm_anmeldeschluss( $aktion->ID );
		$bild    = get_the_post_thumbnail( $aktion->ID, 'medium_large', array( 'class' => 'smj-aktion-karte__bild', 'alt' => '' ) );
		$auszug  = has_excerpt( $aktion ) ? get_the_excerpt( $aktion ) : '';
		$link    = get_permalink( $aktion );

		$karten .= '<article class="smj-aktion-karte">'
			. '<div class="smj-aktion-karte__media">' . ( $bild ?: '<span class="smj-aktion-karte__platzhalter" aria-hidden="true"></span>' ) . '</div>'
			. '<div class="smj-aktion-karte__body">'
			. '<h3 class="smj-aktion-karte__titel"><a href="' . esc_url( $link ) . '">' . esc_html( get_the_title( $aktion ) ) . '</a></h3>'
			. '<p class="smj-chips">'
			. ( $schluss ? '<span class="smj-chip">' . esc_html( smj_ulm_zeitraum( $aktion->ID ) ) . '</span>' : '' )
			. ( $schluss ? '<span class="smj-chip smj-chip--frist">Anmeldung bis ' . esc_html( smj_ulm_datum( $schluss->modify( '-1 day' ), 'd.m.Y' ) ) . '</span>' : '' )
			. '</p>'
			. ( $auszug ? '<p class="smj-aktion-karte__text">' . esc_html( $auszug ) . '</p>' : '' )
			. '<a class="smj-button" href="' . esc_url( $link ) . '">Zur Anmeldung<span class="screen-reader-text">: ' . esc_html( get_the_title( $aktion ) ) . '</span></a>'
			. '</div></article>';
	}

	$wrapper = get_block_wrapper_attributes( array( 'class' => 'smj-aktionen' ) );
	$titel   = trim( (string) ( $attributes['ueberschrift'] ?? '' ) );

	return '<section ' . $wrapper . ' id="anmeldungen">'
		. '<div class="smj-aktionen__kopf">'
		. '<div><p class="smj-eyebrow">Jetzt anmelden</p>' . ( $titel ? '<h2 class="smj-aktionen__titel">' . esc_html( $titel ) . '</h2>' : '' ) . '</div>'
		. '</div>'
		. '<div class="smj-aktionen__grid">' . $karten . '</div>'
		. '</section>';
}

/**
 * Terminliste.
 *
 * @param array $attributes Block-Attribute.
 * @return string
 */
function smj_ulm_render_termine( $attributes ) {
	$wrapper = get_block_wrapper_attributes( array( 'class' => 'smj-termine-block' ) );
	return '<div ' . $wrapper . '>' . smj_ulm_termine_html( $attributes ) . '</div>';
}
