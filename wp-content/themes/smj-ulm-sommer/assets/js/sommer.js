/**
 * Entwurf "Sommer": macht aus der Terminliste im gelben Band ein endloses Laufband.
 * Die Liste wird dafür einmal verdoppelt; die Kopie ist für Screenreader ausgeblendet.
 */
( function () {
	'use strict';

	var reduce = window.matchMedia && window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches;

	document.querySelectorAll( '.sommer-kachel--ticker .smj-termine' ).forEach( function ( liste ) {
		if ( reduce || liste.children.length < 2 ) {
			return;
		}
		Array.prototype.slice.call( liste.children ).forEach( function ( eintrag ) {
			var kopie = eintrag.cloneNode( true );
			kopie.setAttribute( 'aria-hidden', 'true' );
			liste.appendChild( kopie );
		} );
		liste.classList.add( 'ist-laufband' );
	} );
} )();
