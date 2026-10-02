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
<!-- wp:group {"tagName":"footer","className":"smj-fuss","style":{"spacing":{"padding":{"top":"var:preset|spacing|50","bottom":"var:preset|spacing|30"},"margin":{"top":"0"}},"border":{"top":{"color":"var:preset|color|line","width":"1px"}}},"backgroundColor":"white","layout":{"type":"constrained"}} -->
<footer class="wp-block-group smj-fuss has-white-background-color has-background" style="border-top-color:var(--wp--preset--color--line);border-top-width:1px;margin-top:0;padding-top:var(--wp--preset--spacing--50);padding-bottom:var(--wp--preset--spacing--30)">
	<!-- wp:columns {"align":"wide","style":{"spacing":{"blockGap":{"left":"var:preset|spacing|40"}}}} -->
	<div class="wp-block-columns alignwide">
		<!-- wp:column {"width":"34%"} -->
		<div class="wp-block-column" style="flex-basis:34%">
			<!-- wp:html -->
			<a class="smj-logo smj-logo--fuss" href="<?php echo esc_url( home_url( '/' ) ); ?>"><img src="<?php echo esc_url( get_theme_file_uri( 'assets/images/logo-smj-ulm.png' ) ); ?>" width="507" height="573" alt="SMJ Ulm Alb Donau – zur Startseite"></a>
			<!-- /wp:html -->

			<!-- wp:paragraph {"textColor":"muted"} -->
			<p class="has-muted-color has-text-color">Schönstatt-Mannesjugend in den Regionen Ulm, Alb und Donau.</p>
			<!-- /wp:paragraph -->
		</div>
		<!-- /wp:column -->

		<!-- wp:column -->
		<div class="wp-block-column">
			<!-- wp:heading {"level":2,"className":"smj-fuss__titel"} -->
			<h2 class="wp-block-heading smj-fuss__titel">Links</h2>
			<!-- /wp:heading -->

			<!-- wp:navigation {"__unstableLocation":"footer-menu","overlayMenu":"never","className":"smj-fuss__nav","layout":{"type":"flex","orientation":"vertical"},"style":{"spacing":{"blockGap":"0.6rem"}}} /-->
		</div>
		<!-- /wp:column -->

		<!-- wp:column {"width":"38%"} -->
		<div class="wp-block-column" style="flex-basis:38%">
			<!-- wp:heading {"level":2,"className":"smj-fuss__titel"} -->
			<h2 class="wp-block-heading smj-fuss__titel">Newsletter</h2>
			<!-- /wp:heading -->

			<!-- wp:paragraph {"textColor":"muted","fontSize":"small"} -->
			<p class="has-muted-color has-text-color has-small-font-size">Termine und Anmeldestarts direkt ins Postfach.</p>
			<!-- /wp:paragraph -->

			<!-- wp:shortcode -->
			[newsletter_form type="minimal"]
			<!-- /wp:shortcode -->
		</div>
		<!-- /wp:column -->
	</div>
	<!-- /wp:columns -->

	<!-- wp:paragraph {"align":"wide","className":"smj-fuss__copy","textColor":"muted","fontSize":"small"} -->
	<p class="alignwide smj-fuss__copy has-muted-color has-text-color has-small-font-size">© <?php echo esc_html( wp_date( 'Y' ) ); ?> SMJ Ulm Alb Donau</p>
	<!-- /wp:paragraph -->
</footer>
<!-- /wp:group -->
