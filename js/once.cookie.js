/**
 * Version: 1.0, 30.06.2015
 * by Adam Wysocki, support@oncebuilder.com
 *
 * Copyright (c) 2015 Adam Wysocki
 *
 *	This is OnceBuilder Cookie plugin (once.cookie)
 *
*/

$(document).ready(function () {
	// Load code mirror library & modes
	once.loadJSfile(once.path+'/libs/jquery-form/jquery.form.js');
	
	if($("#cookiePlugin").length>0){
		// Initialize mainInit
		once.cookie.actions.mainInit();
	}
});

once.cookie = {
	loaded: false,
	initialized: function(){
		this.loaded=true;
	},
	getCookie: function(cname) {
		var name = cname + "=";
		var decodedCookie = decodeURIComponent(document.cookie);
		var ca = decodedCookie.split(';');
		for(var i = 0; i <ca.length; i++) {
			var c = ca[i];
			while (c.charAt(0) == ' ') {
				c = c.substring(1);
			}
			if (c.indexOf(name) == 0) {
				return c.substring(name.length, c.length);
			}
		}
		return "";
	},
	setCookie: function(cname, cvalue, exdays) {
		var d = new Date();
		d.setTime(d.getTime() + (exdays*24*60*60*1000));
		var expires = "expires="+ d.toUTCString();
		document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
	},
}

once.cookie.actions = {
	mainInit: function(obj){//ok
		if(once.cookie.getCookie(cc_cookie_name)!=cc_coookie_value){
			var str='';
			str='<div class="cc_wrapper">'
				str+='<div class="cc_banner cc_container cc_container--open">';
					str+='<a href="#" class="cc_btn cc_btn_accept_all">'+cc_coookie_button+'</a>';
					str+='<p class="cc_message">'+cc_cookie_message+'</p>';
				str+='</div>';
			str+='</div>';
			// Append at end of the body
			$("body").append(str);

			// Toggle password
			$(".cc_banner-wrapper .cc_btn").click(function(){
				once.cookie.setCookie(cc_cookie_name,cc_coookie_value,365);
				$(".cc_banner-wrapper").hide();
			});
		}
	},
}