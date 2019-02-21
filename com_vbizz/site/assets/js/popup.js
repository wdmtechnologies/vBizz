 window.document.onkeydown = function (e)
{
    if (!e){
        e = event;
    }
    if (e.keyCode == 27){
        lightbox_close();
    }
}
//Checkes if any key pressed. If ESC key pressed it calls the lightbox_close() function.
function lightbox_open(){
    window.scrollTo(0,0);
    document.getElementById('light').style.display='block';
    document.getElementById('fade').style.display='block';  
}
//This script makes light and fade divs visible by setting their display properties to block. Also it scrolls  the browser to top of the page to make sure, the popup will be on //middle of the screen.
function lightbox_close(){
    document.getElementById('fade').style.display='none';
	document.getElementById('light').style.display='none';

}
 
//Checkes if any key pressed. If ESC key pressed it calls the lightbox_close() function.

 