var ScrollingTimeout=false;var programmetic_scrolling=false;function append_popup(data,top_id,style){var elem=document.createElement('div');elem.setAttribute('id',top_id);elem.setAttribute('class','popup');elem.setAttribute('style',style);elem.innerHTML=data.xmlhttp.responseText;document.getElementById('popup_space').appendChild(elem);popup_bring_up(top_id,0);}

function scroll_to_top(){var top=(document.documentElement&&document.documentElement.scrollTop||document.body&&document.body.scrollTop||0);if(top>10){programmetic_scrolling=true;window.scrollBy(0,-1*(2*top/5));programmetic_scrolling=false;ScrollingTimeout=setTimeout("scroll_to_top();",80);}
else{programmetic_scrolling=true;window.scrollTo(0,0);programmetic_scrolling=false;clearTimeout(ScrollingTimeout);ScrollingTimeout=false;}}

//window.onload = function(){}
document.onscroll=function(){
	if (programmetic_scrolling)
		return false;
	clearTimeout(ScrollingTimeout);
	ScrollingTimeout=false;
}

function popup_bring_up(Id,Opacity){set_opacity(document.getElementById(Id),Opacity);if(Opacity==100){}
else{setTimeout("popup_bring_up('"+Id+"', "+Opacity+" + 10);",40);
//if(!ScrollingTimeout) scroll_to_top();
}}

function popup_bring_down(Id,Opacity){var element=document.getElementById(Id);if(element==undefined){history.go(-1);return false;}
set_opacity(element,Opacity);if(Opacity==0){element.parentNode.removeChild(element);}
else{setTimeout("popup_bring_down('"+Id+"', "+Opacity+" - 10);",40);}}

document.onkeyup=function(e){if(!e)e=window.event;
	if((e.keyCode&&e.keyCode=="27")||(e.charCode&&e.charCode=="27")){
		var popups=document.getElementById('popup_space').childNodes;
		if(popups.length>0){
			var last_popup=popups[popups.length-1];
			popup_bring_down('popup_space',100);	//	last_popup.id
		}
	}
	if(typeof document_onkeyup=='function'){
		document_onkeyup(e.keyCode?e.keyCode:e.charCode);
	}
}

document.onkeydown=function(e){if(!e)e=window.event;if((e.keyCode&&e.ctrlKey&&e.keyCode=="83")||(e.charCode&&e.ctrlKey&&e.charCode=="83")){var popups=document.getElementById('popup_space');if(popups!=undefined&&popups.childNodes.length>0)
return false;for(var i=0;i<document.forms.length;i++){if(document.forms[i].getAttribute('class')!="search"&&document.forms[i].name!="format_xml"){if(!(is_changed==undefined))
is_changed=false;document.forms[i].submit();return false;}}}}
function set_opacity(Elem,Opacity){try{Elem.style.opacity=Opacity/100;Elem.style.MozOpacity=Opacity/100;if(Opacity==100)
Elem.style.filter="";else
Elem.style.filter="alpha(opacity = "+Opacity+")";}
catch(e){}}