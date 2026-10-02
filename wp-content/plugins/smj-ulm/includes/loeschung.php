<?php
/**
 * Automatisches Löschen der Anmeldedaten SMJ_ULM_LOESCHFRIST_TAGE nach Aktionsbeginn.
 *
 * Gelöscht wird überall, wo die Anmeldung landet:
 * - Advanced CF7 DB (Tabellen cf7_vdata / cf7_vdata_entry)
 * - Send PDF for Contact Form 7 (Tabelle wpcf7pdf_files und die erzeugten PDF-Dateien)
 *
 * Damit ein Formular für mehrere Aktionen nacheinander benutzt werden kann, werden nur
 * Einträge gelöscht, die vor dem Anmeldeschluss der jeweiligen Aktion eingegangen sind.
 *
 * @package SMJ_Ulm
 */

defined( 'ABSPATH' ) || exit;

add_action( 'init', 'smj_ulm_schedule_cleanup' );
add_action( 'smj_ulm_cleanup', 'smj_ulm_cleanup' );

/**
 * Plant den täglichen Lösch-Job (nachts um 3 Uhr Ortszeit).
 */
function smj_ulm_schedule_cleanup() {
	if ( ! wp_next_scheduled( 'smj_ulm_cleanup' ) ) {
		$next = current_datetime()->setTime( 3, 0 )->modify( '+1 day' );
		wp_schedule_event( $next->getTimestamp(), 'daily', 'smj_ulm_cleanup' );
	}
}

/**
 * Täglicher Job: merkt sich beim Anmeldeschluss den Stand und löscht nach Ablauf der Frist.
 */
function smj_ulm_cleanup() {
	$jetzt    = current_datetime();
	$aktionen = get_posts(
		array(
			'post_type'      => 'smj_aktion',
			'post_status'    => 'any',
			'posts_per_page' => -1,
			'fields'         => 'ids',
			'meta_query'     => array(
				array(
					'key'     => '_smj_geloescht',
					'compare' => 'NOT EXISTS',
				),
			),
		)
	);

	foreach ( $aktionen as $aktion_id ) {
		$schluss = smj_ulm_anmeldeschluss( $aktion_id );
		$formular = (int) get_post_meta( $aktion_id, '_smj_formular', true );
		if ( ! $schluss || ! $formular || $jetzt < $schluss ) {
			continue;
		}

		smj_ulm_merke_pdf_stand( $aktion_id, $formular );

		if ( $jetzt >= smj_ulm_loeschdatum( $aktion_id ) ) {
			smj_ulm_loesche_anmeldungen( $aktion_id, $formular, $schluss );
		}
	}
}

/**
 * Send PDF speichert kein Datum. Darum wird beim ersten Lauf nach dem Anmeldeschluss die
 * höchste Eintrags-ID festgehalten; gelöscht wird später nur bis zu dieser ID.
 *
 * @param int $aktion_id Aktion.
 * @param int $formular  Formular.
 */
function smj_ulm_merke_pdf_stand( $aktion_id, $formular ) {
	global $wpdb;
	$table = $wpdb->prefix . 'wpcf7pdf_files';
	if ( '' !== get_post_meta( $aktion_id, '_smj_pdf_stand', true ) || ! smj_ulm_table_exists( $table ) ) {
		return;
	}
	$max = (int) $wpdb->get_var( $wpdb->prepare( "SELECT MAX(wpcf7pdf_id) FROM {$table} WHERE wpcf7pdf_id_form = %d", $formular ) ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
	update_post_meta( $aktion_id, '_smj_pdf_stand', $max );
}

/**
 * Löscht alle Anmeldungen einer Aktion und protokolliert das Ergebnis an der Aktion.
 *
 * @param int               $aktion_id Aktion.
 * @param int               $formular  Formular.
 * @param DateTimeImmutable $schluss   Anmeldeschluss.
 */
function smj_ulm_loesche_anmeldungen( $aktion_id, $formular, $schluss ) {
	global $wpdb;
	$anzahl_db  = 0;
	$anzahl_pdf = 0;

	// Advanced CF7 DB: Zeitstempel in "created" ist UTC (gmdate()).
	$vdata = $wpdb->prefix . 'cf7_vdata';
	$entry = $wpdb->prefix . 'cf7_vdata_entry';
	if ( smj_ulm_table_exists( $vdata ) && smj_ulm_table_exists( $entry ) ) {
		$ids = $wpdb->get_col(
			$wpdb->prepare(
				"SELECT DISTINCT e.data_id FROM {$entry} e JOIN {$vdata} v ON v.id = e.data_id WHERE e.cf7_id = %d AND v.created < %s", // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
				$formular,
				$schluss->setTimezone( new DateTimeZone( 'UTC' ) )->format( 'Y-m-d H:i:s' )
			)
		);
		foreach ( array_chunk( array_map( 'intval', $ids ), 200 ) as $chunk ) {
			$in = implode( ',', $chunk );
			$wpdb->query( "DELETE FROM {$entry} WHERE data_id IN ({$in})" ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
			$wpdb->query( "DELETE FROM {$vdata} WHERE id IN ({$in})" ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		}
		$anzahl_db = count( $ids );
	}

	// Send PDF: Einträge bis zum gemerkten Stand samt Dateien.
	$pdf   = $wpdb->prefix . 'wpcf7pdf_files';
	$stand = get_post_meta( $aktion_id, '_smj_pdf_stand', true );
	if ( '' !== $stand && smj_ulm_table_exists( $pdf ) ) {
		$rows = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT wpcf7pdf_id, wpcf7pdf_files, wpcf7pdf_files2 FROM {$pdf} WHERE wpcf7pdf_id_form = %d AND wpcf7pdf_id <= %d", // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
				$formular,
				(int) $stand
			)
		);
		foreach ( $rows as $row ) {
			foreach ( array( $row->wpcf7pdf_files, $row->wpcf7pdf_files2 ) as $url ) {
				$file = smj_ulm_upload_url_to_path( $url );
				if ( $file && is_file( $file ) ) {
					wp_delete_file( $file );
				}
			}
			$wpdb->delete( $pdf, array( 'wpcf7pdf_id' => (int) $row->wpcf7pdf_id ), array( '%d' ) );
		}
		$anzahl_pdf = count( $rows );
	}

	update_post_meta(
		$aktion_id,
		'_smj_geloescht',
		array(
			'zeit'      => current_time( 'mysql' ),
			'eintraege' => $anzahl_db,
			'pdfs'      => $anzahl_pdf,
		)
	);
}

/**
 * Wandelt die URL einer Datei im Upload-Ordner in einen Pfad um. Andere URLs werden ignoriert.
 *
 * @param string $url URL.
 * @return string Pfad oder ''.
 */
function smj_ulm_upload_url_to_path( $url ) {
	if ( ! $url ) {
		return '';
	}
	$uploads = wp_upload_dir();
	if ( 0 !== strpos( $url, $uploads['baseurl'] ) ) {
		return '';
	}
	$path = wp_normalize_path( $uploads['basedir'] . substr( $url, strlen( $uploads['baseurl'] ) ) );
	$base = wp_normalize_path( $uploads['basedir'] );
	return ( 0 === strpos( $path, $base . '/' ) && false === strpos( $path, '..' ) ) ? $path : '';
}
