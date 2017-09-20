/**
 * Version: 1.0, 29.07.2015
 * by Adam Wysocki, support@oncebuilder.com
 *
 * Copyright (c) 2015 Adam Wysocki
 *
 *	This is OnceBuilder User plugin (once.user)
 *
*/

once.user = {
	loaded: false,
	initialized: function(){
		this.loaded=true;
	},
	loaded: false,
	initialized: function(){
		this.loaded=true;
	},
	itemFollow: function(obj){
		// Get varibles defined in rendering data-*
		var id=$("#user-data").data('id');
		// Call to del_layer with parm id
		$.ajax({
			type: 'POST',
			url: once.path+"/ajax.php?c=user&o=item_follow&id="+id,
			success: function(data) { 
				// Refresh items list if response ok
				if(data.status=='ok'){
					// Toggle buttons
					$("#user-data .item-follow").toggle();
					$("#user-data .item-unfollow").toggle();
					
					console.log("followed");
				}else{
					if(data.errors[0]=='user not logged'){
						document.location.href="/login";
					}else{
						alert(data.errors[0]);
					}
					console.log("Action Error: "+data.error);
				}
			},
			contentType: "application/json",
			dataType: 'json'
		})
		.error(function() { console.log("Request Error: item_delete"); });
	},
	itemUnfollow: function(obj){
		// Get varibles defined in rendering data-*
		var id=$("#user-data").data('id');
		// Call to del_layer with parm id
		$.ajax({
			type: 'POST',
			url: once.path+"/ajax.php?c=user&o=item_unfollow&id="+id,
			success: function(data) { 
				// Refresh items list if response ok
				if(data.status=='ok'){
					// Toggle buttons
					$("#user-data .item-follow").toggle();
					$("#user-data .item-unfollow").toggle();
					
					console.log("unfollowed");
				}else{
					console.log("Action Error: "+data.error);
				}
			},
			contentType: "application/json",
			dataType: 'json'
		})
		.error(function() { console.log("Request Error: item_delete"); });
	},
	itemUsersFollow: function(obj){
		// Get varibles defined in rendering data-*
		var id=$(obj).data('id');
		// Call to item_follow with parm id
		$.ajax({
			type: 'POST',
			url: once.path+"/ajax.php?c=user&o=item_follow&id="+id,
			success: function(data) { 
				// Refresh items list if response ok
				if(data.status=='ok'){
					// Toggle buttons
					$("#users-data .item-users-follow[data-id="+id+"]").toggle();
					$("#users-data .item-users-unfollow[data-id="+id+"]").toggle();
					
					console.log("followed");
				}else{
					if(data.errors[0]=='user not logged'){
						document.location.href="/login";
					}else{
						alert(data.errors[0]);
					}
					console.log("Action Error: "+data.error);
				}
			},
			contentType: "application/json",
			dataType: 'json'
		})
		.error(function() { console.log("Request Error: item_follow"); });
	},
	itemUsersUnfollow: function(obj){
		// Get varibles defined in rendering data-*
		var id=$(obj).data('id');
		// Call to item_unfollow with parm id
		$.ajax({
			type: 'POST',
			url: once.path+"/ajax.php?c=user&o=item_unfollow&id="+id,
			success: function(data) { 
				// Refresh items list if response ok
				if(data.status=='ok'){
					// Toggle buttons
					$("#users-data .item-users-follow[data-id="+id+"]").toggle();
					$("#users-data .item-users-unfollow[data-id="+id+"]").toggle();
					
					console.log("unfollowed");
				}else{
					console.log("Action Error: "+data.error);
				}
			},
			contentType: "application/json",
			dataType: 'json'
		})
		.error(function() { console.log("Request Error: item_unfollow"); });
	}
}

once.user.actions = {
	userInit: function(obj){
		// Initialize hire item dialog
		once.user.dialogs.itemHire("#user-data .item-hire");
		
		// Initialize hire item dialog
		once.user.dialogs.itemUsersHire("#users-data .item-users-hire");
		
		// Item follow user
		$("#user-data .item-follow").click(function () {
			once.user.itemFollow($(this));
		});
		
		// Item unfollow user
		$("#user-data .item-unfollow").click(function () {
			once.user.itemUnfollow($(this));
		});
		
		// Item follow users
		$("#users-data .item-users-follow").click(function () {
			once.user.itemUsersFollow($(this));
		});
		
		// Item unfollow users
		$("#users-data .item-users-unfollow").click(function () {
			once.user.itemUsersUnfollow($(this));
		});
	},
}

once.user.dialogs = {
	itemHire: function(obj){
		// Append dialog div at end of the body if not exist
		if($("#item-hire").length==0){
			$("body").append("<div id=\"item-hire\"></div>");
		}
		// Read and open hire dialog
		$(obj).click(function () {
			$("#item-hire").load(once.path+"/dialog.php?c=user&o=hire&id="+$("#user-data").data("id"), function() {
				$('#item-hire .modal:first').modal({
					show: 'false'
				}); 
			})
			.error(function() { console.log("Dialog Error: hire"); });
		});
	},
	itemUsersHire: function(obj){
		// Append at end of the body
		$("body").append("<div id=\"item-hire\"></div>");

		// Read and open hire dialog
		$(obj).click(function () {
			$("#item-hire").load(once.path+"/dialog.php?c=user&o=hire&id="+$(this).data("id"), function() {
				$('#item-hire .modal:first').modal({
					show: 'false'
				}); 
			})
			.error(function() { console.log("Dialog Error: hire"); });
		});
	},
}

once.user.forms = {
	hireForm: function(){
		// First validate
		var container = $('.message-error');
			
		// validate the form when it is submitted
		var	validator = $("#hireForm").validate({
			errorContainer: container,
			errorLabelContainer: $("ol", container),
			wrapper: 'li',
			rules: {
				message: {
					required: true,
					minlength: 3
				},
			}
		});
		
		$("#hireForm").attr("action",once.path+"/ajax.php?c=user&o=item_user_hire&id="+$("#hire-data").data("id"));
		var options = {
			dataType:  "json",
			success: function(data){
				// If response ok 
				if(data.status=='ok'){
					//document.location.href=$("#hireForm").data("redirect");
					$("#hireForm .message-error").hide();
					$("#hireForm .message-sent").show();

					$("#hireForm textarea").attr("disabled",true);
					$("#hire-data .item-user-hire").attr("disabled",true);
					
					console.log("user logged!");
				}else{
					if(data.errors[0]=='user not logged'){
						alert('You need to be logged to send any message');
					}else{
						$("#hireForm .message-sent").hide();
						$("#hireForm .message-error").show();
						
						var str='';
						var length=data.errors.length;

						if(length){
							for(var i=0; i<length; i++){
								str+='<li>'+data.errors[i]+'</li>';
							}
						}
						
						$("#hireForm .message-error").find("ol").show();
						$("#hireForm .message-error").find("ol").html(str);
					}
					
					console.log("Action Error: "+data.errors);
				}
			},
			complete: function(){
				//console.log(data.responseText);
			},
			error: function(){
				console.log("Form Error: hireForm");
			}
		};
		$("#hireForm").ajaxForm(options);
	},
}

$(document).ready(function () {
	// Load code mirror library & modes
	once.loadJSfile(once.path+'/js/jquery.form.js');
	//once.loadJSfile('//oss.maxcdn.com/jquery.form/3.50/jquery.form.min.js');
	once.loadJSfile(once.path+'/libs/jquery-validation/dist/jquery.validate.js');
	//once.loadJSfile('http://ajax.aspnetcdn.com/ajax/jquery.validate/1.11.1/jquery.validate.js');

	if($("#user-data").data("id")){
		once.user.actions.userInit();
	}
	
	if($("#user").length>0){
		// Sort actions
		$(".sort-action").click(function () {
			once.user.sortAction($(this));
		});
		
		// Initialize searchForm
		//once.user.forms.searchForm($(this));

		
	}
	
	
		
	// Initialize / sandbox
	once.user.initialized();
});