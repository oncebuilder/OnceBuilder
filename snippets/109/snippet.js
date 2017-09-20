
function cycleImages(container){
      var $active = container.find('.active');
      var $next = ($active.next().length > 0) ? $active.next() : container.find('img:first');
      $next.css('z-index',2);//move the next image up the pile
      $active.fadeOut(1500,function(){//fade out the top image
      $active.css('z-index',1).show().removeClass('active');//reset the z-index and unhide the image
          $next.css('z-index',3).addClass('active');//make the next image the top one
      });
}

$(document).ready(function(){
    setInterval(function(){cycleImages($('#cycler1'))}, 2000);
    setInterval(function(){cycleImages($('#cycler2'))}, 3000);
})