function killCopy(e){ 
    return false } 
function reEnable(){ 
    return true } 


if (window.sidebar){  
    document.onmousedown=killCopy 
    document.onclick=reEnable 
}