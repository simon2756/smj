<?php
/**
 * Übernahme bestehender Anmelde-Beiträge in den Inhaltstyp "Anmeldung".
 *
 * Auf der alten Seite war jede Anmeldung ein normaler Beitrag mit eingebettetem CF7-Formular.
 * Diese Seite listet solche Beiträge auf und legt mit einem Klick die passende Anmeldung an:
 * Titel, Text, Beitragsbild und Formular werden übernommen, Datum und Beitrag ergänzt man.
 * Der alte Beitrag bleibt unverändert; sein Formular schließt automatisch mit der neuen Anmeldung.
 *
 * @package SMJ_Ulm
 */

defined( 'ABSPATH' ) || exit;

add_action( 'admin_menu', 'smj_ulm_uebernahme_menu' );
add_action( 'admin_post_smj_ulm_uebernehmen', 'smj_ulm_uebernehmen' );

/**
 * Untermenü "Aus Beiträgen übernehmen".
 */
function smj_ulm_uebernahme_menu() {
	add_submenu_page(
		'edit.php?post_type=smj_aktion',
		'Aus Beiträgen übernehmen',
		'Aus Beiträgen übernehmen',
		'edit_posts',
		'smj-uebernahme',
		'smj_ulm_uebernahme_seite'
	);
}

/**
 * Findet das CF7-Formular in einem Beitrag (Block oder Shortcode).
 *
 * @param string $content Beitragsinhalt.
 * @return int Post-ID des Formulars oder 0.
 */
function smj_ulm_formular_im_inhalt( $content ) {
	if ( preg_match( '/<!--\s*wp:contact-form-7\/contact-form-selector\s+(\{.*?\})\s*\/?-->/s', $content, $m ) ) {
		$attrs = json_decode( $m[1], true );
		if ( ! empty( $attrs['id'] ) && 'wpcf7_contact_form' === get_post_type( (int) $attrs['id'] ) ) {
			return (int) $attrs['id'];
		}
	}
	if ( preg_match( '/\[contact-form-7\s+([^\]]*)\]/', $content, $m ) ) {
		return smj_ulm_formular_aus_shortcode( shortcode_parse_atts( $m[1] ) );
	}
	return 0;
}

/**
 * Liest den Teilnahmebeitrag aus dem Formular ("Beitrag: <strong>40€</strong>").
 *
 * @param int $form_id Formular.
 * @return string
 */
function smj_ulm_beitrag_aus_formular( $form_id ) {
	$form = (string) get_post_meta( $form_id, '_form', true );
	if ( preg_match( '/Beitrag:?\s*(?:<[^>]+>\s*)*([0-9][0-9.,]*)\s*(?:€|Euro|EUR)/iu', $form, $m ) ) {
		return $m[1] . ' €';
	}
	return '';
}

/**
 * Beiträge mit Anmeldeformular, die noch keine Anmeldung haben.
 *
 * @return array[]
 */
function smj_ulm_uebernahme_kandidaten() {
	$posts = get_posts(
		array(
			'post_type'      => array( 'post', 'page' ),
			'post_status'    => 'publish',
			'posts_per_page' => 50,
			's'              => 'contact-form-7',
			'date_query'     => array( array( 'after' => '18 months ago' ) ),
			'orderby'        => 'date',
			'order'          => 'DESC',
		)
	);

	$uebernommen = get_posts(
		array(
			'post_type'      => 'smj_aktion',
			'post_status'    => 'any',
			'posts_per_page' => -1,
			'fields'         => 'ids',
			'meta_key'       => '_smj_quelle',
		)
	);
	$quellen = array_map( static fn( $id ) => (int) get_post_meta( $id, '_smj_quelle', true ), $uebernommen );

	$kandidaten = array();
	foreach ( $posts as $post ) {
		$form_id = smj_ulm_formular_im_inhalt( $post->post_content );
		if ( ! $form_id || in_array( $post->ID, $quellen, true ) || smj_ulm_aktionen_fuer_formular( $form_id ) ) {
			continue;
		}
		$kandidaten[] = array(
			'post'    => $post,
			'form'    => $form_id,
			'titel'   => trim( preg_replace( '/^Anmeldung\s*:\s*/iu', '', $post->post_title ) ),
			'beitrag' => smj_ulm_beitrag_aus_formular( $form_id ),
		);
	}
	return $kandidaten;
}

/**
 * Admin-Seite.
 */
function smj_ulm_uebernahme_seite() {
	$kandidaten = smj_ulm_uebernahme_kandidaten();
	$erstellt   = isset( $_GET['erstellt'] ) ? absint( $_GET['erstellt'] ) : 0; // phpcs:ignore WordPress.Security.NonceVerification
	?>
	<div class="wrap">
		<h1>Anmeldungen aus bestehenden Beiträgen übernehmen</h1>
		<p>Hier stehen Beiträge der letzten 18 Monate, in denen ein Anmeldeformular steckt und die noch keine Anmeldung haben.
			Datum der Aktion eintragen und „Übernehmen“ klicken. Text, Bilder, Beitragsbild und Formular werden übernommen; der alte Beitrag bleibt, wie er ist.</p>

		<?php if ( $erstellt ) : ?>
			<div class="notice notice-success"><p>Anmeldung angelegt und veröffentlicht – sie erscheint jetzt auf der Startseite. <a href="<?php echo esc_url( get_edit_post_link( $erstellt ) ); ?>">Bearbeiten</a></p></div>
		<?php endif; ?>

		<?php if ( ! $kandidaten ) : ?>
			<p><strong>Keine passenden Beiträge gefunden.</strong> Neue Anmeldungen legst du unter <a href="<?php echo esc_url( admin_url( 'post-new.php?post_type=smj_aktion' ) ); ?>">Anmeldungen → Neue Anmeldung</a> an.</p>
		<?php else : ?>
			<table class="widefat striped" style="max-width: 1100px">
				<thead><tr><th>Beitrag</th><th>Formular</th><th>Datum der Aktion</th><th>Letzter Tag</th><th>Beitrag</th><th></th></tr></thead>
				<tbody>
				<?php foreach ( $kandidaten as $k ) : ?>
					<tr>
						<td><a href="<?php echo esc_url( get_permalink( $k['post'] ) ); ?>" target="_blank" rel="noopener"><?php echo esc_html( $k['post']->post_title ); ?></a><br><small><?php echo esc_html( get_the_date( 'd.m.Y', $k['post'] ) ); ?></small></td>
						<td><?php echo esc_html( get_the_title( $k['form'] ) ); ?></td>
						<td colspan="4">
							<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" style="display:flex;gap:12px;align-items:center;flex-wrap:wrap">
								<?php wp_nonce_field( 'smj_ulm_uebernehmen_' . $k['post']->ID ); ?>
								<input type="hidden" name="action" value="smj_ulm_uebernehmen">
								<input type="hidden" name="quelle" value="<?php echo (int) $k['post']->ID; ?>">
								<label class="screen-reader-text" for="smj-datum-<?php echo (int) $k['post']->ID; ?>">Datum der Aktion</label>
								<input id="smj-datum-<?php echo (int) $k['post']->ID; ?>" type="date" name="smj_datum" required>
								<label class="screen-reader-text" for="smj-ende-<?php echo (int) $k['post']->ID; ?>">Letzter Tag</label>
								<input id="smj-ende-<?php echo (int) $k['post']->ID; ?>" type="date" name="smj_ende">
								<label class="screen-reader-text" for="smj-beitrag-<?php echo (int) $k['post']->ID; ?>">Beitrag</label>
								<input id="smj-beitrag-<?php echo (int) $k['post']->ID; ?>" type="text" name="smj_beitrag" size="8" value="<?php echo esc_attr( $k['beitrag'] ); ?>">
								<button class="button button-primary">Übernehmen</button>
							</form>
						</td>
					</tr>
				<?php endforeach; ?>
				</tbody>
			</table>
		<?php endif; ?>
	</div>
	<?php
}

/**
 * Entfernt Formular-Block bzw. Shortcode aus dem übernommenen Text; das Formular hängt die
 * Anmeldeseite selbst an (und blendet es nach Anmeldeschluss aus).
 *
 * @param string $content Inhalt.
 * @return string
 */
function smj_ulm_inhalt_ohne_formular( $content ) {
	$content = preg_replace( '/<!--\s*wp:contact-form-7\/contact-form-selector\b.*?(?:\/-->|<!--\s*\/wp:contact-form-7\/contact-form-selector\s*-->)/s', '', $content );
	$content = preg_replace( '/\[contact-form-7\s+[^\]]*\]/', '', $content );
	return trim( $content );
}

/**
 * Legt die Anmeldung an.
 */
function smj_ulm_uebernehmen() {
	$quelle = isset( $_POST['quelle'] ) ? absint( $_POST['quelle'] ) : 0;
	check_admin_referer( 'smj_ulm_uebernehmen_' . $quelle );
	if ( ! current_user_can( 'edit_posts' ) || ! $quelle ) {
		wp_die( 'Keine Berechtigung.' );
	}

	$post = get_post( $quelle );
	$form = $post ? smj_ulm_formular_im_inhalt( $post->post_content ) : 0;
	if ( ! $post || ! $form ) {
		wp_die( 'Beitrag oder Formular nicht gefunden.' );
	}

	$datum = sanitize_text_field( wp_unslash( $_POST['smj_datum'] ?? '' ) );
	$ende  = sanitize_text_field( wp_unslash( $_POST['smj_ende'] ?? '' ) );
	if ( ! preg_match( '/^\d{4}-\d{2}-\d{2}$/', $datum ) ) {
		wp_die( 'Bitte ein gültiges Datum eintragen.', '', array( 'back_link' => true ) );
	}

	$aktion = wp_insert_post(
		array(
			'post_type'    => 'smj_aktion',
			'post_status'  => 'publish',
			'post_title'   => trim( preg_replace( '/^Anmeldung\s*:\s*/iu', '', $post->post_title ) ),
			'post_content' => smj_ulm_inhalt_ohne_formular( $post->post_content ),
			'post_excerpt' => $post->post_excerpt,
		),
		true
	);
	if ( is_wp_error( $aktion ) ) {
		wp_die( esc_html( $aktion->get_error_message() ) );
	}

	update_post_meta( $aktion, '_smj_formular', $form );
	update_post_meta( $aktion, '_smj_datum', $datum );
	update_post_meta( $aktion, '_smj_ende', preg_match( '/^\d{4}-\d{2}-\d{2}$/', $ende ) ? $ende : '' );
	update_post_meta( $aktion, '_smj_beitrag', sanitize_text_field( wp_unslash( $_POST['smj_beitrag'] ?? '' ) ) );
	update_post_meta( $aktion, '_smj_quelle', $post->ID );
	$thumb = get_post_thumbnail_id( $post );
	if ( $thumb ) {
		set_post_thumbnail( $aktion, $thumb );
	}

	wp_safe_redirect( admin_url( 'edit.php?post_type=smj_aktion&page=smj-uebernahme&erstellt=' . $aktion ) );
	exit;
}
