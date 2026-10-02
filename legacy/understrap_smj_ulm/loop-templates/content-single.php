<?php
/**
 * Single post partial template
 *
 * @package Understrap
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;
?>

<article <?php post_class(); ?> id="post-<?php the_ID(); ?>">

	<header class="entry-header">
		<div class="post-header-layout">
			<div class="post-header-content">
				<div class="post-header-title-wrap">
					<?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>
				</div>

				<div class="d-flex gap-2 post-meta-row">
					<div class="post-meta-date">
						<?php understrap_posted_on(); ?>
					</div>

					<div class="post-meta-categories">
						<?php
						$categories = get_the_category();
						if ( ! empty( $categories ) ) {
							foreach ( $categories as $category ) {
								echo '<a href=' . get_category_link( $category->term_id ) . ' class="ml-1 mr-1 pt-0 pb-0 btn btn-secondary btn-sm post-meta-category" role="button" aria-pressed="true">' . $category->name . '</a>';
							}
						}
						?>
					</div>
				</div>
			</div>
		</div>

		

	</header><!-- .entry-header -->
	<hr class="my-3">
	<div class="entry-content">

		<?php
		the_content();
		understrap_link_pages();
		?>

	</div><!-- .entry-content -->

	<footer class="entry-footer">

		<?php /*todoFB understrap_entry_footer(); */?>

	</footer><!-- .entry-footer -->

</article><!-- #post-<?php the_ID(); ?> -->
