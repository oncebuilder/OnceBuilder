/**
 * Version: 1.0, 29.07.2015
 * by Adam Wysocki, support@oncebuilder.com
 *
 * Copyright (c) 2015 Adam Wysocki
 *
 *	This is OnceBuilder Themes plugin (once.themes)
 *
*/

once.themes = {
	loaded: false,
	initialized: function(){
		this.loaded=true;
	},
	
	// List function
	displayLimit: function(obj){//ok
		// Call for set limit
		$.getJSON(once.path+"/ajax.php?c=themes&o=set_limit&limit="+$(obj).val(), function(data) {
			$(".pagination a:first").click();
		})
		.error(function() { console.log("Request Error: set_limit"); });
	},
	openPage: function(obj){//ok
		// Open selected page with params
		$.get(once.path+"/view.php?c=themes&o="+$("#themes-data").data("o")+"&type_id="+$("#themes-data").data("type_id")+"&category_id="+$("#themes-data").data("category_id")+"&sort_by="+$("#themes-data").data("sort_by")+"&page="+obj.html()+"&ids="+$("#themes-data").data("ids")+"&query="+$("#themes-data").data("query"), function(data) {
			$("#content-body").html(data);
		})
		.error(function() { console.log("Request Error: "+$("#themes-data").data("o")); });
	},
	sortAction: function(obj){//ok
		if($("#themes-data").data("ajax")){
			$("#sort-label").html(obj.html());
			// Refresh items list by sort key
			$.get(once.path+"/view.php?c=themes&o="+$("#themes-data").data("o")+"&type_id="+$("#themes-data").data("type_id")+"&category_id="+$("#themes-data").data("category_id")+"&sort_by="+obj.data("sort")+"&page="+$("#themes-data").data("page")+"&ids="+$("#themes-data").data("ids")+"&query="+$("#themes-data").data("query"), function(data) {
				$("#content-body").html(data);
			})
			.error(function() { alert("Couldn't load sort"); });
		}else{
			// Refresh items list by sort key
			$("#sort-label").html(obj.html());
			document.location.href='/themes?type_name='+$("#themes-data").data('type')+'&sort_by='+obj.data("sort")+'&p='+$("#themes-data").data("page")+'&query='+$("#themes-data").data('query');
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
				url: once.path+"/ajax.php?c=themes&o=item_delete&id="+id,
				success: function(data) { 
					// Refresh items list if response ok
					if(data.status=='ok'){
						// Intermediatly delete from dom then refresh items list
						$("#item_"+id).remove();
						// Call for refresh
						$.get(once.path+"/view.php?c=themes&o="+$("#themes-data").data("o")+"&type_id="+$("#themes-data").data("type_id")+"&category_id="+$("#themes-data").data("category_id")+"&sort_by="+$("#themes-data").data("sort_by")+"&page="+$("#themes-data").data("page")+"&ids="+$("#themes-data").data("ids")+"&query="+$("#themes-data").data("query"), function(data) {
							$("#content-body").html(data);
						})
						.error(function() { console.log("Request Error: "+$("#themes-data").data("o")); });
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
	itemEditApprove: function(obj){//ok
		// Get varibles defined in rendering data-*
		var id=$("#theme-data").data('id');
		var name=$("#editForm input[name='name']").val();
		// We need to confirm to delete
		var r = confirm("Approve "+name+"?");
		if(r){
			$.ajax({
				type: 'POST',
				url: once.path+"/ajax.php?c=themes&o=item_approve&id="+id,
				success: function(data) { 
					// Refresh items list if response ok
					if(data.status=='ok'){
						// Read and open edit dialog after approve
						$("#item-edit .modal").load("dialog.php?c=themes&o=edit&id="+id+"&nomodal", function() {

						})
						.error(function() { console.log("Dialog Error: approve"); });
					}else{
						console.log("Action Error: "+data.error);
					}
				},
				contentType: "application/json",
				dataType: 'json'
			})
			.error(function() { console.log("Request Error: item_aprove"); });
		}
	},
	itemEditDelete: function(obj){//ok
		// Get varibles defined in rendering data-*
		var id=$("#theme-data").data('id');
		var name=$("#editForm input[name='name']").val();
		// We need to confirm to delete
		var r = confirm("Delete "+name+"?");
		if(r){
			// Call to del_layer with parm id
			$.ajax({
				type: 'POST',
				url: once.path+"/ajax.php?c=themes&o=item_delete&id="+id,
				success: function(data) { 
					// Refresh items list if response ok
					if(data.status=='ok'){
						$("#theme-data .item-close").click();
						// Intermediatly delete from dom then refresh items list
						$("#item_"+id).remove();
						// Call for refresh
						$.get(once.path+"/view.php?c=themes&o="+$("#themes-data").data("o")+"&type_id="+$("#themes-data").data("type_id")+"&category_id="+$("#themes-data").data("category_id")+"&sort_by="+$("#themes-data").data("sort_by")+"&page="+$("#themes-data").data("page")+"&ids="+$("#themes-data").data("ids")+"&query="+$("#themes-data").data("query"), function(data) {
							$("#content-body").html(data);
						})
						.error(function() { console.log("Request Error: "+$("#themes-data").data("o")); });
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
	itemEditExport: function(){//ok
		document.location.href=once.path+"/ajax.php?c=themes&o=item_export&id="+$("#theme-data").data("id");
	},
	itemEditImageDelete: function(obj){//ok
		// Get varibles defined in rendering data-*
		var image=$(obj).parent().parent().next().attr('src');
		
		// We need to confirm to delete
		var r = confirm("Delete "+image+"?");
		if(r){
			// Call to del_layer with parm id
			$.ajax({
				type: 'POST',
				url: once.path+"/ajax.php?c=themes&o=delete_image&id="+$("#theme-data").data("id")+"&currentImage="+$(obj).parent().parent().parent().parent().data('id'),
				success: function(data) { 
					// Refresh items list if response ok
					if(data.status=='ok'){
						$(obj).parent().parent().parent().parent().toggleClass("hidden");
					}else{
						console.log("Action Error: "+data.error);
					}
				},
				contentType: "application/json",
				dataType: 'json'
			})
			.error(function() { console.log("Request Error: delete_image"); });
		}
	},
	itemEditSave: function(obj){//ok
		// Save selected source or just item edit
		if($("#theme-data").data("tab")!==undefined){
			$($("#theme-data").data("tab")+" form").submit();
		}else{
			$("#editForm").submit();
		}
	},
	itemEditStar: function(obj){//ok
		// Call for star item
		$.ajax({
			type: 'POST',
			url: once.path+"/ajax.php?c=themes&o=item_star&id="+$("#theme-data").data("id"),
			success: function(data) {
				// Refresh items list if response ok
				if(data.status=='ok'){
					// Read and open edit dialog with selected page
					$("#item-edit .modal").load("dialog.php?c=themes&o=edit&id="+$("#theme-data").data("id")+"&nomodal", function() {
						
					})
					.error(function() { console.log("Dialog Error: edit"); });
					
					// Check for fonts icos
					var obj=$("#item_"+$("#theme-data").data("id")+" td i");
					var glyph = obj.hasClass("glyphicon");
					var fa = obj.hasClass("fa");
					// Switch states
					if(glyph){
						obj.toggleClass("glyphicon-star");
						obj.toggleClass("glyphicon-star-empty");
					}else if(fa){
						obj.toggleClass("fa-star");
						obj.toggleClass("fa-star-o");
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
	itemImportSave : function(event){//ok
		$("#importForm").submit();
	},
	itemNew: function(obj){//ok
		// Call to item_new for new item
		$.ajax({
			type: 'POST',
			url: once.path+"/ajax.php?c=themes&o=item_new&type_id="+$("#themes-data").data('type_id')+"&category_id="+$("#themes-data").data('category_id'),
			success: function(data) {
				// Refresh items list if response ok
				if(data.status=='ok'){
					// Call for refresh
					$.get(once.path+"/view.php?c=themes&o="+$("#themes-data").data("o")+"&type_id="+$("#themes-data").data("type_id")+"&category_id="+$("#themes-data").data("category_id"), function(data) {
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
	itemPreviewDownload: function(obj){//ok
		// Get item id
		var id=$("#preview-data").data("id");
		$.ajax({
			type: 'POST',
			url: once.path+"/ajax.php?c=themes&o=item_download&id="+id,
			beforeSend: function(data) {
				var button=$("#preview-data .item-download");
				button.css("cursor","pointer");
				button.attr("disabled",true);
			},
			success: function(data) {
				// Refresh items list if response ok
				if(data.status=='ok'){
					
					var button=$("#preview-data .item-download");
					
					button.toggleClass("btn-success");
					button.toggleClass("btn-default");
					button.attr("disabled",true);
					button.css("cursor","pointer");
					button.html("Downloaded");

					//item improt from file
					console.log(data);
				}else{
					console.log("Action Error: "+data.error);
				}
			},
			contentType: "application/json",
			dataType: 'json'
		})
		.error(function() { console.log("Request Error: item_download"); });
	},
	itemPublishDisplayLimit: function(obj){//ok
		// Call for set limit
		$.getJSON("ajax.php?c=themes&o=set_limit&limit="+$(obj).val(), function(data) {
			$(".modal .pagination a:first").click();
		})
		.error(function() { console.log("Request Error: set_limit"); });
	},
	itemPublishPage: function(obj){//ok
		// Read and open publish dialog with selected page
		$("#item-publish .modal").load("dialog.php?c=themes&o=publish&page="+obj.html()+"&nomodal", function() {

		})
		.error(function() { console.log("Dialog Error: publish"); });
	},
	itemPublishSelect: function(obj){//ok
		// Read and open publish dialog with selected theme
		$("#item-publish .modal").load("dialog.php?c=themes&o=publish&id="+obj.parent().parent().data('id')+"&nomodal", function() {
			$("#publishForm input[name='id']").val(obj.parent().parent().data('id'));
		})
		.error(function() { console.log("Dialog Error: publish"); });
	},
	itemPublishSubmit: function(obj){//ok
		$("#publishForm").submit();
	},
	itemUse: function(obj){//ok
		// Get varibles defined in rendering data-*
		var id=$(obj).parent().parent().parent().parent().parent().data('id');
		var name=$("#item_"+id+" td.item-name").html();
		// We need to confirm to delete
		var r = confirm("Your site will be overwrited with this "+name+" theme. Are you sure?");
		if(r){
			// Call to del_layer with parm id
			$.getJSON("ajax.php?c=themes&o=item_use&id="+id, function(data) {
				// Refresh items list if response ok
				if(data.status=='ok'){
					// Intermediatly delete from dom then refresh items list
					$("#item_"+id).remove();
					// Call for refresh
					$.get(once.path+"/view.php?c=themes&o="+$("#themes-data").data("o")+"&type_id="+$("#themes-data").data("type_id")+"&category_id="+$("#themes-data").data("category_id")+"&sort_by="+$("#themes-data").data("sort_by")+"&page="+$("#themes-data").data("page")+"&ids="+$("#themes-data").data("ids")+"&query="+$("#themes-data").data("query"), function(data) {
						$("#content-body").html(data);
					})
					.error(function() { console.log("Request Error: "+$("#themes-data").data("o")); });
				}else{
					console.log("Action Error: "+data.error);
				}
			})
			.error(function() { console.log("Request Error: item_use"); });
		}
	},
}

once.themes.actions = {
	mainInit: function(obj){//ok
		once.loadJSfile(once.path+'/libs/jquery-form/jquery.form.js');

		// Categories
		$("#categories-remote .list-group-item").click(function (e) {
			e.preventDefault();
			e.stopPropagation();
			once.categories.categoryLoad($(this));
		});
		
		// Sort actions
		$(".sort-action").click(function () {
			once.themes.sortAction($(this));
		});
		
		// Install item
		$(".item-use").click(function () {
			once.themes.itemUse($(this));
		});
		
		// Star item
		$(".item-star").click(function () {
			once.themes.itemStar($(this));
		});
		// Initialize itemEdit dialog
		once.themes.dialogs.itemEdit(".item-edit");
		
		// Delete item
		$(".item-delete").click(function () {
			once.themes.itemDelete($(this));
		});
		
		// Set display limit
		$(".display-limit").change(function () {
			once.themes.displayLimit($(this));
		});
		
		// Get selected page
		$(".pagination a").click(function (e) {
			e.preventDefault();
			e.stopPropagation();
			once.themes.openPage($(this));
		});
		
		// Initialize itemPublish dialog
		once.themes.dialogs.itemPreview(".item-preview");
	
		// Initialize searchForm
		once.themes.forms.searchForm($(this));

		// Initialize / sandbox
		once.themes.initialized();
	},
	editInit: function(obj){//ok
		// After show selected tab
		$('#theme-data a[data-toggle="tab"]').on('show.bs.tab', function (e) {
			var tab = $(this); // current tab
			var previous = e.relatedTarget; // previous active tab
			var previous_id = $(e.relatedTarget).data('editor');
			
			$("#theme-data").data("tab",tab.attr('href'));
		});
		
		// Thumbnail hover on change logo
		$("#theme-data .thumbnail").hover(
			function(){
				$(this).find(".caption").slideDown(250); //.fadeIn(250)
			},
			function(){
				$(this).find(".caption").slideUp(250,function(){$ (this).stop( true, true )}); //.fadeOut(205)
			}
		);
		
		// Aprove item
		$("#theme-data .item-approve").click(function () {
			once.themes.itemEditApprove($(this));
		});
		
		// Unaprove item
		$("#theme-data .item-approved").click(function () {
			once.themes.itemEditApprove($(this));
		});

		// Delete item
		$("#theme-data .item-delete").click(function () {
			once.themes.itemEditDelete($(this));
		});
		
		// Export item
		$("#theme-data .item-export").click(function () {
			once.themes.itemEditExport($(this));
		});
		
		// Save item
		$("#theme-data .item-save").click(function () {
			once.themes.itemEditSave($(this));
		});
		
		// Star item
		$("#theme-data .item-star").click(function () {
			once.themes.itemEditStar($(this));
		});
		
		// Unstar item
		$("#theme-data .item-stared").click(function () {
			once.themes.itemEditStar($(this));
		});

		// Change thumbnail
		$("#theme-data .item-thumbnail").click(function () {
			$("#uploadThumbnail input[type='file']").click();
		});

		// Change image item
		$("#theme-data .item-image").click(function () {
			$("#uploadImage input[name='currentImage']").val('');
			$("#uploadImage input[type='file']").click();
		});
		
		// Galery image change
		$("#theme-data .item-image-change").click(function () {
			$("#uploadImage input[name='currentImage']").val($(this).parent().parent().parent().parent().data("id"));
			$("#uploadImage input[type='file']").click();
		});
		
		// Galery image delete
		$("#theme-data .item-image-delete").click(function () {
			once.themes.itemEditImageDelete($(this));
		});
		
		// Initialize editForm
		once.themes.forms.editForm($(this));

		// Initialize uploadImage
		once.themes.forms.uploadImage($(this));

		// Initialize uploadThumbnail
		once.themes.forms.uploadThumbnail($(this));
	},
	importInit: function(obj){//ok
		// After tab show action
		$('#import-data a[data-toggle="tab"]').on('show.bs.tab', function (e) {
			e.target // activated tab
			e.relatedTarget // previous tab
			
			// Action for selected tab
			if($(e.target).attr('href')=='#import_url'){
				$("#importForm input[name='url']").val('');
				$("#importForm input[name='file']").val('');
			}else{
				$("#importForm input[name='url']").val('');
				$("#importForm input[name='file']").val('');
			}
		});
		
		// Import item from import mode
		$("#import-data .item-import-save").click(function () {
			once.themes.itemImportSave($(this));
		});
		
		// Initialize importForm
		once.themes.forms.importForm($(this));
	},
	previewInit: function(obj){//ok
		// Download item
		$("#preview-data .item-download").click(function () {
			once.themes.itemPreviewDownload($(this));
		});
	},
	publishInit: function(obj){//ok
		// Select item
		$("#theme-data .item-select").click(function () {
			once.themes.itemPublishSelect($(this));
		});
		
		// Set display limit
		$("#theme-data .display-limit").change(function () {
			once.themes.itemPublishDisplayLimit($(this));
		});
		
		// Get selected page
		$("#theme-data .pagination a").click(function () {
			once.themes.itemPublishPage($(this));
		});
		
		// Select item
		$("#theme-data .item-submit").click(function () {
			once.themes.itemPublishSubmit($(this));
		});
		
		// Initialize publishForm
		once.themes.forms.publishForm($(this))
	},
}

once.themes.dialogs = {
	itemEdit: function(obj){//ok
		// Append at end of the body
		$("body").append("<div id=\"item-edit\"></div>");

		// Read and open edit dialog
		$(obj).click(function (e) {
			e.preventDefault();
			e.stopPropagation();
			$("#item-edit").load(once.path+"/dialog.php?c=themes&o=edit&id="+$(this).parent().parent().parent().parent().parent().data("id"), function() {
				$('#item-edit .modal:first').modal({
					show: 'false'
				}); 
			})
			.error(function() { console.log("Dialog Error: edit"); });
		});
	},
	itemImport: function(obj){//ok
		// Append at end of the body
		$("body").append("<div id=\"item-import\"></div>");

		// Read and open import dialog
		$(obj).click(function (e) {
			e.preventDefault();
			e.stopPropagation();
			$("#item-import").load(once.path+"/dialog.php?c=themes&o=import", function() {
				$('#item-import .modal:first').modal({
					show: 'false'
				}); 
			})
			.error(function() { console.log("Dialog Error: import"); });
		});
	},
	itemPreview: function(obj){//ok
		// Append at end of the body
		$("body").append("<div id=\"item-preview\"></div>");

		// Read and open preview dialog
		$(obj).click(function (e) {
			e.preventDefault();
			e.stopPropagation();
			$("#item-preview").load(once.path+"/dialog.php?c=themes&o=preview&id="+$(this).parent().parent().data("id"), function() {
				$('#item-preview .modal:first').modal({
					show: 'false'
				}); 
			})
			.error(function() { console.log("Dialog Error: preview"); });
		});
	},
	itemPublish: function(obj){//ok
		// Append at end of the body
		$("body").append("<div id=\"item-publish\"></div>");

		// Read and open publish dialog
		$(obj).click(function (e) {
			e.preventDefault();
			e.stopPropagation();
			$("#item-publish").load(once.path+"/dialog.php?c=themes&o=publish", function() {
				$('#item-publish .modal:first').modal({
					show: 'false'
				}); 
			})
			.error(function() { console.log("Dialog Error: publish"); });
		});
	},
}

once.themes.forms = {
	editForm: function(obj){//ok
		$("#editForm").attr("action",once.path+"/ajax.php?c=themes&o=item_edit&id="+$("#theme-data").data("id"));
		var options = {
			dataType:  "json",
			success: function(data){
				// Update name on items list
				if(data.status=='ok'){
					if($("#theme-data").data('redirect')==undefined){
						// Get new data
						var name=$("#editForm input[name='name']");
						
						// Update DOM
						$("#item_"+data.item.id+" .theme-name").html(name.val());
					}
					
					console.log("Theme updated!");
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
	importForm: function(obj){//ok
		$("#importForm").attr("action",once.path+"/ajax.php?c=themes&o=item_import");
		var options = {
		dataType:  "json",
			success: function(data){
				// If response ok refresh logo
				if(data.status=='ok'){
					// Call for refresh
					$.get(once.path+"/view.php?c=themes&o="+$("#themes-data").data("o")+"&type_id="+$("#themes-data").data("type_id")+"&category_id="+$("#themes-data").data("category_id"), function(data) {
						$("#content-body").html(data);
					})
					.error(function() { alert("Couldnt load page"); });
					
					// Open edit dialog
					setTimeout(function(){
						$("#import-data .item-close").click();
						$("#item_"+data.item.id+" .item-edit").click();
					},500);
					console.log("Theme imported");
				}else{
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
		$("#importForm").ajaxForm(options);
		
		setTimeout(function(){
			$("#importForm input[name=\"url\"]").focus();
		},700);
	},
	publishForm: function(obj){//ok
		$("#publishForm").attr("action",once.path+"/ajax.php?c=themes&o=item_publish&id="+$("#theme-data").data("id"));
		var options = {
		dataType:  "json",
			success: function(data){
				// If response ok refresh logo
				if(data.status=='ok'){
					$("#theme-data .item-close").click();
					console.log("Theme published");
				}else{
					console.log("Action Error: "+data.error);
				}
			},
			complete: function(data){
				//console.log(data.responseText);
			},
			error: function(){
				console.log("Form Error: publishForm");
			}
		}; 
		$("#publishForm").ajaxForm(options);
		
		setTimeout(function(){
			$("#publishForm input[name=\"url\"]").focus();
		},700);
	},
	searchForm: function(obj){//ok
		$("#searchForm").attr("action",once.path+"/view.php?c=themes&o="+$("#themes-data").data("o"));
		var options = {
			complete: function(data){
				$("#content-body").html(data.responseText);
			},
		};
		$("#searchForm").ajaxForm(options);
	},
	uploadImage: function(obj){//ok
		if(!$("#uploadImage").length){
			var str='';
			str+='<form id="uploadImage" method="post" enctype="multipart/form-data" class="hidden">';
				str+='<input type="hidden" size="60" name="currentImage">';
				str+='<input type="file" size="60" name="myImage" id="myImage">';
				str+='<input type="submit" value="Ajax File Upload">';
			str+='</form>';
			// Append at end of the body
			$("body").append(str);
			
			// Onclick event
			$("#uploadImage input[type='file']").change(function(e) {
				$("#uploadImage input[type='submit']").click();
			});
		}

		$("#uploadImage").attr("action",once.path+"/ajax.php?c=themes&o=upload_image&id="+$("#theme-data").data("id"));
		var options = { 
			dataType:  "json",
			success: function(data){
				// If response ok refresh image
				if(data.status=='ok'){
					// Image overwrite
					$("#image_"+data.item.currentImage+" img").attr("src",$("#image_"+data.item.currentImage+" img").data('src')+"?"+Math.random());
					// Show
					$("#image_"+data.item.currentImage).removeClass("hidden");
				}
			},
			complete: function(data){
				//console.log(data.responseText);
			},
			error: function(){
				console.log("Form Error: uploadImage");
			}
		}; 
		$("#uploadImage").ajaxForm(options);
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

		$("#uploadThumbnail").attr("action",once.path+"/ajax.php?c=themes&o=upload_thumbnail&id="+$("#theme-data").data("id"));
		var options = { 
			dataType:  "json",
			success: function(data){
				// If response ok refresh thumbnail
				if(data.status=='ok'){
					$("#item-thumbnail").attr("src","/once/themes/"+data.item.id+"/thumbnail.png?"+Math.random());
					$("#item_"+$("#theme-data").data("id")+" img").attr("src","/once/themes/"+data.item.id+"/thumbnail.png?"+Math.random());
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
		once.themes.itemNew($(this));
    });

	// Initialize itemImport dialog
	once.themes.dialogs.itemImport(".item-import");
		
	// Initialize itemPublish dialog
	once.themes.dialogs.itemPublish(".item-publish");
	
	// Initialize categories & actions
	if($("#categories-data").length){
		once.loadJSfile(once.path+'/js/once.categories.js');
	}
	
	// Load libraries & modes
	if($("#theme-data").data("ajax") || $("#themes-data").data("ajax")){
		once.loadJSfile(once.path+'/libs/jquery-form/jquery.form.js');
		//once.loadJSfile('//oss.maxcdn.com/jquery.form/3.50/jquery.form.min.js');
	}
	
	// Initialize themes actions
	if($("#themes-data").length>0){
		// Sort actions
		$("#themes-data .sort-action").click(function () {
			once.themes.sortAction($(this));
		});
		
		// Initialize searchForm if its ajax only
		if($("#themes-data").data("ajax")){
			once.themes.forms.searchForm($(this));
		}
	}
	
	// Initialize user actions
	if($("#theme-data").length){
		once.loadJSfile(once.path+'/libs/jquery-validation/dist/jquery.validate.js');
		//once.loadJSfile('http://ajax.aspnetcdn.com/ajax/jquery.validate/1.11.1/jquery.validate.js');
		
		once.themes.actions.editInit();
	}

	// Initialize / sandbox
	once.themes.initialized();
});