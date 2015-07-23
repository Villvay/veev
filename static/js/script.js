
var scrollSpeed = 0;
var scrollLatch = false, scrollLatchTimeout;

var viewframeHeight = window.innerHeight;
var viewframeWidth = window.innerWidth;

var header = document.querySelectorAll('nav')[0];
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
		}
	}
}
function numericInputHandler(input){
	input.onkeyup = function(){
		if (isNumeric(this.value))
			this.style.color = '';
		else
			this.style.color = 'red';
	};
	input.onfocus = function(){
		setTimeout(function(){input.select();}, 10);
	};
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
	messageText.onkeyup = function(event){
		var bkpOffsetHeight = this.offsetHeight;
		messageText.style.overflow = 'auto';
		this.style.height = '33px';
		if (messageTextHeight != this.scrollHeight){
			var bkpScrollHeight = this.scrollHeight;
			this.style.height = /*bkpOffsetHeight*/messageTextHeight + 'px';
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
			messageTextHeight = bkpScrollHeight;//this.scrollHeight;
			//
			setTimeout(
				function(){
					messageText.style.transition = '';
					messageText.style.webkitTransition = '';
					messageText.style.mozTransition = '';
				}, 500);
			//
			//this.style.height = this.scrollHeight + 'px';
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

var forms = document.querySelectorAll('form');
if (forms.length > 0){
	forms[0].onsubmit = validate;
	var type;
	for (var i = 0; i < forms[0].elements.length; i++){
		type = forms[0].elements[i].getAttribute('data-validate');
		if (type != null){
			if (type == 'numeric'){
				new numericInputHandler(forms[0].elements[i]);
			}
			else if (type == 'currency'){
				new currencyInputHandler(forms[0].elements[i]);
			}
			else if (type == 'date'){
				new dateInputHandler(forms[0].elements[i]);
			}
		}
		if (forms[0].elements[i].tagName == 'TEXTAREA')
			new textareaHandler(forms[0].elements[i]);
	}
}

function isNumeric(n) {
	return !isNaN(parseFloat(n)) && isFinite(n);
}
