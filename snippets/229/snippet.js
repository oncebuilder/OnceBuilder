function openModal(){
     var modal=document.getElementById("modal");
    var button=document.getElementById("button");
    
    modal.style.opacity="1";
    modal.style.visibility="visible";
    button.style.opacity="0";
    
}
    
function closeModal(){
     var modal=document.getElementById("modal");
    var button=document.getElementById("button");
    
    modal.style.opacity="0";
    modal.style.visibility="hidden";
    button.style.opacity="1";
    
}