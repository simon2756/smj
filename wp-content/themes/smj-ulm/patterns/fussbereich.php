<?php
/**
 * Title: Fußbereich
 * Slug: smj-ulm/fussbereich
 * Categories: footer
 * Block Types: core/template-part/footer
 * Inserter: no
 *
 * @package SMJ_Ulm_Theme
 */

?>
<!-- wp:group {"tagName":"footer","className":"smj-fuss","style":{"spacing":{"padding":{"top":"var:preset|spacing|50","bottom":"var:preset|spacing|30"},"margin":{"top":"0"}}},"backgroundColor":"navy-dark","textColor":"white","layout":{"type":"constrained"}} -->
<footer class="wp-block-group smj-fuss has-white-color has-navy-dark-background-color has-text-color has-background" style="margin-top:0;padding-top:var(--wp--preset--spacing--50);padding-bottom:var(--wp--preset--spacing--30)">

	<!-- wp:group {"align":"wide","className":"smj-newsletter","layout":{"type":"default"}} -->
	<div class="wp-block-group alignwide smj-newsletter">
		<!-- wp:group {"className":"smj-newsletter__text","layout":{"type":"default"}} -->
		<div class="wp-block-group smj-newsletter__text">
			<!-- wp:heading {"level":2,"className":"smj-newsletter__titel"} -->
			<h2 class="wp-block-heading smj-newsletter__titel">Bleib auf dem Laufenden</h2>
			<!-- /wp:heading -->

			<!-- wp:paragraph -->
			<p>Termine, Anmeldestarts und Neuigkeiten aus der Abteilung – ein paar Mal im Jahr per E-Mail.</p>
			<!-- /wp:paragraph -->
		</div>
		<!-- /wp:group -->

		<!-- wp:shortcode -->
		[newsletter_form type="minimal" placeholder="Deine E-Mail-Adresse" button="Abonnieren"]
		<!-- /wp:shortcode -->
	</div>
	<!-- /wp:group -->

	<!-- wp:group {"align":"wide","className":"smj-fuss__haupt","layout":{"type":"default"}} -->
	<div class="wp-block-group alignwide smj-fuss__haupt">
		<!-- wp:group {"className":"smj-fuss__marke","layout":{"type":"default"}} -->
		<div class="wp-block-group smj-fuss__marke">
			<!-- wp:html -->
			<a class="smj-logo smj-logo--fuss" href="<?php echo esc_url( home_url( '/' ) ); ?>"><img src="<?php echo esc_url( get_theme_file_uri( 'assets/images/logo-smj-ulm.png' ) ); ?>" width="507" height="573" alt="SMJ Ulm Alb Donau – zur Startseite"></a>
			<!-- /wp:html -->

			<!-- wp:paragraph -->
			<p>Schönstatt-Mannesjugend<br>Ulm · Alb · Donau</p>
			<!-- /wp:paragraph -->
		</div>
		<!-- /wp:group -->

		<!-- wp:navigation {"__unstableLocation":"footer-menu","overlayMenu":"never","className":"smj-fuss__nav","layout":{"type":"flex","orientation":"horizontal","flexWrap":"wrap"},"style":{"spacing":{"blockGap":"0.5rem 1.75rem"}}} /-->

		<!-- wp:paragraph {"className":"smj-fuss__extern"} -->
		<p class="smj-fuss__extern"><a href="https://www.mjf-ulm.de">Zur Mädchenjugend ↗</a></p>
		<!-- /wp:paragraph -->
	</div>
	<!-- /wp:group -->

	<!-- wp:paragraph {"align":"wide","className":"smj-fuss__copy","fontSize":"small"} -->
	<p class="alignwide smj-fuss__copy has-small-font-size">© <?php echo esc_html( wp_date( 'Y' ) ); ?> SMJ Ulm Alb Donau</p>
	<!-- /wp:paragraph -->
</footer>
<!-- /wp:group -->
