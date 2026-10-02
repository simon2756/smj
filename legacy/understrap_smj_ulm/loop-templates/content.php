<?php
/**
 * Post rendering content according to caller of get_template_part
 *
 * @package Understrap
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

	//set default col size to 6
	$col_size = 6;
	if ( $args['col_size'] ) {
		$col_size =  $args['col_size'];
	}
?>

<article class="mb-3 col-lg-<?php echo $col_size ?>" <?php post_class(); ?> id="post-<?php the_ID(); ?>">


	<div class="card rounded h-100 ">
		<a class="img-card" href= "<?php echo esc_url( get_permalink() ) ; ?>">
			<?php echo get_the_post_thumbnail( $post->ID, 'large' ); ?>
		</a>
		<div class="card-content">

			<!-- meta start -->
			 <!--
			<?php if ( 'post' === get_post_type() ) : ?>
				<div class="d-flex justify-content-start fw-bold gap-1 flex-wrap">
					<?php 
						$categories = get_the_category();
						if ( ! empty( $categories ) ) {
							foreach($categories as $category) { 
								echo '<a href='. get_category_link( $category->term_id)  .' class="ml-1 mr-1 pt-0 pb-0 btn btn-secondary btn-sm fw-bold" role="button" aria-pressed="true">'. $category->name . '</a>'; 
							}
						}
					?>
				</div>
			<?php endif; ?>
			-->
			<!-- .meta end-->

			<!-- card-title start -->
			<h4 class="card-title">
				<a href= "<?php echo esc_url( get_permalink() ) ; ?>">
					<?php the_title(sprintf( '<h3 class="entry-title"><a href="%s" rel="bookmark" class="link-secondary">', esc_url( get_permalink() ) ),'</a></h3>');?>
				</a>
			</h4>
			<!-- card-title end -->





			<!-- posted on  -->
			<div class="d-flex justify-content-between align-items-center fw-bold">
					<?php understrap_posted_on(); ?> 
			</div>



			<!-- content start-->
	
				<?php
					the_excerpt();
					//understrap_link_pages();
				?>
		
			<!-- content end-->
		</div>
	</div>
</article><!-- #post-<?php the_ID(); ?> -->
