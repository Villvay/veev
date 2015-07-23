
function select2filter(select){
	select.parentNode.style.position = 'relative';
	select.style.display = 'none';
	var _self = this;
	//
	var input = elem('input', null, {'type': 'text', 'name': select.name+'_text', 'autocomplete': 'off', 'class': 'form-control'});
	select.parentNode.appendChild(input);
	if (select.value != '')
		for (var i = 0; i < select.options.length; i++)
			if (select.options[i].value == select.value)
				input.value = select.options[i].text;
	//
	this.list = elem('ul', null, {'class': 'suggestion-list'});
	select.parentNode.appendChild(_self.list);
	_self.list.style.display = 'none';
	//
	this.index = -1;
	//
	input.onkeyup = function(e){
		if (e.keyCode == 13 || e.keyCode == 38 || e.keyCode == 40)
			return false;
		_self.list.innerHTML = '';
		_self.index = -1;
		if (input.value.trim() != '')
			for (var i = 0; i < select.options.length; i++)
				if (select.options[i].text.toLowerCase().indexOf(input.value.toLowerCase()) > -1)
					_self.list.appendChild(new select2filter_item(_self, select.options[i].text.replace(new RegExp("("+preg_quote(input.value)+")", 'gi'), "<b>$1</b>"), select.options[i].value));
		_self.list.style.display = (_self.list.childNodes.length == 0) ? 'none' : 'block';
		if (_self.list.childNodes.length > 0){
			_self.list.childNodes[0].className = 'sel';
			_self.index = 0;
		}
	};
	//
	input.onfocus = function(){
		_self.list.style.display = (_self.list.childNodes.length == 0) ? 'none' : 'block';
	};
	select.onfocus = function(){
		input.focus();
	};
	input.onmouseup = function(){
		this.select();
	};
	//
	input.onblur = function(){
		_self.list.style.display = 'none';
	};
	//
	input.onkeydown = function(e){
		//alert(e.keyCode);
		if (e.keyCode == 38 || e.keyCode == 40){
			if (e.keyCode == 38 && _self.index > 0)							//	Up
				_self.index -= 1;
			else if (e.keyCode == 40 && _self.index < _self.list.childNodes.length - 1)	//	Down
				_self.index += 1;
			for (var i = 0; i < _self.list.childNodes.length; i++)
				_self.list.childNodes[i].className = '';
			//
			var selItem = _self.list.childNodes[_self.index];
			selItem.className = 'sel';
			_self.setValue(selItem.getAttribute('data-id'), selItem.innerHTML, false);
			//
			e.stopPropagation();
			return false;
		}
		if (e.keyCode == 13){
			var selItem = _self.list.childNodes[_self.index];
			_self.setValue(selItem.getAttribute('data-id'), selItem.innerHTML, true);
			_self.list.style.display = 'none';
			//
			e.stopPropagation();
			return false;
		}
	};
	//
	this.setValue = function(value, text, refresh_filter){
		//if (typeof refresh_filter === 'undefined')
		//	refresh_filter = false;
		input.value = text.replace(/<b>/g, '').replace(/<\/b>/g, '');
		select.value = value;
		try{
			select.onchange();
		}catch(e){}
		if (refresh_filter)
			input.onkeyup({'e': {'keyCode': 666}});
	}
}

function select2filter_item(select2filter, text, value){
	var li = elem('li', text, {'data-id': value});
	//
	li.onmouseover = function(){
		for (var i = 0; i < select2filter.list.childNodes.length; i++)
			if (select2filter.list.childNodes[i] == li)
				select2filter.index = i;
			else
				select2filter.list.childNodes[i].className = '';
		li.className = 'sel';
	}
	//
	li.onmousedown = function(){
		select2filter.setValue(this.getAttribute('data-id'), this.innerHTML, true);
	}
	//
	return li;
}

function preg_quote( str ) {
    // http://kevin.vanzonneveld.net
    // +   original by: booeyOH
    // +   improved by: Ates Goral (http://magnetiq.com)
    // +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // +   bugfixed by: Onno Marsman
    // *     example 1: preg_quote("$40");
    // *     returns 1: '\$40'
    // *     example 2: preg_quote("*RRRING* Hello?");
    // *     returns 2: '\*RRRING\* Hello\?'
    // *     example 3: preg_quote("\\.+*?[^]$(){}=!<>|:");
    // *     returns 3: '\\\.\+\*\?\[\^\]\$\(\)\{\}\=\!\<\>\|\:'
    return (str+'').replace(/([\\\.\+\*\?\[\^\]\$\(\)\{\}\=\!\<\>\|\:])/g, "\\$1");
}
