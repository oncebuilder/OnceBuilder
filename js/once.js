/**
 * --------------------------------------------------------------------
 * Version: 1.0, 31.05.2012
 * by Adam Wysocki, goldpaid777@gmail.com
 *
 *
 * Copyright (c) 2012 Adam Wysocki
 * --------------------------------------------------------------------
*/
window.console = console;

var once={
	api: false,
	cms: false,
	admin: false,
	creator: false,
	CSSfiles: [],
	JSfiles: [],
	editors: [],
	path: '/once',
	animation: false,
	note: [],
	// Microtime counter
	microtime: function(get_as_float){
		var now = new Date().getTime() / 1000;
		var sec = parseInt(now, 10);
		return (get_as_float) ? now : (Math.round((now - sec) * 1000) / 1000) + ' ' + sec;
	},
	// Initialize once requests
	init: function(){
		once.microtimer = once.microtime(true);

		// Load lib of prototypes
		once.loadJSfile(once.path+'/js/once.prototype.js');
		
		// Load all required JS files
		$("[data-require!='']").each(function() {
			if($(this).data("require")!==undefined){
				once.loadJSfile($(this).data("require"));
			}
		});
		
		// Append $_GET['csrf_token'] for all post request
		var csrf_token = $('meta[name="csrf_token"]').attr('content');
		$.ajaxPrefilter(function(options, originalOptions, jqXHR){
			if (options.type.toLowerCase() === "post") {
				options.url += "&csrf_token=" + csrf_token;
			}
		});

		// Prepend loader
		if(!$("#loader").length){
			// Append at end of the body
			$("body").append('<div id="loader"></div>');
		}

		// Prepend notifications
		if(!$("#note").length){
			var str='';
			str+='<div id="note" style="display: none;">';
				str+='test';
			str+='</div>';
			// Append at end of the body
			$("body").append(str);
		}
		
		// Onclick notifications
		$("#note").click(function () {
			if(once.animation){
				once.animation=false;
			}else{
				$(this).hide();
			}
		});
		
		// Loader / notification on ajax request
		$(document).ajaxStart(function() {
			$("#loader").show(); 
		});

		list=['ajax.php','s=ajax','upload_image','delete_image','item_new',];
		$(document).ajaxComplete(function(event, request, settings) {
			$("#loader").hide();
			for(i=0;i<list.length;i++){
				if(settings.url.indexOf(list[i])>0){
					once.log(request.responseText);
				}
			}
		});
	},
	// Log
	log: function(str){
		once.animation=true;
		$("#note").show();
		$("#note").text(str);
		$("#note").removeClass('animated fadeOutUp');
		$("#note").addClass('animated fadeInDown');
						
		window.setTimeout( function(){
			if(once.animation){
				$("#note").removeClass('animated fadeInDown');
				$("#note").addClass('animated fadeOutUp');
			}
		}, 2000); 
	},
	// Load css file by using jquery, passing attributes to pass it in <head>  
	loadCSSfile: function(src){
		if($.inArray(src,once.CSSfiles)==-1){
			$('link[href="' + src + '"]').remove();
			var style = $('<link>');
			style.attr('href', src+"?"+Math.random());
			style.attr('type', 'text/css');
			style.attr('media', 'screen');
			style.attr('rel', 'stylesheet');
			style.appendTo('head');
			once.CSSfiles.push(src);
		}
	},
	// Load javascript file in <head> by using jquery
	loadJSfile: function(src){
		if($.inArray(src,once.JSfiles)==-1){
			$('script[src="' + src + '"]').remove();
			$('<script>').attr('src', src+"?"+Math.random()).appendTo('head');
			once.JSfiles.push(src);
		}
		
	},
	// Use data 
	usedata: function(type,data){
		eval("this."+type+" = data;");
		eval("console.log(this."+type+");");
	},
}

// Start
$(document).ready(function () {
	once.init();
});