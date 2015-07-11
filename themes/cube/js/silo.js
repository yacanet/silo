function formatangka(objek,tanpatitik) {
	a = objek.value;
	b = a.replace(/[^\d]/g,"");
	c = "";
	panjang = b.length;
	j = 0;
	for (i = panjang; i > 0; i--) {
		j = j + 1;
		if (((j % 3) == 1) && (j != 1)) {
            if (tanpatitik)
                c = b.substr(i-1,1) + c;
            else
                c = b.substr(i-1,1) + "." + c;
		} else {
			c = b.substr(i-1,1) + c;
		}
	}
	objek.value = c;
}
function no_photo (object,url) {
	object.src = url;
	object.onerror = "";
	return true;
}
jQuery(document).click(function(){    
    var target = jQuery(".name");
    if (target.is("[style]")) {        
        target.removeAttr("style");        
    }  
    var target = jQuery(".dropdown.hidden-xs");
    if (target.is("[style]")) {        
        target.removeAttr("style");        
    } 
});
/* CONFIG TOOLS SETTINGS */
jQuery('#config-tool-cog').on('click', function(){
    jQuery('#config-tool').toggleClass('closed');
});

jQuery(document).ready(function(){
   jQuery("#openwindowDaftarLPO").click(function() {
       var left = (screen.width/2)-(800/2);
       var top = (screen.height/2)-(400/2);
       window.open(jQuery(this).attr("href"),"Daftar LPLPO","width=800,height=400,scrollbars=yes,left="+left+",top="+top);
       return false;
   });   
   jQuery("#openwindowDaftarSBBM").click(function() {
       var left = (screen.width/2)-(800/2);
       var top = (screen.height/2)-(400/2);
       window.open(jQuery(this).attr("href"),"Daftar SBBM","width=800,height=400,scrollbars=yes,left="+left+",top="+top);
       return false;
   });   
});
function hitungSelisih (objek,labelid,stock) {
    var a = objek.value;        
    c = a - stock;       
    jQuery('#'+labelid).text(c);    
}