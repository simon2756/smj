/**
 * Entwurf "Nacht": Kopfzeile wird beim Scrollen über dem Einstieg dunkel hinterlegt.
 */
( function () {
	'use strict';

	var kopf = document.querySelector( '.nacht-kopf' );
	var hero = document.querySelector( '.nacht-hero' );
	if ( ! kopf || ! hero || ! ( 'IntersectionObserver' in window ) ) {
		return;
	}

	new IntersectionObserver(
		function ( eintraege ) {
			kopf.classList.toggle( 'ist-gescrollt', ! eintraege[ 0 ].isIntersecting );
		},
		{ rootMargin: '-80px 0px 0px 0px', threshold: 0 }
	).observe( hero.querySelector( '.nacht-hero__text' ) || hero );
} )();
