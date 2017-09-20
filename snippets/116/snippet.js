function animate(){
   
            var b1= document.getElementById("block1");
            var b2= document.getElementById("block2");
            var b3= document.getElementById("block3");
            var b4= document.getElementById("block4");
            var b5= document.getElementById("block5");
            var b6= document.getElementById("block6");
            var b7= document.getElementById("block7");
            var b8= document.getElementById("block8");
            var loader= document.getElementById("loader");
   
    if(b1.style.top!="-50px"){
    b1.style.top="-50px";
    b1.style.left="-50px";
    b2.style.top="-50px";
    b2.style.left="23.33px";
    b3.style.top="-50px";
    b3.style.left="95px";
    b4.style.top="21px";
    b4.style.left="96px";
    b5.style.top="96px";
    b5.style.left="96px";
    b6.style.top="96px";
    b6.style.left="23.33px";
    b7.style.top="96px";
    b7.style.left="-50px";
    b8.style.top="21px";
    b8.style.left="-50px";


    }
    else{
    b1.style.top="0px";
    b1.style.left="0px";
    b2.style.top="0px";
    b2.style.left="23.33px";
    b3.style.top="0px";
    b3.style.left="46.66px";
    b4.style.top="23.33px";
    b4.style.left="46.66px";
    b5.style.top="46.66px";
    b5.style.left="46.66px";
    b6.style.top="46.66px";
    b6.style.left="23.33px";
    b7.style.top="46.66px";
    b7.style.left="0px";
    b8.style.top="23.33px";
    b8.style.left="0px";

    }
       
   
    setTimeout("animate()",1290);
}


$(document).ready(function () {
  
  animate()

}