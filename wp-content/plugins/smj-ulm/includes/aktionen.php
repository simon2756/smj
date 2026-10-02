<?php
/**
 * Inhaltstyp "Aktion" (Anmeldung) und Hilfsfunktionen rund um Anmeldeschluss und Löschtermin.
 *
 * Eine Aktion verknüpft ein Contact-Form-7-Formular mit einem Datum. Ab diesem Datum
 * ist die Anmeldung geschlossen, SMJ_ULM_LOESCHFRIST_TAGE später werden die Daten gelöscht.
 *
 * @package SMJ_Ulm
 */

defined( 'ABSPATH' ) || exit;

add_action( 'init', 'smj_ulm_register_aktion' );

/**
 * Registriert den Inhaltstyp und seine Metafelder.
 */
function smj_ulm_register_aktion() {
	register_post_type(
		'smj_aktion',
		array(
			'labels'        => array(
				'name'               => 'Anmeldungen',
				'singular_name'      => 'Anmeldung',
				'menu_name'          => 'Anmeldungen',
				'add_new'            => 'Neue Anmeldung',
				'add_new_item'       => 'Neue Anmeldung anlegen',
				'edit_item'          => 'Anmeldung bearbeiten',
				'view_item'          => 'Anmeldeseite ansehen',
				'all_items'          => 'Alle Anmeldungen',
				'search_items'       => 'Anmeldungen durchsuchen',
				'not_found'          => 'Keine Anmeldungen gefunden.',
				'not_found_in_trash' => 'Keine Anmeldungen im Papierkorb.',
			),
			'public'        => true,
			'has_archive'   => false,
			'menu_icon'     => 'dashicons-clipboard',
			'menu_position' => 6,
			'rewrite'       => array( 'slug' => 'anmeldung' ),
			'show_in_rest'  => true,
			'supports'      => array( 'title', 'editor', 'thumbnail', 'excerpt' ),
		)
	);

	$meta = array(
		'_smj_datum'   => 'string',  // Erster Tag der Aktion, Y-m-d.
		'_smj_ende'    => 'string',  // Letzter Tag (optional), Y-m-d.
		'_smj_formular' => 'integer', // Post-ID des CF7-Formulars.
		'_smj_ort'     => 'string',
		'_smj_beitrag' => 'string',
	);
	foreach ( $meta as $key => $type ) {
		register_post_meta(
			'smj_aktion',
			$key,
			array(
				'type'          => $type,
				'single'        => true,
				'show_in_rest'  => false,
				'auth_callback' => static fn() => current_user_can( 'edit_posts' ),
			)
		);
	}
}

/**
 * Liefert ein Datum aus den Metadaten als Tagesbeginn in der Zeitzone der Website.
 *
 * @param int    $post_id Aktion.
 * @param string $key     Metafeld.
 * @return DateTimeImmutable|null
 */
function smj_ulm_meta_date( $post_id, $key ) {
	$value = get_post_meta( $post_id, $key, true );
	if ( ! $value ) {
		return null;
	}
	$date = DateTimeImmutable::createFromFormat( '!Y-m-d', $value, wp_timezone() );
	return $date ?: null;
}

/**
 * Erster Tag der Aktion. Ab 0:00 Uhr dieses Tages ist die Anmeldung geschlossen.
 *
 * @param int $post_id Aktion.
 * @return DateTimeImmutable|null
 */
function smj_ulm_anmeldeschluss( $post_id ) {
	return smj_ulm_meta_date( $post_id, '_smj_datum' );
}

/**
 * Tag, an dem die Anmeldedaten gelöscht werden.
 *
 * @param int $post_id Aktion.
 * @return DateTimeImmutable|null
 */
function smj_ulm_loeschdatum( $post_id ) {
	$start = smj_ulm_anmeldeschluss( $post_id );
	return $start ? $start->modify( '+' . SMJ_ULM_LOESCHFRIST_TAGE . ' days' ) : null;
}

/**
 * Ist die Anmeldung zu dieser Aktion gerade offen?
 *
 * Ohne Datum gilt eine Aktion als offen, damit nichts unbemerkt schließt; die
 * Admin-Ansicht weist auf das fehlende Datum hin.
 *
 * @param int $post_id Aktion.
 * @return bool
 */
function smj_ulm_ist_offen( $post_id ) {
	if ( 'publish' !== get_post_status( $post_id ) ) {
		return false;
	}
	$schluss = smj_ulm_anmeldeschluss( $post_id );
	return ! $schluss || current_datetime() < $schluss;
}

/**
 * Formatiert ein Datum deutsch, z. B. "Sa, 28.11.2026".
 *
 * @param DateTimeImmutable|null $date   Datum.
 * @param string                 $format wp_date()-Format.
 * @return string
 */
function smj_ulm_datum( $date, $format = 'D, d.m.Y' ) {
	return $date ? wp_date( $format, $date->getTimestamp(), wp_timezone() ) : '';
}

/**
 * Zeitraum der Aktion als Text, z. B. "Sa, 28.11. – So, 29.11.2026".
 *
 * @param int $post_id Aktion.
 * @return string
 */
function smj_ulm_zeitraum( $post_id ) {
	$start = smj_ulm_anmeldeschluss( $post_id );
	$ende  = smj_ulm_meta_date( $post_id, '_smj_ende' );
	if ( ! $start ) {
		return '';
	}
	if ( ! $ende || $ende <= $start ) {
		return smj_ulm_datum( $start );
	}
	return smj_ulm_datum( $start, 'D, d.m.' ) . ' – ' . smj_ulm_datum( $ende );
}

/**
 * Alle veröffentlichten Aktionen, deren Anmeldung offen ist, nach Datum sortiert.
 *
 * @return WP_Post[]
 */
function smj_ulm_offene_aktionen() {
	$posts = get_posts(
		array(
			'post_type'      => 'smj_aktion',
			'post_status'    => 'publish',
			'posts_per_page' => -1,
			'meta_key'       => '_smj_datum',
			'orderby'        => 'meta_value',
			'order'          => 'ASC',
		)
	);
	return array_values( array_filter( $posts, static fn( $p ) => smj_ulm_ist_offen( $p->ID ) ) );
}

/**
 * Aktionen, die ein bestimmtes Formular verwenden.
 *
 * @param int $form_id Post-ID des CF7-Formulars.
 * @return int[]
 */
function smj_ulm_aktionen_fuer_formular( $form_id ) {
	return get_posts(
		array(
			'post_type'      => 'smj_aktion',
			'post_status'    => array( 'publish', 'draft', 'pending', 'private', 'future' ),
			'posts_per_page' => -1,
			'fields'         => 'ids',
			'meta_key'       => '_smj_formular',
			'meta_value'     => (int) $form_id,
		)
	);
}

/**
 * Ist ein Formular geschlossen?
 *
 * Geschlossen ist es, wenn es zu mindestens einer Aktion gehört und keine davon
 * gerade offen ist. Formulare ohne Aktion (z. B. ein Kontaktformular) sind nie betroffen.
 *
 * @param int $form_id Post-ID des CF7-Formulars.
 * @return bool
 */
function smj_ulm_formular_geschlossen( $form_id ) {
	$aktionen = smj_ulm_aktionen_fuer_formular( $form_id );
	if ( ! $aktionen ) {
		return false;
	}
	foreach ( $aktionen as $aktion_id ) {
		if ( smj_ulm_ist_offen( $aktion_id ) ) {
			return false;
		}
	}
	return true;
}
