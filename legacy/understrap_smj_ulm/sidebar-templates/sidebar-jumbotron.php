<?php
/**
 * Sidebar - The Hero Canvas Widget Area
 *
 * @package Understrap
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

    function parseOffset($offset){
      $offset = intval($offset);
      $defaultVal = 0;
      if(!is_int($offset) || $offset < 0 ||$offset >100){
        return $defaultVal;
      }

      return $offset;

    }


    $jumptronIsActive = get_theme_mod('understrap_jumbotron_isActive');

    //Text Settings
    $jumptronHeadline = get_theme_mod('understrap_jumbotron_headline');
    $jumptronParagraph = get_theme_mod('understrap_jumbotron_paragrahp');

    //Button Settings
    $jumptronButtonText = get_theme_mod('understrap_jumbotron_button_text');
    $jumptronButtonLink = get_theme_mod('understrap_jumbotron_button_link');

    //Image settings 
    $jumptronBgXoffst = parseOffset(get_theme_mod('understrap_jumbotron_image_x_offset'));
    $jumptronBgYoffst = parseOffset(get_theme_mod('understrap_jumbotron_image_y_offset'));

    $jumptronBgYAnimate = ($jumptronBgYoffst -30 > 0) ? $jumptronBgYoffst - 30 : 0;

    $jumptronBgImage = get_theme_mod('understrap_jumbotron_image');

    $jumptronBgImageUrl = wp_get_attachment_url( intval($jumptronBgImage) );


    if($jumptronIsActive){
?>
<!-- start -->

<style>
    .jumbotron_background{
        background: url( "<?php echo $jumptronBgImageUrl; ?>");
        background-repeat: no-repeat;
        background-size: cover;
        background-position: <?php echo $jumptronBgXoffst . "% " . $jumptronBgYoffst."%"; ?>;

        background-image: linear-gradient(to bottom,rgba(0,0,0,0.6) 0%, rgba(0,0,0,0.6) 80%),url(<?php echo $jumptronBgImageUrl; ?>);

        position: absolute;
        z-index: -1;
        top: 0;
        bottom: 0;
        left: 0;
        right: 0;
        width: 100%;
        height: 100%;
        
        -webkit-filter: blur(1px);
        filter: blur(1px);
        animation: animation_bg_image 4s ease-in; 
    }

    .jumbotron_content{
        position: relative;
        z-index: 1;

    }
/*///////////////////////////////////////////////////////////////////////////////////////////////////////////////*/


    .custom-box {
   
}
.custom-headline {
  /*text-shadow: 0px 0px 3px #fff, 0px 0px 3px #fff, 2px 2px 4px #fff;*/
  font-weight: 1000;
}

.custom-paragraph {

  font-weight: 500;
}

/*///////////////////////////////////////////////////////////////////////////////////////////////////////////////*/
    .custom-button{
      color: linear-gradient(to bottom left, #553c9a, #ee4b2b);
    }

    @keyframes animation_bg_image {
      0% {
        background-position:  <?php echo $jumptronBgXoffst . "% " . $jumptronBgYAnimate."%"; ?>;
      }
      100% {
        background-position: <?php echo $jumptronBgXoffst . "% " . $jumptronBgYoffst."%"; ?>;
      }
    }
    /*///////////////////////////////////////////////////////////////////////////////////////////*/
    :root {
  --box-size: 100px;
}
    .bb, .bb::before, .bb::after {
	 position: absolute;
	 top: 0;
	 bottom: 0;
	 left: 0;
	 right: 0;
}
 .bb {

	 width: var(--box-size);
	 height: var(--box-size);
	 padding: 10px;
	 margin: auto;
	 color: black;
	
}
 .bb::before, .bb::after {
	 content: '';
	 z-index: -1;
	 margin: -5%;
	 box-shadow: inset 0 0 0 2px;
	 animation: clipMe 8s linear infinite;
}
 .bb::before {
	 animation-delay: -4s;
}
 @keyframes clipMe {
	 0%, 100% {
		 clip: rect(0px, calc(var(--box-size) + 5px), 2px, 0px);
	}
	 25% {
		 clip: rect(0px, 2px, calc(var(--box-size) + 5px), 0px);
	}
	 50% {
		 clip: rect(calc(var(--box-size) + 3px), calc(var(--box-size) + 5px), calc(var(--box-size) + 5px), 0px);
	}
	 75% {
		 clip: rect(0px, calc(var(--box-size) + 5px), calc(var(--box-size) + 5px), calc(var(--box-size) + 3px));
	}
}
 .bb, .bb::before, .bb::after {
	 box-sizing: border-box;
}
 



</style>

<div class="jumbotron jumbotron-fluid text-center  text-white p-4 jumbotron_content" >
  <div class="jumbotron_background bg-cover"> </div>
  <div class="container-fluid">

        <h1 class="display-1  custom-headline text-white"><?php echo $jumptronHeadline; ?></h1>
      
        <a class="btn btn-outline-secondary btn-lg rounded-3 custom-button text-white border border-5 custom-paragraph"  href="<?php echo $jumptronButtonLink; ?>" role="button" aria-pressed="true">
            <?php echo $jumptronButtonText; ?>
        </a>
  </div>
<!-- /.container   -->
</div>
<!--

  <div class="p-5 m -5" style="position: relative;">
    <button class="bb">
      Zeltlager 2022
    </button>
  </div>
-->



<?php
   }