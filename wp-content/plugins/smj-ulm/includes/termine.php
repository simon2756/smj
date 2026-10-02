<?php
/**
 * Termine aus dem Plugin "SMJ Ulm/Alb/Donau Kalender" (smj-ulm-cal) im neuen Design.
 *
 * Das Kalender-Plugin lädt den Kalender stündlich nach wp-content/plugins/smj-ulm-cal/data/calender.ics.
 * Diese Datei wird hier mit dem ICS-Parser des Kalender-Plugins gelesen; das Kalender-Plugin
 * selbst bleibt unverändert und kümmert sich weiter um Abo-Kalender und Shortcodes.
 *
 * @package SMJ_Ulm
 */

defined( 'ABSPATH' ) || exit;

/**
 * Pfad der vom Kalender-Plugin heruntergeladenen Kalenderdatei.
 *
 * @return string
 */
function smj_ulm_kalender_datei() {
	return WP_PLUGIN_DIR . '/smj-ulm-cal/data/calender.ics';
}

/**
 * Kommende Termine.
 *
 * @param int    $anzahl     Maximale Anzahl.
 * @param int    $monate     Zeitraum ab heute in Monaten.
 * @param string $kategorien Kommagetrennt; "A&B" heißt: beide Kategorien müssen gesetzt sein
 *                           (gleiche Schreibweise wie beim Shortcode des Kalender-Plugins).
 * @return array[]|null Termine oder null, wenn der Kalender nicht verfügbar ist.
 */
function smj_ulm_termine( $anzahl = 5, $monate = 12, $kategorien = '' ) {
	$datei = smj_ulm_kalender_datei();
	if ( ! class_exists( '\ICal\ICal' ) || ! is_readable( $datei ) ) {
		return null;
	}

	$cache_key = 'smj_ulm_termine_' . md5( implode( '|', array( $anzahl, $monate, $kategorien, filemtime( $datei ), current_time( 'Y-m-d' ) ) ) );
	$cached    = get_transient( $cache_key );
	if ( is_array( $cached ) ) {
		return $cached;
	}

	try {
		$ical = new \ICal\ICal(
			$datei,
			array(
				'defaultTimeZone' => 'UTC',
				'defaultWeekStart' => 'MO',
				'skipRecurrence'  => false,
			)
		);
		$events = $ical->eventsFromInterval( max( 1, (int) $monate ) . ' month' );
	} catch ( \Exception $e ) {
		return null;
	}

	$filter = array_filter( array_map( 'trim', explode( ',', (string) $kategorien ) ) );
	if ( $filter ) {
		$events = array_filter(
			$events,
			static function ( $event ) use ( $filter ) {
				$vorhanden = array_map( 'strtolower', array_map( 'trim', $event->get_categories() ) );
				foreach ( $filter as $gruppe ) {
					$alle = array_map( 'strtolower', array_map( 'trim', explode( '&', $gruppe ) ) );
					if ( ! array_diff( $alle, $vorhanden ) ) {
						return true;
					}
				}
				return false;
			}
		);
	}

	$tz     = wp_timezone();
	$termine = array();
	foreach ( array_slice( array_values( $events ), 0, max( 1, (int) $anzahl ) ) as $event ) {
		$start_arr = $event->dtstart_array;
		$end_arr   = property_exists( $event, 'dtend_array' ) && $event->dtend_array ? $event->dtend_array : $start_arr;
		$ganztags  = isset( $start_arr[0]['VALUE'] ) && 'DATE' === $start_arr[0]['VALUE'];

		if ( $ganztags ) {
			// Ganztägige Termine haben keine Uhrzeit und gelten in Ortszeit; DTEND ist der Folgetag.
			$start = DateTimeImmutable::createFromFormat( '!Ymd', substr( (string) $start_arr[1], 0, 8 ), $tz );
			$ende  = DateTimeImmutable::createFromFormat( '!Ymd', substr( (string) $end_arr[1], 0, 8 ), $tz );
			if ( ! $start ) {
				continue;
			}
			$ende = ( $ende && $ende > $start ) ? $ende->modify( '-1 day' ) : $start;
		} else {
			$start = DateTimeImmutable::createFromInterface( $ical->iCalDateToDateTime( $start_arr[3] ) )->setTimezone( $tz );
			$ende  = DateTimeImmutable::createFromInterface( $ical->iCalDateToDateTime( $end_arr[3] ) )->setTimezone( $tz );
		}

		$termine[] = array(
			'titel'        => (string) $event->summary,
			'ort'          => isset( $event->location ) ? (string) $event->location : '',
			'beschreibung' => isset( $event->description ) ? wp_strip_all_tags( (string) $event->description ) : '',
			'start'        => $start->getTimestamp(),
			'ende'         => $ende->getTimestamp(),
			'ganztags'     => $ganztags,
			'mehrtaegig'   => $start->format( 'Y-m-d' ) !== $ende->format( 'Y-m-d' ),
		);
	}

	set_transient( $cache_key, $termine, HOUR_IN_SECONDS );
	return $termine;
}

/**
 * HTML der Terminliste.
 *
 * @param array $args anzahl, monate, kategorien, alleLink, aboLink.
 * @return string
 */
function smj_ulm_termine_html( $args = array() ) {
	$args = wp_parse_args(
		$args,
		array(
			'anzahl'     => 5,
			'monate'     => 12,
			'kategorien' => '',
			'alleLink'   => '',
			'aboLink'    => '',
		)
	);

	$termine = smj_ulm_termine( (int) $args['anzahl'], (int) $args['monate'], (string) $args['kategorien'] );
	if ( null === $termine ) {
		return current_user_can( 'edit_posts' )
			? '<p class="smj-termine__leer">Der Kalender ist nicht verfügbar. Ist das Plugin „SMJ Ulm/Alb/Donau Kalender“ aktiv und hat es den Kalender schon geladen?</p>'
			: '';
	}

	$tz = wp_timezone();
	$html = '<ul class="smj-termine">';
	if ( ! $termine ) {
		$html .= '<li class="smj-termine__leer">Gerade stehen keine Termine an.</li>';
	}
	foreach ( $termine as $t ) {
		$zeit = '';
		if ( $t['mehrtaegig'] ) {
			$zeit = wp_date( 'D, d.m.', $t['start'], $tz ) . ' – ' . wp_date( 'D, d.m.Y', $t['ende'], $tz );
		} elseif ( $t['ganztags'] ) {
			$zeit = wp_date( 'l, d.m.Y', $t['start'], $tz );
		} else {
			$zeit = wp_date( 'D, d.m.', $t['start'], $tz ) . ', ' . wp_date( 'H:i', $t['start'], $tz ) . ' – ' . wp_date( 'H:i', $t['ende'], $tz ) . ' Uhr';
		}

		$html .= '<li class="smj-termin">'
			. '<span class="smj-termin__datum" aria-hidden="true"><span class="smj-termin__tag">' . esc_html( wp_date( 'd', $t['start'], $tz ) ) . '</span>'
			. '<span class="smj-termin__monat">' . esc_html( wp_date( 'M', $t['start'], $tz ) ) . '</span></span>'
			. '<span class="smj-termin__text"><strong class="smj-termin__titel">' . esc_html( $t['titel'] ) . '</strong>'
			. '<span class="smj-termin__zeit">' . esc_html( $zeit ) . '</span>'
			. ( $t['ort'] ? '<span class="smj-termin__ort">' . esc_html( $t['ort'] ) . '</span>' : '' )
			. ( $t['beschreibung'] ? '<span class="smj-termin__beschreibung">' . esc_html( $t['beschreibung'] ) . '</span>' : '' )
			. '</span></li>';
	}
	$html .= '</ul>';

	$links = '';
	if ( $args['alleLink'] ) {
		$links .= '<a class="smj-termine__alle" href="' . esc_url( $args['alleLink'] ) . '">Alle Termine →</a>';
	}
	if ( $args['aboLink'] ) {
		$links .= '<a class="smj-termine__abo" href="' . esc_url( $args['aboLink'] ) . '">Kalender abonnieren</a>';
	}
	if ( $links ) {
		$html .= '<div class="smj-termine__links">' . $links . '</div>';
	}
	return $html;
}
