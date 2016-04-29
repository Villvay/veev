// Vishva Kumara (cc) 2010 vishva8kumara@gmail.com

var ObjectStore = new Array();
var PageScrollTimer;
var FlashFadeTimer;

function StoreObject(Obj){
	for (var i = 0; i < ObjectStore.length; i++){
		if (Obj == ObjectStore[i])
			return i
	}
	ObjectStore.push(Obj);
	return ObjectStore.length - 1;
}

function RetrieveObject(Index){
	return ObjectStore[Index];
}

// ---------------------------------------------------------------------------------------------------

function LoadLibraries(filenames){
	for (var i = 0; i < filenames.length; i++)
		LoadLibrary(filenames[i]);
}
function LoadLibrary(filename){
	if (filename == "Ajax"){
		filename = "ajax.js";
	}
	else if (filename == "ImageSwap"){
		filename = "img_swap.js";
	}
	else if (filename == "ImageRotator"){
		filename = "img_rotate.js";
	}
	else if (filename == "ImageScroller"){
		filename = "img_scroller.js";
	}
	else if (filename == "ImageSlideshow"){
		filename = "img_slideshow.js";
	}
	else if (filename == "TabController"){
		filename = "tab_controller.js";
	}
	else if (filename == "DataGridView"){
		filename = "datagridview.js";
	}
	else if (filename == "JS-XMLParser"){
		filename = "js_xml.js";
	}
	var fileref = document.createElement('script');
	fileref.setAttribute("type","text/javascript");
	fileref.setAttribute("src", filename);
	//alert(filename);
}

// ---------------------------------------------------------------------------------------------------

function GetImage(url){
	var TmpImage = new Image();
	TmpImage.src = url;
	return TmpImage;
}

function GetElem(id){
	return document.getElementById(id);
}

function PixelsToInteger(pixels){
	if (pixels == "")
		return 0;
	pixels = pixels.toString();
	return parseInt(pixels.replace("px", ""));
}

function PercentageToInteger(pixels){
	if (pixels == "")
		return 0;
	pixels = pixels.toString();
	return parseInt(pixels.replace("%", ""));
}

function GetTopLeft(elm){
	var x = 0;
	var y = 0;
	x = elm.offsetLeft;
	y = elm.offsetTop;
	elm = elm.offsetParent;
	while(elm != null){
		x = parseInt(x) + parseInt(elm.offsetLeft);
		y = parseInt(y) + parseInt(elm.offsetTop);
		elm = elm.offsetParent;
	}
	return {Top:y, Left:x};
}

function PadNumber(number, length) {
	var str = '' + number;
	while (str.length < length) {
		str = '0' + str;
	}
	return str;
}

function show_more_text(id, content_id, skip){
	var elem = document.getElementById('shorten_more_'+id);
	var elem2 = document.getElementById('shorten_more_link_'+id);
	content_id = content_id.split(":");
	content_id = content_id[0];
	AjaxLoad(base_url + 'content/more/' + content_id + '/' + skip, 'shorten_more_'+id);
	elem.style.display = 'inline';
	elem2.style.display = 'none';
}

function stopEvent(e){
	if (!e) e = window.event;
	if (e.stopPropagation){
		e.stopPropagation();
	}
	else{
		e.cancelBubble = true;
	}
}

function cancelEvent(e){
	if (!e) e = window.event;
	if (e.preventDefault){
		e.preventDefault();
	}
	else{
		e.returnValue = false;
	}
}

function SetRadioCheckedValue(radioObj, newValue) {
	if(!radioObj)
		return;
	var radioLength = radioObj.length;
	if(radioLength == undefined) {
		radioObj.checked = (radioObj.value == newValue.toString());
		return;
	}
	for(var i = 0; i < radioLength; i++) {
		radioObj[i].checked = false;
		if(radioObj[i].value == newValue.toString()) {
			radioObj[i].checked = true;
		}
	}
}

function SetSelectValue(SelectObject, Value){
	for(index = 0; index < SelectObject.length; index++){
		if(SelectObject[index].value.toString() == Value.toString()){
			SelectObject.selectedIndex = index;
		}
	}
}

function ReplaceAll(Str, Search, Replace){
	if(Search == Replace){
		return Str;
	}
	while(Str.indexOf(Search) != -1){
		Str = Str.replace(Search, "<RePlAcE>");
	}
	while(Str.indexOf("<RePlAcE>") != -1){
		Str = Str.replace("<RePlAcE>", Replace);
	}
	return Str;
}

var valid_chars = "abcdefghijklmnopqrstuvwxyz0123456789.- _ABCDEFGHIJKLMNOPQRSTUVWXTZ";
function strip_all_special_chars(str){
	var schar;
	var output = "";
	for (var i = 0; i < str.length; i++){
		schar = str.substring(i, i+1);
		if (valid_chars.indexOf(schar) > -1){
			output = output + schar;
		}
	}
	return output;
}

var months_str = ["January", "February", "March", "April", "May", "June", "July", "August", "Spetember", "October", "November", "December"];
function date_to_string(js_date){
	return months_str[js_date.getMonth()] + " " + PadNumber(js_date.getDate(), 2) + ", " + js_date.getFullYear();
}

function trim_string(str){
    return str.replace(/^\s*/, "").replace(/\s*$/, "");
}

function RowCellsToArray(row){
	row = row.innerHTML;
	var uppercase = row.toUpperCase();
	var p1 = 0;
	var p2, TmpStr;
	var ReturnArray = new Array();
	while (uppercase.indexOf("<TD", p1) > -1){
		p1 = uppercase.indexOf("<TD", p1);
		p1 = uppercase.indexOf(">", p1)+1;
		p2 = uppercase.indexOf("</TD>", p1);
		TmpStr = row.substring(p1, p2);
		TmpStr = TmpStr.replace("&nbsp;", "");
		ReturnArray.push(TmpStr);
	}
	return ReturnArray;
}

function PageScrollTo(top, last_scrolled){
	try{
		clearTimeout(PageScrollTimer);
		if (document.body.scrollTop == top){// || document.body.scrollTop == 0 || document.body.scrollTop == document.body.height){
		}
		else if (document.body.scrollTop > top){
			var ScrollBy = parseInt((document.body.scrollTop - top) / 3);
			document.body.scrollTop -= ScrollBy;
			if (ScrollBy > 1 && last_scrolled != ScrollBy)
				PageScrollTimer = setTimeout("PageScrollTo("+top+", "+ScrollBy+");", 50);
		}
		else if (document.body.scrollTop < top){
			var ScrollBy = parseInt((top - document.body.scrollTop) / 3);
			document.body.scrollTop += ScrollBy;
			if (ScrollBy > 1 && last_scrolled != ScrollBy)
				PageScrollTimer = setTimeout("PageScrollTo("+top+", "+ScrollBy+");", 50);
		}
	}catch(e){}
}

function FadeFlashMessage(Opacity, fm_id){
	//clearTimeout(FlashFadeTimer);
	var Elem = GetElem("flash_message_"+fm_id);
	if (Elem == null)
		return;
	Opacity -= 5;
	if (Opacity > 4){
		Elem.style.opacity = Opacity/100;
		Elem.style.MozOpacity = Opacity/100;
		Elem.style.filter = "alpha(opacity = " + Opacity + ")";
		//FlashFadeTimer = 
		setTimeout("FadeFlashMessage("+Opacity+", "+fm_id+");", 100);
	}
	else{
		Elem.parentNode.removeChild(Elem);
	}
}

function DeleteElement(elem){
	elem.parentNode.removeChild(elem);
}

/*function PageScrollTo(top){
	try{
		//alert(top +":"+ document.body.scrollTop);
		clearTimeout(PageScrollTimer);
		if (document.body.scrollTop == top){// || document.body.scrollTop == 0 || document.body.scrollTop == document.body.height){
		}
		else if (document.body.scrollTop > top){
			var ScrollBy = parseInt((document.body.scrollTop - top) / 3);
			document.body.scrollTop -= ScrollBy;
			if (ScrollBy > 1)
				PageScrollTimer = setTimeout("PageScrollTo("+top+");", 50);
			//alert(ScrollBy);
		}
		else if (document.body.scrollTop < top){
			var ScrollBy = parseInt((top - document.body.scrollTop) / 3);
			document.body.scrollTop += ScrollBy;
			if (ScrollBy > 1)
				PageScrollTimer = setTimeout("PageScrollTo("+top+");", 50);
			//alert(ScrollBy);
		}
	}catch(e){}
}*/

function Manage_KeyPress_Submit_Deligate(deligate, e){
	if (e.keyCode == 13){
		cancelEvent(e);
		setTimeout(deligate, 20);
		//alert(deligate);
	}
}
