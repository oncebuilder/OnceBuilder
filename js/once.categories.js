/**
 * Version: 1.0, 29.07.2015
 * by Adam Wysocki, support@oncebuilder.com
 *
 * Copyright (c) 2015 Adam Wysocki
 *
 *	This is OnceBuilder Categories plugin (once.categories)
 *
*/

once.categories = {
	loaded: false,
	initialized: function(){
		this.loaded=true;
	},
	// Load category
	categoryLoad: function(obj){
		// Remove all highlights
		$(".list-group-item").removeClass("current");
		// Highlight selected object
		obj.addClass("current");
		var c=$("div[data-c]");
		// Call for items list with selected category
		$.get(once.path+"/view.php?c="+$(c).data("c")+"&o="+$(c).data("o")+"&category_id="+obj.data("id"), function(data) {
			$("#content-body").html(data);
		})
		.error(function() { console.log("Request Error: "+$(c).data("o")); });
	},
	// Add new category
	categoryNew: function(obj){
		// Get c name
		var c=$("div[data-c]");
		// Call to create new category
		$.getJSON(once.path+"/ajax.php?c=core&o=category_new&module="+$(c).data("c"), function(data) {
			var id=data.item.id;
			// Call to open new category then click edit it.
			$.get(once.path+"/view.php?c=categories&o=list&module="+$(c).data("c")+"&id="+id, function(data) {
				$("#categories-data").html(data);
				// Call for items list with selected category
				$(".list-group-item.current").click();
				$(".list-group-item.current .category-edit").click();
			})
			.error(function() { console.log("Request Error: categories"); });
		})
		.error(function() { console.log("Request Error: category_new"); });
	},
	// Delete category
	categoryDelete: function(obj){
		// Get c name
		var c=$("div[data-c]");
		// We need to confirm to set it default
		var r = confirm("Delete this root category?");
		if(r){
			var li=obj.parent().parent();
			$.getJSON(once.path+"/ajax.php?c=core&o=category_delete&id="+li.data("id")+"&module="+$(c).data("c")+"", function(data) {
				// Check if its current then click or load it directly
				if(li.hasClass("current")){
					if(li.parent().find('li').length>0){
						$(".list-group-item:first").click();
					}else{
						$.get(once.path+"/view.php?c=categories&o=list&id=0", function(data) {
							$("#content-body").html(data);
						});
					}
					li.remove();
				}else{
					li.remove();
				}
			})
			.error(function() { console.log("Request Error: category_delete"); });
		}
	},

	// Add new root category 
	addRootFirstCategory: function(obj){
		var li=obj.parent();
		var ul=li.parent();
		// Get c name
		var c=$("div[data-c]");
		// Call to create new category then edit it
		$.getJSON("ajax.php?c=core&o=category_new&module="+$(c).data("c")+"&id="+$("#category-data").data("id"), function(data) {
			// Set new category if response ok
			if(data.status=='ok'){
				var str='';
				str+='<li id="category_0" data-id="'+data.item.parent_id+'" data-parent_id="'+$("#category-data").data("id")+'" data-title="root">'
					str+='<ul id="menu" class="nav nav-list tree">'
						str+='<li id="category_'+data.item.id+'" data-id="'+data.item.id+'" data-parent_id="'+data.item.parent_id+'" data-name="'+data.item.name+'">'
							str+='<a><b>'+data.item.name+'</b>'
								str+='<span>'
									str+='<i title="edit category" class="fa fa-edit"></i>'
									str+='<i title="create relative category" class="fa fa-level-down"></i>'
									str+='<i title="create sub category" class="fa fa-plus"></i>'
									str+='<i title="del category" class="fa fa-minus"></i>'
								str+='</span>'
							str+='</a>'
						str+='</li>'
					str+='</ul>'
				str+='</li>'
				ul.html(str);
				
				// Set DOM actions
				$('li[data-id="'+data.item.id+'"] .fa-level-down').click(function () {
					once.categories.addRootCategory($(this));
				});
				$('li[data-id="'+data.item.id+'"] .fa-plus').click(function () {
					once.categories.addRootSubcategory($(this));
				});
				$('li[data-id="'+data.item.id+'"] .fa-minus').click(function () {
					once.categories.delRootCategory($(this));
				});
				$('li[data-id="'+data.item.id+'"] .fa-edit').click(function () {
					$("#item-edit").load("dialog.php?c=categories&o=subedit&id="+$(this).parent().parent().parent().data('id'), function() {
						$('#item-edit .modal').modal({
							show: 'false'
						}); 
					});
				});
				$('li[data-id="'+data.item.id+'"] .fa-edit').click();
			}else{
				console.log("Action Error: "+data.error);
			}
		})
		.error(function() { console.log("Request Error: category_new"); });
	},
	// Add new root category
	addRootCategory: function(obj){
		var li=obj.parent().parent().parent();
		var ul=li.parent();
		// Get c name
		var c=$("div[data-c]");
		// Call to create new category then edit it
		$.getJSON("ajax.php?c=core&o=category_new&module="+$(c).data("c")+"&id="+li.data('parent_id'), function(data) {
			// Set new category if response ok
			if(data.status=='ok'){
				var str='';
				str+='<li id="category_'+data.item.id+'" data-id="'+data.item.id+'" data-parent_id="'+data.item.parent_id+'" data-name="'+data.item.name+'">'
					str+='<a><b>'+data.item.name+'</b>'
						str+='<span>'
							str+='<i title="edit category" class="fa fa-edit"></i>'
							str+='<i title="create relative category" class="fa fa-level-down"></i>'
							str+='<i title="create sub category" class="fa fa-plus"></i>'
							str+='<i title="del category" class="fa fa-minus"></i>'
						str+='</span>'
					str+='</a>'
				str+='</li>'
				ul.append(str);

				// Set DOM actions
				$('li[data-id="'+data.item.id+'"] .fa-level-down').click(function () {
					once.categories.addRootCategory($(this));
				});
				$('li[data-id="'+data.item.id+'"] .fa-plus').click(function () {
					once.categories.addRootSubcategory($(this));
				});
				$('li[data-id="'+data.item.id+'"] .fa-minus').click(function () {
					once.categories.delRootCategory($(this));
				});
				$('li[data-id="'+data.item.id+'"] .fa-edit').click(function () {
					$("#item-edit").load("dialog.php?c=categories&o=subedit&id="+$(this).parent().parent().parent().data('id'), function() {
						$('#item-edit .modal').modal({
							show: 'false'
						}); 
					});
				});
				$('li[data-id="'+data.item.id+'"] .fa-edit').click();
			}else{
				console.log("Action Error: "+data.error);
			}
		})
		.error(function() { console.log("Request Error: category_new"); });
	},
	// Add new root subcategory
	addRootSubcategory: function(obj){
		var li=obj.parent().parent().parent();
		var ul=li.parent();
		// Get c name
		var c=$("div[data-c]");
		// Call to create new category then edit it
		$.getJSON("ajax.php?c=core&o=category_new&module="+$(c).data("c")+"&id="+li.data('id'), function(data) {
			// Set new category if response ok
			if(data.status=='ok'){
				var str='';
				str+='<li id="category_'+data.item.id+'" data-id="'+data.item.id+'" data-parent_id="'+data.item.parent_id+'" data-name="'+data.item.name+'">'
					str+='<a><b>'+data.item.name+'</b>'
						str+='<span>'
							str+='<i title="edit category" class="fa fa-edit"></i>'
							str+='<i title="create relative category" class="fa fa-level-down"></i>'
							str+='<i title="create sub category" class="fa fa-plus"></i>'
							str+='<i title="del category" class="fa fa-minus"></i>'
						str+='</span>'
					str+='</a>'
				str+='</li>'
				
				// Check if need to create new ul list else just append li
				if(li.find("ul:first").length==0){
					li.html(li.html()+'<ul class="nav nav-list tree"></ul>');
					li.find("ul:first").append(str);
					
					// Set DOM actions and click to edit
					$('li[data-id="'+data.item.parent_id+'"] .fa-level-down').click(function () {
						once.categories.addRootCategory($(this));
					});
					$('li[data-id="'+data.item.parent_id+'"] .fa-plus').click(function () {
						once.categories.addRootSubcategory($(this));
					});
					$('li[data-id="'+data.item.parent_id+'"] .fa-minus').click(function () {
						once.categories.delRootCategory($(this));
					});
					$('li[data-id="'+data.item.parent_id+'"] .fa-edit').click(function () {
						$("#item-edit").load("dialog.php?c=categories&o=subedit&id="+$(this).parent().parent().parent().data('id'), function() {
							$('#item-edit .modal').modal({
								show: 'false'
							}); 
						});
					});
					$('li[data-id="'+data.item.id+'"] .fa-edit').click();
				}else{
					li.find("ul:first").append(str);
					
					// Set DOM actions and click to edit
					$('li[data-id="'+data.item.id+'"] .fa-level-down').click(function () {
						once.categories.addRootCategory($(this));
					});
					$('li[data-id="'+data.item.id+'"] .fa-plus').click(function () {
						once.categories.addRootSubcategory($(this));
					});
					$('li[data-id="'+data.item.id+'"] .fa-minus').click(function () {
						once.categories.delRootCategory($(this));
					});
					$('li[data-id="'+data.item.id+'"] .fa-edit').click(function () {
						$("#item-edit").load("dialog.php?c=categories&o=subedit&id="+$(this).parent().parent().parent().data('id'), function() {
							$('#item-edit .modal').modal({
								show: 'false'
							}); 
						});
					});
					$('li[data-id="'+data.item.id+'"] .fa-edit').click();
				}
			}else{
				console.log("Action Error: "+data.error);
			}
		})
		.error(function() { console.log("Request Error: category_new"); });
	},
	// Del root category
	delRootCategory: function(obj){
		var li=obj.parent().parent().parent();
		var ul=li.parent();
		// Get c name
		var c=$("div[data-c]");
		// We need to confirm to set it default
		var r = confirm("Delete this "+li.data('name')+"?");
		if(r){
			$.getJSON("ajax.php?c=core&o=category_delete&module="+$(c).data("c")+"&id="+li.data('id'), function(data) {
				if(ul.children('li').length==1){
					if(ul.parent().parent().attr('id')=='menu'){
						ul.html('<li id="category_0" data-title="root" data-parent_id="0" data-id="'+li.data('parent_id')+'"><a class="add-first">Add first category</a></li>');
						ul.removeClass('tree');
					}else{
						ul.remove();
					}
				}
				
				li.remove();
				
				$('.add-first').click(function () {
					once.categories.addRootFirstCategory($(this));
				});
			})
			.error(function() { alert("couldnt"); });
		}
	},
}

once.categories.actions = {
	// Initialize category Init
	categoryInit: function(obj){
		if(!once.categories.loaded){
			// Initialize on click
			$("#categories-data li.list-group-item").click(function (e) {
				e.preventDefault();
				e.stopPropagation();
				once.categories.categoryLoad($(this));
			});
			// Only creators & admin can make new categories
			if(once.creator || once.admin){
				// Make hover working on categories if exist
				if($(this).find(".list-group-hover")){
					$(".list-group-item").hover(
						function(){
							$(this).find(".list-group-hover").show(); //.fadeIn(250)
						},
						function(){
							$(this).find(".list-group-hover").hide(); //.fadeOut(205)
						}
					);
				}
				
				$(".list-group").sortable({
					items: ".list-group-item",
					axis: "y",
					update: function (event, ui) {
						var data = $(this).sortable("serialize");
						event.stopPropagation();
						// POST to server using $.post or $.ajax
						var c=$("div[data-c]");
						$.ajax({
							data: data,
							type: "POST",
							url: "ajax.php?c=core&o=category_sort&module="+$(c).data("c")
						});
					}
				});
			}
			
			// Initialize edit category dialog
			once.categories.dialogs.categoryEdit(".category-edit");
		
			$("#category-new").click(function () {
				once.categories.categoryNew($(this));
			});
			
			$(".list-group-item .category-delete").click(function (e) {
				e.preventDefault();
				e.stopPropagation();
				once.categories.categoryDelete($(this));
			});
			
			//once.categories.loaded=true;

			//setTimeout("$(\".list-group-item .category-edit:first\").click();",500);
		}
	},
	// Initialize categories dialog
	categoryEdit: function(obj){
		if(!once.categories.loaded){
			// Save item
			$("#category-data .item-save").click(function () {
				$("#categoryForm").submit();
			});
			
			$(".fa-level-down").click(function () {
				once.categories.addRootCategory($(this));
			});
			$(".fa-plus").click(function () {
				once.categories.addRootSubcategory($(this));
			});
			$(".fa-minus").click(function () {
				once.categories.delRootCategory($(this));
			});
			$(".add-first").click(function () {
				once.categories.addRootFirstCategory($(this));
			});
			$(".fa-edit").click(function (e) {
				e.preventDefault();
				e.stopPropagation();
				var c=$("div[data-c]");
				$("#category-subedit").load(once.path+"/dialog.php?c=categories&o=subedit&module="+$(c).data("c")+"&id="+$(this).parent().parent().parent().data('id'), function() {
					$('#category-subedit .modal:first').modal({
						show: 'false'
					}); 
				})
				.error(function() { console.log("Dialog Error: category subedit"); });
			});
			//$(".fa-edit:first").click();
			$(".sortable").sortable({
				axis: "y",
				update: function (event, ui) {
					var data = $(this).sortable("serialize");
					event.stopPropagation();
					//ui.item.attr("data-id")
					// POST to server using $.post or $.ajax
					var c=$("div[data-c]");
					$.ajax({
						data: data,
						type: "POST",
						url: "ajax.php?c=core&o=category_sort&module="+$(c).data("c")
					});
				}
			});
			
			// Initialize categoryForm
			once.categories.forms.categoryForm($(this));
			
			// Set loaded
			once.categories.loaded=false;
		}
	},
	// Initialize subcategories dialog
	subcategoryEdit: function(obj){
		// Save item
		$("#subcategory-data .item-save").click(function () {
			$("#subcategoryForm").submit();
		});
		
		// Initialize categoryForm
		once.categories.forms.subcategoryEdit($(this));
	},
}

once.categories.dialogs = {
	// Open dialog mode
	categoryEdit: function(obj){
		// Append at end of the body
		$("body").append("<div id=\"category-edit\"></div>");
		
		// Append at end of the body
		$("body").append("<div id=\"category-subedit\"></div>");
		
		// Read and open edit dialog
		$(obj).click(function (e) {
			e.preventDefault();
			e.stopPropagation();
			var c=$("div[data-c]");
			$("#category-edit").load(once.path+"/dialog.php?c=categories&o=edit&module="+$(c).data("c")+"&id="+$(this).parent().parent().data("id"), function() {
				$('#category-edit .modal:first').modal({
					show: 'false'
				}); 
			})
			.error(function() { console.log("Dialog Error: category edit"); });
		});
	},
}

once.categories.forms = {
	// Initialize category form
	categoryForm: function(obj){
		var c=$("div[data-c]");
		$("#categoryForm").attr("action",once.path+"/ajax.php?c=core&o=category_edit&module="+$(c).data("c")+"&id="+$("#category-data").data("id"));
		var options = {
			dataType:  "json",
			success: function(data){
				// If response ok 
				if(data.status=='ok'){
					if($("#type-data").data('redirect')==undefined){
						// Update name & ico on items list
						var name=$("#categoryForm input[name='name']");
						var ico=$("#categoryForm input[name='ico']");
						
						// Update DOM
						$("#category_"+data.item.id+" .list-group-header span").html(name.val());
						$("#category_"+data.item.id+" .list-group-header i").attr('class', ico.val());
					}
					console.log("Category updated!");
				}else{
					console.log("Action Error: "+data.error);
				}
			},
			complete: function(){
				//console.log(data.responseText);
			},
			error: function(){
				console.log("Form Error: categoryForm");
			}
		};
		$("#categoryForm").ajaxForm(options);
		
		setTimeout(function(){
			$("#editForm input[name=\"name\"]").focus();
		},1000);
	},
	// Initialize subcategory form
	subcategoryEdit: function(obj){
		var c=$("div[data-c]");
		$("#subcategoryForm").attr("action",once.path+"/ajax.php?c=core&o=category_edit&module="+$(c).data("c")+"&id="+$("#subcategory-data").data("id"));
		var options = {
			dataType:  "json",
			success: function(data){
				// If response ok 
				if(data.status=='ok'){
					console.log("Subcategory updated!");
				}else{
					console.log("Action Error: "+data.error);
				}
			},
			complete: function(){
				//console.log(data.responseText);
			},
			error: function(){
				console.log("Form Error: subcategoryForm");
			}
		};
		$("#subcategoryForm").ajaxForm(options);
		
		setTimeout(function(){
			$("#subcategoryForm input[name=\"name\"]").focus();
		},1000);
	},
}

$(document).ready(function () {
	// Initialize categories actions
	if($("#categories-data").length){
		once.categories.actions.categoryInit();
	}
});