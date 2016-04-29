
var xmlhttp, ajax_engaged;
var ajax_q = new Array();

function ajax(URL, Method, Data, Deligate, Deligate_alt) {
    var _this = this;
    var loading_indicator = document.getElementById('loading_indicator');
    if (typeof Deligate == 'function') {
        this.function_deligate = Deligate;
    } else {
        var elem = document.getElementById(Deligate);
        if (typeof elem == 'object' && elem.toString().indexOf("Element") > -1) {
            this.element = elem;
        }
    }
    if (typeof Deligate_alt == 'function') {
        this.function_deligate_alt = Deligate_alt;
    }
    if (window.XMLHttpRequest) {
        this.xmlhttp = new XMLHttpRequest();
    } else if (window.ActiveXObject) {
        this.xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
    } else {
        alert("Your browser does not support AJAX!");
        return false;
    }
    this.ReceiveAjax = function () {
        if (this.readyState == 4) {
            if (this.status == 200) {
                if (_this.element != undefined)
                    _this.element.innerHTML = this.responseText;
                else if (_this.function_deligate != undefined)
                    _this.function_deligate(this);
                if (this.responseText.indexOf('<script>') > -1) {
                    var P1 = this.responseText.indexOf('<script>') + 8;
                    var P2 = this.responseText.indexOf('</script>', P1);
                    try {
                        eval(this.responseText.substring(P1, P2));
                    } catch (e) {}
                }
            } else {
                if (_this.function_deligate_alt != undefined)
                    _this.function_deligate_alt(this);
            }
            /*if (AJAX_disable_form_while_submitting && (Data.toString().indexOf("Form") > -1 && (Data.tagName != undefined && Data.tagName == "FORM"))) {
                var ElemCount = Data.elements.length;
                for (var i = 0; i < ElemCount; i++) {
                    Data.elements[i].disabled = false;
                }
            }*/
            if (loading_indicator != undefined)
                loading_indicator.style.display = 'none';
        }
    };
    if (loading_indicator != undefined)
        loading_indicator.style.display = 'block';
    Method = Method.toUpperCase();
    this.xmlhttp.open(Method, URL, true);
    this.xmlhttp.onreadystatechange = this.ReceiveAjax;
    if (Method == "POST"){
	if (Data != null && Data.toString().indexOf("Form") > -1 && (Data.tagName != undefined && Data.tagName == "FORM")){
		var params = '';
		var ElemCount = Data.elements.length;
		for (var i = 0; i < ElemCount; i++) {
		    if (Data.elements[i].type == "checkbox")
			params += Data.elements[i].checked ? (params == '' ? '' : '&') + Data.elements[i].name + '=on' : '';
		    else if (Data.elements[i].type == "radio")
			params += Data.elements[i].checked ? (params == '' ? '' : '&') + Data.elements[i].name + '=' + encodeURIComponent(Data.elements[i].value) : '';
		    else
			params += (params == '' ? '' : '&') + Data.elements[i].name + '=' + encodeURIComponent(Data.elements[i].value);
		    /*if (AJAX_disable_form_while_submitting)
			Data.elements[i].disabled = true;*/
		}
	}
	else
		params = Data;
        //this.xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        this.xmlhttp.send(params);
    } else {
        this.xmlhttp.send(null);
    }
}

//	------------------------------------------------------

function elem(tagname, innerHTML, options){
	var obj = document.createElement(tagname);
	if (typeof innerHTML !== 'undefined' && innerHTML != null && innerHTML != false)
		obj.innerHTML = innerHTML;
	if (typeof options !== 'undefined')
		for (var key in options)
			obj.setAttribute(key, options[key]);
	return obj;
}

function arcReactor(data){
	var obj;
	for (var tagname in data){
		obj = document.createElement(tagname);
		for (var key in data[tagname]){
			var value = data[tagname][key]
			if (key == 'content'){
				if (typeof value == 'string' || typeof value == 'number')
					obj.innerHTML = value;
				else if (typeof value == 'object')
					for (var i = 0; i < value.length; i++)
						obj.appendChild(arcReactor(value[i]));
			}
			else if (key.substring(0, 2) == 'on')
				obj[key] = value;
			else
				obj.setAttribute(key, value);
		}
	}
	return obj;
}

function arcTable(data, schema){
	var table = elem('table', false, {class: 'table-striped', width: '100%'});
	var tr = table.appendChild(elem('tr', false, {'data-id': 'head'}));
	for (var i = 0; i < schema.length; i++){
		if (schema[i].type != undefined){
			if (schema[i].type == 'numeric')
				tr.appendChild(elem('th', schema[i].title, {align: 'right'}));
			else
				tr.appendChild(elem('th', schema[i].title));
		}
		else
			tr.appendChild(elem('th', schema[i].title));
	}
	var tmp;
	for (var i = 0; i < data.length; i++){
		tr = table.appendChild(elem('tr', false, {'data-id': (data[i].id != undefined ? data[i].id : '')}));
		for (var j = 0; j < schema.length; j++){
			tmp = data[i][schema[j].name];
			if (schema[j]['enum'] != undefined && schema[j]['enum'][tmp] != undefined)
				tmp = schema[j]['enum'][tmp];
			if (schema[j].type != undefined){
				if (schema[j].type == 'numeric')
					tr.appendChild(elem('td', tmp+'&nbsp;', {align: 'right'}));
				else
					tr.appendChild(elem('td', tmp+'&nbsp;'));
			}
			else
				tr.appendChild(elem('td', tmp+'&nbsp;'));
		}
	}
	return table;
}

//	------------------------------------------------------

Array.prototype.max = function() {
	return Math.max.apply(null, this);
};

Array.prototype.min = function() {
	return Math.min.apply(null, this);
};

Float32Array.prototype.max = function() {
	return Math.max.apply(null, this);
};

Float32Array.prototype.min = function() {
	return Math.min.apply(null, this);
};

//	------------------------------------------------------

function isNumeric(n) {
	return !isNaN(parseFloat(n)) && isFinite(n);
}

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

//	------------------------------------------------------

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

var forms = document.querySelectorAll('form.autopilot');
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

//	------------------------------------------------------

var ScrollingTimeout = false;
var programmetic_scrolling = false;

function append_popup(data, top_id, style) {
    var elem = document.createElement('div');
    elem.setAttribute('id', top_id);
    elem.setAttribute('class', 'popup');
    elem.setAttribute('style', style);
    elem.innerHTML = data.xmlhttp.responseText;
    document.getElementById('popup_space').appendChild(elem);
    popup_bring_up(top_id, 0);
}

function popup_bring_up(Id, Opacity) {
    set_opacity(document.getElementById(Id), Opacity);
    if (Opacity == 100) {} else {
        setTimeout("popup_bring_up('" + Id + "', " + Opacity + " + 20);", 40);
        if (!ScrollingTimeout)
            scroll_to_top();
    }
}

function scroll_to_top() {
    var top = (document.documentElement && document.documentElement.scrollTop || document.body && document.body.scrollTop || 0);
    if (top > 10) {
        programmetic_scrolling = true;
        window.scrollBy(0, -1 * (2 * top / 5));
        programmetic_scrolling = false;
        ScrollingTimeout = setTimeout("scroll_to_top();", 80);
    } else {
        programmetic_scrolling = true;
        window.scrollTo(0, 0);
        programmetic_scrolling = false;
        clearTimeout(ScrollingTimeout);
        ScrollingTimeout = false;
    }
}
document.onscroll = function () {
    if (programmetic_scrolling)
        return false;
    clearTimeout(ScrollingTimeout);
    ScrollingTimeout = false;
}

function popup_bring_down(Id, Opacity) {
    var element = document.getElementById(Id);
    if (element == undefined) {
        history.go(-1);
        return false;
    }
    set_opacity(element, Opacity);
    if (Opacity == 0) {
        element.parentNode.removeChild(element);
    } else {
        setTimeout("popup_bring_down('" + Id + "', " + Opacity + " - 20);", 50);
    }
}
/*document.onkeyup = function (e) {
    if (!e) e = window.event;
    if ((e.keyCode && e.keyCode == "27") || (e.charCode && e.charCode == "27")) {
        var popups = document.getElementById('popup_space').childNodes;
        if (popups.length > 0) {
            var last_popup = popups[popups.length - 1];
            popup_bring_down('popup_space', 100);	//	last_popup.id
        }
    }
    if (typeof document_onkeyup == 'function') {
        document_onkeyup(e.keyCode ? e.keyCode : e.charCode);
    }
}*/
document.onkeydown = function (e) {
    if (!e) e = window.event;
    if ((e.keyCode && e.ctrlKey && e.keyCode == "83") || (e.charCode && e.ctrlKey && e.charCode == "83")) {
        var popups = document.getElementById('popup_space');
        if (popups != undefined && popups.childNodes.length > 0)
            return false;
        for (var i = 0; i < document.forms.length; i++) {
            if (document.forms[i].getAttribute('class') != "search" && document.forms[i].name != "format_xml") {
                if (!(is_changed == undefined))
                    is_changed = false;
                document.forms[i].submit();
                return false;
            }
        }
    }
}

function set_opacity(Elem, Opacity) {
    try {
        Elem.style.opacity = Opacity / 100;
        Elem.style.MozOpacity = Opacity / 100;
        if (Opacity == 100)
            Elem.style.filter = "";
        else
            Elem.style.filter = "alpha(opacity = " + Opacity + ")";
    } catch (e) {}
}
