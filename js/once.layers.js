/**
 * Version: 1.0, 31.05.2012
 * by Adam Wysocki, support@oncebuilder.com
 *
 * Copyright (c) 2012 Adam Wysocki
 *
 *	This is OnceBuilder Layers plugin (once.layers)
 *
*/

once.layers = {
	loaded: false,
	serialized_plugins: [],
	serialized_data: [],
	initialized: function(){
		this.loaded=true;
	},
	
	// Grid initialize
	setPluginsData: function(data){
		this.serialized_plugins=data;
	},
	setLayersData: function(data){
		this.serialized_data=data;
	},
	setTab: function(obj){
		if(obj!=''){
			$(".nav-tabs a[data-tab='"+obj+"']").click();
		}
	},
	
	// View function
	itemChange: function(obj){//ok
		$.get(once.path+"/view.php?c=layers&o=grid&id="+obj.val(), function(data) {
			$("#content-body").html(data);
		})
		.error(function() { console.log("Request Error: grid"); });
	},
	itemCopyTo: function(obj){//ok
		// We need to confirm to copy
		var r = confirm("Copy all elements to selected layer?");
		if(r){
			$.ajax({
				type: 'POST',
				url: once.path+"/ajax.php?c=layers&o=item_copy&layer_id="+$("#layers-data").data("id")+"&layer_id_to="+$(obj).data("id"),
				success: function(data) { 
					// Refresh items list if response ok
					if(data.status=='ok'){
						console.log("Grid copied");
					}else{
						console.log("Action Error: "+data.error);
					}
				},
				contentType: "application/json",
				dataType: 'json'
			})
			.error(function() { console.log("Request Error: item_copy"); });
		}
	},
	itemDownload: function(){//ok
		document.location.href=once.path+"/ajax.php?c=layers&o=item_grid_download&layer_id="+$("#layers-data").data("id")+"";
	},
	itemEdit: function(){//ok
		// Append dialog div at end of the body if not exist
		if($("#item-edit").length==0){
			$("body").append("<div id=\"item-edit\"></div>");
		}
		
		// Load dialog
		$("#item-edit").load("dialog.php?c=layers&o=edit&id="+$("#layers-data").data("id"), function() {
			$("#item-edit .modal:first").modal({
				show: "false"
			}); 
		})
		.error(function() { console.log("Dialog Error: item"); });
	},
	itemEditDelete: function(obj){//ok
		// We need to confirm to delete
		var r = confirm("Delete whole layer?");
		if(r){
			// Call to create new layer set
			$.ajax({
				type: 'POST',
				url: once.path+"/ajax.php?c=layers&o=item_delete&id="+$("#layer-data").data("id"),
				success: function(data) { 
					// Refresh page if response ok
					if(data.status=='ok'){
						$("#layer-data .item-close").click();
						// Refresh content
						$.get(once.path+"/view.php?c=layers&o=grid&id="+$(".grid-change option:first").val(), function(data) {
							$("#content-body").html(data);
						})
						.error(function() { console.log("Request Error: grid"); });
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
		$("#editForm").submit();
	},
	itemNew: function(){//ok
		// Call to create new grid
		$.ajax({
			type: 'POST',
			url: once.path+"/ajax.php?c=layers&o=item_new",
			success: function(data) { 
				// Refresh page if response ok
				if(data.status=='ok'){
					$.get(once.path+"/view.php?c=layers&o=grid&id="+data.item.id, function(data) {
						$("#content-body").html(data);
					})
					.error(function() { console.log("Request Error: grid"); });
				}else{
					console.log("Action Error: "+data.error);
				}
			},
			contentType: "application/json",
			dataType: 'json'
		})
		.error(function() { console.log("Request Error: item_new"); });
	},

	// Grid items function
	itemGridCode: function(obj){//ok
		// Append dialog div at end of the body if not exist
		if($("#item-grid-edit").length==0){
			$("body").append("<div id=\"item-grid-edit\"></div>");
		}
		
		// Open dialog with selected tab
		$("#item-grid-edit").load("dialog.php?c=layers&o=grid&id="+$(obj).parent().data('id')+"&tab=php", function() {
			$('#item-grid-edit .modal:first').modal({
				show: 'false'
			}); 
		})
		.error(function() { console.log("Dialog Error: edit"); });
	},
	itemGridDelete: function(obj){//ok
		// We need to confirm to delete
		var r = confirm("Delete whole element?");
		if(r){
			var grid=obj.parent();
			// Call to delete element
			$.ajax({
				type: 'POST',
				url: once.path+"/ajax.php?c=layers&o=item_grid_delete&id="+grid.data('id'),
				success: function(data) { 
					// Del col / row if response ok
					if(data.status=='ok'){
						grid.remove();
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
	itemGridEdit: function(obj){//ok
		// Append dialog div at end of the body if not exist
		if($("#item-grid-edit").length==0){
			$("body").append("<div id=\"item-grid-edit\"></div>");
		}
		
		// Load dialog
		$("#item-grid-edit").load("dialog.php?c=layers&o=grid&id="+$(obj).parent().data('id'), function() {
			$('#item-grid-edit .modal:first').modal({
				show: 'false'
			}); 
		})
		.error(function() { console.log("Dialog Error: edit"); });
	},
	itemGridEditCopy: function(obj){//ok
		// Call copy element to another layer set
		$.ajax({
			type: 'POST',
			url: once.path+"/ajax.php?c=layers&o=item_grid_copy&id="+$("#grid-data").data('id')+"&layer_id="+obj.data('id'),
			success: function(data) { 
				// If response ok
				if(data.status=='ok'){
					console.log("Action Ok");
				}else{
					//console.log("Action Error: "+data.error);
				}
			},
			contentType: "application/json",
			dataType: 'json'
		})
		.error(function() { console.log("Request Error: item_grid_copy"); });
	},
	itemGridEditDelete: function(obj){//ok
		// We need to confirm to delete
		var r = confirm("Delete element?");
		if(r){
			var id=$("#grid-data").data("id");
			$.ajax({
				type: 'POST',
				url: once.path+"/ajax.php?c=layers&o=item_grid_delete&id="+id,
				success: function(data) { 
					// Del col / row if response ok
					if(data.status=='ok'){
						$("#layer_"+id+"").remove();
						$("#grid-data .item-close").click();
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
	itemGridEditPreview: function(obj){
		var icon=obj.find(".fa-chevron-down");
		if(icon.length){
			icon.removeClass("fa-chevron-down");
			icon.addClass("fa-minus");
			
			$("#edit_grid_preview").addClass("show");
			$("#edit_grid_preview iframe").attr("src","preview.php?path=layers&file=layer_"+$("#grid-data").data("id")+".php");
		}else{
			var icon=obj.find(".fa-minus");
			if(icon.length){
				icon.removeClass("fa-minus");
				icon.addClass("fa-chevron-down");
				
				$("#edit_grid_preview").removeClass("show");
			}
		}
	},
	itemGridEditSave: function(obj){//ok
		// Save selected source or just item edit
		if($("#grid-data").data("tab")=='#edit_grid_source'){
			once.layers.itemGridEditSource();
		}else{
			$("#editGridForm").submit();
		}
	},
	itemGridEditSaveAs : function(obj){//ok
		// We need to confirm to delete
		var r = confirm("Save as "+obj.data('type')+"?");
		if(r){
			var id=$("#grid-data").data("id");
			// Call to item_grid_save_as
			$.ajax({
				type: 'POST',
				url: once.path+"/ajax.php?c=layers&o=item_grid_save_as&type="+obj.data('type')+"&id="+id,
				success: function(data) {
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
	itemGridEditSource: function(obj){//ok
		// Save selected source
		if($("#grid-data").data("tab")=='#edit_grid_source'){
			var source = rawurlencode(once.editors[1].getValue());
			$.post(once.path+"/ajax.php?c=layers&o=save_source&id="+$("#grid-data").data("id")+"&page_id="+$("#grid-data").data("page_id")+"&path="+$("#grid-data").data("path")+"&file="+$("#grid-data").data("file")+"&title="+$("#grid-data").data("title"), { source: source }, function(data) {
				// Check if preview is open then refresh
				var icon=$("#grid-data .item-preview .fa-minus");
				if(icon.length){
					$("#edit_grid_preview iframe").attr("src","preview.php?path=layers&file=layer_"+$("#grid-data").data("id")+".php");
				}
				console.log("Source saved");
			})
			.error(function() { console.log("Request Error: save_source"); });
		}
	},
	itemGridLoad: function(){//ok
		var grid = $('.grid-stack').data('gridstack');
		if(grid!==undefined){
			grid.remove_all();
			grid.batch_update();
			
			var items = GridStackUI.Utils.sort(once.layers.serialized_data);

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
						str+='<option value="-1" '+(node.plugin_id==-1?'selected':'')+'>Once Page Route</option>'
						str+='<option disabled role=separator>'
						if(once.layers.serialized_plugins.length>0){
							for(var i=0;i<once.layers.serialized_plugins.length;i++){ 
								str+='<option value="'+once.layers.serialized_plugins[i].id+'" '+(once.layers.serialized_plugins[i].id==node.plugin_id?'selected':'')+'>'+once.layers.serialized_plugins[i].name+'</option>'
							}
						}
					str+='</select>'
					// End grid content
				str+='<div/>'
				grid.add_widget($(str), node.col_id, node.row_id, node.size, 1);
			}, this);

			grid.commit();
			
			// Set DOM actions
			$('.grid-code').click(function () {
				once.layers.itemGridCode($(this));
			});
			
			$('.grid-delete').click(function () {
				once.layers.itemGridDelete($(this));
			});
					
			$('.grid-edit').click(function () {
				once.layers.itemGridEdit($(this));
			});
			
			$('.grid-select').change(function () {
				once.layers.itemGridSelect($(this));
			});
			
			$('.grid-visibility').click(function () {
				once.layers.itemGridVisibility($(this));
			});
			
			if($("#layers-data").data("switcher")){
				$("option[value=-1]").prop("disabled",true);
			}
		}else{
			console.log("Action Error: grid is empty.");
		}
		
	},
	itemGridNew: function(){//ok
		// Call to create new element
		$.ajax({
			type: 'POST',
			url: once.path+"/ajax.php?c=layers&o=item_grid_new&layer_id="+$("#layers-data").data("id"),
			success: function(data) { 
				// Add row if response ok
				if(data.status=='ok'){
					// Get grid obj
					var grid = $('.grid-stack').data('gridstack');

					str='<div id="layer_'+data.item.id+'" data-id="'+data.item.id+'"><div class="grid-stack-item-content"/>';
						// Grid content
						str+='<div class="grid-delete" title="delete id: '+data.item.id+'">'
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
							str+='<option value="-1" '+($("#layers-data").data("switcher")==true?'disabled':'')+'>Once Page</option>'
							if(once.layers.serialized_plugins.length>0){
								for(var i=0;i<once.layers.serialized_plugins.length;i++){
									str+='<option value="'+once.layers.serialized_plugins[i].id+'">'+once.layers.serialized_plugins[i].name+'</option>'
								}
							}
						str+='</select>'
						// End grid content
					str+='<div/>'
					
					// Appending new element
					grid.add_widget($(str), 1, 1, 1, 1);
				
					// Set DOM actions
					$('#layer_'+data.item.id+' .grid-code').click(function () {
						once.layers.itemGridCode($(this));
					});
					
					$('#layer_'+data.item.id+' .grid-delete').click(function () {
						once.layers.itemGridDelete($(this));
					});
							
					$('#layer_'+data.item.id+' .grid-edit').click(function () {
						once.layers.itemGridEdit($(this));
					});
					
					$('#layer_'+data.item.id+' .grid-select').change(function () {
						once.layers.itemGridSelect($(this));
					});
					
					$('#layer_'+data.item.id+' .grid-visibility').click(function () {
						once.layers.itemGridVisibility($(this));
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
	itemGridSave: function(){//ok
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
		
		$.post(once.path+"/ajax.php?c=layers&o=item_grid_save&id="+$("#layers-data").data("id"), { data: JSON.stringify(serialized_data, null, '    ') }, function(data, textStatus) {
			// Check if preview is open then refresh
			// console.log("Source saved");
		}, "json")
		.error(function() { console.log("Request Error: item_grid_save"); });
	},
	itemGridSelect: function(obj){//ok
		// We need to confirm to delete
		var r = confirm("Changing of plugin is going to oversave PHP/CSS/JS/AJAX/CLASS sure?");
		if(r){
			var col=obj.parent();
			var row=col.parent();

			var field=obj.attr('name');
			var plugin_id=obj.val();
			var grid=obj.parent();
			// Call to create new layer set
			$.ajax({
				type: 'POST',
				url: once.path+"/ajax.php?c=layers&o=item_grid_select&id="+grid.data('id')+"&plugin_id="+plugin_id,
				success: function(data) { 
					// Set col if response ok
					if(data.status=='ok'){
						// Remove disabled if it was page content
						if(data.item.old==-1){
							$('option[value=-1]').prop("disabled",false);
						}
						
						// Make it disabled if selected page content
						if(field=='plugin_id' && plugin_id==-1){
							$('option[value=-1]').prop("disabled",true);
						}
						
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
	itemGridVisibility: function(obj){//ok
		// Call to show/hide element
		$.ajax({
			type: 'POST',
			url: once.path+"/ajax.php?c=layers&o=item_grid_visibility&id="+obj.parent().data('id'),
			success: function(data) { 
				//If response ok
				if(data.status=='ok'){
					if(data.item.hidden==0){
						obj.find("i").removeClass("fa-eye-slash");
						obj.find("i").addClass("fa-eye");
					}else{
						obj.find("i").removeClass("fa-eye");
						obj.find("i").addClass("fa-eye-slash");
					}
				}else{
					console.log("Action Error: "+data.error);
				}
			},
			contentType: "application/json",
			dataType: 'json'
		})
		.error(function() { console.log("Request Error: item_grid_visibility"); });
	},
}

once.layers.actions = {
	mainInit: function(obj){
		// Load grid library
		//once.loadJSfile(once.path+'/libs/gridstack/dist/gridstack.js');
		//once.loadCSSfile(once.path+'/libs/gridstack/dist/gridstack.css');	
		
		var options = {
			cell_height: 100,
			vertical_margin: 5,
			resizable: {autoHide: true, handles: 'e'},
			auto: false
		};
		$('.grid-stack').gridstack(options);
		once.layers.itemGridLoad();

		// Download whole grid
		$(".item-copy-to").click(function () {
			once.layers.itemCopyTo($(this));
		});
		
		// Change grid after select
		$(".item-change").change(function () {
			once.layers.itemChange($(this));
		});
		
		// Download whole grid
		$(".item-download").click(function () {
			once.layers.itemDownload($(this));
		});

		// Adding new col
		$(".item-edit").click(function () {
			once.layers.itemEdit($(this));
		});
		
		// Adding new col
		$(".item-new").click(function () {
			once.layers.itemGridNew($(this));
		});
			
		// Save layers grid
		$(".item-save").click(function () {
			once.layers.itemGridSave($(this));
		});
		
		// Initialize / sandbox
		once.layers.initialized();
	},
	editInit: function(obj){
		// Turn on/off preview
		$("#layer-data .item-save").click(function () {
			once.layers.itemEditSave($(this));
		});
		
		// Delete whole grid
		$("#layer-data .item-delete").click(function () {
			once.layers.itemEditDelete($(this));
		});
		
		// Initialize editForm
		once.layers.forms.editForm($(this));
	},
	gridInit: function(obj){
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
			}else if(tab.attr('href')=='#edit_grid_theme'){
				// Reset
				$("#ajax-grid-theme").html('');
				
				// Load UI file
				$.get(tab.attr('data-ajax'), function(data) {
					$("#ajax-grid-theme").html(data);
				})
				.error(function() { console.log("Request Error: load_edit_grid_theme"); });
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
					case 'Html HEAD':
						mode= {
						name: "htmlmixed",
						scriptTypes: [{matches: /\/x-handlebars-template|\/x-mustache/i,
									   mode: null},
									  {matches: /(text|application)\/(x-)?vb(a|script)/i,
									   mode: "vbscript"}]
						};
					break;
					case 'Global CSS':
						mode='css';
					break;
					case 'Main JS':
						mode='javascript';
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
				}

				once.editors[1].focus();
				
				// Reset
				$("#code-playground").html('');
				
				$.ajax({
					type: 'POST',
					url: once.path+"/ajax.php?c=layers&o=load_source&id="+$("#grid-data").data("id")+"&file="+tab.attr('data-file'),
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
		$(document).on("hidden.bs.modal", "#item-edit", function (event) {
			$("#edit_grid_preview").removeClass("show");
			$("#grid-data").data("source",false);
		});
		
		// Active keypress 
		$("#grid-data").keypress(function(event) {
			// Ctr + S to save
			if(event.ctrlKey && event.which==115){
				event.preventDefault();
				event.stopPropagation();
				once.layers.itemGridEditSource();
			}
		});

		// Turn on/off preview
		$("#grid-data .item-copy").click(function () {
			once.layers.itemGridEditCopy($(this));
		});
		
		// Delete whole grid
		$("#grid-data .item-delete").click(function () {
			once.layers.itemGridEditDelete($(this));
		});
		
		// Turn on/off preview
		$("#grid-data .item-preview").click(function () {
			once.layers.itemGridEditPreview($(this));
		});

		// Save item
		$("#grid-data .item-save").click(function () {
			once.layers.itemGridEditSave($(this));
		});
		
		// Save as plugins / snippet
		$("#grid-data .item-save-as").click(function () {
			once.layers.itemGridEditSaveAs($(this));
		});
		
		// Initialize editGridForm
		once.layers.forms.editGridForm($(this));
	},
}

once.layers.forms = {
	editForm: function(obj){
		$("#editForm").attr("action",once.path+"/ajax.php?c=layers&o=item_edit&id="+$("#layer-data").data("id"));
		var options = {
		dataType:  "json",
			success: function(data){
				// Change label if response ok
				if(data.status=='ok'){
					$(".item-change option[value='"+data.item.id+"']").html(data.item.name);
					if(data.item.default==1){
						$("#layer-data .grid-delete").attr("disabled",true);
						$("#editForm option[value=0]").attr("disabled",true);
					}
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
		},700);
	},
	editGridForm: function(obj){
		$("#editGridForm").attr("action",once.path+"/ajax.php?c=layers&o=item_grid_edit&id="+$("#grid-data").data("id"));
		var options = {
		dataType:  "json",
			success: function(data){
				// if response ok
				if(data.status=='ok'){
					$("#layer_"+$("#grid-data").data('id')+" .item-label").html((data.item.item_id!=''?'#'+data.item.item_id:'')+''+(data.item.item_class!=''?'.'+data.item.item_class:''));
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
}

$(document).ready(function () {
	
	$("#item-set-new").click(function () {
		once.layers.itemNew($(this));
    });

	// Initialize / sandbox
	once.layers.initialized();
});