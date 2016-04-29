
function slideShow(ul){
	var slides = ul.querySelectorAll('li');
	var i = 0, pi = 0;
	var _self = this;
	slides[0].className = 'active';
	ul.style.height = slides[0].offsetHeight + 'px';
	//
	this.getNext = function(){
		pi = i;
		i += 1;
		if (i == slides.length)
			i = 0;
		getSlide();
	}
	//
	this.getPrev = function(){
		pi = i;
		i -= 1;
		if (i == -1)
			i = slides.length - 1;
		getSlide();
	}
	//
	var getSlide = function(){
		slides[i].className = 'active';
		slides[pi].className = 'prev';
		setTimeout(function(){slides[pi].className = '';}, 1000);
		ul.style.height = slides[i].offsetHeight + 'px';
	}
	//
	var autoTimer = false;
	var autoSlide = function(){
		_self.getNext();
		setTimeout(autoSlide, 3200);
	}
	setTimeout(autoSlide, 3200);
}
