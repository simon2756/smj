/**
 * Editor-Oberfläche der Theme-Blöcke Diashow, Countdown und Brotkrümel.
 * Die Vorschau kommt vom Server (ServerSideRender), daher ist kein Build-Schritt nötig.
 */
( function ( wp ) {
	var el = wp.element.createElement;
	var Fragment = wp.element.Fragment;
	var be = wp.blockEditor;
	var c = wp.components;
	var ServerSideRender = wp.serverSideRender;

	wp.blocks.registerBlockType( 'smj/diashow', {
		apiVersion: 3,
		title: 'Diashow',
		description: 'Ausgewählte Bilder, die automatisch durchwechseln.',
		icon: 'images-alt2',
		category: 'media',
		edit: function ( props ) {
			var a = props.attributes;
			var bilder = a.bilder || [];
			var auswahl = el( be.MediaUpload, {
				allowedTypes: [ 'image' ],
				multiple: true,
				gallery: true,
				value: bilder.map( function ( b ) {
					return b.id;
				} ),
				onSelect: function ( media ) {
					props.setAttributes( {
						bilder: media.map( function ( m ) {
							return { id: m.id };
						} ),
					} );
				},
				render: function ( o ) {
					return el( c.Button, { variant: 'primary', onClick: o.open }, bilder.length ? 'Bilder ändern (' + bilder.length + ')' : 'Bilder auswählen' );
				},
			} );
			return el(
				'div',
				be.useBlockProps(),
				el(
					be.InspectorControls,
					null,
					el(
						c.PanelBody,
						{ title: 'Diashow' },
						el( be.MediaUploadCheck, null, auswahl ),
						el( c.RangeControl, {
							label: 'Sekunden pro Bild',
							min: 2,
							max: 15,
							value: a.sekunden,
							onChange: function ( v ) {
								props.setAttributes( { sekunden: v } );
							},
						} )
					)
				),
				el( ServerSideRender, { block: 'smj/diashow', attributes: a } ),
				el( 'div', { style: { marginTop: '8px' } }, el( be.MediaUploadCheck, null, auswahl ) )
			);
		},
		save: function () {
			return null;
		},
	} );

	wp.blocks.registerBlockType( 'smj/countdown', {
		apiVersion: 3,
		title: 'Countdown',
		description: 'Zählt bis zu einem Zeitpunkt herunter, z. B. bis zum Zeltlager.',
		icon: 'clock',
		category: 'widgets',
		supports: { align: [ 'wide', 'full' ] },
		edit: function ( props ) {
			var a = props.attributes;
			var set = function ( key ) {
				return function ( v ) {
					var o = {};
					o[ key ] = v;
					props.setAttributes( o );
				};
			};
			return el(
				'div',
				be.useBlockProps(),
				el(
					be.InspectorControls,
					null,
					el(
						c.PanelBody,
						{ title: 'Countdown' },
						el( c.TextControl, { label: 'Datum und Uhrzeit', type: 'datetime-local', value: a.ziel, onChange: set( 'ziel' ) } ),
						el( c.TextControl, { label: 'Oberzeile', value: a.oberzeile, onChange: set( 'oberzeile' ) } ),
						el( c.TextControl, { label: 'Überschrift', value: a.titel, onChange: set( 'titel' ) } ),
						el( c.TextControl, { label: 'Button-Text (optional)', value: a.buttonText, onChange: set( 'buttonText' ) } ),
						el( c.TextControl, { label: 'Button-Link', type: 'url', value: a.buttonLink, onChange: set( 'buttonLink' ) } )
					)
				),
				el( ServerSideRender, { block: 'smj/countdown', attributes: a } )
			);
		},
		save: function () {
			return null;
		},
	} );

	wp.blocks.registerBlockType( 'smj/brotkruemel', {
		apiVersion: 3,
		title: 'Brotkrümel-Navigation',
		description: 'Zeigt, wo man sich auf der Website befindet: Startseite / Über uns / Gruppen.',
		icon: 'arrow-right-alt2',
		category: 'theme',
		edit: function () {
			return el(
				Fragment,
				null,
				el( 'nav', Object.assign( be.useBlockProps( { className: 'smj-brotkruemel' } ), { 'aria-label': 'Brotkrümel' } ), el( 'ol', null, el( 'li', null, el( 'a', { href: '#' }, 'Startseite' ) ), el( 'li', null, el( 'span', null, 'Aktuelle Seite' ) ) ) )
			);
		},
		save: function () {
			return null;
		},
	} );
} )( window.wp );
