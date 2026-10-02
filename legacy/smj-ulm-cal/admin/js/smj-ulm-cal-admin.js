(function( $ ) {
	'use strict';

	/**
	 * All of the code for your admin-facing JavaScript source
	 * should reside in this file.
	 *
	 * Note: It has been assumed you will write jQuery code here, so the
	 * $ function reference has been prepared for usage within the scope
	 * of this function.
	 *
	 * This enables you to define handlers, for when the DOM is ready:
	 *
	 * $(function() {
	 *
	 * });
	 *
	 * When the window is loaded:
	 *
	 * $( window ).load(function() {
	 *
	 * });
	 *
	 * ...and/or other possibilities.
	 *
	 * Ideally, it is not considered best practise to attach more than a
	 * single DOM-ready or window-load handler for a particular page.
	 * Although scripts in the WordPress core, Plugins and Themes may be
	 * practising this, we should strive to set a better example in our own work.
	 */  

	// A $( document ).ready() block.
	$( document ).on('ready', function() {

		let oldValue = $('#smj_ulm_cal__num_output_calendars').val();


		$('#smj_ulm_cal__num_output_calendars').on('change', function() {


			// Your code here
			let newValue = $(this).val();
		
			if(oldValue < newValue && newValue > 0){
				//add calendars
				let diff = newValue - oldValue;
				for(let i =0; i < diff; i++){
					let str =  "<div class='sync-calendar'>";
			
						str +=  "<div>";

							str +=  "<label> Kalendername: </label>";
							str +=  "<input id='smj_ulm_cal_options[calendar_name][]'	name='smj_ulm_cal_options[calendar_name][]' type='text' value='' />";

							str +=  "<label> Kategorien: </label>";
							str +=  "<input id='smj_ulm_cal_options[categories][]'   name='smj_ulm_cal_options[categories][]' type='text' value='' />";
								
						str +=  "</div>";
			
					str +=  "</div>";

					$("#calendar-sync-list").append(str);
				}
			}

			else if(oldValue > newValue){
				//remove calendars
				let diff = oldValue - newValue;
				for(let i =0; i < diff; i++){
					$("#calendar-sync-list  > div:last-child").remove();
				}
			}

			oldValue = newValue;
		});
		
	});



	
})( jQuery );
