<?php
/**
 * Title: Kopfbereich (Sommer)
 * Slug: smj-ulm-sommer/kopf
 * Categories: header
 * Block Types: core/template-part/header
 * Inserter: no
 *
 * @package SMJ_Ulm_Sommer
 */

?>
<!-- wp:group {"tagName":"header","className":"sommer-kopf","layout":{"type":"default"}} -->
<header class="wp-block-group sommer-kopf">
	<!-- wp:group {"className":"sommer-pille","layout":{"type":"flex","flexWrap":"nowrap","justifyContent":"space-between"}} -->
	<div class="wp-block-group sommer-pille">
		<!-- wp:html -->
		<a class="smj-logo sommer-logo" href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><img src="<?php echo esc_url( get_theme_file_uri( 'assets/images/logo-smj-ulm.png' ) ); ?>" width="507" height="573" alt="SMJ Ulm Alb Donau – zur Startseite"></a>
		<!-- /wp:html -->

		<!-- wp:navigation {"__unstableLocation":"primary","overlayMenu":"mobile","className":"smj-nav sommer-nav","layout":{"type":"flex","justifyContent":"right"},"style":{"spacing":{"blockGap":"0.25rem"}}} /-->
	</div>
	<!-- /wp:group -->
</header>
<!-- /wp:group -->
