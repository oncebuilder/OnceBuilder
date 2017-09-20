
$('.add').click(function(){
    $('.all').prop("checked",false);
    var items = $("#list1 input:checked:not('.all')");
    var n = items.length;
  	if (n > 0) {
      items.each(function(idx,item){
        var choice = $(item);
        choice.prop("checked",false);
        choice.parent().appendTo("#list2");
      });
  	}
    else {
  		alert("Choose an item from list 1");
    }
});

$('.remove').click(function(){
    $('.all').prop("checked",false);
    var items = $("#list2 input:checked:not('.all')");
	items.each(function(idx,item){
      var choice = $(item);
      choice.prop("checked",false);
      choice.parent().appendTo("#list1");
    });
});

/* toggle all checkboxes in group */
$('.all').click(function(e){
	e.stopPropagation();
	var $this = $(this);
    if($this.is(":checked")) {
    	$this.parents('.list-group').find("[type=checkbox]").prop("checked",true);
    }
    else {
    	$this.parents('.list-group').find("[type=checkbox]").prop("checked",false);
        $this.prop("checked",false);
    }
});

$('[type=checkbox]').click(function(e){
  e.stopPropagation();
});

/* toggle checkbox when list group item is clicked */
$('.list-group a').click(function(e){
  
    e.stopPropagation();
  
  	var $this = $(this).find("[type=checkbox]");
    if($this.is(":checked")) {
    	$this.prop("checked",false);
    }
    else {
    	$this.prop("checked",true);
    }
  
    if ($this.hasClass("all")) {
    	$this.trigger('click');
    }
});
