
var FocusElem;
var ElemYear, ElemMonth, ElemDay;
var CalYear, CalMonth, CalDay;
var HideTimeout, TTransition;
var today = new Date();
var clock_div;
var cal_hover_latch = false;

//document.onclick = PreHideCalendar;

function ShowCalendar(ToElem, clock){
	if (cal_hover_latch == 2)
		return false;
	FocusElem = ToElem;
	var Cal = GetElem("calendar");
	var positon = GetTopLeft(ToElem);
	//Cal.style
	clock_div = GetElem('clock');
	if (clock == 'clock'){
		clock_div.style.display = 'block';
	}
	else{
		clock_div.style.display = 'none';
	}
	//
	Cal.style.left = (positon.Left) + 'px';
	Cal.style.top = (positon.Top) + 'px';
	Cal.style.display = "block";
	CalDay = -1;
	ElemYear = -1;
	ElemMonth = -1;
	ElemDay = -1;
	var DParam = ToElem.value.split("-");
	if (ToElem.value != "" && (ToElem.value != "0000-00-00" || ToElem.value != "0000-00-00 00:00:00") && DParam.length == 3){
		CalYear = parseInt(DParam[0], 10);
		CalMonth = parseInt(DParam[1], 10);
		CalDay = parseInt(DParam[2], 10);
		ElemYear = CalYear;
		ElemMonth = CalMonth;
		ElemDay = CalDay;
		document.calendar_form.year.value = CalYear;
		document.calendar_form.cal_month.value = CalMonth;
		//
		if (ToElem.value.length > 11){
			var time = ToElem.value.split(' ');
			time = time[1].split(':');
			document.calendar_form.hour.value = time[0];
			document.calendar_form.minute.value = parseInt(time[1]/5)*5;
		}
	}
	Calendar_FillMonth();
	FadeInCalendar(0);
	clearTimeout(HideTimeout);
	//
	ToElem.onblur = function(){
		if (!cal_hover_latch)
			PreHideCalendar();
	}
	Cal.onmouseover = function(){
		cal_hover_latch = 2;
		FocusElem.focus();
		cal_hover_latch = true;
	}
	Cal.onmouseout = function(){
		/*cal_hover_latch = 2;
		FocusElem.focus();*/
		cal_hover_latch = false;
	}
	return false;
}

function FadeInCalendar(Opacity){
	var ThisImage = GetElem("calendar");
	ThisImage.style.opacity = Opacity/100;
	ThisImage.style.MozOpacity = Opacity/100;
	ThisImage.style.filter = "alpha(opacity = " + Opacity + ")";
	if (Opacity > 99)
		GetElem("calendar").style.display = "block";
	else
		TTransition = setTimeout("FadeInCalendar(" + (Opacity+20) + ");", 40);
}

function FadeOutCalendar(Opacity){
	var ThisImage = GetElem("calendar");
	ThisImage.style.opacity = Opacity/100;
	ThisImage.style.MozOpacity = Opacity/100;
	ThisImage.style.filter = "alpha(opacity = " + Opacity + ")";
	if (Opacity < 14)
		GetElem("calendar").style.display = "none";
	else
		TTransition = setTimeout("FadeOutCalendar(" + (Opacity-20) + ");", 40);
}

function PreHideCalendar(){
	//HideCalendar();
	HideTimeout = setTimeout("HideCalendar();", 10);
}

function CloseCalendar(){
	FocusElem.value = '';
	FocusElem.onchange();
	FadeOutCalendar(100);
}

function HideCalendar(){
	//GetElem("calendar").style.display = "none";
	FadeOutCalendar(100);
}

function calendar_prev_month(){
	clearTimeout(HideTimeout);
	if (CalMonth == 1){
		CalMonth = 12;
		CalYear = parseInt(CalYear) - 1;
	}
	else{
		CalMonth = parseInt(CalMonth) - 1;
	}
	CalDay = -1;
	document.calendar_form.year.value = CalYear;
	document.calendar_form.cal_month.value = CalMonth;
	Calendar_FillMonth();
}

function calendar_next_month(){
	clearTimeout(HideTimeout);
	if (CalMonth == 12){
		CalMonth = 1;
		CalYear = parseInt(CalYear) + 1;
	}
	else{
		CalMonth = parseInt(CalMonth) + 1;
	}
	CalDay = -1;
	document.calendar_form.year.value = CalYear;
	document.calendar_form.cal_month.value = CalMonth;
	Calendar_FillMonth();
}

function Calendar_FillMonth(){
	if (document.calendar_form == undefined || document.calendar_form.year.value == undefined)
		return;
	CalYear = parseInt(document.calendar_form.year.value);
	CalMonth = parseInt(document.calendar_form.cal_month.value);
	// ---------------------------------------------------------------------------------
	var CalContent = GetElem("CalendarContent");
	var MDate = new Date(CalYear, CalMonth - 1, 1, 12, 0, 0, 0);
	var WeekDay = MDate.getDay();
	// ---------------------------------------------------------------------------------
	CalContent.innerHTML = "";
	for (var i = 0; i < WeekDay; i++)
		CalContent.innerHTML += "<div class=\"cal_date_empty\"></div>";
	for (var i = 1; i < 33; i++){
		//MDate.setDate(i);
		WeekDay = MDate.getDay();
		if (i == ElemDay && ElemMonth == CalMonth && ElemYear == CalYear)
			CalContent.innerHTML += "<div class=\"cal_date current_date\" onclick=\"Calendar_Set_Date(" + i + ")\">" + i + "</div>";
		else if (i == today.getDate() && CalMonth == today.getMonth()+1 && CalYear == today.getFullYear())
			CalContent.innerHTML += "<div class=\"cal_date today\" onclick=\"Calendar_Set_Date(" + i + ")\">" + i + "</div>";
		else
			CalContent.innerHTML += "<div class=\"cal_date\" onclick=\"Calendar_Set_Date(" + i + ")\">" + i + "</div>";
		MDate.setDate(i+1);
		if (MDate.getMonth() != CalMonth - 1)
			break;
		if (WeekDay == 6)
			CalContent.innerHTML += "<br style=\"clear:both;\"/>";
	}
	if (WeekDay != 0)
		for (var i = WeekDay; i < 6; i++)
			CalContent.innerHTML += "<div class=\"cal_date_empty\"></div>";
}

function Calendar_Set_Date(Day){
	CalDay = Day;
	FocusElem.value = CalYear + "-" + PadNumber(CalMonth, 2) + "-" + PadNumber(Day, 2);
	if (clock_div.style.display == 'block')
		FocusElem.value += ' '+(document.calendar_form.hour.value == '' ? '00' : document.calendar_form.hour.value)+':'+(document.calendar_form.minute.value == '' ? '00' : document.calendar_form.minute.value)+':00';
	PreHideCalendar();
	FocusElem.onchange();
}

/*var cal_sels;
window.onload = function(){
	cal_sels = document.querySelectorAll('#calendar select');
	for (var i = 0; i < cal_sels.length; i++){
		cal_sels[i].onblur = function(){
			cal_hover_latch = 2;
			FocusElem.focus();
			cal_hover_latch = true;
		}
	}
}*/
