/**
 * Version: 1.0, 31.05.2012
 * by Adam Wysocki, support@oncebuilder.com
 *
 * Copyright (c) 2012 Adam Wysocki
 *
 *	This is OnceBuilder Pages plugin (once.pages)
 *
*/

once.pages = {
	loaded: false,
	keypress: false,
	serialized_plugins: [],
	serialized_data: [],
	initialized: function(){
		this.loaded=true;
	},

	// Initialize data
	setPluginsData: function(data){//ok
		this.serialized_plugins=data;
	},
	setLayersData: function(data){//ok
		this.serialized_data=data;
	},
	setTab: function(obj){//ok
		if(obj!=''){
			$(".nav-tabs a[data-tab='"+obj+"']").click();
		}
	},
	
	// List function
	bulkAction: function(obj){//ok
		// Set action value
		$("#checkForm input[name='action']").val(obj.data("action"));
		// Set action o
		$("#checkForm").attr("action",once.path+"/ajax.php?c=pages&o=bulk_action");
		// Submit form
		$("#checkForm").submit();
	},
	displayLimit: function(obj){//ok
		// Call for set limit
		$.getJSON(once.path+"/ajax.php?c=pages&o=set_limit&limit="+$(obj).val(), function(data) {
			$(".pagination a:first").click();
		})
		.error(function() { console.log("Request Error: set_limit"); });
	},
	openPage: function(obj){//ok
		// Open selected page with params
		$.get(once.path+"/view.php?c=pages&o="+$("#pages-data").data("o")+"&type_id="+$("#pages-data").data("type_id")+"&category_id="+$("#pages-data").data("category_id")+"&sort_by="+$("#pages-data").data("sort_by")+"&page="+obj.html()+"&ids="+$("#pages-data").data("ids")+"&query="+$("#pages-data").data("query"), function(data) {
			$("#content-body").html(data);
		})
		.error(function() { console.log("Request Error: "+$("#pages-data").data("o")); });
	},
	sortAction: function(obj){//ok
		if($("#pages-data").data("ajax")){
			$("#sort-label").html(obj.html());
			// Refresh items list by sort key
			$.get(once.path+"/view.php?c=pages&o="+$("#pages-data").data("o")+"&type_id="+$("#pages-data").data("type_id")+"&category_id="+$("#pages-data").data("category_id")+"&sort_by="+obj.data("sort")+"&page="+$("#pages-data").data("page")+"&ids="+$("#pages-data").data("ids")+"&query="+$("#pages-data").data("query"), function(data) {
				$("#content-body").html(data);
			})
			.error(function() { alert("Couldn\'t load sort"); });
		}else{
			// Refresh items list by sort key
			$("#sort-label").html(obj.html());
			document.location.href='/pages?category_name='+$("#pages-data").data('category')+'&p='+$("#pages-data").data("page")+'&sort_by='+obj.data("sort")+"&query="+$("#pages-data").data('query');
		}
	},

	// Grid function
	itemGridCode: function(obj){
		// Append dialog div at end of the body if not exist
		if($("#item-grid-edit").length==0){
			$("body").append("<div id=\"item-grid-edit\"></div>");
		}

		// Open dialog with selected tab
		$("#item-grid-edit").load("dialog.php?c=pages&o=grid&id="+$(obj).parent().data('id')+"&tab=php", function() {
			$('#item-grid-edit .modal:first').modal({
				show: 'false'
			}); 
		})
		.error(function() { console.log("Dialog Error: edit"); });
	},
	itemGridDelete: function(obj){
		// We need to confirm to delete
		var r = confirm("Delete whole col?");
		if(r){
			var grid=obj.parent();
			var id=grid.data('id');
			var plugin_id=$("#layer_"+id+" select[name='plugin_id']").val();
			$.ajax({
				type: 'POST',
				url: once.path+"/ajax.php?c=pages&o=item_grid_delete&id="+id,
				success: function(data) { 
					// Del col / row if response ok
					if(data.status=='ok'){
						// Read and open edit dialog with selected tab
						$("#item-edit .modal").load("dialog.php?c=pages&o=edit&id="+$('#page-data').data("id")+"&nomodal&tab=edit_grid", function() {
								
						})
						.error(function() { console.log("Dialog Error: pages"); });
					}else{
						console.log("Action Error: "+data.error);
					}
				},
				contentType: "application/json",
				dataType: 'json'
			})
			.error(function() { console.log("Request Error: item_grid_delete"); });
		}
	},
	itemGridEdit: function(obj){
		var id=$(obj).parent().data('id');
		// Read and open edit dialog
		$("#item-grid-edit").load("dialog.php?c=pages&o=grid&id="+id, function() {
			$('#item-grid-edit .modal:first').modal({
				show: 'false'
			}); 
		})
		.error(function() { console.log("Dialog Error: edit"); });
	},
	itemGridEditDelete: function(obj){
		// We need to confirm to delete
		var r = confirm("Delete whole col?");
		if(r){
			var id=$("#grid-data").data("id");
			var plugin_id=$("#layer_"+id+" select[name='plugin_id']").val();
			$.ajax({
				type: 'POST',
				url: once.path+"/ajax.php?c=pages&o=item_grid_delete&id="+id,
				success: function(data) { 
					// Del col / row if response ok
					if(data.status=='ok'){
						// Read and open edit dialog with selected tab
						$("#item-edit .modal").load("dialog.php?c=pages&o=edit&id="+$('#page-data').data("id")+"&nomodal&tab=edit_grid", function() {
								
						})
						.error(function() { console.log("Dialog Error: pages"); });
					}else{
						console.log("Action Error: "+data.error);
					}
				},
				contentType: "application/json",
				dataType: 'json'
			})
			.error(function() { console.log("Request Error: item_grid_delete"); });
		}
	},
	itemGridEditSave: function(obj){
		// Save selected source or just item edit
		if($("#grid-data").data("tab")=='#edit_grid_source'){
			once.pages.itemGridSource();
		}else{
			$("#editGridForm").submit();
		}
	},
	itemGridLoad: function(){
		var grid = $('.grid-stack').data('gridstack');
		
		grid.remove_all();
		grid.batch_update();
		
		var items = GridStackUI.Utils.sort(once.pages.serialized_data);
		
		_.each(items, function (node) {
			str='<div id="layer_'+node.id+'" data-id="'+node.id+'"><div data-id="'+node.id+'" class="grid-stack-item-content"/>';
				// Grid content
				str+='<div class="grid-delete" title="delete id: '+node.id+'/'+node.plugin_id+'">'
					str+='<a><i class="fa fa-times"></i></a>'
				str+='</div>'
				str+='<div class="grid-visibility">'
					str+='<a><i class="fa fa-eye'+(node.hidden==1?'-slash':'')+'"></i></a>'
				str+='</div>'
				str+='<div class="grid-edit">'
					str+='<a><i class="fa fa-gear"></i></a>'
				str+='</div>'
				str+='<div class="grid-code">'
					str+='<a><i class="fa fa-code"></i></a>'
				str+='</div>'
				str+='<div class="grid-label">'
					str+=''+(node.css_id!=''?'#'+node.css_id:'')+''+(node.css_class!=''?'.'+node.css_class:'')+''
				str+='</div>'
				str+='<select name="plugin_id" class="form-control grid-select">'
					str+='<option value="0">Once Plain Code</option>'
					str+='<option disabled role=separator>'
					for(var i=0;i<once.pages.serialized_plugins.length;i++){
						str+='<option value="'+once.pages.serialized_plugins[i].id+'" '+(once.pages.serialized_plugins[i].id==node.plugin_id?'selected':'')+'>'+once.pages.serialized_plugins[i].name+'</option>'
					}
				str+='</select>'
				// End grid content
			str+='<div/>'
			grid.add_widget($(str), node.col_id, node.row_id, node.size, 1);
		}, this);

		grid.commit();
		
		// Set DOM actions
		$('.grid-code').click(function () {
			once.pages.itemGridCode($(this));
		});
		
		$('.grid-delete').click(function () {
			once.pages.itemGridDelete($(this));
		});
				
		$('.grid-edit').click(function () {
			once.pages.itemGridEdit($(this));
		});
		
		$('.grid-select').change(function () {
			once.pages.itemGridSelect($(this));
		});
		
		$('.grid-visibility').click(function () {
			once.pages.itemGridVisibility($(this));
		});

		if($("#page-data").data("switcher")){
			$("option[value=-1]").prop("disabled",true);
		}
		
	},
	itemGridNew: function(){
		// Call to create new block
		$.ajax({
			type: 'POST',
			url: once.path+"/ajax.php?c=pages&o=item_grid_new&page_id="+$("#page-data").data("id"),
			success: function(data) { 
				// Add row if response ok
				if(data.status=='ok'){
					// Get grid obj
					var grid = $('.grid-stack').data('gridstack');

					str='<div id="layer_'+data.col.id+'" data-id="'+data.col.id+'"><div class="grid-stack-item-content"/>';
						// Grid content
						str+='<div class="grid-delete" title="delete id: '+data.col.id+'">'
							str+='<a><i class="fa fa-times"></i></a>'
						str+='</div>'
						str+='<div class="grid-visibility">'
							str+='<a><i class="fa fa-eye"></i></a>'
						str+='</div>'
						str+='<div class="grid-edit">'
							str+='<a><i class="fa fa-gear"></i></a>'
						str+='</div>'
						str+='<div class="grid-code">'
							str+='<a><i class="fa fa-code"></i></a>'
						str+='</div>'
						str+='<div class="grid-label"></div>'
						str+='<select name="plugin_id" class="form-control grid-select">'
							str+='<option value="0">Once Code</option>'
							for(var i=0;i<once.pages.serialized_plugins.length;i++){
								str+='<option value="'+once.pages.serialized_plugins[i].id+'">'+once.pages.serialized_plugins[i].name+'</option>'
							}
						str+='</select>'
						// End grid content
					str+='<div/>'
					
					// Appending new block in another row
					grid.add_widget($(str), 1, 1, 1, 1);

					// Set DOM actions
					$('#layer_'+data.col.id+' .grid-code').click(function () {
						once.pages.itemGridCode($(this));
					});
					
					$('#layer_'+data.col.id+' .grid-delete').click(function () {
						once.pages.itemGridDelete($(this));
					});
							
					$('#layer_'+data.col.id+' .grid-edit').click(function () {
						once.pages.itemGridEdit($(this));
					});
					
					$('#layer_'+data.col.id+' .grid-select').change(function () {
						once.pages.itemGridSelect($(this));
					});
					
					$('#layer_'+data.col.id+' .grid-visibility').click(function () {
						once.pages.itemGridVisibility($(this));
					});
				}else{
					console.log("Action Error: "+data.error);
				}
			},
			contentType: "application/json",
			dataType: 'json'
		})
		.error(function() { console.log("Request Error: item_grid_new"); });
	},
	itemGridPreview: function(obj){
		var icon=obj.find(".fa-chevron-down");
		if(icon.length){
			icon.removeClass("fa-chevron-down");
			icon.addClass("fa-minus");
			
			$("#edit_preview").addClass("show");
			$("#edit_preview iframe").attr("src","preview.php?path=pages&file=page_"+$("#grid-data").data("page_id")+"_"+$("#grid-data").data("id")+".php");
		}else{
			var icon=obj.find(".fa-minus");
			if(icon.length){
				icon.removeClass("fa-minus");
				icon.addClass("fa-chevron-down");
				
				$("#edit_preview").removeClass("show");
			}
		}
	},
	itemGridSave : function(obj){
		var serialized_data = _.map($('.grid-stack > .grid-stack-item:visible'), function (el) {
			el = $(el);
			var node = el.data('_gridstack_node');
			return {
				id: el.data("id"),
				col_id: node.x,
				row_id: node.y,
				size: node.width
			};
		}, this);

		//console.log(JSON.stringify(serialized_data, null, '    '));
		
		$.post("ajax.php?c=pages&o=item_grid_save&page_id="+$("#page-data").data("id"), { data: JSON.stringify(serialized_data, null, '    ') }, function(data, textStatus) {
			// Check if preview is open then refresh
			// console.log("Source saved");
		}, "json")
		.error(function() { console.log("Request Error: item_grid_save"); });
	},
	itemGridSaveAs : function(obj){
		// We need to confirm to delete
		var r = confirm("Save as "+obj.data('type')+"?");
		if(r){
			var id=$("#grid-data").data("id");
			$.ajax({
				type: 'POST',
				url: once.path+"/ajax.php?c=pages&o=item_grid_save_as&type="+obj.data('type')+"&id="+id,
				success: function(data) { 
					// Del col / row if response ok
					if(data.status=='ok'){
						console.log("Saved "+data.error);
					}else{
						console.log("Action Error: "+data.error);
					}
				},
				contentType: "application/json",
				dataType: 'json'
			})
			.error(function() { console.log("Request Error: item_grid_save_as"); });
		}
	},
	itemGridSource: function(obj){
		// Save selected source
		if($("#grid-data").data("tab")=='#edit_grid_source'){
			var source = rawurlencode(once.editors[1].getValue());
			$.post("ajax.php?c=pages&o=save_source&id="+$("#grid-data").data("id")+"&page_id="+$("#grid-data").data("page_id")+"&file="+$("#grid-data").data("file"), { source: source }, function(data) {
				// Check if preview is open then refresh
				var icon=$("#grid-data .grid-preview .fa-minus");
				if(icon.length){
					$("#edit_preview iframe").attr("src","preview.php?path=pages&file=page_"+$("#grid-data").data("page_id")+"_"+$("#grid-data").data("id")+".php");
				}
				console.log("Source saved");
			})
			.error(function() { console.log("Request Error: save_source"); });
			
		}
	},
	itemGridSelect: function(obj){
		// We need to confirm to delete
		var r = confirm("Changing of plugin is going to oversave PHP/CSS/JS/AJAX/CLASS sure?");
		if(r){
			var col=obj.parent();
			var row=col.parent();

			var field=obj.attr('name');
			var value=obj.val();
			var grid=obj.parent();
			$.ajax({
				type: 'POST',
				url: once.path+"/ajax.php?c=pages&o=item_grid_select&id="+grid.data('id')+"&value="+value+"&field="+field,
				success: function(data) { 
					// Set col if response ok
					if(data.status=='ok'){
						// Read and open edit dialog with selected tab
						$("#item-edit .modal").load("dialog.php?c=pages&o=edit&id="+$('#page-data').data("id")+"&nomodal&tab=edit_grid", function() {
								
						})
						.error(function() { console.log("Dialog Error: pages"); });
						
						console.log("Plugin changed");
					}else{
						// Restore selection
						$('option[value=-1]').prop("disabled",true);
						obj.find('option[value='+data.item.old+']').prop("selected",true);
						
						console.log("Action Error: "+data.error);
					}
				},
				contentType: "application/json",
				dataType: 'json'
			})
			.error(function() { console.log("Request Error: item_grid_select"); });
		}
	},
	itemGridVisibility: function(obj){
		var grid=obj.parent();
		$.ajax({
			type: 'POST',
			url: once.path+"/ajax.php?c=pages&o=item_grid_visibility&id="+grid.data('id'),
			success: function(data) { 
				// Del col / row if response ok
				if(data.status=='ok'){
					if(data.item.hidden==0){
						obj.find("i").removeClass("fa-eye-slash");
						obj.find("i").addClass("fa-eye");
					}else{
						obj.find("i").removeClass("fa-eye");
						obj.find("i").addClass("fa-eye-slash");
					}
					//$("#layer_"+id+"").remove();
					//$("#page-data .item-close").click();
				}else{
					//console.log("Action Error: "+data.error);
				}
			},
			contentType: "application/json",
			dataType: 'json'
		})
		.error(function() { console.log("Request Error: item_grid_visibility"); });
	},
	
	// View function
	itemDelete: function(obj){//ok
		// Get varibles defined in rendering data-*
		var id=$(obj).parent().parent().data('id');
		var name=$("#item_"+id+" td.item-name").html();
		// We need to confirm to delete
		var r = confirm("Delete "+name+"?");
		if(r){
			// Call to item_delete with parm id
			$.ajax({
				type: 'POST',
				url: once.path+"/ajax.php?c=pages&o=item_delete&id="+id,
				success: function(data) { 
					// Refresh items list if response ok
					if(data.status=='ok'){
						// Intermediatly delete from dom then refresh items list
						$("#item_"+id).remove();
						// Call for refresh
						$.get(once.path+"/view.php?c=pages&o="+$("#pages-data").data("o")+"&type_id="+$("#pages-data").data("type_id")+"&category_id="+$("#pages-data").data("category_id")+"&sort_by="+$("#pages-data").data("sort_by")+"&page="+$("#pages-data").data("page")+"&ids="+$("#pages-data").data("ids")+"&query="+$("#pages-data").data("query"), function(data) {
							$("#content-body").html(data);
						})
						.error(function() { console.log("Request Error: "+$("#pages-data").data("o")); });
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
	itemCode: function(obj){//ok
		
	},
	itemEditDelete: function(obj){//ok
		// Get varibles defined in rendering data-*
		var id=$("#page-data").data('id');
		var name=$("#editForm input[name='name']").val();
		// We need to confirm to delete
		var r = confirm("Delete "+name+"?");
		if(r){
			// Call to del_layer with parm id
			$.ajax({
				type: 'POST',
				url: once.path+"/ajax.php?c=pages&o=item_delete&id="+id,
				success: function(data) { 
					// Refresh items list if response ok
					if(data.status=='ok'){
						$("#page-data .item-close").click();
						// Intermediatly delete from dom then refresh items list
						$("#item_"+id).remove();
						// Call for refresh
						$.get(once.path+"/view.php?c=pages&o="+$("#pages-data").data("o")+"&type_id="+$("#pages-data").data("type_id")+"&category_id="+$("#pages-data").data("category_id")+"&sort_by="+$("#pages-data").data("sort_by")+"&page="+$("#pages-data").data("page")+"&ids="+$("#pages-data").data("ids")+"&query="+$("#pages-data").data("query"), function(data) {
							$("#content-body").html(data);
						})
						.error(function() { console.log("Request Error: "+$("#pages-data").data("o")); });
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
	itemEditSave: function(obj){
		// Save selected source or just item edit
		if($("#page-data").data("tab")=='#edit_grid' || $("#page-data").data("tab")=='#edit_plugin'){
			once.pages.itemGridSave();
		}else{
			$("#editForm").submit();
		}
	},
	itemNew: function(obj){//ok
		// Call to item_new for new item
		$.ajax({
			type: 'POST',
			url: once.path+"/ajax.php?c=pages&o=item_new&type_id="+$("#pages-data").data('type_id')+"&category_id="+$("#pages-data").data('category_id'),
			success: function(data) { 
				// Refresh items list if response ok
				if(data.status=='ok'){
					// Call for refresh
					$.get(once.path+"/view.php?c=pages&o="+$("#pages-data").data("o")+"&type_id="+$("#pages-data").data("type_id")+"&category_id="+$("#pages-data").data("category_id"), function(data) {
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
			url: once.path+"/ajax.php?c=pages&o=item_star&id="+row.data("id"),
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
	
	
	

	

	
	itemEditContent: function(obj){
		// Save selected source
		var source = rawurlencode($(".textarea").val());
		$.post(once.path+"/ajax.php?c=pages&o=save_content&id="+$("#page-data").data("id"), { source: source }, function(data) {
			console.log("Source saved");
		})
		.error(function() { console.log("Request Error: save_content"); });
	},

}

once.pages.actions = {
	mainInit: function(obj){//ok
		once.loadJSfile(once.path+'/libs/jquery-form/jquery.form.js');

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
				once.pages.bulkAction($(this));
			});
		}
		
		// Sort actions
		$(".sort-action").click(function () {
			once.pages.sortAction($(this));
		});
		
		// Star item
		$(".item-star").click(function () {
			once.pages.itemStar($(this));
		});

		// Initialize itemEdit dialog
		once.pages.dialogs.itemEdit(".item-edit");
		
		// Initialize itemEdit code
		once.pages.dialogs.itemCode(".item-code");
		
		// Delete item
		$(".item-delete").click(function () {
			once.pages.itemDelete($(this));
		});
		
		// Set display limit
		$(".display-limit").change(function () {
			once.pages.displayLimit($(this));
		});
		
		// Get selected page
		$(".pagination a").click(function (e) {
			e.preventDefault();
			e.stopPropagation();
			once.pages.openPage($(this));
		});
		
		// Initialize checkForm
		once.pages.forms.checkForm($(this));
				
		// Initialize searchForm
		once.pages.forms.searchForm($(this));

		// Initialize / sandbox
		once.pages.initialized();
	},
	editInit: function(obj){//ok
		// Load grid library
		once.loadJSfile(once.path+'/libs/gridstack/dist/gridstack.js');
		once.loadCSSfile(once.path+'/libs/gridstack/dist/gridstack.css');	

		// After show selected tab
		$('#page-data a[data-toggle="tab"]').on('show.bs.tab', function (e) {
			var tab = $(this); // current tab
			var previous = e.relatedTarget; // previous active tab
			var previous_id = $(e.relatedTarget).data('editor');
			
			$("#page-data").data("tab",tab.attr('href'));
			
			// Load tab content
			if(tab.attr('href')=='#edit_settings'){
				$("#page-data .item-save").show();
				$("#page-data .item-grid-new").hide();
			}else if(tab.attr('href')=='#edit_grid'){
				$("#page-data .item-save").show();
				$("#page-data .item-grid-new").show();
			}else if(tab.attr('href')=='#edit_plugin'){
				$("#page-data .item-save").hide();
				$("#page-data .item-grid-new").hide();
				
				// Reset
				$("#ajax-plugin").html('');
				
				// Load UI file
				$.get(tab.attr('data-ajax'), function(data) {
					$("#ajax-plugin").html(data);
				})
				.error(function() { console.log("Request Error: edit_plugin"); });
			}
		});
		
		// Delete item
		$("#page-data .item-delete").click(function () {
			once.pages.itemEditDelete($(this));
		})
		
		// New grid col
		$("#page-data .item-grid-new").click(function () {
			once.pages.itemGridNew($(this));
		});
			
		// Save pages grid
		$("#page-data .item-save").click(function () {
			once.pages.itemEditSave($(this));
		});

		// Initialize editForm
		once.pages.forms.editForm($(this));
		
		// Set grid data and initialize 
		var options = {
			cell_height: 100,
			vertical_margin: 5,
			resizable: {autoHide: true, handles: 'e'},
			auto: false
		};
		$('.grid-stack').gridstack(options);
		once.pages.itemGridLoad();
	},
	gridInit: function(obj){//ok
		once.editors = new Array();
		
		// Code mirror for sources
		var value = "// The bindings defined specifically in the Sublime Text mode\nvar bindings = {\n";
		var map = CodeMirror.keyMap.sublime, mapK = CodeMirror.keyMap["sublime-Ctrl-K"];
		for (var key in map) {
			if (key != "Ctrl-K" && key != "fallthrough" && (!/find/.test(map[key]) || /findUnder/.test(map[key]))) value += "  \"" + key + "\": \"" + map[key] + "\",\n";
		}
		for (var key in mapK) {
			if (key != "auto" && key != "nofallthrough") value += "  \"Ctrl-K " + key + "\": \"" + mapK[key] + "\",\n";
		}
		
		value += "}\n\n// The implementation of joinLines\n";
		value += CodeMirror.commands.joinLines.toString().replace(/^function\s*\(/, "function joinLines(").replace(/\n  /g, "\n") + "\n";
	  
		once.editors.push(0);
		
		// Initialize on code window functions
		// Replace the <textarea id="code"> with an CodeMirror
		once.editors[1] = CodeMirror.fromTextArea(document.getElementById("code-playground"), {
			value: value,
			styleActiveLine: false,
			lineNumbers: true,
			lineWrapping: true,
			extraKeys: [{"Ctrl-Q": function(cm){ cm.foldCode(cm.getCursor()); }},{"Ctrl-J": "toMatchingTag"}],
			foldGutter: true,
			gutters: ["CodeMirror-linenumbers", "CodeMirror-foldgutter"],
			highlightSelectionMatches: {showToken: /\w/},
			matchTags: {bothTags: true},
			keyMap: "sublime",
			autoCloseBrackets: true,
			matchBrackets: true,
			showCursorWhenSelecting: true,
			theme: "monokai"
		});
		
		once.editors[1].getScrollerElement().style.maxHeight = "400px";
		once.editors[1].getScrollerElement().style.minHeight = "400px";

		// After show selected tab
		$('#grid-data a[data-toggle="tab"]').on('show.bs.tab', function (e) {
			var tab = $(this); // current tab
			var previous = e.relatedTarget; // previous active tab
			var previous_id = $(e.relatedTarget).data('editor');

			$("#grid-data").data("tab",tab.attr('href'));

			// Load tab content
			if(tab.attr('href')=='#edit_grid_plugin'){
				// Reset
				$("#ajax-grid-plugin").html('');
				
				// Load UI file
				$.get(tab.attr('data-ajax'), function(data) {
					$("#ajax-grid-plugin").html(data);
				})
				.error(function() { console.log("Request Error: load_edit_grid_plugin"); });
			}else if(tab.attr('href')=='#edit_grid_source'){
				// Set other settings
				$("#grid-data").data("editor",tab.attr('data-editor'));
				$("#grid-data").data("path",tab.attr('data-path'));
				$("#grid-data").data("file",tab.attr('data-file'));
				$("#grid-data").data("title",tab.html());
				
				// Refresh codemirror
				setTimeout("once.editors[1].refresh()", 1000);
				
				// Get codemirror mode
				var name=tab.html();
				var mode='';
				switch(tab.html()){
					case 'Plugin UI':
						mode= {
						name: "htmlmixed",
						scriptTypes: [{matches: /\/x-handlebars-template|\/x-mustache/i,
									   mode: null},
									  {matches: /(text|application)\/(x-)?vb(a|script)/i,
									   mode: "vbscript"}]
						};
					break;
					case 'HTML':
						mode='application/x-httpd-php';
					break;
					case 'PHP':
						mode= {
						name: "htmlmixed",
						scriptTypes: [{matches: /\/x-handlebars-template|\/x-mustache/i,
									   mode: null},
									  {matches: /(text|application)\/(x-)?vb(a|script)/i,
									   mode: "vbscript"}]
						};
					break;
					case 'CSS':
						mode='css';
					break;
					case 'JS':
						mode='javascript';
					break;
					case 'AJAX':
						mode='application/x-httpd-php';
					break;
					case 'CLASS':
						mode='application/x-httpd-php';
					break;
				}
				
				once.editors[1].focus();
				
				// Reset
				$("#code-playground").html('');

				$.ajax({
					type: 'POST',
					url: once.path+"/ajax.php?c=pages&o=load_source&id="+$("#grid-data").data("id")+"&page_id="+$("#grid-data").data("page_id")+"&file="+tab.attr('data-file'),
					success: function(data) { 
						if(data.status=='ok'){
							// Fill editor
							once.editors[1].setValue(data.item.source);
										
							// Set editor mode
							once.editors[1].setOption('mode', mode);
						}else{
							console.log("Action Error: "+data.error);
						}
					},
					contentType: "application/json",
					dataType: 'json'
				})
				.error(function() { console.log("Request Error: load_source"); });
			}
		});
		
		// After close this modal
		$(document).on("hidden.bs.modal", "#item-grid-edit", function (event) {
			$("#edit_preview").removeClass("show");
			$("#grid-data").data("tab",false);
			
			// Fix 2nd modal open
			$("body").addClass("modal-open");
		});
		
		// Active keypress 
		$("#grid-data").keypress(function(event) {
			// Ctr + S to save
			if(event.ctrlKey && event.which==115){
				event.preventDefault();
				event.stopPropagation();
				once.pages.itemGridSource();
			}
		});

		// Delete whole grid
		$("#grid-data .grid-delete").click(function () {
			once.pages.itemGridEditDelete($(this));
		});
		
		// Turn on/off preview
		$("#grid-data .grid-preview").click(function () {
			once.pages.itemGridPreview($(this));
		});

		// Save item
		$("#grid-data .grid-save").click(function () {
			once.pages.itemGridEditSave($(this));
		});
		
		// Save as plugins / snippet
		$("#grid-data .grid-save-as").click(function () {
			once.pages.itemGridSaveAs($(this));
		});
		
		// Initialize editGridForm
		once.pages.forms.editGridForm($(this));
	},
}

once.pages.dialogs = {
	itemEdit: function(obj){//ok
		// Append at end of the body
		if($("#item-edit").length==0){
			$("body").append("<div id=\"item-edit\"></div>");
		}

		// Read and open edit dialog
		$(obj).click(function (e) {
			e.preventDefault();
			e.stopPropagation();
			$("#item-edit").load(once.path+"/dialog.php?c=pages&o=edit&id="+$(this).parent().parent().data("id"), function() {
				$('#item-edit .modal:first').modal({
					show: 'false'
				}); 
			})
			.error(function() { console.log("Dialog Error: edit"); });
		});
	},
	itemCode: function(obj){//ok
		// Append at end of the body
		if($("#item-edit").length==0){
			$("body").append("<div id=\"item-edit\"></div>");
		}

		// Read and open edit dialog
		$(obj).click(function (e) {
			e.preventDefault();
			e.stopPropagation();
			$("#item-edit").load(once.path+"/dialog.php?c=pages&o=edit&id="+$(this).parent().parent().data("id")+"&tab=edit_grid", function() {
				$('#item-edit .modal:first').modal({
					show: 'false'
				}); 
			})
			.error(function() { console.log("Dialog Error: edit"); });
		});
	},
}

once.pages.forms = {
	checkForm: function(obj){//ok
		var options = {
			dataType:  "json",
			success: function(data){
				
				console.log($("#checkForm").data("type"));
				console.log($("#checkForm").data("module"));
				
				// Call for refresh
				$.get(once.path+"/view.php?c=pages&o="+$("#pages-data").data("o")+"&type_id="+$("#pages-data").data("type_id")+"&category_id="+$("#pages-data").data("category_id")+"&sort_by="+$("#pages-data").data("sort_by")+"&page="+$("#pages-data").data("page")+"&ids="+$("#pages-data").data("ids")+"&query="+$("#pages-data").data("query"), function(data) {
					$("#content-body").html(data);
				})
				.error(function() { console.log("Request Error: "+$("#pages-data").data("o")); });
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
		$("#editForm").attr("action",once.path+"/ajax.php?c=pages&o=item_edit&id="+$("#page-data").data("id"));
		var options = {
			dataType:  "json",
			success: function(data){
				// Update title & layer_id & route_id on items list
				if(data.status=='ok'){
					// Get new data
					var title=$("#editForm input[name='title']");
					var layer_id=$("#editForm select[name='layer_id'] option:selected");
					var route_id=$("#editForm select[name='route_id'] option:selected");
					
					// Update DOM
					$("tr[data-id='"+data.item.id+"'] td[data-link='title']").html(title.val());
					$("tr[data-id='"+data.item.id+"'] td[data-link='layer_id']").html(layer_id.text());
					$("tr[data-id='"+data.item.id+"'] td[data-link='route_id']").html(route_id.data("name"));

					console.log("Page list updated");
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
	editGridForm: function(obj){//ok
		$("#editGridForm").attr("action",once.path+"/ajax.php?c=pages&o=item_grid_edit&id="+$("#grid-data").data("id"));
		var options = {
		dataType:  "json",
			success: function(data){
				// Change label if response ok
				if(data.status=='ok'){
					$(".grid-change option[value='"+data.item.id+"']").html(data.item.name);
					if(data.item.default==1){
						$("#grid-data .grid-delete").attr("disabled",true);
						$("#editGridForm option[value=0]").attr("disabled",true);
					}
				}else{
					console.log("Action Error: "+data.error);
				}
			},
			complete: function(data){
				//console.log(data.responseText);
			},
			error: function(){
				console.log("Form Error: editGridForm");
			}
		}; 
		$("#editGridForm").ajaxForm(options);
		
		setTimeout(function(){
			$("#editGridForm input[name=\"name\"]").focus();
		},1000);
	},
	searchForm: function(obj){//ok
		$("#searchForm").attr("action",once.path+"/view.php?c=pages&o="+$("#pages-data").data("o"));
		var options = {
			complete: function(data){
				$("#content-body").html(data.responseText);
			},
		};
		$("#searchForm").ajaxForm(options);
	},
}

$(document).ready(function () {
	// Initialize onclick action
	$(".item-new").click(function () {
		once.pages.itemNew($(this));
    });

	// Initialize types & actions
	if($("#types-data").length){
		once.loadJSfile(once.path+'/js/once.types.js');
	}
	
	// Load libraries & modes
	if($("#page-data").data("ajax") || $("#pages-data").data("ajax")){
		once.loadJSfile(once.path+'/libs/jquery-form/jquery.form.js');
		//once.loadJSfile('//oss.maxcdn.com/jquery.form/3.50/jquery.form.min.js');
	}
	
	// Initialize pages actions
	if($("#pages-data").length>0){
		// Sort actions
		$("#pages-data .sort-action").click(function () {
			once.pages.sortAction($(this));
		});
		
		// Initialize searchForm if its ajax only
		if($("#pages-data").data("ajax")){
			once.pages.forms.searchForm($(this));
		}
		
		if(once.cms){
			setTimeout(function(){
				// Load code mirror library & modes
				once.loadJSfile(once.path+'/libs/codemirror/libs/codemirror.js');
				once.loadJSfile(once.path+'/libs/codemirror/mode/xml/xml.js');
				once.loadJSfile(once.path+'/libs/codemirror/mode/javascript/javascript.js');
				once.loadJSfile(once.path+'/libs/codemirror/mode/css/css.js');
				once.loadJSfile(once.path+'/libs/codemirror/mode/vbscript/vbscript.js');
				once.loadJSfile(once.path+'/libs/codemirror/mode/htmlmixed/htmlmixed.js');
				once.loadJSfile(once.path+'/libs/codemirror/mode/clike/clike.js');
				once.loadJSfile(once.path+'/libs/codemirror/mode/php/php.js');
				once.loadJSfile(once.path+'/libs/codemirror/mode/markdown/markdown.js');
				once.loadJSfile(once.path+'/libs/codemirror/addon/comment/comment.js');
				once.loadJSfile(once.path+'/libs/codemirror/addon/dialog/dialog.js');
				once.loadJSfile(once.path+'/libs/codemirror/addon/edit/matchtags.js');
				once.loadJSfile(once.path+'/libs/codemirror/addon/edit/matchbrackets.js');
				once.loadJSfile(once.path+'/libs/codemirror/addon/fold/foldcode.js');
				once.loadJSfile(once.path+'/libs/codemirror/addon/fold/foldgutter.js');
				once.loadJSfile(once.path+'/libs/codemirror/addon/fold/brace-fold.js');
				once.loadJSfile(once.path+'/libs/codemirror/addon/fold/xml-fold.js');
				once.loadJSfile(once.path+'/libs/codemirror/addon/fold/markdown-fold.js');
				once.loadJSfile(once.path+'/libs/codemirror/addon/fold/comment-fold.js');
				once.loadJSfile(once.path+'/libs/codemirror/addon/hint/show-hint.js');
				once.loadJSfile(once.path+'/libs/codemirror/addon//tern/tern.js');
				once.loadJSfile(once.path+'/libs/codemirror/addon/search/match-highlighter.js');
				once.loadJSfile(once.path+'/libs/codemirror/addon/search/search.js');
				once.loadJSfile(once.path+'/libs/codemirror/addon/search/searchcursor.js');
				once.loadJSfile(once.path+'/libs/codemirror/addon/selection/active-line.js');
				once.loadJSfile(once.path+'/libs/codemirror/addon/wrap/hardwrap.js');
				once.loadJSfile(once.path+'/libs/codemirror/keymap/sublime.js');

				// Load code mirror styles
				once.loadCSSfile(once.path+'/libs/codemirror/libs/codemirror.css');
				once.loadCSSfile(once.path+'/libs/codemirror/addon/fold/foldgutter.css');
				once.loadCSSfile(once.path+'/libs/codemirror/addon/dialog/dialog.css');
				once.loadCSSfile(once.path+'/libs/codemirror/theme/monokai.css');
			}, 3000);
		}
	}
	
	// Initialize page actions
	if($("#page-data").length){
		once.loadJSfile(once.path+'/libs/jquery-validation/dist/jquery.validate.js');
		//once.loadJSfile('http://ajax.aspnetcdn.com/ajax/jquery.validate/1.11.1/jquery.validate.js');

		// Load code mirror library & modes
		once.loadJSfile(once.path+'/libs/codemirror/libs/codemirror.js');
		once.loadJSfile(once.path+'/libs/codemirror/mode/xml/xml.js');
		once.loadJSfile(once.path+'/libs/codemirror/mode/javascript/javascript.js');
		once.loadJSfile(once.path+'/libs/codemirror/mode/css/css.js');
		once.loadJSfile(once.path+'/libs/codemirror/mode/vbscript/vbscript.js');
		once.loadJSfile(once.path+'/libs/codemirror/mode/htmlmixed/htmlmixed.js');
		once.loadJSfile(once.path+'/libs/codemirror/mode/clike/clike.js');
		once.loadJSfile(once.path+'/libs/codemirror/mode/php/php.js');
		once.loadJSfile(once.path+'/libs/codemirror/mode/markdown/markdown.js');
		once.loadJSfile(once.path+'/libs/codemirror/addon/comment/comment.js');
		once.loadJSfile(once.path+'/libs/codemirror/addon/dialog/dialog.js');
		once.loadJSfile(once.path+'/libs/codemirror/addon/edit/matchtags.js');
		once.loadJSfile(once.path+'/libs/codemirror/addon/edit/matchbrackets.js');
		once.loadJSfile(once.path+'/libs/codemirror/addon/fold/foldcode.js');
		once.loadJSfile(once.path+'/libs/codemirror/addon/fold/foldgutter.js');
		once.loadJSfile(once.path+'/libs/codemirror/addon/fold/brace-fold.js');
		once.loadJSfile(once.path+'/libs/codemirror/addon/fold/xml-fold.js');
		once.loadJSfile(once.path+'/libs/codemirror/addon/fold/markdown-fold.js');
		once.loadJSfile(once.path+'/libs/codemirror/addon/fold/comment-fold.js');
		once.loadJSfile(once.path+'/libs/codemirror/addon/hint/show-hint.js');
		once.loadJSfile(once.path+'/libs/codemirror/addon//tern/tern.js');
		once.loadJSfile(once.path+'/libs/codemirror/addon/search/match-highlighter.js');
		once.loadJSfile(once.path+'/libs/codemirror/addon/search/search.js');
		once.loadJSfile(once.path+'/libs/codemirror/addon/search/searchcursor.js');
		once.loadJSfile(once.path+'/libs/codemirror/addon/selection/active-line.js');
		once.loadJSfile(once.path+'/libs/codemirror/addon/wrap/hardwrap.js');
		once.loadJSfile(once.path+'/libs/codemirror/keymap/sublime.js');

		// Load code mirror styles
		once.loadCSSfile(once.path+'/libs/codemirror/libs/codemirror.css');
		once.loadCSSfile(once.path+'/libs/codemirror/addon/fold/foldgutter.css');
		once.loadCSSfile(once.path+'/libs/codemirror/addon/dialog/dialog.css');
		once.loadCSSfile(once.path+'/libs/codemirror/theme/monokai.css');
		
		once.pages.actions.editInit();
	}

	// Initialize / sandbox
	once.pages.initialized();
});