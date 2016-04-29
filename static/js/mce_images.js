
var popup = false;

document.body.addEventListener("DOMNodeInserted",
	function(e){
		var elem = e.target;
		try{
			if (elem.className.indexOf('mce-window') > -1){	//	getAttribute('aria-label') == 'Insert/edit image'
				var body = elem.querySelectorAll('.mce-container-body.mce-abs-layout')[0];
				//	mce-container mce-panel mce-floatpanel mce-window
				var iframe = document.createElement('iframe');
				iframe.className = 'image-library';
				iframe.src = base_url+'admin/images/'+upload_path;
				//elem.style.width = '70%';
				body.appendChild(iframe);
				new delayedSetHeight(body);
				//
				popup = body;
			}
		}
		catch(e){}
	}, false);

function delayedSetHeight(body){
	setTimeout(
		function(){
			body.style.height = '250px';
		}, 800);
}

function setImg(img){
	popup.querySelectorAll('input')[0].value = img;
}
