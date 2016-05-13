
var sections = document.querySelectorAll('section');
for (var i = 0; i < sections.length; i++){
//sections[i].style.height = sections[i].offsetHeight+'px';
sections[i].setAttribute('data-height', sections[i].offsetHeight);
}
var toggles = document.querySelectorAll('section a.toggle');
var togglingElem, expandedHeight;
for (var i = 0; i < toggles.length; i++)
toggles[i].onclick = function(){
	if (this.parentNode.className.indexOf('collapse') > 0){
		togglingElem = this.parentNode;
		togglingElem.style.transition = '';
		togglingElem.style.height = 'auto';
		togglingElem.className = togglingElem.className.replace(' collapse', '');
		expandedHeight = togglingElem.offsetHeight-12;
		togglingElem.style.height = '28px';
		setTimeout(
			function(){
				togglingElem.style.height = expandedHeight + 'px';
				togglingElem.setAttribute('data-height', expandedHeight);
				togglingElem.style.transition = 'height 1s';
			}, 50
		);
		setTimeout(
			function(){
				togglingElem.style.height = 'auto';
			}, 1500
		);
	}
	else{
		togglingElem = this.parentNode;
		togglingElem.style.height = togglingElem.getAttribute('data-height') + 'px';
		togglingElem.style.transition = 'height 1s';
		setTimeout(
			function(){
				togglingElem.style.height = '28px';
				togglingElem.className = togglingElem.className + ' collapse';
			}, 50
		);
	}
}
