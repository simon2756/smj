<?php
/**
 * Title: Kopfbereich (Nacht)
 * Slug: smj-ulm-nacht/kopf
 * Categories: header
 * Block Types: core/template-part/header
 * Inserter: no
 *
 * @package SMJ_Ulm_Nacht
 */

?>
<!-- wp:group {"tagName":"header","className":"nacht-kopf","layout":{"type":"constrained"}} -->
<header class="wp-block-group nacht-kopf">
	<!-- wp:group {"align":"wide","className":"nacht-kopf__innen","layout":{"type":"flex","flexWrap":"nowrap","justifyContent":"space-between"}} -->
	<div class="wp-block-group alignwide nacht-kopf__innen">
		<!-- wp:html -->
		<a class="smj-logo nacht-logo" href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><img src="<?php echo esc_url( get_theme_file_uri( 'assets/images/logo-smj-ulm.png' ) ); ?>" width="507" height="573" alt="SMJ Ulm Alb Donau – zur Startseite"><span class="nacht-logo__text">SMJ<br>Ulm · Alb · Donau</span></a>
		<!-- /wp:html -->

		<!-- wp:navigation {"__unstableLocation":"primary","overlayMenu":"mobile","className":"smj-nav nacht-nav","layout":{"type":"flex","justifyContent":"right"},"style":{"spacing":{"blockGap":"2rem"}}} /-->
	</div>
	<!-- /wp:group -->
</header>
<!-- /wp:group -->
