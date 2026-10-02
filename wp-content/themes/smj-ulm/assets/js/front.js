/**
 * Diashow und Countdown auf der Website.
 */
( function () {
	'use strict';

	var reduceMotion = window.matchMedia && window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches;

	function initDiashow( root ) {
		var slides = root.querySelectorAll( '.smj-diashow__slide' );
		var dots = root.querySelectorAll( '.smj-diashow__punkt' );
		var pause = root.querySelector( '.smj-diashow__pause' );
		if ( slides.length < 2 ) {
			return;
		}
		var ms = Math.max( 2, parseInt( root.getAttribute( 'data-sekunden' ), 10 ) || 5 ) * 1000;
		var current = 0;
		var timer = null;
		var playing = ! reduceMotion;

		function show( index ) {
			current = ( index + slides.length ) % slides.length;
			for ( var i = 0; i < slides.length; i++ ) {
				var active = i === current;
				slides[ i ].classList.toggle( 'is-aktiv', active );
				if ( active ) {
					slides[ i ].removeAttribute( 'aria-hidden' );
				} else {
					slides[ i ].setAttribute( 'aria-hidden', 'true' );
				}
				if ( dots[ i ] ) {
					dots[ i ].classList.toggle( 'is-aktiv', active );
					if ( active ) {
						dots[ i ].setAttribute( 'aria-current', 'true' );
					} else {
						dots[ i ].removeAttribute( 'aria-current' );
					}
				}
			}
		}

		function start() {
			stop();
			if ( playing ) {
				timer = window.setInterval( function () {
					show( current + 1 );
				}, ms );
			}
		}

		function stop() {
			if ( timer ) {
				window.clearInterval( timer );
				timer = null;
			}
		}

		function updatePause() {
			if ( ! pause ) {
				return;
			}
			pause.classList.toggle( 'is-pausiert', ! playing );
			pause.setAttribute( 'aria-label', pause.getAttribute( playing ? 'data-label-pause' : 'data-label-play' ) );
		}

		dots.forEach( function ( dot ) {
			dot.addEventListener( 'click', function () {
				show( parseInt( dot.getAttribute( 'data-index' ), 10 ) );
				start();
			} );
		} );

		if ( pause ) {
			pause.addEventListener( 'click', function () {
				playing = ! playing;
				updatePause();
				start();
			} );
		}

		// Beim Lesen mit Tastatur oder Maus nicht weiterblättern.
		root.addEventListener( 'mouseenter', stop );
		root.addEventListener( 'mouseleave', start );
		root.addEventListener( 'focusin', stop );
		root.addEventListener( 'focusout', start );

		updatePause();
		start();
	}

	function initCountdown( root ) {
		var ziel = parseInt( root.getAttribute( 'data-ziel' ), 10 );
		if ( ! ziel ) {
			return;
		}
		var felder = {};
		root.querySelectorAll( '[data-teil]' ).forEach( function ( el ) {
			felder[ el.getAttribute( 'data-teil' ) ] = el;
		} );
		var pad = function ( n ) {
			return ( n < 10 ? '0' : '' ) + n;
		};

		function tick() {
			var rest = Math.max( 0, Math.floor( ( ziel - Date.now() ) / 1000 ) );
			if ( felder.tage ) {
				felder.tage.textContent = String( Math.floor( rest / 86400 ) );
			}
			if ( felder.stunden ) {
				felder.stunden.textContent = pad( Math.floor( ( rest % 86400 ) / 3600 ) );
			}
			if ( felder.minuten ) {
				felder.minuten.textContent = pad( Math.floor( ( rest % 3600 ) / 60 ) );
			}
			if ( felder.sekunden ) {
				felder.sekunden.textContent = pad( rest % 60 );
			}
			if ( rest === 0 ) {
				window.clearInterval( timer );
			}
		}

		var timer = window.setInterval( tick, 1000 );
		tick();
	}

	function init() {
		document.querySelectorAll( '.smj-diashow' ).forEach( initDiashow );
		document.querySelectorAll( '.smj-countdown' ).forEach( initCountdown );
	}

	if ( document.readyState === 'loading' ) {
		document.addEventListener( 'DOMContentLoaded', init );
	} else {
		init();
	}
} )();
