/**
 * Editor-Oberfläche der Blöcke "Offene Anmeldungen" und "Termine".
 * Die Vorschau kommt vom Server (ServerSideRender), daher ist kein Build-Schritt nötig.
 */
( function ( wp ) {
	var el = wp.element.createElement;
	var InspectorControls = wp.blockEditor.InspectorControls;
	var useBlockProps = wp.blockEditor.useBlockProps;
	var PanelBody = wp.components.PanelBody;
	var TextControl = wp.components.TextControl;
	var RangeControl = wp.components.RangeControl;
	var ServerSideRender = wp.serverSideRender;

	wp.blocks.registerBlockType( 'smj/aktionen', {
		apiVersion: 3,
		title: 'Offene Anmeldungen',
		description: 'Zeigt alle Anmeldungen, die gerade offen sind. Geschlossene verschwinden automatisch.',
		icon: 'clipboard',
		category: 'widgets',
		supports: { align: [ 'wide', 'full' ] },
		edit: function ( props ) {
			return el(
				'div',
				useBlockProps(),
				el(
					InspectorControls,
					null,
					el(
						PanelBody,
						{ title: 'Einstellungen' },
						el( TextControl, {
							label: 'Überschrift',
							value: props.attributes.ueberschrift,
							onChange: function ( v ) {
								props.setAttributes( { ueberschrift: v } );
							},
						} )
					)
				),
				el( ServerSideRender, { block: 'smj/aktionen', attributes: props.attributes } )
			);
		},
		save: function () {
			return null;
		},
	} );

	wp.blocks.registerBlockType( 'smj/termine', {
		apiVersion: 3,
		title: 'Termine',
		description: 'Die nächsten Termine aus dem SMJ-Kalender.',
		icon: 'calendar-alt',
		category: 'widgets',
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
				useBlockProps(),
				el(
					InspectorControls,
					null,
					el(
						PanelBody,
						{ title: 'Termine' },
						el( RangeControl, { label: 'Anzahl', min: 1, max: 30, value: a.anzahl, onChange: set( 'anzahl' ) } ),
						el( RangeControl, { label: 'Zeitraum in Monaten', min: 1, max: 24, value: a.monate, onChange: set( 'monate' ) } ),
						el( TextControl, {
							label: 'Kategorien (optional)',
							help: 'Kommagetrennt, z. B. „Teilnehmer, Zeltlager“. „SMJ&Teilnehmer“ heißt: beide Kategorien.',
							value: a.kategorien,
							onChange: set( 'kategorien' ),
						} ),
						el( TextControl, { label: 'Link „Alle Termine“', type: 'url', value: a.alleLink, onChange: set( 'alleLink' ) } ),
						el( TextControl, { label: 'Link „Kalender abonnieren“', type: 'url', value: a.aboLink, onChange: set( 'aboLink' ) } )
					)
				),
				el( ServerSideRender, { block: 'smj/termine', attributes: a } )
			);
		},
		save: function () {
			return null;
		},
	} );
} )( window.wp );
