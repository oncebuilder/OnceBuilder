/**
 * Version: 1.0, 29.07.2015
 * by Adam Wysocki, support@oncebuilder.com
 *
 * Copyright (c) 2015 Adam Wysocki
 *
 *	This is OnceBuilder About plugin (once.about)
 *
*/

once.about = {
	loaded: false,
	initialized: function(){
		this.loaded=true;
	},
	checkUpdate: function(obj){//ok
		$.ajax({
			type: 'POST',
			url: once.path+"/ajax.php?c=about&o=check_server&id=1",
			success: function(data) { 
				if(data.status=='ok'){
					var str='<p>'+data.server_info;
					if(data.server_status==1){
						str+='<a href="http://oncebuilder.com/download"><button class="btn btn-default btn-sm pull-right item-link" type="button"> Check for update</button></a>'
					}else{
						str+='<button class="btn btn-default btn-sm pull-right disabled" type="button"> Updated</button>'
					}
					str+='</p>'
					$("#info-header").html(str);
				}else{
					alert(data.error);
				}
			},
			contentType: "application/json",
			dataType: 'json'
		})
		.error(function() { console.log("Request Error: check_server"); });
	},
}

$(document).ready(function () {
	once.about.checkUpdate();
	
	// Initialize / sandbox
	once.about.initialized();
});