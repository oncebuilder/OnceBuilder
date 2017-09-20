/**
 * Version: 1.0, 21.07.2015
 * by Adam Wysocki, support@oncebuilder.com
 *
 * Copyright (c) 2015 Adam Wysocki
 *
 *	This is OnceBuilder Posts plugin (once.posts)
 *
*/

once.posts = {
	loaded: false,
	keypress: false,
	initialized: function(){
		this.loaded=true;
	},
	
	// List function
	bulkAction: function(obj){//ok
		// Set action value
		$("#checkForm input[name='action']").val(obj.data("action"));
		// Set action o
		$("#checkForm").attr("action",once.path+"/ajax.php?c=posts&o=bulk_action");
		// Submit form
		$("#checkForm").submit();
	},
	displayLimit: function(obj){//ok
		// Call for set limit
		$.getJSON(once.path+"/ajax.php?c=posts&o=set_limit&limit="+$(obj).val(), function(data) {
			$(".pagination a:first").click();
		})
		.error(function() { console.log("Request Error: set_limit"); });
	},
	openPage: function(obj){//ok
		// Open selected page with params
		$.get(once.path+"/view.php?c=posts&o="+$("#posts-data").data("o")+"&type_id="+$("#posts-data").data("type_id")+"&category_id="+$("#posts-data").data("category_id")+"&sort_by="+$("#posts-data").data("sort_by")+"&page="+obj.html()+"&ids="+$("#posts-data").data("ids")+"&query="+$("#posts-data").data("query"), function(data) {
			$("#content-body").html(data);
		})
		.error(function() { console.log("Request Error: "+$("#posts-data").data("o")); });
	},
	sortAction: function(obj){//ok
		if($("#posts-data").data("ajax")){
			$("#sort-label").html(obj.html());
			// Refresh items list by sort key
			$.get(once.path+"/view.php?c=posts&o="+$("#posts-data").data("o")+"&type_id="+$("#posts-data").data("type_id")+"&category_id="+$("#posts-data").data("category_id")+"&sort_by="+obj.data("sort")+"&page="+$("#posts-data").data("page")+"&ids="+$("#posts-data").data("ids")+"&query="+$("#posts-data").data("query"), function(data) {
				$("#content-body").html(data);
			})
			.error(function() { alert("Couldn\'t load sort"); });
		}else{
			// Refresh items list by sort key
			$("#sort-label").html(obj.html());
			document.location.href='/posts?category_name='+$("#posts-data").data('category')+'&sort_by='+obj.data("sort")+'&p='+$("#posts-data").data("page")+'&query='+$("#posts-data").data('query');
		}
	},
	
	// View function
	itemDelete: function(obj){//ok
		// Get varibles defined in rendering data-*
		var id=$(obj).parent().parent().data('id');
		var title=$("#item_"+id+" td.item-title").html();
		// We need to confirm to delete
		var r = confirm("Delete "+title+"?");
		if(r){
			// Call to item_delete with parm id
			$.ajax({
				type: 'POST',
				url: once.path+"/ajax.php?c=posts&o=item_delete&id="+id,
				success: function(data) {
					// Refresh items list if response ok
					if(data.status=='ok'){
						// Intermediatly delete from dom then refresh items list
						$("#item_"+id).remove();
						// Call for refresh
						$.get(once.path+"/view.php?c=posts&o="+$("#posts-data").data("o")+"&type_id="+$("#posts-data").data("type_id")+"&category_id="+$("#posts-data").data("category_id")+"&sort_by="+$("#posts-data").data("sort_by")+"&page="+$("#posts-data").data("page")+"&ids="+$("#posts-data").data("ids")+"&query="+$("#posts-data").data("query"), function(data) {
							$("#content-body").html(data);
						})
						.error(function() { console.log("Request Error: "+$("#posts-data").data("o")); });
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
		var id=$("#post-data").data('id');
		var title=$("#editForm input[name='title']").val();
		// We need to confirm to delete
		var r = confirm("Delete "+title+"?");
		if(r){
			// Call to del_layer with parm id
			$.ajax({
				type: 'POST',
				url: once.path+"/ajax.php?c=posts&o=item_delete&id="+id,
				success: function(data) {
					// Refresh items list if response ok
					if(data.status=='ok'){
						$("#post-data .item-close").click();
						// Intermediatly delete from dom then refresh items list
						$("#item_"+id).remove();
						// Call for refresh
						$.get(once.path+"/view.php?c=posts&o="+$("#posts-data").data("o")+"&type_id="+$("#posts-data").data("type_id")+"&category_id="+$("#posts-data").data("category_id")+"&sort_by="+$("#posts-data").data("sort_by")+"&page="+$("#posts-data").data("page")+"&ids="+$("#posts-data").data("ids")+"&query="+$("#posts-data").data("query"), function(data) {
							$("#content-body").html(data);
						})
						.error(function() { console.log("Request Error: "+$("#posts-data").data("o")); });
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
	itemEditImageDelete: function(obj){//ok
		// Get varibles defined in rendering data-*
		var image=$(obj).parent().parent().next().attr('src');
		
		// We need to confirm to delete
		var r = confirm("Delete "+image+"?");
		if(r){
			// Call to del_layer with parm id
			$.ajax({
				type: 'POST',
				url: once.path+"/ajax.php?c=posts&o=delete_image&id="+$("#post-data").data("id")+"&currentImage="+$(obj).parent().parent().parent().parent().data('id'),
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

	itemEditSave: function(obj){
		// Save selected source or just item edit
		if($("#post-data").data("tab")!=='#edit_settings'){
			once.posts.itemEditSaveContent();
		}else{
			$("#editForm").submit();
		}
	},
	itemEditSaveContent: function(obj){
		// Save selected source
		if($("#post-data").data("source")){
			var source = $('.textarea').val();
			$.post(once.path+"/ajax.php?c=posts&o=item_edit_content&id="+$("#post-data").data("id")+"&path="+$("#post-data").data("path")+"&file="+$("#post-data").data("file")+"&title="+$("#post-data").data("title"), { source: source }, function(data) {
				// Check if preview is open then refresh
		
				console.log("Source saved");
			})
			.error(function() { console.log("Request Error: item_edit_content"); });
		}
	},
	itemEditStar: function(obj){//ok
		// Call for star item
		$.ajax({
			type: 'POST',
			url: once.path+"/ajax.php?c=posts&o=item_star&id="+$("#post-data").data("id"),
			success: function(data) {
				// Refresh items list if response ok
				if(data.status=='ok'){
					// Read and open edit dialog with selected page
					$("#item-edit .modal").load("dialog.php?c=posts&o=edit&id="+$("#post-data").data("id")+"&nomodal", function() {
						
					})
					.error(function() { console.log("Dialog Error: edit"); });
					
					// Check for fonts icos
					var obj=$("#item_"+$("#post-data").data("id")+" td i");
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
	itemNew: function(obj){//ok
		// Call to item_new for new item
		$.ajax({
			type: 'POST',
			url: once.path+"/ajax.php?c=posts&o=item_new&type_id="+$("#posts-data").data('type_id')+"&category_id="+$("#posts-data").data('category_id'),
			success: function(data) {
				// Refresh items list if response ok
				if(data.status=='ok'){
					// Call for refresh
					$.get(once.path+"/view.php?c=posts&o="+$("#posts-data").data("o")+"&type_id="+$("#posts-data").data("type_id")+"&category_id="+$("#posts-data").data("category_id"), function(data) {
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
			url: once.path+"/ajax.php?c=posts&o=item_star&id="+row.data("id"),
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

once.posts.actions = {
	mainInit: function(obj){//ok
		once.loadJSfile(once.path+'/libs/jquery-form/jquery.form.js');

		// Types
		$("#types-remote .list-group-item").click(function (e) {
			e.preventDefault();
			e.stopPropagation();
			once.types.categoryLoad($(this));
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
				once.posts.bulkAction($(this));
			});
		}

		// Sort actions
		$(".sort-action").click(function () {
			once.posts.sortAction($(this));
		});

		// Download item
		$(".item-download").click(function () {
			once.posts.itemDownload($(this));
		});

		// Star item
		$(".item-star").click(function () {
			once.posts.itemStar($(this));
		});
		
		// Initialize itemEdit dialog
		once.posts.dialogs.itemEdit(".item-edit");

		// Delete item
		$(".item-delete").click(function () {
			once.posts.itemDelete($(this));
		});

		// Set display limit
		$(".display-limit").change(function () {
			once.posts.displayLimit($(this));
		});

		// Get selected page
		$(".pagination a").click(function (e) {
			e.preventDefault();
			e.stopPropagation();
			once.posts.openPage($(this));
		});

		// Initialize checkForm
		once.posts.forms.checkForm($(this));

		// Initialize searchForm
		once.posts.forms.searchForm($(this));

		// Initialize / sandbox
		once.posts.initialized();
	},
	editInit: function(obj){//ok
		once.loadCSSfile(once.path+'/libs/bootstrap3-wysihtml5/src/bootstrap-wysihtml5.css');
		once.loadJSfile(once.path+'/libs/bootstrap3-wysihtml5/lib/js/wysihtml5-0.3.0.js');
		once.loadJSfile(once.path+'/libs/bootstrap3-wysihtml5/src/bootstrap3-wysihtml5.js');
		//Initialize bootstrap WYSIHTML5 - text editor
		var editor = $(".textarea").wysihtml5();

		window.editor.on('focus', function() {
			if(!$("body").hasClass("modal-open")) $("body").toggleClass("modal-open")
		});
	
		
		once.loadJSfile(once.path+'/libs/momentjs/moment.js');
		once.loadCSSfile(once.path+'/libs/bootstrap3-datetimepicker/build/css/bootstrap-datetimepicker.min.css');
		once.loadJSfile(once.path+'/libs/bootstrap3-datetimepicker/build/js/bootstrap-datetimepicker.min.js');
	
		// Datepicker
		$(".data_publish").datetimepicker({ locale: 'en'});
		
		
	
		// After show selected tab
		$('#post-data a[data-toggle="tab"]').on('show.bs.tab', function (e) {
			var tab = $(this); // current tab
			var previous = e.relatedTarget; // previous active tab
			var previous_id = $(e.relatedTarget).data('editor');

			$("#post-data").data("tab",tab.attr('href'));

			// Load tab content
			if(tab.attr('href')=='#edit_source'){
				
			}
		});

		// Thumbnail hover to change logo
		$("#post-data .thumbnail").hover(
			function(){
				$(this).find(".caption").slideDown(250); //.fadeIn(250)
			},
			function(){
				$(this).find(".caption").slideUp(250,function(){$ (this).stop( true, true )}); //.fadeOut(205)
			}
		);

		// After close this modal
		$(document).on("hidden.bs.modal", "#item-edit", function (e) {
			$("#edit_preview").removeClass("show");
			$("#post-data").data("tab",false);
		});

		// Active keypress
		$("#post-data").keypress(function(e) {
			// Ctr + S to save
			if(e.ctrlKey && e.which==115){
				e.preventDefault();
				e.stopPropagation();
				once.posts.itemEditSaveSource();
			}
		});

		// Delete item
		$("#post-data .item-delete").click(function () {
			once.posts.itemEditDelete($(this));
		});

		// Export item
		$("#post-data .item-export").click(function () {
			once.posts.itemEditExport($(this));
		});

		// Preview file
		$("#post-data .item-preview").click(function () {
			once.posts.itemEditPreview($(this));
		});

		// Save item
		$("#post-data .item-save").click(function () {
			once.posts.itemEditSave($(this));
		});

		// Star item
		$("#post-data .item-star").click(function () {
			once.posts.itemEditStar($(this));
		});

		// Unstar item
		$("#post-data .item-stared").click(function () {
			once.posts.itemEditStar($(this));
		});

		// Change thumbnail
		$("#post-data .item-thumbnail").click(function () {
			$("#uploadThumbnail input[type='file']").click();
		});

		// Change image item
		$("#post-data .item-image").click(function () {
			$("#uploadImage input[name='currentImage']").val('');
			$("#uploadImage input[type='file']").click();
		});
		
		// Galery image change
		$("#post-data .item-image-change").click(function () {
			$("#uploadImage input[name='currentImage']").val($(this).parent().parent().parent().parent().data("id"));
			$("#uploadImage input[type='file']").click();
		});
		
		// Galery image delete
		$("#post-data .item-image-delete").click(function () {
			once.posts.itemEditImageDelete($(this));
		});
		
		// Initialize editForm
		once.posts.forms.editForm($(this));

		// Initialize uploadImage
		once.posts.forms.uploadImage($(this));
		
		// Initialize uploadThumbnail
		once.posts.forms.uploadThumbnail($(this));
		
		
	},
}

once.posts.dialogs = {
	itemEdit: function(obj){//ok
		// Append at end of the body
		$("body").append("<div id=\"item-edit\"></div>");

		// Read and open edit dialog
		$(obj).click(function (e) {
			e.preventDefault();
			e.stopPropagation();
			$("#item-edit").load(once.path+"/dialog.php?c=posts&o=edit&id="+$(this).parent().parent().data("id"), function() {
				$('#item-edit .modal:first').modal({
					show: 'false'
				});
			})
			.error(function() { console.log("Dialog Error: edit"); });
		});
	},
}

once.posts.forms = {
	checkForm: function(obj){//ok
		var options = {
			dataType:  "json",
			success: function(data){
				
				console.log($("#checkForm").data("type"));
				console.log($("#checkForm").data("module"));
				
				// Call for refresh
				$.get(once.path+"/view.php?c=posts&o="+$("#posts-data").data("o")+"&type_id="+$("#posts-data").data("type_id")+"&category_id="+$("#posts-data").data("category_id")+"&sort_by="+$("#posts-data").data("sort_by")+"&page="+$("#posts-data").data("page")+"&ids="+$("#posts-data").data("ids")+"&query="+$("#posts-data").data("query"), function(data) {
					$("#content-body").html(data);
				})
				.error(function() { console.log("Request Error: "+$("#posts-data").data("o")); });
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
		$("#editForm").attr("action",once.path+"/ajax.php?c=posts&o=item_edit&id="+$("#post-data").data("id"));
		var options = {
			dataType:  "json",
			success: function(data){
				// Update name & version on items list
				if(data.status=='ok'){
					if($("#post-data").data('redirect')==undefined){
						// Get new data
						var name=$("#editForm input[name='name']");
						var version=$("#editForm input[name='version']");

						// Update DOM
						$("tr[data-id='"+data.item.id+"'] td[data-link='name']").html(name.val());
						$("tr[data-id='"+data.item.id+"'] td[data-link='version']").html(version.val());
					}
					
					console.log("Post updated!");
				}else{
					console.log("Action Error: "+data.error);
				}
			},
			complete: function(data){
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
	searchForm: function(obj){//ok
		$("#searchForm").attr("action",once.path+"/view.php?c=posts&o="+$("#posts-data").data("o"));
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

		$("#uploadImage").attr("action",once.path+"/ajax.php?c=posts&o=upload_image&id="+$("#post-data").data("id"));
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

		$("#uploadThumbnail").attr("action",once.path+"/ajax.php?c=posts&o=upload_thumbnail&id="+$("#post-data").data("id"));
		var options = { 
			dataType:  "json",
			success: function(data){
				// If response ok refresh thumbnail
				if(data.status=='ok'){
					$("#item-thumbnail").attr("src","/once/posts/"+data.item.id+"/thumbnail.png?"+Math.random());
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
		once.posts.itemNew($(this));
    });

	// Initialize types & actions
	if($("#types-data").length){
		once.loadJSfile(once.path+'/js/once.types.js');
	}

	// Load libraries & modes
	if($("#post-data").data("ajax") || $("#posts-data").data("ajax")){
		once.loadJSfile(once.path+'/libs/jquery-form/jquery.form.js');
		//once.loadJSfile('//oss.maxcdn.com/jquery.form/3.50/jquery.form.min.js');
	}

	// Initialize posts actions
	if($("#posts-data").length>0){
		// Sort actions
		$("#posts-data .sort-action").click(function () {
			once.posts.sortAction($(this));
		});

		// Initialize searchForm if its ajax only
		if($("#posts-data").data("ajax")){
			once.posts.forms.searchForm($(this));
		}

		if(once.cms){
			setTimeout(function(){
				// Load code mirror library & modes
			}, 3000);
		}
	}

	// Initialize post actions
	if($("#post-data").length){
		once.loadJSfile(once.path+'/lib/jquery-validation/dist/jquery.validate.js');
		//once.loadJSfile('http://ajax.aspnetcdn.com/ajax/jquery.validate/1.11.1/jquery.validate.js');
		
		once.posts.actions.editInit();
	}

	// Initialize / sandbox
	once.posts.initialized();
});