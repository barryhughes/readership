$j = jQuery.noConflict();

$j(document).ready(function($) {
	/**
	 * Draw attention to update messages and alerts,
	 * then hide from view.
	 */
	$("div.updatemsg").delay(200).animate(
		{ "padding-left": "+=22", },
  		340, 'swing', function() {
  			$(this).animate(
  				{ "padding-left": "-=22" },
  				340, 'swing', function() {
  					$(this).delay(2100).slideUp();
	}); })

	/**
	 * jQuery UI datepicker support.
	 */

	$("input.datefield").datepicker();
});