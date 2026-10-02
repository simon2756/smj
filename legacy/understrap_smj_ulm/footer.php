<?php
/**
 * The template for displaying the footer
 *
 * Contains the closing of the #content div and all content after
 *
 * @package Understrap
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

$container = get_theme_mod( 'understrap_container_type' );
?>

<footer class="footer mt-auto">

<?php get_template_part( 'sidebar-templates/sidebar', 'footerfull' ); ?>



<nav class="d-flex justify-content-center  navbar navbar-expand-lg navbar-dark bg-secondary">

    <ul class="navbar-nav">
	<?php


    $menuLocations = get_nav_menu_locations(); 
    if(isset( $menuLocations['footer-menu'])){
        
        $footerMenuID = $menuLocations['footer-menu'];
        $menuitems = wp_get_nav_menu_items($footerMenuID, array( 'order' => 'DESC' ) );
        foreach($menuitems as $item)
        {
            echo '<li class="nav-item active text-center">';
            echo '<a class="nav-link" href="'. $item->url  . '">'. $item->title  . '<span class="sr-only">(current)</span></a>';
            echo '</li>';
        }
    }
	?>
    </ul>
</nav>



<?php wp_footer(); ?>

</footer>
</body>

</html>

