/**
 * Version: 1.0, 01.07.2015
 * by Adam Wysocki, support@oncebuilder.com
 *
 * Copyright (c) 2015 Adam Wysocki
 *
 *	This is OnceBuilder Checkout plugin (once.checkout)
 *
*/

$(document).ready(function () {
	// Load code mirror library & modes
	if($("#checkoutPlugin").length>0){
		if($("#checkoutPlugin").data('gateway')=='stripe'){
			once.loadJSfile('https://checkout.stripe.com/checkout.js');
		}
		
		
		
	}
});

once.checkout = {
	loaded: false,
	initialized: function(){
		this.loaded=true;
	},
}