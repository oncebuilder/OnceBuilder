/**
 * Version: 1.0, 30.06.2015
 * by Adam Wysocki, support@oncebuilder.com
 *
 * Copyright (c) 2015 Adam Wysocki
 *
 *	This is OnceBuilder Login plugin (once.login)
 *
*/
$(document).ready(function () {
	// Load code mirror library & modes
	once.loadJSfile('/once/libs/jquery-form/jquery.form.js');
	//once.loadJSfile('//oss.maxcdn.com/jquery.form/3.50/jquery.form.min.js');
	once.loadJSfile('/once/libs/jquery-validation/dist/jquery.validate.js');
	//once.loadJSfile('http://ajax.aspnetcdn.com/ajax/jquery.validate/1.11.1/jquery.validate.js');
	
	if($("#loginPlugin").length>0){
		// First validate
		var container = $('.message-error');
		
		// validate the form when it is submitted
		var	validator = $("#loginForm").validate({
			errorContainer: container,
			errorLabelContainer: $("ol", container),
			wrapper: 'li',
			rules: {
				email: {
					required: true,
					minlength: 3
				},
				password: {
					required: true,
					minlength: 3
				}
			}
		});

		// Toggle password
		$("#password-toggle").click(function(){
			var input=$("#loginPlugin input[name=\"password\"]");
			
			if(input.prop("type")=='text'){
				input.prop("type","password");
			}else{
				input.prop("type","text");
			}
			$(this).find("i").toggleClass("glyphicon-eye-open");
			$(this).find("i").toggleClass("glyphicon-eye-close");
		});
		
		// Toggle form to remind
		$(".item-forgot").click(function(){
			$("#loginForm").hide();
			$("#remindForm").show();
			$("#changeForm").hide();
		});
		
		// Toggle form to remind
		$(".item-back").click(function(){
			$("#loginForm").show();
			$("#remindForm").hide();
			$("#changeForm").hide();
		});

		// Initialize loginForm / remindForm / changeForm
		//once.login.forms.loginForm($(this));
		//once.login.forms.remindForm($(this));
		//once.login.forms.changeForm($(this));
	}
});

once.login = {
	loaded: false,
	initialized: function(){
		this.loaded=true;
	},
}

once.login.forms = {
	loginForm: function(obj){
		$("#loginForm").attr("action",once.path+"/ajax.php?c=login&o=item_login");
		var options = {
			dataType:  "json",
			success: function(data){
				// If response ok refresh logo
				if(data.status=='ok'){
					//document.location.href=$("#loginPlugin").data("redirect");
					
					console.log("user logged!");
				}else{
					$("#loginForm .message-sent").hide();
					$("#loginForm .message-error").show();
					
					var str='';
					var length=data.errors.length;
					if(length>0){
						for(var i=0; i<length; i++){
							str+='<li>'+data.errors[i]+'</li>';
						}
					}
					
					$("#loginForm .message-error").find("ol").show();
					$("#loginForm .message-error").find("ol").html(str);

					console.log("Action Error: "+data.error);
				}
			},
			complete: function(data){
				//console.log(data.responseText);
			},
			error: function(){
				console.log("Form Error: importForm");
			}
		};
		$("#loginForm").ajaxForm(options);
	},
	remindForm: function(obj){
		$("#remindForm").attr("action",once.path+"/ajax.php?c=login&o=item_remind");
		var options = {
			dataType:  "json",
			success: function(data){
				// If response ok refresh logo
				if(data.status=='ok'){
					//document.location.href=$("#loginPlugin").data("redirect");
					
					console.log("message sent!");
				}else{
					$(".message-sent").hide();
					$(".message-error").show();
					
					console.log("Action Error: "+data.error);
				}
			},
			complete: function(data){
				//console.log(data.responseText);
			},
			error: function(){
				console.log("Form Error: importForm");
			}
		};
		$("#remindForm").ajaxForm(options);
	},
	changeForm: function(obj){
		$("#changeForm").attr("action",once.path+"/ajax.php?c=login&o=item_change");
		var options = {
			dataType:  "json",
			success: function(data){
				// If response ok refresh logo
				if(data.status=='ok'){
					//document.location.href=$("#changeForm").data("redirect");
					
					console.log("message sent!");
				}else{
					$(".message-sent").hide();
					$(".message-error").show();
					
					console.log("Action Error: "+data.error);
				}
			},
			complete: function(data){
				//console.log(data.responseText);
			},
			error: function(){
				console.log("Form Error: changeForm");
			}
		};
		$("#changeForm").ajaxForm(options);
	},
}