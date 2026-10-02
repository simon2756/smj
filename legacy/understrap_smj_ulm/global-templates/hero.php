<?php
/**
 * Hero setup
 *
 * @package Understrap
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;
get_template_part( 'sidebar-templates/sidebar', 'jumbotron' ); //FB: Werbung soll immer angezeigt werden
if ( is_active_sidebar( 'hero' ) || is_active_sidebar( 'statichero' )  ) :
	?>
	<div class="wrapper" id="wrapper-hero">
		<?php
		get_template_part( 'sidebar-templates/sidebar', 'hero' );
		get_template_part( 'sidebar-templates/sidebar', 'statichero' );
		?>

	</div>

	<?php
endif;
