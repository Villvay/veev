
var scrollSpeed = 0;
var scrollLatch = false, scrollLatchTimeout;

var viewframeHeight = window.innerHeight;
var viewframeWidth = window.innerWidth;

var header = document.querySelectorAll('nav')[0];
if (header != undefined)
	var nav = header.querySelectorAll('ul.nav')[0];

var menu = document.getElementById('hamberger');
if (menu != undefined && nav != undefined)
	menu.onclick = function(){
		if (nav.style.display == 'block'){
			nav.style.display = 'none';
		}
		else{
			nav.style.display = 'block';
		}
	}

window.onwheel = window.onmousewheel = function(e){  
	var evt = window.event || e;
	scrollSpeed = (evt.wheelDelta > 0 || evt.deltaY < 0) ? 600 : -600;
	return false;
}

window.onscroll = function(){
	if (!scrollLatch)
		scrollSpeed = 0;
	//
	scrolling();
}

window.onresize = function(e){
	viewframeHeight = window.innerHeight;
	viewframeWidth = window.innerWidth;
	document.querySelectorAll('main')[0].style.minHeight = (viewframeHeight - 106) + 'px';
}
document.querySelectorAll('main')[0].style.minHeight = (viewframeHeight - 106) + 'px';

function scrolling(){
	if (document.body.scrollTop > 20)
		header.className = 'scrolling';
	else
		header.className = '';
}

function animationFrame(){
	if (Math.abs(scrollSpeed / 40) > 1){
		scrollLatch = true;
		document.body.scrollTop -= scrollSpeed / 40;
		scrollSpeed = (9 * scrollSpeed / 10);
		//
		if (scrollSpeed < 0 && document.body.scrollTop == 0)
			scrollSpeed = 0;
		else if (scrollSpeed > 0 && document.body.scrollTop == document.body.scrollHeight - viewframeHeight)
			scrollSpeed = 0;
		//
		clearTimeout(scrollLatchTimeout);
		scrollLatchTimeout = setTimeout('scrollLatch = false;', 200);
	}
	else
		scrollSpeed = 0;
	//
	requestAnimationFrame(animationFrame);
	//setTimeout(animationFrame, 250);
}
animationFrame();

if ( !window.requestAnimationFrame ) {
	window.requestAnimationFrame = ( function() {
		return window.webkitRequestAnimationFrame ||
		window.mozRequestAnimationFrame ||
		window.oRequestAnimationFrame ||
		window.msRequestAnimationFrame ||
		function(callback, element) {
			window.setTimeout( callback, 1000 / 60 );
		};
	} )();
}

function elem(tagname, innerHTML, options){		//	Create a Dom Element of given type, innerHTML and options
	var obj = document.createElement(tagname);
	if (typeof innerHTML !== 'undefined' && innerHTML != null && innerHTML != false)
		obj.innerHTML = innerHTML;
	if (typeof options !== 'undefined')
		for (var key in options)
			obj.setAttribute(key, options[key]);
	return obj;
}

function validate(){
	var type;
	for (var i = 0; i < this.elements.length; i++){
		type = this.elements[i].getAttribute('data-validate');
		if (type != null){
			if (type == 'numeric' && !isNumeric(this.elements[i].value)){
				this.elements[i].style.color = 'red';
				this.elements[i].focus();
				alert('Please enter a valid numeric');
				return false;
			}
			else if (type == 'currency' && !isNumeric(this.elements[i].value.replace(/,/g, ''))){
				this.elements[i].style.color = 'red';
				this.elements[i].focus();
				alert('Please enter a valid amount of money');
				return false;
			}
			else if (type == 'date'){
				type = this.elements[i].value.split('-');
				if (type.length != 3 || !isNumeric(this.elements[i].value.replace(/-/g, '')) || type[0].length != 4 || type[1].length != 2 || type[2].length != 2){
					this.elements[i].style.color = 'red';
					this.elements[i].focus();
					alert('Please enter a valid date');
					return false;
				}
			}
			else if (type == 'required'){
				if (this.elements[i].value.trim() == ''){
					alert('Please fill this field');
					this.elements[i].focus();
					return false;
				}
			}
		}
	}
}

function numericInputHandler(input){
	var maxlength = input.getAttribute('maxlength') == undefined ? -1 : input.getAttribute('maxlength')*1;
	input.onkeydown = function(e){
		e = e || window.event;
		var keyCode = e.keyCode || e.which;
		var charCode = e.charCode || e.keyCode;
		var shiftCode = e.shiftKey || false;
		//
		if (keyCode == 9 || (keyCode > 36 && keyCode < 41))
			return true;
                //
		if ((charCode == 32 || charCode == 8 || charCode == 46))// && shiftCode == false
			return true;	//	Allow backspace / delete
		if (maxlength > -1 && this.value.toString().length >= maxlength)
			return false;	//	Deny
		if (((charCode > 47 && charCode < 58) || (charCode > 95 && charCode < 106)) && shiftCode == false)
			return true;	//	Allow numbers
		else
			return false;	//	Deny
	}
	input.onfocus = function(){
		setTimeout(function(){input.select();}, 10);
	};
	input.onkeyup = input.onchange = function(e){
		if (maxlength > -1)
			this.value = this.value.replace(/[^0-9]/g, '').substring(0, maxlength);
	}
}

function alphaInputHandler(input){
	input.onkeydown = function(e){0
		e = e || window.event;
		var keyCode = e.keyCode || e.which;
		//
		if (keyCode == 9 || (keyCode > 36 && keyCode < 41))
			return true;
		//
		e = e || window.event;
		var charCode = e.charCode || e.keyCode;
		if (charCode == 32 || charCode == 8 || charCode == 46)
			return true;	//	Allow backspace / delete / space
		if (charCode < 65 || charCode > 90)
			return false;	//	Deny anything not a latin character
	}
	input.onchange = function(e){
		this.value = this.value.replace(/[^A-Z a-z]/g, '');
	}
}

function alphaNumericInputHandler(input){
	input.onkeydown = function(e){
		e = e || window.event;
		var keyCode = e.keyCode || e.which;
		var charCode = e.charCode || e.keyCode;
		var shiftCode = e.shiftKey || false;
		//
		if (((charCode > 47 && charCode < 58) || (charCode > 95 && charCode < 106)) && shiftCode == false)
			return true;	//	Allow numbers
		//
		if (keyCode == 9 || (keyCode > 36 && keyCode < 41))
			return true;
		//
		if (charCode == 32 || charCode == 8 || charCode == 46)
			return true;	//	Allow backspace / delete / space
		//if (charCode < 65 || charCode > 90)
		return false;	//	Deny anything not an alphanumeric character
	}
	input.onchange = function(e){
		this.value = this.value.replace(/[^A-Z a-z 0-9]/g, '');
	}
}

function currencyInputHandler(input){
	input.onkeyup = function(){
		if (isNumeric(this.value.replace(/,/g, '')))
			this.style.color = '';
		else
			this.style.color = 'red';
	};
	input.onchange = function(){
		this.value = this.value.replace(/,/g, '').replace(/\B(?=(\d{3})+(?!\d))/g, ',');
		this.style.color = '';
	};
	input.value = input.value.replace(/,/g, '').replace(/\B(?=(\d{3})+(?!\d))/g, ',');
	input.onfocus = function(){
		setTimeout(function(){input.select();}, 10);
	};
}

function dateInputHandler(input){
	input.onchange = function(){
		this.style.color = '';
	};
}

function textareaHandler(messageText){
	messageText.style.overflow = 'auto';
	var messageTextHeight = messageText.scrollHeight;
	messageText.style.overflow = 'hidden';
	messageText.style.maxWidth = '100%';
	messageText.style.width = '100%';
	messageText.onkeyup = function(event){
		var bkpOffsetHeight = this.offsetHeight;
		messageText.style.overflow = 'auto';
		this.style.height = '33px';
		if (messageTextHeight != this.scrollHeight){
			var bkpScrollHeight = this.scrollHeight;
			this.style.height = messageTextHeight + 'px';
			this.scrollTop = 0;
			//
			setTimeout(
				function(){
					this.scrollTop = 0;
					messageText.style.transition = 'height 0.25s';
					messageText.style.webkitTransition = 'height 0.25s';
					messageText.style.mozTransition = 'height 0.25s';
					messageText.style.height = messageTextHeight + 'px';
				}, 5);
			//
			messageTextHeight = bkpScrollHeight;
			//
			setTimeout(
				function(){
					messageText.style.transition = '';
					messageText.style.webkitTransition = '';
					messageText.style.mozTransition = '';
				}, 500);
			//
			messageText.style.overflow = 'hidden';
		}
		else{
			this.style.height = this.scrollHeight + 'px';
			messageText.style.overflow = 'hidden';
		}
		if (event != undefined){
			event.stopPropagation();
			event.preventDefault();
		}
	}
	messageText.onkeyup();
	messageText.onkeypress = function(){
		messageText.scrollTop = 0;
		setTimeout(function(){
					messageText.scrollTop = 0;
				}, 1);
		setTimeout(function(){
					messageText.scrollTop = 0;
				}, 5);
	}
}

function maskedInputHandler(input, mask){
	this.input_box = input;
	this.input_mask = mask;
	var _self = this;
	//
	var output = mask.replace(/9/g, '_').replace(/A/g, '_');
	var originalLength = output.length;
	//
	input.onkeydown = function(e){
		e = e || event;
		var target = e.target || e.srcElement;
		if ((e.keyCode > 36 && e.keyCode < 41) || e.keyCode == 9)
			return true;
		else if (e.keyCode == 46){
			e.cancelBubble = true;
			return false;
		}
		else if (e.keyCode == 8){
			var selStart;
			var totLen = target.value.length;
			if (document.selection){
				var oSel = document.selection.createRange();
				oSel.moveStart ('character', -originalLength);
				selStart = oSel.text.length;
			}
			else
				selStart = target.selectionStart;
			//
			if (selStart == 0)
				return false;
			//
			if (target.value.substring(selStart-1, selStart) != '_' && isNumber(target.value.substring(selStart-1, selStart)))
				target.value = target.value.substring(0, selStart-1) + '_' + target.value.substring(selStart, totLen);
			try{
				target.setSelectionRange(selStart-1, selStart-1);
			}catch(e){	//	IE 8 support (fir IE testing)
				var range = target.createTextRange();
				range.collapse(true);
				range.moveStart('character', selStart+1);
				range.moveEnd('character', 0);
				range.select();
			}
			//
			e.cancelBubble = true;
			return false;
		}
	}
	//
	input.onkeypress = function(e){
		e = e || event;
		var target = e.target || e.srcElement
		var ch = String.fromCharCode(e.charCode || e.keyCode);
		if ((e.keyCode > 36 && e.keyCode < 41) || e.keyCode == 9)
			return true;
		else if (!isNumber(ch) || ch == '\t'){
			e.cancelBubble = true;
			return false;
		}
		var output = _self.input_mask.replace(/9/g, '_').replace(/A/g, '_');
		var totLen = target.value.length;
		//
		var selStart;
		if (document.selection){
			var oSel = document.selection.createRange();
			oSel.moveStart ('character', -originalLength);
			selStart = oSel.text.length;
		}
		else
			selStart = target.selectionStart;
		//
		if (totLen == selStart){
			e.cancelBubble = true;
			return false;
		}
		//
		while (selStart < totLen && (target.value.substring(selStart, selStart+1) != '_' && !isNumber(target.value.substring(selStart, selStart+1))))
			selStart += 1;
		target.value = target.value.substring(0, selStart) + ch + target.value.substring(selStart+1, totLen);
		try{
			target.setSelectionRange(selStart+1, selStart+1);
		}
		catch(e){	//	IE 8 support (for IE testing)
			var range = target.createTextRange();
			range.collapse(true);
			range.moveStart('character', selStart+1);
			range.moveEnd('character', 0);
			range.select();
		}
		//
		e.cancelBubble = true;
		return false;
	}
	//
	input.onmouseup = input.onfocus = function(e){
		if (this.value == '')
			this.value = output;
		if (this.value == output)	//	Do not put 'else' on this line
			try{
				this.setSelectionRange(0, 0);
			}
			catch(e){	//	IE 8 support (for IE testing)
				var range = this.createTextRange();
				range.collapse(true);
				range.moveStart('character', 0);
				range.moveEnd('character', 0);
				range.select();
			}
	}
	//
	input.onblur = function(e){
		if (this.value == output)
			this.value = '';
	}
}

var forms = document.querySelectorAll('form.autopilot');
for (var j = 0; j < forms.length; j++){
	forms[j].onsubmit = validate;
	var type;
	for (var i = 0; i < forms[j].elements.length; i++){
		type = forms[j].elements[i].getAttribute('data-validate');
		var mask = forms[j].elements[i].getAttribute('data-mask');
		if (type != null){
			if (type == 'alpha'){
				new alphaInputHandler(forms[j].elements[i]);
			}
			else if (type == 'numeric'){
				new numericInputHandler(forms[j].elements[i]);
			}
			else if (type == 'alphanumeric'){
				new alphaNumericInputHandler(forms[j].elements[i]);
			}
			else if (type == 'currency'){
				new currencyInputHandler(forms[j].elements[i]);
			}
			else if (type == 'date'){
				new dateInputHandler(forms[j].elements[i]);
			}
			else if (mask != undefined){
				new maskedInputHandler(forms[j].elements[i], mask);
			}
		}
		if (forms[j].elements[i].tagName == 'TEXTAREA')
			new textareaHandler(forms[j].elements[i]);
	}
}

function isNumeric(n) {
	return !isNaN(parseFloat(n)) && isFinite(n);
}
