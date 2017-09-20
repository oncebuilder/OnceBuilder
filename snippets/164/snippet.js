
$('#go-btn').click(function () {
    var btn = $(this);
    var input = $("#cyrillic-input").val();
   
    input = encodeURIComponent(input);
 
    var utf = input.replace(new RegExp("%",'g'), "0x");
    
    $('#utf-result').text(utf);
  
    var dummyImage = $('#dumy-img');
    dummyImage.attr("src",dummyImage.attr("title") + utf);
});