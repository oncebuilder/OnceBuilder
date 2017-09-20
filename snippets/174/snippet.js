
/* shake */
jQuery.fn.shake = function(intShakes, intDistance, intDuration) {
    this.each(function() {
        $(this).css("position","relative"); 
        for (var x=1; x<=intShakes; x++) {
        $(this).animate({left:(intDistance*-1)}, (((intDuration/intShakes)/4)))
    .animate({left:intDistance}, ((intDuration/intShakes)/2))
    .animate({left:0}, (((intDuration/intShakes)/4)));
    }
  });
return this;
};

$("#shaker").click(function(){
	$("#shaker").shake(3,7,800);
});
/**********************/


/* progress bar */
var progress = setInterval(function() {
	var $bar = $('.bar');

   	if ($bar.width()==400) {
        clearInterval(progress);
      	$bar.width(0);
    } else {
        $bar.width($bar.width()+40);
    }
    $bar.text($bar.width()/4 + "%");
  
}, 800);
/**********************/



/* toggle switch button */
$('.btn-toggle').click(function() {
    $(this).find('.btn').toggleClass('active');  
    
    if ($(this).find('.btn-primary').size()>0) {
    	$(this).find('.btn').toggleClass('btn-primary');
    }
    if ($(this).find('.btn-danger').size()>0) {
    	$(this).find('.btn').toggleClass('btn-danger');
    }
    if ($(this).find('.btn-success').size()>0) {
    	$(this).find('.btn').toggleClass('btn-success');
    }
    if ($(this).find('.btn-info').size()>0) {
    	$(this).find('.btn').toggleClass('btn-info');
    }
    
    $(this).find('.btn').toggleClass('btn-default');
       
});
/************************/


/* pagination */
$.fn.pageMe = function(opts){
    var $this = this,
        defaults = {
            perPage: 7,
            showPrevNext: false,
            numbersPerPage: 1,
            hidePageNumbers: false
        },
        settings = $.extend(defaults, opts);
    
    var listElement = $this;
    var perPage = settings.perPage; 
    var children = listElement.children();
    var pager = $('.pagination');
    
    if (typeof settings.childSelector!="undefined") {
        children = listElement.find(settings.childSelector);
    }
    
    if (typeof settings.pagerSelector!="undefined") {
        pager = $(settings.pagerSelector);
    }
    
    var numItems = children.size();
    var numPages = Math.ceil(numItems/perPage);

    pager.data("curr",0);
    
    if (settings.showPrevNext){
        $('<li><a href="#" class="prev_link">«</a></li>').appendTo(pager);
    }
    
    var curr = 0;
    while(numPages > curr && (settings.hidePageNumbers==false)){
        $('<li><a href="#" class="page_link">'+(curr+1)+'</a></li>').appendTo(pager);
        curr++;
    }
  
    if (settings.numbersPerPage>1) {
       $('.page_link').hide();
       $('.page_link').slice(pager.data("curr"), settings.numbersPerPage).show();
    }
    
    if (settings.showPrevNext){
        $('<li><a href="#" class="next_link">»</a></li>').appendTo(pager);
    }
    
    pager.find('.page_link:first').addClass('active');
    if (numPages<=1) {
        pager.find('.next_link').hide();
    }
  	pager.children().eq(1).addClass("active");
    
    children.hide();
    children.slice(0, perPage).show();
    
    pager.find('li .page_link').click(function(){
        var clickedPage = $(this).html().valueOf()-1;
        goTo(clickedPage,perPage);
        return false;
    });
    pager.find('li .prev_link').click(function(){
        previous();
        return false;
    });
    pager.find('li .next_link').click(function(){
        next();
        return false;
    });
    
    function previous(){
        var goToPage = parseInt(pager.data("curr")) - 1;
        goTo(goToPage);
    }
     
    function next(){
        goToPage = parseInt(pager.data("curr")) + 1;
        goTo(goToPage);
    }
    
    function goTo(page){
        var startAt = page * perPage,
            endOn = startAt + perPage;
        
        children.css('display','none').slice(startAt, endOn).show();
        
        if (page>=1) {
            pager.find('.prev_link').show();
        }
        else {
            pager.find('.prev_link').hide();
        }
        
        if (page<(numPages-1)) {
            pager.find('.next_link').show();
        }
        else {
            pager.find('.next_link').hide();
        }
        
        pager.data("curr",page);
       
        if (settings.numbersPerPage>1) {
       		$('.page_link').hide();
       		$('.page_link').slice(page, settings.numbersPerPage+page).show();
    	}
      
      	pager.children().removeClass("active");
        pager.children().eq(page+1).addClass("active");  
    }
};

$('#pages').pageMe({pagerSelector:'#myPager',childSelector:'.page',showPrevNext:true,hidePageNumbers:false,perPage:1});
/************************/


/* dropdown as select */
$(".dropdown-menu li a").click(function(){
  var selText = $(this).text();
  $(this).parents('.btn-group').find('.dropdown-toggle').html(selText+' <span class="caret"></span>').dropdown('toggle');
  return false;
});
/************************/


/* email validation */
$.fn.goValidate = function() {
    var $form = this,
        $inputs = $form.find('input:text, input:password');
  
    var validators = {
        email: {
            regex: /^[\w\-\.\+]+\@[a-zA-Z0-9\.\-]+\.[a-zA-z0-9]{2,4}$/
        }
    };
    var validate = function(klass, value) {
        var isValid = true,
            error = '';
            
        if (!value && /required/.test(klass)) {
            error = 'This field is required';
            isValid = false;
        } else {
            klass = klass.split(/\s/);
            $.each(klass, function(i, k){
                if (validators[k]) {
                    if (value && !validators[k].regex.test(value)) {
                        isValid = false;
                        error = validators[k].error;
                    }
                }
            });
        }
        return {
            isValid: isValid,
            error: error
        }
    };
    var showError = function($input) {
        var klass = $input.attr('class'),
            value = $input.val(),
            test = validate(klass, value);
      
        $input.removeClass('invalid');
        
        if (!test.isValid) {
            $input.addClass('invalid');
            if(typeof $input.data("shown") == "undefined" || $input.data("shown") == false){
               $input.popover('show');
            }
            
        }
      	else {
        	$input.popover('hide');
            $input.parent().removeClass('has-error').addClass('has-success');
      	}
    };
   
    $inputs.keyup(function() {
        showError($(this));
    });
  
    $inputs.on('shown.bs.popover', function () {
  		$(this).data("shown",true);
	});
  
    $inputs.on('hidden.bs.popover', function () {
  		$(this).data("shown",false);
	});
  
    $form.submit(function(e) {
      
        $inputs.each(function() {
          if ($(this).is('.required')) {
            //showError($(this)); /* rem comment to enable initial display of validation rules */
          }
    	});
      
      
        if ($form.find('input.invalid').length) {
            e.preventDefault();
            alert('The form does not validate!');
        }
    });
    return this;
};

$('#emailForm').goValidate();
/************************/


/* caps lock check */
$('[type=password]').keypress(function(e) {
    var $password = $(this),
        tooltipVisible = $('.tooltip').is(':visible'),
        s = String.fromCharCode(e.which);
    
    // check if capslock is on.
    if ( s.toUpperCase() === s && s.toLowerCase() !== s && !e.shiftKey ) {
      if (!tooltipVisible)
          $password.tooltip('show');
    } else {
      if (tooltipVisible)
          $password.tooltip('hide');
    }
    
    // hide the tooltip when moving away from the password field
    $password.blur(function(e) {
    	$password.tooltip('hide');
    });
});
/************************/


/* collapse tabs */
var autocollapse = function() {
  
  var tabs = $('#tabs');
  var tabsHeight = tabs.innerHeight();
  
  if (tabsHeight >= 50) {
    while(tabsHeight > 50) {
      //console.log("new"+tabsHeight);
      
      var children = tabs.children('li:not(:last-child)');
      var count = children.size();
      $(children[count-1]).prependTo('#collapsed');
      
      tabsHeight = tabs.innerHeight();
    }
  }
  else {
  	while(tabsHeight < 50 && (tabs.children('li').size()>0)) {
      
      var collapsed = $('#collapsed').children('li');
      var count = collapsed.size();
      $(collapsed[0]).insertBefore(tabs.children('li:last-child'));
      tabsHeight = tabs.innerHeight();
    }
    if (tabsHeight>50) { // double chk height again
    	autocollapse();
    }
  }
};
  
autocollapse(); // when document first loads
/************************/

/* loading button */
$('#loadingBtn').click(function () {
  var btn = $(this);
  btn.button('loading');
  
  // perform ajax processing here is reset button when complete
  setTimeout(function() {
    btn.button('reset');
  }, 2000);
  
});
/************************/

