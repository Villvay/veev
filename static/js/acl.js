
function aclSelect(chk){
	var name = chk.name.replace(/]/g, '').split('[');
	var view = chk.form.elements[name[0]+'['+name[1]+'][view]'];
	var add = chk.form.elements[name[0]+'['+name[1]+'][add]'];
	var edit = chk.form.elements[name[0]+'['+name[1]+'][edit]'];
	var del = chk.form.elements[name[0]+'['+name[1]+'][delete]'];
	var full = chk.form.elements[name[0]+'['+name[1]+'][full]'];
	if (name[2] == 'full')
		view.checked = add.checked = edit.checked = del.checked = chk.checked;
	else
		full.checked = (view.checked && add.checked && edit.checked && del.checked);
	for (var i = 0; i < chk.form.elements.length; i++)
		if (chk.form.elements[i].name.startsWith(name[0]+'['+name[1]+'/') && (name[2] == 'full' || chk.form.elements[i].name.endsWith(']['+name[2]+']'))){
			chk.form.elements[i].disabled = !chk.checked;
			if (!chk.checked){
				chk.form.elements[i].checked = false;
				aclSelect(chk.form.elements[i]);
			}
		}
}

var checkboxes = document.querySelectorAll('input[type="checkbox"]');
for (var i = 0; i < checkboxes.length; i++)
	if (!checkboxes[i].name.endsWith('[full]'))
		aclSelect(checkboxes[i]);
