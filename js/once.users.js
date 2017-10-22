/**
 * Version: 1.0, 29.07.2015
 * by Adam Wysocki, support@oncebuilder.com
 *
 * Copyright (c) 2015 Adam Wysocki
 *
 *	This is OnceBuilder Users plugin (once.users)
 *
*/

once.users = {
	loaded: false,
	initialized: function(){
		this.loaded=true;
	},
	
	// List function
	bulkAction: function(obj){//ok
		// Set action value
		$("#checkForm input[name='action']").val(obj.data("action"));
		// Set action o
		$("#checkForm").attr("action",once.path+"/ajax.php?c=users&o=bulk_action");
		// Submit form
		$("#checkForm").submit();
	},
	displayLimit: function(obj){//ok
		// Call for set limit
		$.getJSON(once.path+"/ajax.php?c=users&o=set_limit&limit="+$(obj).val(), function(data) {
			$(".pagination a:first").click();
		})
		.error(function() { console.log("Request Error: set_limit"); });
	},
	openPage: function(obj){//ok
		// Open selected page with params
		$.get(once.path+"/view.php?c=users&o="+$("#users-data").data("o")+"&type_id="+$("#users-data").data("type_id")+"&category_id="+$("#users-data").data("category_id")+"&sort_by="+$("#users-data").data("sort_by")+"&page="+obj.html()+"&ids="+$("#users-data").data("ids")+"&query="+$("#users-data").data("query"), function(data) {
			$("#content-body").html(data);
		})
		.error(function() { console.log("Request Error: "+$("#users-data").data("o")); });
	},
	sortAction: function(obj){//ok
		if($("#users-data").data("ajax")){
			$("#sort-label").html(obj.html());
			// Refresh items list by sort key
			$.get(once.path+"/view.php?c=users&o="+$("#users-data").data("o")+"&type_id="+$("#users-data").data("type_id")+"&category_id="+$("#users-data").data("category_id")+"&sort_by="+obj.data("sort")+"&page="+$("#users-data").data("page")+"&ids="+$("#users-data").data("ids")+"&query="+$("#users-data").data("query"), function(data) {
				$("#content-body").html(data);
			})
			.error(function() { alert("Couldn\'t load sort"); });
		}else{
			// Refresh items list by sort key
			$("#sort-label").html(obj.html());
			document.location.href='/users?type_name='+$("#users-data").data('type')+'&sort_by='+obj.data("sort")+'&p='+$("#users-data").data("page")+'&query='+$("#users-data").data('query');
		}
	},
	
	// View function
	itemDelete: function(obj){//ok
		// Get varibles defined in rendering data-*
		var id=$(obj).parent().parent().data('id');
		var login=$("#item_"+id+" td.item-login").html();
		// We need to confirm to delete
		var r = confirm("Delete "+login+"?");
		if(r){
			// Call to item_delete with parm id
			$.ajax({
				type: 'POST',
				url: once.path+"/ajax.php?c=users&o=item_delete&id="+id,
				success: function(data) { 
					// Refresh items list if response ok
					if(data.status=='ok'){
						// Intermediatly delete from dom then refresh items list
						$("#item_"+id).remove();
						// Call for refresh
						$.get(once.path+"/view.php?c=users&o="+$("#users-data").data("o")+"&type_id="+$("#users-data").data("type_id")+"&category_id="+$("#users-data").data("category_id")+"&sort_by="+$("#users-data").data("sort_by")+"&page="+$("#users-data").data("page")+"&ids="+$("#users-data").data("ids")+"&query="+$("#users-data").data("query"), function(data) {
							$("#content-body").html(data);
						})
						.error(function() { console.log("Request Error: "+$("#users-data").data("o")); });
					}else{
						console.log("Action Error: "+data.error);
					}
				},
				contentType: "application/json",
				dataType: 'json'
			})
			.error(function() { console.log("Request Error: item_delete"); });
		}
	},
	itemEditDelete: function(obj){//ok
		// Get varibles defined in rendering data-*
		var id=$("#user-data").data('id');
		var login=$("#editForm input[name='login']").val();
		// We need to confirm to delete
		var r = confirm("Delete "+login+"?");
		if(r){
			// Call to del_layer with parm id
			$.ajax({
				type: 'POST',
				url: once.path+"/ajax.php?c=users&o=item_delete&id="+id,
				success: function(data) { 
					// Refresh items list if response ok
					if(data.status=='ok'){
						$("#user-data .item-close").click();
						// Intermediatly delete from dom then refresh items list
						$("#item_"+id).remove();
						// Call for refresh
						$.get(once.path+"/view.php?c=users&o="+$("#users-data").data("o")+"&type_id="+$("#users-data").data("type_id")+"&category_id="+$("#users-data").data("category_id")+"&sort_by="+$("#users-data").data("sort_by")+"&page="+$("#users-data").data("page")+"&ids="+$("#users-data").data("ids")+"&query="+$("#users-data").data("query"), function(data) {
							$("#content-body").html(data);
						})
						.error(function() { console.log("Request Error: "+$("#users-data").data("o")); });
					}else{
						console.log("Action Error: "+data.error);
					}
				},
				contentType: "application/json",
				dataType: 'json'
			})
			.error(function() { console.log("Request Error: item_delete"); });
		}
	},
	itemEditSave: function(obj){//ok
		// Save selected source or just item edit
		if($("#user-data").data("tab")!==undefined){
			$($("#user-data").data("tab")+" form").submit();
		}else{
			$("#editForm").submit();
		}
	},
	itemNew: function(obj){//ok
		// Call to item_new for new item
		$.ajax({
			type: 'POST',
			url: once.path+"/ajax.php?c=users&o=item_new&type_id="+$("#users-data").data('type_id')+"&category_id="+$("#users-data").data('category_id'),
			success: function(data) { 
				// Refresh items list if response ok
				if(data.status=='ok'){
					// Call for refresh
					$.get(once.path+"/view.php?c=users&o="+$("#users-data").data("o")+"&type_id="+$("#users-data").data("type_id")+"&category_id="+$("#users-data").data("category_id"), function(data) {
						$("#content-body").html(data);
					})
					.error(function() { alert("couldnt load selected page"); });
					
					// Open edit dialog
					setTimeout(function(){
						$("#item_"+data.item.id+" .item-edit").click();
					},500);
				}else{
					console.log("Action Error: "+data.error);
				}
			},
			contentType: "application/json",
			dataType: 'json'
		})
		.error(function() { console.log("Request Error: item_new"); });
	},
	itemStar: function(obj){//ok
		var col=obj.parent();
		var row=col.parent();
		// Call for star item
		$.ajax({
			type: 'POST',
			url: once.path+"/ajax.php?c=users&o=item_star&id="+row.data("id"),
			success: function(data) { 
				// Refresh items list if response ok
				if(data.status=='ok'){
					// Check for fonts icos
					var glyph = $(obj).hasClass("glyphicon");
					var fa = $(obj).hasClass("fa");
					// Switch states
					if(glyph){
						$(obj).toggleClass("glyphicon-star");
						$(obj).toggleClass("glyphicon-star-empty");
					}else if(fa){
						$(obj).toggleClass("fa-star");
						$(obj).toggleClass("fa-star-o");
					}
				}else{
					console.log("Action Error: "+data.error);
				}
			},
			contentType: "application/json",
			dataType: 'json'
		})
		.error(function() { console.log("Request Error: item_star"); });
	},
}

once.users.actions = {
	mainInit: function(obj){//ok
		once.loadJSfile(once.path+'/libs/jquery-form/jquery.form.js');
		
		// Categories
		$("#categories-remote .list-group-item").click(function (e) {
			e.preventDefault();
			e.stopPropagation();
			once.categories.categoryLoad($(this));
		});
		
		// iCheck for checkbox and radio inputs
		if($('input[type="checkbox"]').length){
			$('input[type="checkbox"]').iCheck({
				checkboxClass: 'icheckbox_minimal-blue',
				radioClass: 'iradio_minimal-blue'
			});
			
			// When unchecking the checkbox
			$("#check-all").on('ifUnchecked', function(event) {
				//Uncheck all checkboxes
				$("input[type='checkbox']", ".table-mailbox").iCheck("uncheck");
			});
			
			//When checking the checkbox
			$("#check-all").on('ifChecked', function(event) {
				//Check all checkboxes
				$("input[type='checkbox']", ".table-mailbox").iCheck("check");
			});
			
			// Bulk actions
			$(".bulk-action").click(function () {
				once.users.bulkAction($(this));
			});
		}
		
		// Sort actions
		$(".sort-action").click(function () {
			once.users.sortAction($(this));
		});
		
		// Star item
		$(".item-star").click(function () {
			once.users.itemStar($(this));
		});

		// Initialize itemEdit dialog
		once.users.dialogs.itemEdit(".item-edit");
		
		// Delete item
		$(".item-delete").click(function () {
			once.users.itemDelete($(this));
		});
		
		// Set display limit
		$(".display-limit").change(function () {
			once.users.displayLimit($(this));
		});
		
		// Get selected page
		$(".pagination a").click(function (e) {
			e.preventDefault();
			e.stopPropagation();
			once.users.openPage($(this));
		});
		
		// Initialize checkForm
		once.users.forms.checkForm($(this));
				
		// Initialize searchForm
		once.users.forms.searchForm($(this));

		// Initialize / sandbox
		once.users.initialized();
	},
	editInit: function(obj){//ok
		// After show selected tab
		$('#user-data a[data-toggle="tab"]').on('show.bs.tab', function (e) {
			var tab = $(this); // current tab
			var previous = e.relatedTarget; // previous active tab
			var previous_id = $(e.relatedTarget).data('editor');
			
			$("#user-data").data("tab",tab.attr('href'));
		});
		
		// Thumbnail hover on logo
		$("#user-data .thumbnail").hover(
			function(){
				$(this).find(".caption").slideDown(250); //.fadeIn(250)
			},
			function(){
				$(this).find(".caption").slideUp(250,function(){$ (this).stop( true, true )}); //.fadeOut(205)
			}
		);

		// Delete item
		$("#user-data .item-delete").click(function () {
			once.users.itemEditDelete($(this));
		});
		
		// Save item
		$("#user-data .item-save").click(function () {
			once.users.itemEditSave($(this));
		});
		
		// Change thumbnail
		$("#user-data .item-thumbnail").click(function () {
			$("#uploadThumbnail input[type='file']").click();
		});
		
		// Initialize editForm
		once.users.forms.editForm($(this));
		
		// Initialize informationForm
		once.users.forms.informationForm($(this));
		
		// Initialize socialForm
		once.users.forms.socialForm($(this));
		
		// Initialize uploadThumbnail
		once.users.forms.uploadThumbnail($(this));
	},
}

once.users.dialogs = {
	itemEdit: function(obj){//ok
		// Append at end of the body
		$("body").append("<div id=\"item-edit\"></div>");

		// Read and open edit dialog
		$(obj).click(function (e) {
			e.preventDefault();
			e.stopPropagation();
			$("#item-edit").load(once.path+"/dialog.php?c=users&o=edit&id="+$(this).parent().parent().data("id"), function() {
				$('#item-edit .modal:first').modal({
					show: 'false'
				}); 
			})
			.error(function() { console.log("Dialog Error: edit"); });
		});
	},
}

once.users.forms = {
	checkForm: function(obj){//ok
		var options = {
			dataType:  "json",
			success: function(data){
			
				console.log($("#checkForm").data("type"));
				console.log($("#checkForm").data("module"));
				
				// Call for refresh
				$.get(once.path+"/view.php?c=users&o="+$("#users-data").data("o")+"&type_id="+$("#users-data").data("type_id")+"&category_id="+$("#users-data").data("category_id")+"&sort_by="+$("#users-data").data("sort_by")+"&page="+$("#users-data").data("page")+"&ids="+$("#users-data").data("ids")+"&query="+$("#users-data").data("query"), function(data) {
					$("#content-body").html(data);
				})
				.error(function() { console.log("Request Error: "+$("#users-data").data("o")); });
			},
			complete: function(){
				//console.log(data.responseText);
			},
			error: function(){
				console.log("Form Error: checkForm");
			}
		};
		$("#checkForm").ajaxForm(options);
	},
	editForm: function(obj){//ok
		$("#editForm").attr("action",once.path+"/ajax.php?c=users&o=item_edit&id="+$("#user-data").data("id"));
		var options = {
			dataType:  "json",
			success: function(data){
				// Update login & username & email on items list
				if(data.status=='ok'){
					// Check for redirects then save
					if($("#user-data").data('redirect')==undefined){
						// Get new data
						var login=$("#editForm input[name='login']");
						var username=$("#editForm input[name='username']");
						var email=$("#editForm input[name='email']");
						
						var type_id=$("#editForm select[name='type_id'] option:selected");
						
						// Update DOM
						$("tr[data-id='"+data.item.id+"'] td[data-link='login']").html(login.val());
						$("tr[data-id='"+data.item.id+"'] td[data-link='username']").html(username.val());
						$("tr[data-id='"+data.item.id+"'] td[data-link='email']").html(email.val());

						
						if($("#type_"+type_id.val()).length){
							$("tr[data-id='"+data.item.id+"'] td[data-link='type_id']").html($("#type_"+type_id.val()+" .list-group-header span").html());
						}else if(type_id.val()==-1){
							$("tr[data-id='"+data.item.id+"'] td[data-link='type_id']").html('Not actived');
						}else if(type_id.val()==-2){
							$("tr[data-id='"+data.item.id+"'] td[data-link='type_id']").html('Banned');
						}else if(type_id.val()==0){
							$("tr[data-id='"+data.item.id+"'] td[data-link='type_id']").html('User');
						}else{
							$("tr[data-id='"+data.item.id+"'] td[data-link='type_id']").html('Unknown');
						}
						
						if(type_id.val()==-1){
							$("tr[data-id='"+data.item.id+"'] td[data-link='status']").html('<span class="label label-warning">Not actived</span>');
						}else if(type_id.val()==-2){
							$("tr[data-id='"+data.item.id+"'] td[data-link='status']").html('<span class="label label-danger">Banned</span>');
						}else{
							$("tr[data-id='"+data.item.id+"'] td[data-link='status']").html('<span class="label label-success">Actived</span>');
						}
					}
					
					console.log("User updated!");
				}else{
					console.log("Action Error: "+data.error);
				}
			},
			complete: function(){
				//console.log(data.responseText);
			},
			error: function(){
				console.log("Form Error: editForm");
			}
		};
		$("#editForm").ajaxForm(options);
		
		setTimeout(function(){
			$("#editForm input[name=\"name\"]").focus();
		},1000);
	},
	informationForm : function(obj){//ok?2do
		$("#informationForm").attr("action",once.path+"/ajax.php?c=users&o=item_edit_contact&id="+$("#user-data").data("id"));
		var options = {
			dataType:  "json",
			success: function(data){
				// Update name & author on items list
				if(data.status=='ok'){
					// Check for redirects then save
					if($("#user-data").data('redirect')!==undefined){
						// Get source
						var source = rawurlencode($(".textarea").val());
						$.post(once.path+"/ajax.php?c=users&o=save_content&id="+$("#user-data").data("id"), { source: source }, function(data) {
							document.location.href=$("#user-data").data('redirect');
						})
						.error(function() { console.log("Request Error: save_content"); });
					}
					
					if($("#user-data").data('redirect')==undefined){
						// Get new data
						var name=$("#informationForm input[name='name']");
						var author=$("#informationForm input[name='author']");
						
						// Update DOM
						$("tr[data-id='"+data.item.id+"'] td[data-link='name']").html(name.val());
						$("tr[data-id='"+data.item.id+"'] td[data-link='author']").html(author.val());
					}
					
					console.log("User updated!");
				}else{
					console.log("Action Error: "+data.error);
				}
			},
			complete: function(){
				//console.log(data.responseText);
			},
			error: function(){
				console.log("Form Error: informationForm");
			}
		};
		$("#informationForm").ajaxForm(options);
		
		setTimeout(function(){
			$("#informationForm input[name=\"name\"]").focus();
		},1000);
	},
	searchForm: function(obj){//ok
		$("#searchForm").attr("action",once.path+"/view.php?c=users&o="+$("#users-data").data("o"));
		var options = {
			complete: function(data){
				$("#content-body").html(data.responseText);
			},
		};
		$("#searchForm").ajaxForm(options);
	},
	socialForm : function(obj){//ok?2do
		$("#socialForm").attr("action",once.path+"/ajax.php?c=users&o=item_edit_social&id="+$("#user-data").data("id"));
		var options = {
			dataType:  "json",
			success: function(data){
				// Update name & author on items list
				if(data.status=='ok'){
					// Check for redirects then save
					if($("#user-data").data('redirect')!==undefined){
						// Get source
						var source = rawurlencode($(".textarea").val());
						$.post(once.path+"/ajax.php?c=users&o=save_content&id="+$("#user-data").data("id"), { source: source }, function(data) {
							document.location.href=$("#user-data").data('redirect');
						})
						.error(function() { console.log("Request Error: save_content"); });
					}
					
					if($("#user-data").data('redirect')==undefined){
						// Get new data
						var name=$("#socialForm input[name='name']");
						var author=$("#socialForm input[name='author']");
						
						// Update DOM
						$("tr[data-id='"+data.item.id+"'] td[data-link='name']").html(name.val());
						$("tr[data-id='"+data.item.id+"'] td[data-link='author']").html(author.val());
					}
					
					console.log("User updated!");
				}else{
					console.log("Action Error: "+data.error);
				}
			},
			complete: function(){
				//console.log(data.responseText);
			},
			error: function(){
				console.log("Form Error: socialForm");
			}
		};
		$("#socialForm").ajaxForm(options);
		
		setTimeout(function(){
			$("#socialForm input[name=\"name\"]").focus();
		},1000);
	},
	uploadThumbnail: function(obj){//ok
		if(!$("#uploadThumbnail").length){
			var str='';
			str+='<form id="uploadThumbnail" method="post" enctype="multipart/form-data" class="hidden">';
				str+='<input type="file" size="60" name="myImage" id="myImage">';
				str+='<input type="submit" value="Ajax File Upload">';
			str+='</form>';
			// Append at end of the body
			$("body").append(str);
			
			// Onclick event
			$("#uploadThumbnail input[type='file']").change(function(e) {
				$("#uploadThumbnail input[type='submit']").click();
			});
		}

		$("#uploadThumbnail").attr("action",once.path+"/ajax.php?c=users&o=upload_thumbnail&id="+$("#user-data").data("id"));
		var options = { 
			dataType:  "json",
			success: function(data){
				// If response ok refresh thumbnail
				if(data.status=='ok'){
					$("#item-thumbnail").attr("src","/once/users/"+data.item.id+"/thumbnail.png?"+Math.random());
				}
			},
			complete: function(data){
				//console.log(data.responseText);
			},
			error: function(){
				console.log("Form Error: uploadThumbnail");
			}
		}; 
		$("#uploadThumbnail").ajaxForm(options);
	},
}

$(document).ready(function () {
	// Initialize onclick action
	$(".item-new").click(function () {
		once.users.itemNew($(this));
    });

	// Initialize types & actions
	if($("#types-data").length){
		once.loadJSfile(once.path+'/js/once.types.js');
	}
	
	// Load libraries & modes
	if($("#user-data").data("ajax") || $("#users-data").data("ajax")){
		once.loadJSfile(once.path+'/libs/jquery-form/jquery.form.js');
		//once.loadJSfile('//oss.maxcdn.com/jquery.form/3.50/jquery.form.min.js');
	}
	
	// Initialize users actions
	if($("#users-data").length>0){
		// Sort actions
		$("#users-data .sort-action").click(function () {
			once.users.sortAction($(this));
		});
		
		// Initialize searchForm if its ajax only
		if($("#users-data").data("ajax")){
			once.users.forms.searchForm($(this));
		}
	}
	
	// Initialize user actions
	if($("#user-data").length){
		once.loadJSfile(once.path+'/lib/jquery-validation/dist/jquery.validate.js');
		//once.loadJSfile('http://ajax.aspnetcdn.com/ajax/jquery.validate/1.11.1/jquery.validate.js');
		
		once.users.actions.editInit();
	}

	// Initialize / sandbox
	once.users.initialized();
});