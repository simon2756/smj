<?php
/**
 * SMJ Ulm – Theme-Funktionen.
 *
 * Funktionen der Website (Anmeldungen, Termine, Newsletter) stecken im Plugin
 * "SMJ Ulm – Funktionen". Das Theme kümmert sich nur um Darstellung und die
 * Gestaltungsblöcke Diashow, Countdown und Brotkrümel.
 *
 * @package SMJ_Ulm_Theme
 */

defined( 'ABSPATH' ) || exit;

define( 'SMJ_THEME_VERSION', wp_get_theme( get_template() )->get( 'Version' ) );

require_once get_theme_file_path( 'inc/bloecke.php' );

add_action( 'after_setup_theme', 'smj_theme_setup' );
add_action( 'wp_enqueue_scripts', 'smj_theme_assets' );

/**
 * Theme-Unterstützung.
 */
function smj_theme_setup() {
	add_theme_support( 'editor-styles' );
	add_editor_style( array( 'assets/css/theme.css', 'assets/css/inhalte.css' ) );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'custom-logo' );
}

/**
 * Stylesheet und Skript für die Website.
 */
function smj_theme_assets() {
	wp_enqueue_style( 'smj-theme', get_theme_file_uri( 'assets/css/theme.css' ), array(), SMJ_THEME_VERSION );
	wp_enqueue_style( 'smj-inhalte', get_theme_file_uri( 'assets/css/inhalte.css' ), array( 'smj-theme' ), SMJ_THEME_VERSION );
}

add_action( 'after_setup_theme', 'smj_theme_menus' );

/**
 * Menü-Positionen mit denselben Namen wie im alten Theme, damit WordPress die
 * bestehenden Menüs beim Theme-Wechsel automatisch übernimmt.
 */
function smj_theme_menus() {
	register_nav_menus(
		array(
			'primary'     => 'Hauptmenü',
			'footer-menu' => 'Footer-Menü',
		)
	);
}

add_filter( 'render_block_data', 'smj_theme_navigation_aus_menue' );
add_filter( 'render_block_core/shortcode', 'smj_theme_shortcode_in_parts' );

/**
 * Füllt die Navigationsblöcke im Kopf- und Fußbereich mit dem klassischen Menü, das der
 * Menü-Position zugewiesen ist. So bleiben die Menüs unter Design → Menüs pflegbar,
 * auch ohne das Gutenberg-Plugin. Wird im Website-Editor ein eigenes Navigationsmenü
 * gewählt (Attribut "ref"), hat dieses Vorrang.
 *
 * @param array $block Geparster Block.
 * @return array
 */
function smj_theme_navigation_aus_menue( $block ) {
	if (
		'core/navigation' !== $block['blockName'] ||
		empty( $block['attrs']['__unstableLocation'] ) ||
		! empty( $block['attrs']['ref'] ) ||
		! empty( $block['innerBlocks'] )
	) {
		return $block;
	}

	$locations = get_nav_menu_locations();
	$menu_id   = $locations[ $block['attrs']['__unstableLocation'] ] ?? 0;
	$menu      = $menu_id ? wp_get_nav_menu_object( $menu_id ) : false;
	if ( ! $menu || ! class_exists( 'WP_Classic_To_Block_Menu_Converter' ) ) {
		return $block;
	}

	$markup = WP_Classic_To_Block_Menu_Converter::convert( $menu );
	if ( ! is_string( $markup ) || '' === $markup ) {
		return $block;
	}

	$inner = array_values( array_filter( parse_blocks( $markup ), static fn( $b ) => ! empty( $b['blockName'] ) ) );
	$block['innerBlocks']  = $inner;
	$block['innerContent'] = array_fill( 0, count( $inner ), null );
	return $block;
}

/**
 * WordPress führt Shortcodes nur im Template selbst aus, nicht in Template-Teilen wie dem
 * Fußbereich. Damit z. B. das Newsletter-Formular dort funktioniert, wird der Shortcode-Block
 * hier ausgeführt.
 *
 * @param string $content Inhalt des Shortcode-Blocks.
 * @return string
 */
function smj_theme_shortcode_in_parts( $content ) {
	// Der Block legt den Shortcode in einen Absatz; Formulare darin wären ungültiges HTML.
	$content = preg_replace( '#^\s*<p>\s*(.*?)\s*</p>\s*$#s', '$1', $content );
	return do_shortcode( $content );
}

add_filter( 'render_block_core/post-title', 'smj_theme_doppelter_seitentitel', 5, 3 );

/**
 * Viele bestehende Seiten beginnen mit einer eigenen Überschrift ("Kreise in unserer Abteilung").
 * Das alte Theme hat dort den Seitentitel ausgeblendet. Damit die Überschrift nicht doppelt
 * erscheint, entfällt der Seitentitel, wenn der Inhalt mit einer Überschrift beginnt.
 *
 * @param string   $content Gerenderter Titel.
 * @param array    $parsed  Geparster Block.
 * @param WP_Block $block   Block-Instanz.
 * @return string
 */
function smj_theme_doppelter_seitentitel( $content, $parsed, $block ) {
	$post_id = (int) ( $block->context['postId'] ?? 0 );
	if ( ! is_page() || get_queried_object_id() !== $post_id ) {
		return $content;
	}
	foreach ( parse_blocks( (string) get_post_field( 'post_content', $post_id ) ) as $erster ) {
		if ( empty( $erster['blockName'] ) ) {
			continue;
		}
		$ist_ueberschrift = 'core/heading' === $erster['blockName']
			|| ( 'core/html' === $erster['blockName'] && preg_match( '/^\s*<h[12]\b/i', $erster['innerHTML'] ) );
		return $ist_ueberschrift ? '' : $content;
	}
	return $content;
}

add_filter( 'render_block_core/post-title', 'smj_theme_galerie_titel', 10, 3 );

/**
 * Zeltlager-Galerien in Übersichten: "Zeltlager Bilder 2026: Asterix und Obelix" wird zu
 * Jahreszahl und Lagerthema aufgeteilt, damit die Kacheln das Jahr groß zeigen können.
 *
 * @param string   $content Gerenderter Titel.
 * @param array    $parsed  Geparster Block.
 * @param WP_Block $block   Block-Instanz.
 * @return string
 */
function smj_theme_galerie_titel( $content, $parsed, $block ) {
	$post_id = $block->context['postId'] ?? 0;
	$ist_hauptbeitrag = is_singular() && get_queried_object_id() === (int) $post_id;
	if ( ! $post_id || $ist_hauptbeitrag || ! has_category( 'zetlager-galerie', $post_id ) ) {
		return $content;
	}
	if ( ! preg_match( '/^\s*Zeltlager[- ]?Bilder\s+(\d{4})\s*[:–-]\s*(.+)$/u', wp_strip_all_tags( get_the_title( $post_id ) ), $m ) ) {
		return $content;
	}
	// Text bleibt vollständig lesbar ("Zeltlager Bilder 2026: …"); nur die Galerie-Kacheln
	// blenden per CSS "Zeltlager Bilder" aus und zeigen das Jahr groß.
	$inner = '<span class="smj-galerie-titel__vor">Zeltlager Bilder </span>'
		. '<span class="smj-galerie-titel__jahr">' . esc_html( $m[1] ) . '</span>'
		. '<span class="smj-galerie-titel__trenner">: </span>'
		. '<span class="smj-galerie-titel__thema">' . esc_html( trim( $m[2] ) ) . '</span>';

	if ( preg_match( '#<a\b[^>]*>.*?</a>#s', $content ) ) {
		return preg_replace( '#(<a\b[^>]*>).*?(</a>)#s', '$1' . str_replace( '$', '\\$', $inner ) . '$2', $content, 1 );
	}
	return preg_replace( '#(<h[1-6]\b[^>]*>).*?(</h[1-6]>)#s', '$1' . str_replace( '$', '\\$', $inner ) . '$2', $content, 1 );
}

add_filter( 'render_block_core/paragraph', 'smj_theme_ohne_maedchen_button' );

/**
 * Der Button "Zur Mädchenjugend" unter den Terminen ist entfallen (der Link steht im Footer).
 * Wurde die Startseite im Website-Editor schon gespeichert, steckt der Button noch in dieser
 * gespeicherten Fassung; er wird deshalb auch dort nicht mehr ausgegeben.
 *
 * @param string $content Gerenderter Absatz.
 * @return string
 */
function smj_theme_ohne_maedchen_button( $content ) {
	return false !== strpos( $content, 'smj-linkkarte' ) ? '' : $content;
}
