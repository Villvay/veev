
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

function elem(tagname, innerHTML, options){
	var obj = document.createElement(tagname);
	if (typeof innerHTML !== 'undefined' && innerHTML != null && innerHTML != false)
		obj.innerHTML = innerHTML;
	if (typeof options !== 'undefined')
		for (var key in options)
			obj.setAttribute(key, options[key]);
	return obj;
}

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
