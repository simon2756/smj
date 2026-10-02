<?php
/**
 * Admin-Oberfläche für Aktionen: Eingabefelder, Übersichtsspalten, Hinweise.
 *
 * @package SMJ_Ulm
 */

defined( 'ABSPATH' ) || exit;

add_action( 'add_meta_boxes_smj_aktion', 'smj_ulm_add_meta_box' );
add_action( 'save_post_smj_aktion', 'smj_ulm_save_meta', 10, 2 );
add_filter( 'manage_smj_aktion_posts_columns', 'smj_ulm_columns' );
add_action( 'manage_smj_aktion_posts_custom_column', 'smj_ulm_column_content', 10, 2 );
add_action( 'admin_notices', 'smj_ulm_admin_notices' );

/**
 * Meta-Box "Anmeldung" im Editor.
 */
function smj_ulm_add_meta_box() {
	add_meta_box( 'smj-ulm-aktion', 'Anmeldung', 'smj_ulm_render_meta_box', 'smj_aktion', 'side', 'high' );
}

/**
 * Anzahl gespeicherter Anmeldungen eines Formulars in Advanced CF7 DB.
 *
 * @param int $form_id Post-ID des CF7-Formulars.
 * @return int|null Null, wenn Advanced CF7 DB nicht installiert ist.
 */
function smj_ulm_anzahl_eintraege( $form_id ) {
	global $wpdb;
	if ( ! $form_id || ! smj_ulm_table_exists( $wpdb->prefix . 'cf7_vdata_entry' ) ) {
		return null;
	}
	return (int) $wpdb->get_var(
		$wpdb->prepare( "SELECT COUNT(DISTINCT data_id) FROM {$wpdb->prefix}cf7_vdata_entry WHERE cf7_id = %d", $form_id )
	);
}

/**
 * Prüft, ob eine Datenbanktabelle existiert.
 *
 * @param string $table Vollständiger Tabellenname.
 * @return bool
 */
function smj_ulm_table_exists( $table ) {
	global $wpdb;
	return $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $wpdb->esc_like( $table ) ) ) === $table;
}

/**
 * Inhalt der Meta-Box.
 *
 * @param WP_Post $post Aktion.
 */
function smj_ulm_render_meta_box( $post ) {
	wp_nonce_field( 'smj_ulm_save_meta', 'smj_ulm_nonce' );

	$formular = (int) get_post_meta( $post->ID, '_smj_formular', true );
	$formulare = get_posts(
		array(
			'post_type'      => 'wpcf7_contact_form',
			'posts_per_page' => -1,
			'orderby'        => 'title',
			'order'          => 'ASC',
		)
	);
	$schluss = smj_ulm_anmeldeschluss( $post->ID );
	$loesch  = smj_ulm_loeschdatum( $post->ID );
	$anzahl  = smj_ulm_anzahl_eintraege( $formular );
	?>
	<style>
		.smj-fields { display: grid; grid-template-columns: 1fr; gap: 12px; margin: 8px 0 16px; }
		.smj-fields label { display: flex; flex-direction: column; gap: 4px; font-weight: 600; }
		.smj-fields input, .smj-fields select { font-weight: 400; }
		.smj-auto { background: #f0f6fc; border-left: 4px solid #004e89; padding: 12px 16px; margin: 0; }
		.smj-auto p { margin: 4px 0; }
	</style>
	<div class="smj-fields">
		<label>Formular
			<select name="smj_formular">
				<option value="0">– Formular wählen –</option>
				<?php foreach ( $formulare as $f ) : ?>
					<option value="<?php echo esc_attr( $f->ID ); ?>" <?php selected( $formular, $f->ID ); ?>><?php echo esc_html( $f->post_title ); ?></option>
				<?php endforeach; ?>
			</select>
		</label>
		<label>Datum der Aktion
			<input type="date" name="smj_datum" value="<?php echo esc_attr( get_post_meta( $post->ID, '_smj_datum', true ) ); ?>" required>
		</label>
		<label>Letzter Tag (optional)
			<input type="date" name="smj_ende" value="<?php echo esc_attr( get_post_meta( $post->ID, '_smj_ende', true ) ); ?>">
		</label>
		<label>Ort
			<input type="text" name="smj_ort" value="<?php echo esc_attr( get_post_meta( $post->ID, '_smj_ort', true ) ); ?>">
		</label>
		<label>Teilnahmebeitrag
			<input type="text" name="smj_beitrag" placeholder="z. B. 20 €" value="<?php echo esc_attr( get_post_meta( $post->ID, '_smj_beitrag', true ) ); ?>">
		</label>
	</div>
	<div class="smj-auto">
		<?php if ( $schluss ) : ?>
			<p><strong>Anmeldung schließt automatisch am <?php echo esc_html( smj_ulm_datum( $schluss ) ); ?></strong> (Beginn der Aktion). Danach ist das Formular nicht mehr erreichbar.</p>
			<p><strong>Alle Anmeldedaten werden am <?php echo esc_html( smj_ulm_datum( $loesch ) ); ?> gelöscht</strong> (<?php echo (int) SMJ_ULM_LOESCHFRIST_TAGE; ?> Tage nach Beginn). Wer die Liste länger braucht, exportiert sie vorher in Advanced CF7 DB.</p>
		<?php else : ?>
			<p>Sobald ein Datum eingetragen ist, schließt die Anmeldung an diesem Tag automatisch und die Daten werden <?php echo (int) SMJ_ULM_LOESCHFRIST_TAGE; ?> Tage später gelöscht.</p>
		<?php endif; ?>
		<?php if ( null !== $anzahl ) : ?>
			<p>Bisher <strong><?php echo (int) $anzahl; ?></strong> Anmeldung(en).
				<?php if ( $formular ) : ?>
					<a href="<?php echo esc_url( admin_url( 'admin.php?page=contact-form-listing&cf7_id=' . $formular ) ); ?>">Anmeldungen ansehen und exportieren</a>
				<?php endif; ?>
			</p>
		<?php endif; ?>
	</div>
	<p class="description">Der Text oben im Editor erscheint auf der Anmeldeseite über dem Formular. Der Auszug und das Beitragsbild werden auf der Startseite angezeigt.</p>
	<?php
}

/**
 * Speichert die Felder der Meta-Box.
 *
 * @param int     $post_id Aktion.
 * @param WP_Post $post    Aktion.
 */
function smj_ulm_save_meta( $post_id, $post ) {
	if (
		! isset( $_POST['smj_ulm_nonce'] ) ||
		! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['smj_ulm_nonce'] ) ), 'smj_ulm_save_meta' ) ||
		( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) ||
		! current_user_can( 'edit_post', $post_id )
	) {
		return;
	}

	$datum = static function ( $field ) {
		$value = isset( $_POST[ $field ] ) ? sanitize_text_field( wp_unslash( $_POST[ $field ] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification
		return preg_match( '/^\d{4}-\d{2}-\d{2}$/', $value ) ? $value : '';
	};
	$text = static function ( $field ) {
		return isset( $_POST[ $field ] ) ? sanitize_text_field( wp_unslash( $_POST[ $field ] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification
	};

	update_post_meta( $post_id, '_smj_formular', absint( $_POST['smj_formular'] ?? 0 ) );
	update_post_meta( $post_id, '_smj_datum', $datum( 'smj_datum' ) );
	update_post_meta( $post_id, '_smj_ende', $datum( 'smj_ende' ) );
	update_post_meta( $post_id, '_smj_ort', $text( 'smj_ort' ) );
	update_post_meta( $post_id, '_smj_beitrag', $text( 'smj_beitrag' ) );
}

/**
 * Spalten der Übersicht.
 *
 * @param array $columns Spalten.
 * @return array
 */
function smj_ulm_columns( $columns ) {
	$date = $columns['date'] ?? null;
	unset( $columns['date'] );
	$columns['smj_datum']   = 'Aktion';
	$columns['smj_status']  = 'Anmeldung';
	$columns['smj_anzahl']  = 'Anmeldungen';
	$columns['smj_loesch']  = 'Daten gelöscht am';
	if ( $date ) {
		$columns['date'] = $date;
	}
	return $columns;
}

/**
 * Inhalt der Spalten.
 *
 * @param string $column  Spalte.
 * @param int    $post_id Aktion.
 */
function smj_ulm_column_content( $column, $post_id ) {
	switch ( $column ) {
		case 'smj_datum':
			echo esc_html( smj_ulm_zeitraum( $post_id ) ?: '– kein Datum –' );
			break;
		case 'smj_status':
			echo smj_ulm_ist_offen( $post_id ) ? '<span style="color:#1f6b3a;font-weight:600">offen</span>' : 'geschlossen';
			break;
		case 'smj_anzahl':
			$anzahl = smj_ulm_anzahl_eintraege( (int) get_post_meta( $post_id, '_smj_formular', true ) );
			echo null === $anzahl ? '–' : (int) $anzahl;
			break;
		case 'smj_loesch':
			$geloescht = get_post_meta( $post_id, '_smj_geloescht', true );
			echo esc_html( $geloescht ? 'gelöscht' : smj_ulm_datum( smj_ulm_loeschdatum( $post_id ) ) );
			break;
	}
}

/**
 * Hinweise auf fehlende Voraussetzungen.
 */
function smj_ulm_admin_notices() {
	$screen = get_current_screen();
	if ( ! $screen || 'smj_aktion' !== $screen->post_type ) {
		return;
	}
	if ( ! defined( 'WPCF7_VERSION' ) ) {
		echo '<div class="notice notice-error"><p>Für Anmeldungen wird das Plugin <strong>Contact Form 7</strong> benötigt.</p></div>';
	}
	if ( ! wp_next_scheduled( 'smj_ulm_cleanup' ) ) {
		echo '<div class="notice notice-warning"><p>Der automatische Lösch-Job ist nicht geplant. Bitte das Plugin „SMJ Ulm – Funktionen“ einmal deaktivieren und wieder aktivieren.</p></div>';
	}
}
