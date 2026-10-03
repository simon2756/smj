<?php
/**
 * Title: Fußbereich (Sommer)
 * Slug: smj-ulm-sommer/fuss
 * Categories: footer
 * Block Types: core/template-part/footer
 * Inserter: no
 *
 * @package SMJ_Ulm_Sommer
 */

?>
<!-- wp:group {"tagName":"footer","className":"sommer-fuss","layout":{"type":"constrained"}} -->
<footer class="wp-block-group sommer-fuss">
	<!-- wp:group {"align":"wide","className":"sommer-fuss__raster","layout":{"type":"default"}} -->
	<div class="wp-block-group alignwide sommer-fuss__raster">
		<!-- wp:group {"className":"sommer-kachel sommer-kachel--newsletter","layout":{"type":"default"}} -->
		<div class="wp-block-group sommer-kachel sommer-kachel--newsletter">
			<!-- wp:heading {"level":2} -->
			<h2 class="wp-block-heading">Post von uns? <span class="sommer-strich">Gerne!</span></h2>
			<!-- /wp:heading -->

			<!-- wp:paragraph -->
			<p>Termine, Anmeldestarts und Neuigkeiten – ein paar Mal im Jahr per E-Mail.</p>
			<!-- /wp:paragraph -->

			<!-- wp:shortcode -->
			[newsletter_form type="minimal" placeholder="Deine E-Mail-Adresse" button="Abonnieren"]
			<!-- /wp:shortcode -->
		</div>
		<!-- /wp:group -->

		<!-- wp:group {"className":"sommer-kachel sommer-kachel--marke","layout":{"type":"default"}} -->
		<div class="wp-block-group sommer-kachel sommer-kachel--marke">
			<!-- wp:html -->
			<a class="smj-logo sommer-logo sommer-logo--gross" href="<?php echo esc_url( home_url( '/' ) ); ?>"><img src="<?php echo esc_url( get_theme_file_uri( 'assets/images/logo-smj-ulm.png' ) ); ?>" width="507" height="573" alt="SMJ Ulm Alb Donau – zur Startseite"></a>
			<!-- /wp:html -->

			<!-- wp:navigation {"__unstableLocation":"footer-menu","overlayMenu":"never","className":"sommer-fuss__nav","layout":{"type":"flex","flexWrap":"wrap"},"style":{"spacing":{"blockGap":"0.5rem"}}} /-->

			<!-- wp:paragraph {"className":"sommer-fuss__extern"} -->
			<p class="sommer-fuss__extern"><a href="https://www.mjf-ulm.de">Zur Mädchenjugend ↗</a></p>
			<!-- /wp:paragraph -->
		</div>
		<!-- /wp:group -->
	</div>
	<!-- /wp:group -->

	<!-- wp:paragraph {"align":"wide","className":"sommer-fuss__copy"} -->
	<p class="alignwide sommer-fuss__copy">© <?php echo esc_html( wp_date( 'Y' ) ); ?> Schönstatt-Mannesjugend Ulm · Alb · Donau</p>
	<!-- /wp:paragraph -->
</footer>
<!-- /wp:group -->
