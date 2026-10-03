<?php
/**
 * Title: Fußbereich (Nacht)
 * Slug: smj-ulm-nacht/fuss
 * Categories: footer
 * Block Types: core/template-part/footer
 * Inserter: no
 *
 * @package SMJ_Ulm_Nacht
 */

?>
<!-- wp:group {"tagName":"footer","className":"nacht-fuss","layout":{"type":"constrained"}} -->
<footer class="wp-block-group nacht-fuss">
	<!-- wp:group {"align":"wide","className":"nacht-fuss__oben","layout":{"type":"default"}} -->
	<div class="wp-block-group alignwide nacht-fuss__oben">
		<!-- wp:group {"className":"nacht-fuss__newsletter","layout":{"type":"default"}} -->
		<div class="wp-block-group nacht-fuss__newsletter">
			<!-- wp:heading {"level":2} -->
			<h2 class="wp-block-heading">Nichts verpassen</h2>
			<!-- /wp:heading -->

			<!-- wp:paragraph -->
			<p>Termine, Anmeldestarts und Neuigkeiten – ein paar Mal im Jahr per E-Mail.</p>
			<!-- /wp:paragraph -->

			<!-- wp:shortcode -->
			[newsletter_form type="minimal" placeholder="Deine E-Mail-Adresse" button="Abonnieren"]
			<!-- /wp:shortcode -->
		</div>
		<!-- /wp:group -->

		<!-- wp:group {"className":"nacht-fuss__links","layout":{"type":"default"}} -->
		<div class="wp-block-group nacht-fuss__links">
			<!-- wp:navigation {"__unstableLocation":"footer-menu","overlayMenu":"never","className":"nacht-fuss__nav","layout":{"type":"flex","orientation":"vertical"},"style":{"spacing":{"blockGap":"0.6rem"}}} /-->

			<!-- wp:paragraph {"className":"nacht-fuss__extern"} -->
			<p class="nacht-fuss__extern"><a href="https://www.mjf-ulm.de">Mädchenjugend ↗</a></p>
			<!-- /wp:paragraph -->
		</div>
		<!-- /wp:group -->
	</div>
	<!-- /wp:group -->

	<!-- wp:paragraph {"align":"wide","className":"nacht-fuss__riesig"} -->
	<p class="alignwide nacht-fuss__riesig" aria-hidden="true">SMJ Ulm Alb Donau</p>
	<!-- /wp:paragraph -->

	<!-- wp:paragraph {"align":"wide","className":"nacht-fuss__copy"} -->
	<p class="alignwide nacht-fuss__copy">© <?php echo esc_html( wp_date( 'Y' ) ); ?> Schönstatt-Mannesjugend Ulm/Alb/Donau</p>
	<!-- /wp:paragraph -->
</footer>
<!-- /wp:group -->
