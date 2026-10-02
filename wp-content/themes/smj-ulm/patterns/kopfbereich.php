<?php
/**
 * Title: Kopfbereich
 * Slug: smj-ulm/kopfbereich
 * Categories: header
 * Block Types: core/template-part/header
 * Inserter: no
 *
 * @package SMJ_Ulm_Theme
 */

?>
<!-- wp:group {"tagName":"header","className":"smj-kopf","style":{"spacing":{"padding":{"top":"0.75rem","bottom":"0.75rem"}},"border":{"bottom":{"color":"var:preset|color|line","width":"1px"}}},"backgroundColor":"white","layout":{"type":"constrained"}} -->
<header class="wp-block-group smj-kopf has-white-background-color has-background" style="border-bottom-color:var(--wp--preset--color--line);border-bottom-width:1px;padding-top:0.75rem;padding-bottom:0.75rem">
	<!-- wp:group {"align":"wide","layout":{"type":"flex","flexWrap":"nowrap","justifyContent":"space-between"}} -->
	<div class="wp-block-group alignwide">
		<!-- wp:html -->
		<a class="smj-logo" href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><img src="<?php echo esc_url( get_theme_file_uri( 'assets/images/logo-smj-ulm.png' ) ); ?>" width="507" height="573" alt="SMJ Ulm Alb Donau – zur Startseite"></a>
		<!-- /wp:html -->

		<!-- wp:navigation {"__unstableLocation":"primary","overlayMenu":"mobile","className":"smj-nav","layout":{"type":"flex","justifyContent":"right"},"style":{"spacing":{"blockGap":"1.75rem"},"typography":{"fontWeight":"600"}}} /-->
	</div>
	<!-- /wp:group -->
</header>
<!-- /wp:group -->
