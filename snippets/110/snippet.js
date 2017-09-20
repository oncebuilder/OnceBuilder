function openModal(){
    var modal = document.getElementById("modal");

   
    modal.style.top="10%";
    modal.style.height="65%";
    modal.style.left="32.5%";
    modal.style.width="35%";
    modal.style.borderWidth="2px";
    modal.style.opacity="1";
}
   
function closeModal(){
    var modal = document.getElementById("modal");

   
    modal.style.top="50%";
    modal.style.height="0%";
    modal.style.left="50%";
    modal.style.width="0%";
    modal.style.borderWidth="0px";
    modal.style.opacity="0";
}
   
function animate(){
    var button = document.getElementById("button");
   
    if(button.style.top!="43%"){
        button.style.top="43%";
    }
    else{
        button.style.top="40%";
    }
   
    setTimeout("animate()",320);
}